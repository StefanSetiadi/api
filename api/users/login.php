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

    // Get raw user data
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->username) && isset($data->password)){
      $user = new User($db);
      $user->username = $data->username;
      $user->password = $data->password;
      
      // login query
      $result = $user->login();
      $num = $result->rowCount();
      
      if($num > 0) {
        $result = $result->fetch(PDO::FETCH_ASSOC); 
        // create token
        $timestamp = time();
        $raw_token = $result['id'] . '|' . $timestamp;
        $encrypted_token = hash_hmac('sha256', $raw_token, '3bacd2366b2a95d35b52dea21c35e1de47bc986c9da79da4f6666d7f5ea1ba38');

        $user = new User($db);
        $query = 'UPDATE users SET token= :token WHERE id = :id';
        $stmt = $user->conn->prepare($query);
        $stmt->bindParam(':id', $result['id']);
        $stmt->bindParam(':token', $encrypted_token);
        if($stmt->execute()) {
          http_response_code(200);
          echo json_encode(
            array('data' => array (
              'id' => $result['id'],
              'username' => $result['username'],
              'email' => $result['email'],
              'name' => $result['name'],
              'token' => $encrypted_token
            ))
          );
        } else {
          printf("Error: %s.\n", $stmt->error);
        }

        
      } else {
        echo json_encode(
          array('errors' => array(
            'message' => 'username or password wrong'
          ))
        );
      }
    } else {
      http_response_code(400);
      $errors = [];
      if (!isset($data->username)) {
        $errors['username'][] = 'The username field is required.';
      }
      if (!isset($data->password)) {
          $errors['password'][] = 'The password field is required.';
      }
      echo json_encode(['errors' => $errors]);
    }
  } else {
    http_response_code(405);
    echo json_encode(
      array('errors' => array (
        'message' => 'method not allowed'
      ))
    );
  }
  
