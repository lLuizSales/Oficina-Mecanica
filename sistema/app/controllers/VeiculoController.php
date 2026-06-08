<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/VeiculoModel.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado. Faça login primeiro.']);
    exit;
}

$model = new VeiculoModel($conn);
$method = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';
$dados = [];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true) ?? [];
    $acao = $dados['acao'] ?? $acao;
}

$perfilSessao = $_SESSION['perfil'] ?? '';

if ($acao === 'listar') {
    echo json_encode($model->listar());
    exit;
}

if ($acao === 'salvar') {
    $placa = trim($dados['placa'] ?? '');
    $ano = intval($dados['ano'] ?? 0);
    $id_cliente = intval($dados['id_cliente'] ?? 0);
    $id_modelo = intval($dados['id_modelo'] ?? 0);

    if (empty($placa) || !$ano || !$id_cliente || !$id_modelo) {
        echo json_encode(['erro' => 'Todos os campos são obrigatórios.']);
        exit;
    }

    if ($model->salvar($placa, $ano, $id_cliente, $id_modelo)) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['erro' => 'Erro ao salvar veículo.']);
    }
    exit;
}

if ($acao === 'editar') {
    $id = intval($dados['id'] ?? 0);
    $placa = trim($dados['placa'] ?? '');
    $ano = intval($dados['ano'] ?? 0);
    $id_cliente = intval($dados['id_cliente'] ?? 0);
    $id_modelo = intval($dados['id_modelo'] ?? 0);

    if (!$id || empty($placa) || !$ano || !$id_cliente || !$id_modelo) {
        echo json_encode(['erro' => 'Dados incompletos para edição.']);
        exit;
    }

    if ($model->editar($id, $placa, $ano, $id_cliente, $id_modelo)) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['erro' => 'Erro ao atualizar veículo.']);
    }
    exit;
}

if ($acao === 'excluir') {
    $id = intval($dados['id'] ?? 0);
    if (!$id) {
        echo json_encode(['erro' => 'ID inválido.']);
        exit;
    }
    echo json_encode($model->excluir($id, $perfilSessao));
    exit;
}

http_response_code(400);
echo json_encode(['erro' => 'Ação ou método inválido.']);
?>