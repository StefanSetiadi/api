<?php 
  include_once '../../config/Database.php';

  class User {
    // DB stuff
    public $conn;
    private $table = 'users';

    // Post Properties
    public $id;
    public $username;
    public $password;
    public $name;
    public $email;
    public $token;
    public $bio;
    public $avatar;


    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Create User
    public function register() {
      // Create query
      $query = 'INSERT INTO ' . $this->table . ' SET username = :username, password = :password, name = :name, email = :email';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->username = htmlspecialchars(strip_tags($this->username));
      $this->password = htmlspecialchars(strip_tags($this->password));
      $this->name = htmlspecialchars(strip_tags($this->name));
      $this->email = htmlspecialchars(strip_tags($this->email));

      $this->password = hash('sha256',$this->password);
      // Bind data
      $stmt->bindParam(':username', $this->username);
      $stmt->bindParam(':password', $this->password);
      $stmt->bindParam(':name', $this->name);
      $stmt->bindParam(':email', $this->email);

      // Execute query
      if($stmt->execute()) {
        return [$this->id = $this->conn->lastInsertId(), 
                $this->name = $this->name,
                $this->username = $this->username,
                $this->email = $this->email];
      }
      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }

    // Login User
    public function loginUsername() {
      // Create query
      $query = 'SELECT * FROM ' . $this->table . ' WHERE username = :username AND password = :password';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->username = htmlspecialchars(strip_tags($this->username));
      $this->password = htmlspecialchars(strip_tags($this->password));

      $this->password = hash('sha256',$this->password);
      // Bind data
      $stmt->bindParam(':username', $this->username);
      $stmt->bindParam(':password', $this->password);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

    public function loginEmail() {
      // Create query
      $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email AND password = :password';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->email = htmlspecialchars(strip_tags($this->email));
      $this->password = htmlspecialchars(strip_tags($this->password));

      $this->password = hash('sha256',$this->password);
      // Bind data
      $stmt->bindParam(':email', $this->email);
      $stmt->bindParam(':password', $this->password);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Authentication User
    public function Auth_Check() {
      $query = 'SELECT * FROM users WHERE token = :token';
      $stmt = $this->conn->prepare($query);
      $this->token = htmlspecialchars(strip_tags($this->token));
      $stmt->bindParam(':token', $this->token);
      $stmt->execute();
      $result = $stmt->rowCount();

      if ($result > 0) {
        return true;
      } else {
        return false;
      }
    }

    // Get Current User
    public function getcurrent() {
      $query = 'SELECT * FROM users WHERE token = :token';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':token', $this->token);
      if($stmt->execute()) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC); 
        return json_encode(
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
    }

    // Update profile
    public function update() {
      $setUpdate = '';
      $arrayUpdate = [];
      if($this->name != NULL){
        $this->name = htmlspecialchars(strip_tags($this->name));
        $setUpdate = $setUpdate . 'name="' . $this->name .'", ';
        $arrayUpdate['name'][] = $this->name;
      }
      if($this->bio != NULL){
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $setUpdate = $setUpdate . 'bio="' . $this->bio .'", ';
        $arrayUpdate['bio'][] = $this->bio;
      }
      if($this->avatar != NULL){
        // create random name
        $timestamp = time();
        $raw_token = $this->avatar . '|' . $timestamp;
        $random = hash_hmac('sha256', $raw_token, '3bacd2366b2a95d35b52dea21c35e1de47bc986c9da79da4f6666d7f5ea1ba38');

        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $extfoto = 'png';
        $foto = base64_decode($this->avatar);
        file_put_contents('avatar/'.$random.'.'.$extfoto, $foto);
        // membuat nama foto
        $database = new Database();
        $this->avatar = $database->domain_name() . '/api/users/avatar/' .$random.'.'.$extfoto;
        $setUpdate = $setUpdate . 'avatar="' . $this->avatar .'", ';
        $arrayUpdate['avatar'][] = $this->avatar;
      }
      $setUpdate = rtrim($setUpdate, " \t\n\r\0\x0B,");
      // Create query
      $query = "UPDATE " . $this->table . " SET " . $setUpdate . " WHERE token='" . $this->token . "';";
      // $query = "UPDATE {$this->table} SET {$setUpdate} WHERE token={$this->token};";
      
      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      if($stmt->execute()){
        return json_encode(['data' => $arrayUpdate]);
      }      
      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }

    // Follow User
    public function follow($iduser) {
      // Create query
      $query1 = 'INSERT INTO follow (user_id, follower_id) VALUES (' . $iduser . ', (SELECT id FROM users WHERE token = "'. $this->token . '"));';
      $query2 = 'UPDATE users SET following = following + 1 WHERE token = "' . $this->token . '";';
      $query3 = 'UPDATE users SET followers = followers + 1 WHERE id = ' . $iduser . ';';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      $stmt3 = $this->conn->prepare($query3);

      // Execute query
      if($stmt1->execute() && $stmt2->execute() && $stmt3->execute()){        
        return json_encode(
        array('data' => array (
            'iduser' => $iduser
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Unfollow User
    public function unfollow($iduser) {
      // Create query
      $query1 = 'DELETE FROM follow WHERE user_id =' . $iduser . ' AND follower_id = (SELECT id FROM users WHERE token = "'. $this->token . '");';
      $query2 = 'UPDATE users SET following = following-1 WHERE token = "' . $this->token . '";';
      $query3 = 'UPDATE users SET followers = followers-1 WHERE id = ' . $iduser . ';';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      $stmt3 = $this->conn->prepare($query3);

      // Execute query
      if($stmt1->execute() && $stmt2->execute() && $stmt3->execute()){        
        return json_encode(
        array('data' => array (
            'iduser' => $iduser
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Delete Follower
    public function deletefollower($iduser) {
      // Create query
      $query1 = 'DELETE FROM follow WHERE user_id =(SELECT id FROM users WHERE token = "'. $this->token . '") AND follower_id = ' . $iduser . ';';
      $query2 = 'UPDATE users SET following = following-1 WHERE id = ' . $iduser . ';';
      $query3 = 'UPDATE users SET followers = followers-1 WHERE token = "' . $this->token . '";';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      $stmt3 = $this->conn->prepare($query3);

      // Execute query
      if($stmt1->execute() && $stmt2->execute() && $stmt3->execute()){        
        return json_encode(
        array('data' => array (
            'iduser' => $iduser
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Get Following User
    public function getfollowing(){
      $query = 'SELECT * FROM users WHERE id IN (
        SELECT user_id 
        FROM follow 
        WHERE follower_id = (SELECT id FROM users WHERE token = :token)
      )';
      $stmt = $this->conn->prepare($query);
      
      $stmt->bindParam(':token', $this->token, PDO::PARAM_STR);
      
      if ($stmt->execute()) {
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          return json_encode(array('data' => $result));
      } else {
          printf("Error: %s.\n", $stmt->error);
      }  
    }

    // Get Followers User
    public function getfollowers(){
      $query = 'SELECT * FROM users WHERE id IN (
        SELECT follower_id 
        FROM follow 
        WHERE user_id = (SELECT id FROM users WHERE token = :token)
      )';
      $stmt = $this->conn->prepare($query);
      
      $stmt->bindParam(':token', $this->token, PDO::PARAM_STR);
      
      if ($stmt->execute()) {
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          return json_encode(array('data' => $result));
      } else {
          printf("Error: %s.\n", $stmt->error);
      }  
    }

    // Search User
    public function search($search){
      $search = "%" . $search . "%";
      $query = 'SELECT * FROM users WHERE id IN (
        SELECT id
        FROM users
        WHERE LOWER(name) LIKE :search OR LOWER(username) LIKE :search
      )';
      $stmt = $this->conn->prepare($query);
      
      $stmt->bindParam(':search', $search, PDO::PARAM_STR);
      
      if ($stmt->execute()) {
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          return json_encode(array('data' => $result));
      } else {
          printf("Error: %s.\n", $stmt->error);
      }  
    }

    // Get Random User
    public function getrandom(){
      $query = 'SELECT * FROM users WHERE NOT token="' . $this->token . '" ORDER BY RAND() LIMIT 10;';
      $stmt = $this->conn->prepare($query);
      
      if ($stmt->execute()) {
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          return json_encode(array('data' => $result));
      } else {
          printf("Error: %s.\n", $stmt->error);
      }  
    }



    
  }