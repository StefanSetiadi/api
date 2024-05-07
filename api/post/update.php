<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/User.php';
  include_once '../../models/Post.php';

  $method = $_SERVER['REQUEST_METHOD'];

  if($method == 'POST'){  
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Get X-Authorization from HTTP header
    if(isset($_SERVER['HTTP_X_AUTHORIZATION'])){
        $x_authorization = $_SERVER['HTTP_X_AUTHORIZATION'];
        $post = new Post($db);

        if (!isset($data->id)) {
            http_response_code(400);
            $errors = [];
            $errors['id'][] = 'The id field is required';
            echo json_encode(['errors' => $errors]);
            exit();
        }

        $post->id = isset($data->id) ? $data->id : NULL;
        if(!$post->Auth_Check($x_authorization)){
            http_response_code(401);
            echo json_encode(
            array('errors' => array (
                'message' => 'unauthorized'
            ))
            );
            exit();
        }

        if (isset($data->caption) && isset($data->image)){
            $post->urlimage = isset($data->image) ? $data->image : NULL;
            $post->caption = isset($data->caption) ? $data->caption : NULL;
            $update = $post->update($post->id);
            echo $update;
        } else {
            http_response_code(400);
            $errors = [];
            if (!isset($data->caption) || !isset($data->image)) {
                $errors['message'][] = 'Use caption and image photo fields to update your post';
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