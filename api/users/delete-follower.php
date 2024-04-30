<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/User.php';

  $method = $_SERVER['REQUEST_METHOD'];

  if($method == 'DELETE'){  
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
            // Check id user
            $query_id = 'SELECT * FROM users WHERE id=' . $data->iduser . ';';
            $stmt_id = $user->conn->prepare($query_id);
            if($stmt_id->execute()) {
                $result_id = $stmt_id->rowCount();
                if($result_id == 0) {
                    http_response_code(400);
                    echo json_encode(
                    array('errors' => array (
                        'message' => 'user not found'
                    ))
                    );
                } else {
                    // Check if the user has followed 
                    $query = 'SELECT * FROM follow WHERE follower_id=' . $data->iduser .' AND user_id = (SELECT id FROM USERS WHERE token="' . $user->token . '");';
                    $stmt = $user->conn->prepare($query);

                    if($stmt->execute()) {
                        $result = $stmt->rowCount();

                        if ($result > 0) {
                            $deletefollower = $user->deletefollower($data->iduser);
                            http_response_code(200);
                            echo $deletefollower;
                        } else {
                            http_response_code(400);
                            echo json_encode(
                            array('errors' => array (
                                'message' => 'follower not found'
                            ))
                            );
                        }
                    }
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