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

  if($method == 'GET'){
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
      $article = new Article($db);
      $article = $article->getRecommendation($user->token);
      http_response_code(200);
      echo $article;

    } else {
      http_response_code(401);
      echo json_encode(
        array('errors' => array (
          'message' => 'X-Authorization header not found. Authorization is required.'
        ))
      );
    }
  } 
  else {
    http_response_code(405);
    echo json_encode(
      array('errors' => array (
        'message' => 'method not allowed'
      ))
    );
  }