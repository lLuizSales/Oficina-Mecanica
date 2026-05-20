<?php
include "../../config/conexao.php";

function login($documento, $senha) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE documento = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_start();
        $_SESSION['id']       = $usuario['ID_cliente'];
        $_SESSION['nome']     = $usuario['nome'];
        $_SESSION['perfil']   = $usuario['perfil'];

        echo json_encode(["sucesso" => true, "perfil" => $usuario['perfil']]);
    } else {
        echo json_encode(["erro" => "Documento ou senha incorretos"]);
    }
}

function logout() {
    session_start();
    session_destroy();
    echo json_encode(["sucesso" => true]);
}