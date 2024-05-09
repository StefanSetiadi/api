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

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Get X-Authorization from HTTP header
    if(isset($_SERVER['HTTP_X_AUTHORIZATION'])){
        $x_authorization = $_SERVER['HTTP_X_AUTHORIZATION'];
        $article = new Article($db);

        if (!isset($data->id)) {
            http_response_code(400);
            $errors = [];
            $errors['id'][] = 'The id field is required';
            echo json_encode(['errors' => $errors]);
            exit();
        }

        $article->id = isset($data->id) ? $data->id : NULL;
        if(!$article->Auth_Check($x_authorization)){
            http_response_code(401);
            echo json_encode(
            array('errors' => array (
                'message' => 'unauthorized'
            ))
            );
            exit();
        }

        if (isset($data->title) || isset($data->subtitle) || isset($data->content) || isset($data->category) || isset($data->image)){
            $article = new Article($db);
            $article->title = isset($data->title) ? $data->title : NULL;
            $article->subtitle = isset($data->subtitle) ? $data->subtitle : NULL;
            $article->content = isset($data->content) ? $data->content : NULL;
            $article->category = isset($data->category) ? $data->category : NULL;
            $article->urlimage = isset($data->image) ? $data->image : NULL;
            $update = $article->update($data->id);
            echo $update;
        } else {
            http_response_code(400);
            $errors = [];
            if (!isset($data->title) && !isset($data->subtitle) && !isset($data->content) && !isset($data->category) && !isset($data->image)) {
                $errors['message'][] = 'Use title, subtitle, content, category or image photo fields to update your article';
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