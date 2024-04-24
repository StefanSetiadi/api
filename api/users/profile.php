<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/User.php';

  $method = $_SERVER['REQUEST_METHOD'];

  if($method == 'PUT'){  
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Get X-Authorization from HTTP header
    if(isset($_SERVER['HTTP_X_AUTHORIZATION'])){
        $x_authorization = $_SERVER['HTTP_X_AUTHORIZATION'];
        $user = new User($db);
        $user->token = $x_authorization;
        if(!$user->Auth_Check()){
            http_response_code(401);
            echo json_encode(
            array('errors' => array (
                'message' => 'unauthorized'
            ))
            );
            exit();
        }

        // Get raw posted data
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->name) || isset($data->bio) || isset($data->avatar)){
            $user->name = isset($data->name) ? $data->name : NULL;
            $user->bio = isset($data->bio) ? $data->bio : NULL;
            $user->avatar = isset($data->avatar) ? $data->avatar : NULL;
            $update = $user->update();
            echo $update;
        } else {
            http_response_code(400);
            $errors = [];
            if (!isset($data->name) || !isset($data->bio) || !isset($data->avatar)) {
                $errors['message'][] = 'Use the name, bio, or avatar fields to update your profile';
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