<?php
require_once 'mysql_connect.php';

$search = $_GET['search'] ?? '';
$developer = $_GET['developer'] ?? '';
$genre = $_GET['genre'] ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';
$created_after = $_GET['created_after'] ?? '';

$query = "SELECT name FROM games WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND name LIKE :search";
    $params['search'] = '%' . $search . '%';
}
if (!empty($developer)) {
    $query .= " AND developer = :developer";
    $params['developer'] = $developer;
}
if (!empty($genre)) {
    $query .= " AND genre = :genre";
    $params['genre'] = $genre;
}
if (!empty($price_min)) {
    $query .= " AND cost >= :price_min";
    $params['price_min'] = $price_min;
}
if (!empty($price_max)) {
    $query .= " AND cost <= :price_max";
    $params['price_max'] = $price_max;
}
if (!empty($created_after)) {
    $query .= " AND date_create >= :created_after";
    $params['created_after'] = $created_after;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);

$games = $stmt->fetchAll(PDO::FETCH_COLUMN);

header('Content-Type: application/json');
echo json_encode($games);
?>
