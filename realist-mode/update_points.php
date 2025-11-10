<?php
session_start();
require '../db.php';

header('Content-Type: application/json');

$allowed_origins = [
    'https://coconutsimulator.com',
    'https://www.coconutsimulator.com',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    http_response_code(403);
    echo json_encode(["error" => "Acesso não permitido"]);
    exit;
}

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(["error" => "Não autenticado"]);
    exit;
}

if (!isset($_SERVER['CONTENT_TYPE']) || stripos($_SERVER['CONTENT_TYPE'], 'application/json') === false) {
    http_response_code(400);
    echo json_encode(["error" => "Content-Type deve ser application/json"]);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($data['points']) || !is_numeric($data['points'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos"]);
    exit;
}

$user_name = $_SESSION['username'];
$new_points = (int) $data['points'];

if ($new_points < 0 || $new_points > 10000000) {
    http_response_code(400);
    echo json_encode(["error" => "Valor de pontos inválido"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET game_points = :points WHERE username = :username");
    $stmt->execute(['points' => $new_points, 'username' => $user_name]);
    
    echo json_encode(["message" => "Pontos atualizados com sucesso"]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro interno do servidor"]);
}
?>