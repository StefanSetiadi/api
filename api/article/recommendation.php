<?php
// Include database configuration and Article model
include_once '../../config/Database.php';
include_once '../../models/Article.php';

// Instantiate Database and Article objects
$database = new Database();
$db = $database->connect();
$article = new Article($db);

// Assuming user_id is passed as a parameter (you can get it from user session or API request)
$user_id = $_GET['user_id'];

// Get recommended articles based on user's behavior
$recommended_articles = $article->recommendArticles($user_id);

// Check if any articles are recommended
if (!empty($recommended_articles)) {
    http_response_code(200);
    echo json_encode($recommended_articles);
} else {
    http_response_code(404);
    echo json_encode(array('message' => 'No recommended articles found.'));
}
?>
