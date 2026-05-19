<?php
session_start(); 

function login($documento, $senha) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE documento = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id']     = $usuario['ID_cliente'];
        $_SESSION['nome']   = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['perfil'];

        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["erro" => "Credenciais inválidas"]);
    }
}

function salvarUsuario($nome, $documento, $perfil, $senha) {
    global $conn;

    $hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clientes (nome, documento, perfil, senha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $documento, $perfil, $hash);
    $stmt->execute();

    echo json_encode(["sucesso" => true]);
}

function excluirUsuario($id) { 
    global $conn;

    if (!isset($_SESSION['id'])) {
        echo json_encode(["erro" => "Não autenticado"]);
        return;
    }

    $perfil_de_quem_esta_excluindo = $_SESSION['perfil'];

    $stmt = $conn->prepare("SELECT perfil FROM clientes WHERE ID_cliente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    if ($usuario['perfil'] == 'administrador' && $perfil_de_quem_esta_excluindo != 'administrador') {
        echo json_encode(["erro" => "Gerência não pode excluir administrador"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM clientes WHERE ID_cliente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(["sucesso" => true]);
}

function logout() {
    session_destroy();
    echo json_encode(["sucesso" => true]);
}