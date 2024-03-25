<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../app/database.php';
$database = new App\Database;
$pdo = $database->getConnection();


$app->get('/posts', function (Request $request, Response $response, $args) use ($pdo) {
    try {
        
        $stmt = $pdo->query('SELECT * FROM posts');
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $body = json_encode($posts);
        $response->getBody()->write($body);

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {

        $error = array("text" => $e->getMessage(), "code" => $e->getCode());
        $response->getBody()->write(json_encode($error));

        return $response->withHeader("Content-Type","application/json");
    }
});


$app->get('/comments', function (Request $request, Response $response, $args) use ($pdo) {
    try {

        $stmt = $pdo->query('SELECT * FROM comments');
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $body = json_encode($comments);
        $response->getBody()->write($body);

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {

        $error = array("text" => $e->getMessage(), "code" => $e->getCode());
        $response->getBody()->write(json_encode($error));

        return $response->withHeader("Content-Type","application/json");
    }
});


$app->get('/posts/{post_id}/comments', function (Request $request, Response $response, $args) use ($pdo) {

    $post_id = (int) $args['post_id'];

    try {

        $stmt = $pdo->prepare('SELECT * FROM comments WHERE post_id = :post_id');
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $body = json_encode($comments);
        $response->getBody()->write($body);

        return $response->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {
        
        $error = array("text" => $e->getMessage(), "code" => $e->getCode());
        $response->getBody()->write(json_encode($error));

        return $response->withHeader("Content-Type","application/json");
    }
});