<?php 
  class Post {
    // DB stuff
    public $conn;
    private $table = 'post';

    // Post Properties
    public $id;
    public $user_id;
    public $caption;
    public $urlimage;
    public $countlike;
    public $countcomment;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Create Post
    public function create($token) {
      // create random name
      $timestamp = time();
      $raw_token = $token . '|' . $timestamp;
      $random = hash_hmac('sha256', $raw_token, '3bacd2366b2a95d35b52dea21c35e1de47bc986c9da79da4f6666d7f5ea1ba38');
              
      // Create query
      $query = 'INSERT INTO ' . $this->table . ' SET user_id = (SELECT id FROM users WHERE token = :token), caption = :caption, urlimage = :urlimage';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $token = htmlspecialchars(strip_tags($token));
      $this->caption = htmlspecialchars(strip_tags($this->caption));
      $this->urlimage = htmlspecialchars(strip_tags($this->urlimage));
      $extfoto = 'png';

      // mengubah base64 menjadi foto
      $foto = base64_decode($this->urlimage);
      file_put_contents('image/'.$random.'.'.$extfoto, $foto);
      // membuat nama foto
      $database = new Database();
      $this->urlimage = $database->domain_name() . '/api/post/image/' .$random.'.'.$extfoto;

      // Bind data
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':caption', $this->caption);
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
      }
      
      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }

    // Authentication user post
    public function Auth_Check($token) {
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

    // Authentication user comment post
    public function Auth_Check_Comment($token, $idcomment) {
      $query = 'SELECT * FROM commentpost WHERE user_id=(SELECT id FROM users WHERE token = :token) AND id= :comment_id';
      $stmt = $this->conn->prepare($query);
      $token = htmlspecialchars(strip_tags($token));
      $idcomment = htmlspecialchars(strip_tags($idcomment));
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':comment_id', $idcomment);
      $stmt->execute();
      $result = $stmt->rowCount();

      if ($result > 0) {
        return true;
      } else {
        return false;
      }
    }

    // Authentication user like post
    public function Auth_Check_Like($token, $id) {
      $query = 'SELECT * FROM likepost WHERE user_id=(SELECT id FROM users WHERE token = :token) AND post_id= :id';
      $stmt = $this->conn->prepare($query);
      $token = htmlspecialchars(strip_tags($token));
      $id = htmlspecialchars(strip_tags($id));
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':id', $id);
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
      // create random name
      $timestamp = time();
      $raw_token = $id . '|' . $timestamp;
      $random = hash_hmac('sha256', $raw_token, '3bacd2366b2a95d35b52dea21c35e1de47bc986c9da79da4f6666d7f5ea1ba38');
              
      // Clean data
      $id = htmlspecialchars(strip_tags($id));
      $this->caption = htmlspecialchars(strip_tags($this->caption));
      $this->urlimage = htmlspecialchars(strip_tags($this->urlimage));
      $extfoto = 'png';

      // mengubah base64 menjadi foto
      $foto = base64_decode($this->urlimage);
      file_put_contents('image/'.$random.'.'.$extfoto, $foto);
      // membuat nama foto
      $database = new Database();
      $this->urlimage = $database->domain_name() . '/api/post/image/' .$random.'.'.$extfoto;

      // Update query
      $query = "UPDATE post SET caption='" . $this->caption . "', urlimage='" . $this->urlimage ."' WHERE id='" . $id ."';";

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

    public function current($token){
      $query_total = 'SELECT id FROM post WHERE user_id=(SELECT id FROM users WHERE token = :token)';
      $stmt_total = $this->conn->prepare($query_total);
      $stmt_total->bindParam(':token', $token, PDO::PARAM_STR);
      $data = [];
      if($stmt_total->execute()){
        $result_total = $stmt_total->fetchAll(PDO::FETCH_ASSOC);
        $index = 0;
        foreach ($result_total as $item) {
          $index++;
          $query1 = 'SELECT * FROM post WHERE user_id=(SELECT id FROM users WHERE token = :token) AND id= :id';
          $query2 = 'SELECT * FROM likepost WHERE post_id = :id';
          $query3 = 'SELECT * FROM commentpost WHERE post_id = :id';
          $stmt1 = $this->conn->prepare($query1);
          $stmt2 = $this->conn->prepare($query2);
          $stmt3 = $this->conn->prepare($query3);
          $stmt1->bindParam(':token', $token, PDO::PARAM_STR);
          $stmt1->bindParam(':id', $item['id'], PDO::PARAM_STR);
          $stmt2->bindParam(':id', $item['id'], PDO::PARAM_STR);
          $stmt3->bindParam(':id', $item['id'], PDO::PARAM_STR);
          if ($stmt1->execute() &&$stmt2->execute() && $stmt3->execute()) {
            $data[$index]['post'] = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            $data[$index]['like'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $data[$index]['comment'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            
          }
        }
        return json_encode(array('data' => $data));

      }
      
      if ($stmt2->execute() && $stmt3->execute()) {
          $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
          $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
          $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
          
          return json_encode(array('data' => $result1,'comment' => $result2, 'like' =>$result3));
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
            'id' => $idpost
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Create Comment Post
    public function createcomment($token,$idpost,$comment) {
      // Create query
      $query1 = 'INSERT INTO commentpost SET  post_id ="' . $idpost . '", user_id=(SELECT id FROM users WHERE token ="' . $token . '"), comment="' . $comment .'";';
      $query2 = 'UPDATE post SET countcomment = countcomment+1 WHERE id = ' . $idpost . ';';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      
      // Execute query
      if($stmt1->execute() && $stmt2->execute()){        
        return json_encode(
        array('data' => array (
            'id' => $idpost,
            'comment' => $comment
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Delete comment Post
    public function deletecomment($idcomment) {
      // Create query
      $query1 = 'UPDATE post SET countcomment = countcomment-1 WHERE id=(SELECT post_id FROM commentpost WHERE  id ="' . $idcomment . '");';
      $query2 = 'DELETE FROM commentpost WHERE id ="' . $idcomment . '";';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);

      // Execute query
      if($stmt1->execute() && $stmt2->execute()){    
        return json_encode(
        array('data' => array (
            'idcomment' => $idcomment
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }
    
    // Create Like Post
    public function like($token,$idpost) {
      // Create query
      $query1 = 'INSERT INTO likepost SET  post_id ="' . $idpost . '", user_id=(SELECT id FROM users WHERE token ="' . $token . '");';
      $query2 = 'UPDATE post SET countlike = countlike+1 WHERE id = ' . $idpost . ';';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      
      // Execute query
      if($stmt1->execute() && $stmt2->execute()){        
        return json_encode(
        array('data' => array (
            'id' => $idpost
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Delete like Post
    public function deletelike($id, $token) {
      // Create query
      $query1 = 'UPDATE post SET countlike = countlike-1 WHERE id = :id;';
      $query2 = 'DELETE FROM likepost WHERE post_id = :id AND user_id=(SELECT id FROM users WHERE token = :token)';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      $stmt1->bindParam(':id', $id, PDO::PARAM_STR);
      $stmt2->bindParam(':id', $id, PDO::PARAM_STR);
      $stmt2->bindParam(':token', $token, PDO::PARAM_STR);
      // Execute query
      if($stmt1->execute() && $stmt2->execute()){    
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

    public function getRecommendation($token){
      $query_total = 'SELECT id FROM post WHERE user_id=(SELECT id FROM users WHERE token = :token) ORDER BY RAND() LIMIT 10';
      $stmt_total = $this->conn->prepare($query_total);
      $stmt_total->bindParam(':token', $token, PDO::PARAM_STR);
      $data = [];
      if($stmt_total->execute()){
        $result_total = $stmt_total->fetchAll(PDO::FETCH_ASSOC);
        $index = 0;
        foreach ($result_total as $item) {
          $index++;
          $query1 = 'SELECT * FROM post WHERE id= :id';
          $query2 = 'SELECT * FROM likepost WHERE post_id = :id';
          $query3 = 'SELECT * FROM commentpost WHERE post_id = :id';
          $stmt1 = $this->conn->prepare($query1);
          $stmt2 = $this->conn->prepare($query2);
          $stmt3 = $this->conn->prepare($query3);
          $stmt1->bindParam(':id', $item['id'], PDO::PARAM_STR);
          $stmt2->bindParam(':id', $item['id'], PDO::PARAM_STR);
          $stmt3->bindParam(':id', $item['id'], PDO::PARAM_STR);
          if ($stmt1->execute() &&$stmt2->execute() && $stmt3->execute()) {
            $data[$index]['post'] = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            $data[$index]['like'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $data[$index]['comment'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            
          }
        }
        return json_encode(array('data' => $data));

      }
      
      if ($stmt2->execute() && $stmt3->execute()) {
          $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
          $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
          $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
          
          return json_encode(array('data' => $result1,'comment' => $result2, 'like' =>$result3));
      } else {
          printf("Error: %s.\n", $stmt->error);
      }  
    }


    
  }