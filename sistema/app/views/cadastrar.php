<?php
session_start();
header("Content-Type: application/json");

include "conexao.php";
require_once "AuthModel.php";
require_once "UsuarioModel.php";

if (!isset($_SESSION['id']) || $_SESSION['perfil'] !== 'administrador') {
    http_response_code(403);
    echo json_encode(["erro" => "Acesso negado. Apenas administradores podem cadastrar usuários."]);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

$campos = ['nome', 'documento', 'perfil', 'senha'];
foreach ($campos as $campo) {
    if (empty($dados[$campo])) {
        http_response_code(400);
        echo json_encode(["erro" => "Campo obrigatório ausente: $campo"]);
        exit;
    }
}

$nome      = trim($dados['nome']);
$documento = trim($dados['documento']);
$perfil    = trim($dados['perfil']);
$senha     = trim($dados['senha']);

$perfis_validos = ['administrador', 'gerencia', 'usuario'];
if (!in_array($perfil, $perfis_validos)) {
    http_response_code(400);
    echo json_encode(["erro" => "Perfil inválido. Use: administrador, gerencia ou usuario."]);
    exit;
}

$check = $conn->prepare("SELECT ID_cliente FROM clientes WHERE documento = ?");
$check->bind_param("s", $documento);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["erro" => "Documento já cadastrado no sistema."]);
    $check->close();
    exit;
}
$check->close();

$model = new UsuarioModel($conn);
$ok    = $model->salvar($nome, $documento, $perfil, $senha);

if ($ok) {
    echo json_encode(["sucesso" => true, "mensagem" => "Usuário cadastrado com sucesso."]);
} else {
    http_response_code(500);
    echo json_encode(["erro" => "Erro interno ao cadastrar usuário."]);
}

$conn->close();
?>