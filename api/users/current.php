<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/User.php';

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

      $query = 'SELECT * FROM users WHERE token = :token';
      $stmt = $user->conn->prepare($query);
      $stmt->bindParam(':token', $x_authorization);
      if($stmt->execute()) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC); 
        http_response_code(200);
        echo json_encode(
          array('data' => array (
            'id' => $result['id'],
            'username' => $result['username'],
            'email' => $result['email'],
            'name' => $result['name'],
            'bio' => $result['bio'],
            'avatar' => $result['avatar'],
            'verified' => $result['verified'],
            'countpost' => $result['countpost'],
            'countarticle' => $result['countarticle'],
            'following' => $result['following'],
            'followers' => $result['followers']
          ))
        );
      } else {
        printf("Error: %s.\n", $stmt->error);
      }

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