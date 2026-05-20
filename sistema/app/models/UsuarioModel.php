<?php
include "../../config/conexao.php";

function salvarUsuario($nome, $documento, $perfil, $senha) {
    global $conn;

    $hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clientes (nome, documento, perfil, senha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $documento, $perfil, $hash);
    $stmt->execute();

    echo json_encode(["sucesso" => true]);
}

function excluirUsuario($id, $perfil_de_quem_esta_excluindo) {
    global $conn;

    $stmt = $conn->prepare("SELECT perfil FROM clientes WHERE ID_cliente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario['perfil'] == 'administrador' && $perfil_de_quem_esta_excluindo != 'administrador') {
        echo json_encode(["erro" => "Gerência não pode excluir administrador"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM clientes WHERE ID_cliente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(["sucesso" => true]);
}

function listarUsuarios() {
    global $conn;

    $stmt = $conn->prepare("SELECT ID_cliente, nome, documento, perfil FROM clientes");
    $stmt->execute();
    $result = $stmt->get_result();

    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }

    return $usuarios;
}

function buscarPorId($id) {
    global $conn;

    $stmt = $conn->prepare("SELECT ID_cliente, nome, documento, perfil FROM clientes WHERE ID_cliente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

function editarUsuario($id, $nome, $documento, $perfil) {
    global $conn;

    $stmt = $conn->prepare("UPDATE clientes SET nome = ?, documento = ?, perfil = ? WHERE ID_cliente = ?");
    $stmt->bind_param("sssi", $nome, $documento, $perfil, $id);
    $stmt->execute();

    echo json_encode(["sucesso" => true]);
}