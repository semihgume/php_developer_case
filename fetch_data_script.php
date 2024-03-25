<?php

$host = 'localhost';
$port = '5432';
$dbname = 'apidb';
$user = 'postgres';
$password = 'root';

function connectDatabase($host, $port, $dbname, $user, $password) {
    try {
        return new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        die("Veritabanına bağlanılamadı: " . $e->getMessage());
    }
}

function fetchDataAndInsert($conn, $url, $table, $fields) {
    try {
        $jsonData = file_get_contents($url);
        $data = json_decode($jsonData, true);

        if ($data === null) {
            die("Veriler çözümlenemedi: $url");
        }

        $conn->beginTransaction();

        foreach ($data as $item) {
            $placeholders = array_map(function ($field) {
                return ":$field";
            }, $fields);

            $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $conn->prepare($sql);

            $params = [];
            foreach ($fields as $field) {
                $params[] = $field == 'user_id' ? $item['userId'] : ($field == 'post_id' ? $item['postId'] : $item[$field]);
            }

            $stmt->execute($params);
        }

        $conn->commit();

        echo ucfirst($table) . " verileri başarıyla veritabanına kaydedildi.\n";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

try {
    $conn = connectDatabase($host, $port, $dbname, $user, $password);

    fetchDataAndInsert($conn, 'https://jsonplaceholder.typicode.com/posts', 'posts', ['id', 'user_id', 'title', 'body']);
    fetchDataAndInsert($conn, 'https://jsonplaceholder.typicode.com/comments', 'comments', ['id', 'post_id', 'name', 'email', 'body']);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;