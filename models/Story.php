<?php 
  class Story {
    // DB stuff
    public $conn;
    private $table = 'story';

    // Story Properties
    public $id;
    public $user_id;
    public $urlimage;
    public $countview;
    public $limittime;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Create Story
    public function create($token) {
      // Create query
      $query = 'INSERT INTO ' . $this->table . ' SET user_id = (SELECT id FROM users WHERE token = :token), urlimage = :urlimage';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $token = htmlspecialchars(strip_tags($token));
      $this->urlimage = htmlspecialchars(strip_tags($this->urlimage));

      // Bind data
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':urlimage', $this->urlimage);

      if($stmt->execute()){
        // Read data
        $query_result = 'SELECT * FROM ' . $this->table . ' WHERE id="' . $this->conn->lastInsertId() . '";';
        $stmt_result = $this->conn->prepare($query_result);
        if($stmt_result->execute()) {
          $result = $stmt_result->fetch(PDO::FETCH_ASSOC); 
          return json_encode(
            array('data' => array (
              'id' => $result['id'],
              'user_id' => $result['user_id'],
              'urlimage' => $result['urlimage'],
              'created_at' => $result['created_at'],
              'updated_at' => $result['updated_at']
            ))
          );
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
      }
      
      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }

    // Authentication user post
    public function Auth_Check_Story($token) {
      $query = 'SELECT * FROM post WHERE user_id=(SELECT id FROM users WHERE token = :token) AND id= :post_id';
      $stmt = $this->conn->prepare($query);
      $token = htmlspecialchars(strip_tags($token));
      $this->id = htmlspecialchars(strip_tags($this->id));
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':post_id', $this->id);
      $stmt->execute();
      $result = $stmt->rowCount();

      if ($result > 0) {
        return true;
      } else {
        return false;
      }
    }

    // Update Post
    public function update($id) {
      // Clean data
      $id = htmlspecialchars(strip_tags($id));
      $this->caption = htmlspecialchars(strip_tags($this->caption));
      $this->urlimage = htmlspecialchars(strip_tags($this->urlimage));

      // Update query
      $query = "UPDATE post SET caption='" . $this->caption . "', urlimage='" . $this->urlimage ."' WHERE id='" . $id ."';";

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // if($stmt->execute()){
      //   return json_encode(
      //     array('message' => array (
      //       'testing' => 'Ini bisa masuk',
      //       'query' => $stmt->queryString
      //     ))
      //   );

      if($stmt->execute()){
        // Read data
        $query_result = 'SELECT * FROM ' . $this->table . ' WHERE id="' . $id . '";';
        $stmt_result = $this->conn->prepare($query_result);
        if($stmt_result->execute()) {
          $result = $stmt_result->fetch(PDO::FETCH_ASSOC); 
          return json_encode(
            array('data' => array (
              'id' => $result['id'],
              'user_id' => $result['user_id'],
              'caption' => $result['caption'],
              'urlimage' => $result['urlimage'],
              'countlike' => $result['countlike'],
              'countcomment' => $result['countcomment'],
              'created_at' => $result['created_at'],
              'updated_at' => $result['updated_at']
            ))
          );
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);

        return false;
      }
      
      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }

    public function getstory($token){
      $query = 'SELECT * FROM story WHERE user_id=(SELECT id FROM users WHERE token = :token)';
      $stmt = $this->conn->prepare($query);
      
      $stmt->bindParam(':token', $token, PDO::PARAM_STR);
      
      if ($stmt->execute()) {
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          return json_encode(array('data' => $result));
      } else {
          printf("Error: %s.\n", $stmt->error);
      }  
    }

    // Delete Post
    public function deletepost($idpost) {
      // Create query
      $query1 = 'DELETE FROM commentpost WHERE post_id ="' . $idpost . '";';
      $query2 = 'DELETE FROM post WHERE id ="' . $idpost . '";';
      // $query3 = 'UPDATE users SET followers = followers-1 WHERE token = "' . $this->token . '";';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      // $stmt3 = $this->conn->prepare($query3);

      // Execute query
      if($stmt1->execute() && $stmt2->execute()){        
        return json_encode(
        array('data' => array (
            'idpost' => $idpost
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }




    
  }