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

        // // Get raw posted data
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id)){
            // Check id post
            $query_id = 'SELECT * FROM post WHERE id=' . $data->id . ';';
            $stmt_id = $user->conn->prepare($query_id);
            if($stmt_id->execute()) {
                $result_id = $stmt_id->rowCount();
                if($result_id == 0) {
                    http_response_code(400);
                    echo json_encode(
                    array('errors' => array (
                        'message' => 'post not found'
                    ))
                    );
                } else {
                    $post = new Post($db);
                    // check if user has like post
                    $query = 'SELECT * FROM likepost WHERE post_id = :post_id AND user_id = (SELECT id FROM users WHERE token = :token);';
                    $data->id = htmlspecialchars(strip_tags($data->id));
                    $user->token = htmlspecialchars(strip_tags($user->token));
                    $stmt = $user->conn->prepare($query);
                    $stmt->bindParam(':post_id', $data->id);
                    $stmt->bindParam(':token', $user->token);
                    if($stmt->execute()) {
                        $result = $stmt->rowCount();
              
                        if ($result > 0) {
                            http_response_code(400);
                            echo json_encode(
                            array('errors' => array (
                                'message' => 'user has like post'
                            ))
                            );
                        } else{
                            $like = $post->like($user->token, $data->id);
                            echo $like;
                        }
                }   
            }
            
            }
        } else {
            http_response_code(400);
            $errors = [];
            if (!isset($data->id)) {
                $errors['message'][] = 'The id field is required';
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