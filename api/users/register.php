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

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->username) && isset($data->password) && isset($data->name) && isset($data->email)){
      if(filter_var($data->email, FILTER_VALIDATE_EMAIL) !== false){
        // check if username exists in the same database
        $user = new User($db);
        $query = 'SELECT * FROM users WHERE username = :username OR email = :email';
        $data->username = htmlspecialchars(strip_tags($data->username));
        $data->email = htmlspecialchars(strip_tags($data->email));
        $stmt = $user->conn->prepare($query);
        $stmt->bindParam(':username', $data->username);
        $stmt->bindParam(':email', $data->email);
        if($stmt->execute()) {
          $result = $stmt->rowCount();

          if ($result > 0) {
            http_response_code(400);
            echo json_encode(
              array('errors' => array (
                'message' => 'username or email already registered'
              ))
            );
          } else {
            // register user
            $user = new User($db);
            $user->username = $data->username;
            $user->password = $data->password;
            $user->name = $data->name;
            $user->email = $data->email;
            if($user->register()) {
              http_response_code(201);
              echo json_encode(
                array('data' => array(
                      'id' => $user->id,
                      'name' => $user->name,
                      'username' => $user->username,
                      'email' => $user->email))
              );
            } else {
              printf("Error: %s.\n", $stmt->error);
            }
          } 
        }
        
      } else {
        echo json_encode(
          array('errors' => array (
            'email' => 'The email field must be a valid email address.'
          ))
        );
      }
    } else {
      http_response_code(400);
      $errors = [];
      if (!isset($data->username)) {
        $errors['username'][] = 'The username field is required.';
      }
      if (!isset($data->email)) {
          $errors['email'][] = 'The email field is required.';
      }
      if (!isset($data->name)) {
          $errors['name'][] = 'The name field is required.';
      }
      if (!isset($data->password)) {
          $errors['password'][] = 'The password field is required.';
      }
      echo json_encode(['errors' => $errors]);
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