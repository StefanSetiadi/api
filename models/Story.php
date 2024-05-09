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
      // create random name
      $timestamp = time();
      $raw_token = $token . '|' . $timestamp;
      $random = hash_hmac('sha256', $raw_token, '3bacd2366b2a95d35b52dea21c35e1de47bc986c9da79da4f6666d7f5ea1ba38');
        
      // Create query
      $query = 'INSERT INTO ' . $this->table . ' SET user_id = (SELECT id FROM users WHERE token = :token), urlimage = :urlimage';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $token = htmlspecialchars(strip_tags($token));
      $this->urlimage = htmlspecialchars(strip_tags($this->urlimage));
      $extfoto = 'png';

      // mengubah base64 menjadi foto
      $foto = base64_decode($this->urlimage);
      file_put_contents('image/'.$random.'.'.$extfoto, $foto);
      // membuat nama foto
      $database = new Database();
      $this->urlimage = $database->domain_name() . '/api/story/image/' .$random.'.'.$extfoto;


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

    // Authentication user story
    public function Auth_Check_Story($token) {
      $query = 'SELECT * FROM story WHERE user_id=(SELECT id FROM users WHERE token = :token) AND id= :story_id';
      $stmt = $this->conn->prepare($query);
      $token = htmlspecialchars(strip_tags($token));
      $this->id = htmlspecialchars(strip_tags($this->id));
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':story_id', $this->id);
      $stmt->execute();
      $result = $stmt->rowCount();

      if ($result > 0) {
        return true;
      } else {
        return false;
      }
    }

    // Update Story
    public function update($id) {
      // create random name
      $timestamp = time();
      $raw_token = $id . '|' . $timestamp;
      $random = hash_hmac('sha256', $raw_token, '3bacd2366b2a95d35b52dea21c35e1de47bc986c9da79da4f6666d7f5ea1ba38');
        
      // Clean data
      $id = htmlspecialchars(strip_tags($id));
      $this->urlimage = htmlspecialchars(strip_tags($this->urlimage));
      $extfoto = 'png';

      // mengubah base64 menjadi foto
      $foto = base64_decode($this->urlimage);
      file_put_contents('image/'.$random.'.'.$extfoto, $foto);
      // membuat nama foto
      $database = new Database();
      $this->urlimage = $database->domain_name() . '/api/story/image/' .$random.'.'.$extfoto;


      // Update query
      $query = "UPDATE story SET urlimage='" . $this->urlimage ."' WHERE id='" . $id ."';";

      // Prepare statement
      $stmt = $this->conn->prepare($query);


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
              'urlimage' => $result['urlimage'],
              'countview' => $result['countview'],
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

    // Delete Story
    public function deletestory($id) {
      // Create query
      // $query1 = 'DELETE FROM commentpost WHERE post_id ="' . $id . '";';
      $query2 = 'DELETE FROM story WHERE id ="' . $id . '";';

      // Prepare statement
      // $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      // $stmt3 = $this->conn->prepare($query3);

      // Execute query
      if($stmt2->execute()){        
        return json_encode(
        array('data' => array (
            'id' => $id
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }




    
  }