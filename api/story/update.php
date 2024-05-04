<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/User.php';
  include_once '../../models/Story.php';

  $method = $_SERVER['REQUEST_METHOD'];

  if($method == 'POST'){  
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Get X-Authorization from HTTP header
    if(isset($_SERVER['HTTP_X_AUTHORIZATION'])){
        $x_authorization = $_SERVER['HTTP_X_AUTHORIZATION'];
        $post = new Story($db);

        if (!isset($_POST['id'])) {
            http_response_code(400);
            $errors = [];
            $errors['id'][] = 'The id field is required';
            echo json_encode(['errors' => $errors]);
            exit();
        }

        $post->id = isset($_POST['id']) ? $_POST['id'] : NULL;
        if(!$post->Auth_Check($x_authorization)){
            http_response_code(401);
            echo json_encode(
            array('errors' => array (
                'message' => 'unauthorized'
            ))
            );
            exit();
        }

        // // Get raw posted data
        // $data = json_decode(file_get_contents("php://input"));

        if (isset($_POST['caption']) && isset($_FILES['image'] )){
            $image_tmp = $_FILES['image']['tmp_name'];
            $name_image = $_FILES['image']['name'];
    
            move_uploaded_file($image_tmp, 'image/'.$name_image);
            $post->urlimage = $database->domain_name() . '/api/post/image/' . $name_image;


            $post->caption = isset($_POST['caption']) ? $_POST['caption'] : NULL;
            $post->id = isset($_POST['id']) ? $_POST['id'] : NULL;
            $update = $post->update($post->id);
            echo $update;
        } else {
            http_response_code(400);
            $errors = [];
            if (!isset($data->name) || !isset($data->bio) || !isset($data->image)) {
                $errors['message'][] = 'Use caption and image photo fields to create your post';
            }
            echo json_encode(['errors' => $errors]);
        }
    } else {
        http_response_code(401);
        echo json_encode(
          array('errors' => array (
            'message' => 'X-Authorization header not found. Authorization is required.'
          ))
        );
      }

  } else {
        http_response_code(405);
        echo json_encode(
        array('errors' => array (
            'message' => 'method not allowed'
        ))
        );
    }