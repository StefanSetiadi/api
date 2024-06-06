<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Article.php';

  $method = $_SERVER['REQUEST_METHOD'];

  if ($method == 'POST') {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Check if search query is provided
    if (isset($data->search)) {
      $search_query = $data->search;

      // Instantiate Article object
      $article = new Article($db);
      
      // Search articles based on search query
      $result = $article->searchArticles($search_query);

      // Check if any articles found
      if (count($result) > 0) {
        echo json_encode($result);
      } else {
        http_response_code(404);
        echo json_encode(array('message' => 'No articles found for the given search query.'));
      }
    } else {
      http_response_code(400);
      echo json_encode(array('message' => 'Search query is required.'));
    }
  } else {
    http_response_code(405);
    echo json_encode(array('message' => 'Method not allowed.'));
  }
?>
