<?php 
  class Article {
    // DB stuff
    public $conn;
    private $table = 'article';

    // Article Properties
    public $id;
    public $user_id;
    public $title;
    public $subtitle;
    public $content;
    public $category;
    public $urlimage;
    public $countlike;
    public $countcomment;
    

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Create Article
    public function create($token) {
      // create random name
      $timestamp = time();
      $raw_token = $token . '|' . $timestamp;
      $random = hash_hmac('sha256', $raw_token, '3bacd2366b2a95d35b52dea21c35e1de47bc986c9da79da4f6666d7f5ea1ba38');
              
      $setCreate = '';
      $arrayCreate = [];
      if($this->title != NULL){
        $this->title = htmlspecialchars(strip_tags($this->title));
        $setCreate = $setCreate . 'title="' . $this->title .'", ';
        $arrayCreate['title'][] = $this->title;
      }
      if($this->subtitle != NULL){
        $this->subtitle = htmlspecialchars(strip_tags($this->subtitle));
        $setCreate = $setCreate . 'subtitle="' . $this->subtitle .'", ';
        $arrayCreate['subtitle'][] = $this->subtitle;
      }
      if($this->content != NULL){
        $this->content = htmlspecialchars(strip_tags($this->content));
        $setCreate = $setCreate . 'content="' . $this->content .'", ';
        $arrayCreate['content'][] = $this->content;
      }
      if($this->category != NULL){
        $this->category = htmlspecialchars(strip_tags($this->category));
        $setCreate = $setCreate . 'category="' . $this->category .'", ';
        $arrayCreate['category'][] = $this->category;
      }
      $setCreate = rtrim($setCreate, " \t\n\r\0\x0B,");


      // Create query
      $query = 'INSERT INTO ' . $this->table . ' SET user_id = (SELECT id FROM users WHERE token = :token),' . $setCreate . ', urlimage = :urlimage';

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
      $this->urlimage = $database->domain_name() . '/api/article/image/' .$random.'.'.$extfoto;

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
              'title' => $result['title'],
              'subtitle' => $result['subtitle'],
              'content' => $result['content'],
              'category' => $result['category'],
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

    // Authentication user article
    public function Auth_Check($token) {
      $query = 'SELECT * FROM article WHERE user_id=(SELECT id FROM users WHERE token = :token) AND id= :article_id';
      $stmt = $this->conn->prepare($query);
      $token = htmlspecialchars(strip_tags($token));
      $this->id = htmlspecialchars(strip_tags($this->id));
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':article_id', $this->id);
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
      $query = 'SELECT * FROM commentarticle WHERE user_id=(SELECT id FROM users WHERE token = :token) AND id= :comment_id';
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

    // Authentication user like article
    public function Auth_Check_Like($token, $idlike) {
      $query = 'SELECT * FROM likearticle WHERE user_id=(SELECT id FROM users WHERE token = :token) AND id= :idlike';
      $stmt = $this->conn->prepare($query);
      $token = htmlspecialchars(strip_tags($token));
      $idlike = htmlspecialchars(strip_tags($idlike));
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':idlike', $idlike);
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
      
      $setUpdate = '';
      $arrayUpdate = [];
      if($this->title != NULL){
        $this->title = htmlspecialchars(strip_tags($this->title));
        $setUpdate = $setUpdate . 'title="' . $this->title .'", ';
        $arrayUpdate['title'][] = $this->title;
      }
      if($this->subtitle != NULL){
        $this->subtitle = htmlspecialchars(strip_tags($this->subtitle));
        $setUpdate = $setUpdate . 'subtitle="' . $this->subtitle .'", ';
        $arrayUpdate['subtitle'][] = $this->subtitle;
      }
      if($this->content != NULL){
        $this->content = htmlspecialchars(strip_tags($this->content));
        $setUpdate = $setUpdate . 'content="' . $this->content .'", ';
        $arrayUpdate['content'][] = $this->content;
      }
      if($this->category != NULL){
        $this->category = htmlspecialchars(strip_tags($this->category));
        $setUpdate = $setUpdate . 'category="' . $this->category .'", ';
        $arrayUpdate['category'][] = $this->category;
      }
      $setUpdate = rtrim($setUpdate, " \t\n\r\0\x0B,");

      // Clean data
      $id = htmlspecialchars(strip_tags($id));
      $this->urlimage = htmlspecialchars(strip_tags($this->urlimage));
      $extfoto = 'png';

      // mengubah base64 menjadi foto
      $foto = base64_decode($this->urlimage);
      file_put_contents('image/'.$random.'.'.$extfoto, $foto);
      // membuat nama foto
      $database = new Database();
      $this->urlimage = $database->domain_name() . '/api/article/image/' .$random.'.'.$extfoto;

      // Update query
      $query = "UPDATE article SET " . $setUpdate ." WHERE id='" . $id ."';";

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
              'title' => $result['title'],
              'subtitle' => $result['subtitle'],
              'content' => $result['content'],
              'category' => $result['category'],
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
      $query_total = 'SELECT id FROM article WHERE user_id=(SELECT id FROM users WHERE token = :token)';
      $stmt_total = $this->conn->prepare($query_total);
      $stmt_total->bindParam(':token', $token, PDO::PARAM_STR);
      $data = [];
      if($stmt_total->execute()){
        $result_total = $stmt_total->fetchAll(PDO::FETCH_ASSOC);
        $index = 0;
        foreach ($result_total as $item) {
          $index++;
          $query1 = 'SELECT * FROM article WHERE user_id=(SELECT id FROM users WHERE token = :token) AND id= :id';
          $query2 = 'SELECT * FROM likearticle WHERE article_id = :id';
          $query3 = 'SELECT * FROM commentarticle WHERE article_id = :id';
          $stmt1 = $this->conn->prepare($query1);
          $stmt2 = $this->conn->prepare($query2);
          $stmt3 = $this->conn->prepare($query3);
          $stmt1->bindParam(':token', $token, PDO::PARAM_STR);
          $stmt1->bindParam(':id', $item['id'], PDO::PARAM_STR);
          $stmt2->bindParam(':id', $item['id'], PDO::PARAM_STR);
          $stmt3->bindParam(':id', $item['id'], PDO::PARAM_STR);
          if ($stmt1->execute() &&$stmt2->execute() && $stmt3->execute()) {
            $data[$index]['article'] = $stmt1->fetchAll(PDO::FETCH_ASSOC);
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

    // Delete Article
    public function deletearticle($idarticle) {
      // Create query
      $query1 = 'DELETE FROM commentarticle WHERE article_id ="' . $idarticle . '";';
      $query2 = 'DELETE FROM article WHERE id ="' . $idarticle . '";';
      // $query3 = 'UPDATE users SET followers = followers-1 WHERE token = "' . $this->token . '";';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      // $stmt3 = $this->conn->prepare($query3);

      // Execute query
      if($stmt1->execute() && $stmt2->execute()){        
        return json_encode(
        array('data' => array (
            'id' => $idarticle
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Create Comment Article
    public function createcomment($token,$idarticle,$comment) {
      // Create query
      $query1 = 'INSERT INTO commentarticle SET  article_id ="' . $idarticle . '", user_id=(SELECT id FROM users WHERE token ="' . $token . '"), comment="' . $comment .'";';
      $query2 = 'UPDATE article SET countcomment = countcomment+1 WHERE id = ' . $idarticle . ';';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      
      // Execute query
      if($stmt1->execute() && $stmt2->execute()){        
        return json_encode(
        array('data' => array (
            'id' => $idarticle,
            'comment' => $comment
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Delete comment Article
    public function deletecomment($idcomment) {
      // Create query
      $query1 = 'UPDATE article SET countcomment = countcomment-1 WHERE id=(SELECT article_id FROM commentarticle WHERE  id ="' . $idcomment . '");';
      $query2 = 'DELETE FROM commentarticle WHERE id ="' . $idcomment . '";';

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
    public function like($token,$idarticle) {
      // Create query
      $query1 = 'INSERT INTO likearticle SET  article_id ="' . $idarticle . '", user_id=(SELECT id FROM users WHERE token ="' . $token . '");';
      $query2 = 'UPDATE article SET countlike = countlike+1 WHERE id = ' . $idarticle . ';';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);
      
      // Execute query
      if($stmt1->execute() && $stmt2->execute()){        
        return json_encode(
        array('data' => array (
            'id' => $idarticle
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

    // Delete like Article
    public function deletelike($idlike) {
      // Create query
      $query1 = 'UPDATE article SET countlike = countlike-1 WHERE id=(SELECT article_id FROM likearticle WHERE  id ="' . $idlike . '");';
      $query2 = 'DELETE FROM likearticle WHERE id ="' . $idlike . '";';

      // Prepare statement
      $stmt1 = $this->conn->prepare($query1);
      $stmt2 = $this->conn->prepare($query2);

      // Execute query
      if($stmt1->execute() && $stmt2->execute()){    
        return json_encode(
        array('data' => array (
            'idlike' => $idlike
        ))
        );
      }      
      // Print error if something goes wrong
      printf("Error: Error database system");

      return false;
    }

// Method to search articles based on search query
  public function searchArticles($search_query) {
    $query = "SELECT * FROM $this->table WHERE title LIKE ? OR content LIKE ?";
    
    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Bind parameters
    $search_param = "%$search_query%";
    $stmt->bindParam(1, $search_param);
    $stmt->bindParam(2, $search_param);

    // Execute query
    $stmt->execute();

    $articles_arr = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $article_item = array(
        'id' => $id,
        'title' => $title,
        'content' => html_entity_decode($content),
        'category' => $category,
        'urlimage' => $urlimage
      );

      array_push($articles_arr, $article_item);
    }

    return $articles_arr;
  }
// Method to recommend articles based on user's behavior or preferences
  public function recommendArticles($user_id) {
    // Example: You can implement your recommendation algorithm here based on user's behavior or preferences
    // For simplicity, let's assume we're just fetching some random articles
    $query = "SELECT * FROM $this->table ORDER BY RAND() LIMIT 5";

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Execute query
    $stmt->execute();

    $articles_arr = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $article_item = array(
        'id' => $id,
        'title' => $title,
        'content' => html_entity_decode($content),
        'category' => $category,
        'urlimage' => $urlimage
      );

      array_push($articles_arr, $article_item);
    }

    return $articles_arr;
  }

  }
