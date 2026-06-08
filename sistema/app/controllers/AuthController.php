<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/AuthModel.php';

$json = json_decode(file_get_contents('php://input'), true);
$acao = $json['acao'] ?? $_GET['acao'] ?? '';

if ($acao === 'login') {
    $documento = trim($json['documento'] ?? '');
    $senha     = trim($json['senha'] ?? '');

    if (empty($documento) || empty($senha)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Documento e senha são obrigatórios.']);
        exit;
    }
    
    login($documento, $senha);
    exit;
}

if ($acao === 'logout') {
    logout();
    exit;
}

http_response_code(400);
echo json_encode(['erro' => 'Ação inválida.']);
?>