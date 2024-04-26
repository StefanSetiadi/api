<?php 
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
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $setUpdate = $setUpdate . 'avatar="' . $this->avatar .'", ';
        $arrayUpdate['avatar'][] = $this->avatar;
      }
      $setUpdate = rtrim($setUpdate, " \t\n\r\0\x0B,");
      // Create query
      $query = 'UPDATE ' . $this->table . ' SET ' . $setUpdate . ' WHERE token="' . $this->token . '";';

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
      $query = 'SELECT * FROM USERS WHERE token= ' . '"' .$this->token . '";';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      if($stmt->execute()){
        $following = $stmt->fetch(PDO::FETCH_ASSOC);
        $following = $following['following'];

        $following = $following . '.' . $iduser;

        // Create query
        $query = 'UPDATE ' . $this->table . ' SET ' . 'following="' . $following . '" WHERE token="' . $this->token . '";';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        if($stmt->execute()){
          return json_encode(['iduser' => $iduser]);
        }      
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
      }      
      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }


    
  }