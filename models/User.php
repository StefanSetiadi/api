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

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Get Posts
    // public function read() {
    //   // Create query
    //   $query = 'SELECT c.name as category_name, p.id, p.category_id, p.title, p.body, p.author, p.created_at
    //                             FROM ' . $this->table . ' p
    //                             LEFT JOIN
    //                               categories c ON p.category_id = c.id
    //                             ORDER BY
    //                               p.created_at DESC';
      
    //   // Prepare statement
    //   $stmt = $this->conn->prepare($query);

    //   // Execute query
    //   $stmt->execute();

    //   return $stmt;
    // }

    // // Get Single Post
    // public function read_single() {
    //       // Create query
    //       $query = 'SELECT c.name as category_name, p.id, p.category_id, p.title, p.body, p.author, p.created_at
    //                                 FROM ' . $this->table . ' p
    //                                 LEFT JOIN
    //                                   categories c ON p.category_id = c.id
    //                                 WHERE
    //                                   p.id = ?
    //                                 LIMIT 0,1';

    //       // Prepare statement
    //       $stmt = $this->conn->prepare($query);

    //       // Bind ID
    //       $stmt->bindParam(1, $this->id);

    //       // Execute query
    //       $stmt->execute();

    //       $row = $stmt->fetch(PDO::FETCH_ASSOC);

    //       // Set properties
    //       $this->title = $row['title'];
    //       $this->body = $row['body'];
    //       $this->author = $row['author'];
    //       $this->category_id = $row['category_id'];
    //       $this->category_name = $row['category_name'];
    // }

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
    public function login() {
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

    // // Update Post
    // public function update() {
    //       // Create query
    //       $query = 'UPDATE ' . $this->table . '
    //                             SET title = :title, body = :body, author = :author, category_id = :category_id
    //                             WHERE id = :id';

    //       // Prepare statement
    //       $stmt = $this->conn->prepare($query);

    //       // Clean data
    //       $this->title = htmlspecialchars(strip_tags($this->title));
    //       $this->body = htmlspecialchars(strip_tags($this->body));
    //       $this->author = htmlspecialchars(strip_tags($this->author));
    //       $this->category_id = htmlspecialchars(strip_tags($this->category_id));
    //       $this->id = htmlspecialchars(strip_tags($this->id));

    //       // Bind data
    //       $stmt->bindParam(':title', $this->title);
    //       $stmt->bindParam(':body', $this->body);
    //       $stmt->bindParam(':author', $this->author);
    //       $stmt->bindParam(':category_id', $this->category_id);
    //       $stmt->bindParam(':id', $this->id);

    //       // Execute query
    //       if($stmt->execute()) {
    //         return true;
    //       }

    //       // Print error if something goes wrong
    //       printf("Error: %s.\n", $stmt->error);

    //       return false;
    // }

    // // Delete Post
    // public function delete() {
    //       // Create query
    //       $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

    //       // Prepare statement
    //       $stmt = $this->conn->prepare($query);

    //       // Clean data
    //       $this->id = htmlspecialchars(strip_tags($this->id));

    //       // Bind data
    //       $stmt->bindParam(':id', $this->id);

    //       // Execute query
    //       if($stmt->execute()) {
    //         return true;
    //       }

    //       // Print error if something goes wrong
    //       printf("Error: %s.\n", $stmt->error);

    //       return false;
    // }
    
  }