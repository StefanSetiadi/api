<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/User.php';

  $method = $_SERVER['REQUEST_METHOD'];

  if($method == 'POST'){  
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

        if (isset($data->iduser)){
            // Check if the user has followed 
            // $query = 'SELECT * FROM follow WHERE user_id = :user_id AND follower_id = (SELECT id FROM USERS WHERE token=":token");';
            $query = 'SELECT * FROM follow WHERE user_id=' . $data->iduser .' AND follower_id = (SELECT id FROM USERS WHERE token="' . $user->token . '");';
            $stmt = $user->conn->prepare($query);
            // $stmt->bindParam(':user_id', $data->iduser);
            // $stmt->bindParam(':token', $user->token);

            if($stmt->execute()) {
                $result = $stmt->rowCount();

                if ($result > 0) {
                    $unfollow = $user->unfollow($data->iduser);
                    http_response_code(200);
                    echo $unfollow;
                } else {
                    http_response_code(400);
                    echo json_encode(
                    array('errors' => array (
                        'message' => 'user not followed'
                    ))
                    );
                }
            }
            
        } else {
            http_response_code(400);
            $errors = [];
            if (!isset($data->iduser)) {
                $errors['iduser'][] = 'The iduser field is required';
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