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

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Get X-Authorization from HTTP header
    if(isset($_SERVER['HTTP_X_AUTHORIZATION'])){
        $x_authorization = $_SERVER['HTTP_X_AUTHORIZATION'];
        $story = new Story($db);

        if (!isset($data->id) && !isset($data->image)) {
            http_response_code(400);
            $errors = [];
            $errors['message'][] = 'The id and image photo field is required';
            echo json_encode(['errors' => $errors]);
            exit();
        }

        $story->id = isset($data->id) ? $data->id : NULL;
        if(!$story->Auth_Check_Story($x_authorization)){
            http_response_code(401);
            echo json_encode(
            array('errors' => array (
                'message' => 'unauthorized'
            ))
            );
            exit();
        }

        if (isset($data->id) && isset($data->image)){
            $story->id = isset($data->id) ? $data->id : NULL;
            $story->urlimage = isset($data->image) ? $data->image : NULL;
            $update = $story->update($story->id);
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