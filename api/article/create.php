<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/User.php';
  include_once '../../models/Article.php';

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

        if (isset($data->title) && isset($data->content) && isset($data->image)){
            $article = new Article($db);
            $article->title = isset($data->title) ? $data->title : NULL;
            $article->subtitle = isset($data->subtitle) ? $data->subtitle : NULL;
            $article->content = isset($data->content) ? $data->content : NULL;
            $article->category = isset($data->category) ? $data->category : NULL;
            $article->urlimage = isset($data->image) ? $data->image : NULL;
            $create = $article->create($user->token);
            echo $create;
        } else {
            http_response_code(400);
            $errors = [];
            if (!isset($data->title) || !isset($data->content) || !isset($data->image)) {
                $errors['message'][] = 'Use title, content, and image photo fields to create your article';
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