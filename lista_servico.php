<?php
session_start();

include("conectadb.php");


if(isset($_POST['id_agendamento'])) {
    $id_agendamento = $_POST['id_agendamento'];
    $cliente_email = $_POST['cliente_email'];
    

    // Query preparada para cancelar o agendamento com base no ID fornecido, no email do cliente e no token
    $sql_cancelar = "DELETE FROM agendamentos WHERE id = ? AND email = ? AND token = ?";
    
    // Preparar a declaração
    $stmt = mysqli_prepare($link, $sql_cancelar);
    
    // Vincular parâmetros
    mysqli_stmt_bind_param($stmt, "iss", $id_agendamento, $cliente_email, $token);
    
    // Executar a declaração
    if (mysqli_stmt_execute($stmt)) {
        echo "Agendamento cancelado com sucesso!";
    } else {
        echo "Erro ao cancelar o agendamento: " . mysqli_error($link);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $servico = $_POST['servico'];
    $mensagem = $_POST['mensagem'];
    $barbeiro = $_POST['barbeiro'];

    if(empty($nome) || empty($email) || empty($telefone) || empty($data) || empty($horario) || empty($servico) || empty($barbeiro)) {
        $mensagemErro = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagemErro = "Formato de e-mail inválido.";
    } elseif (!preg_match("/^\([0-9]{2}\) [0-9]{4,5}-[0-9]{4}$/", $telefone)) {
        $mensagemErro = "Formato de telefone inválido. Use o formato (xx) xxxxx-xxxx.";
    } else {
        // Iniciar a transação
        mysqli_begin_transaction($link);

        // Inserir os dados na tabela dentro da transação
        $sql = "INSERT INTO agendamentos (nome, email, telefone, data, horario, servico, mensagem, barbeiro, token)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssss", $nome, $email, $telefone, $data, $horario, $servico, $mensagem, $barbeiro, $token);
        $result = mysqli_stmt_execute($stmt);

        if ($result === TRUE) {
            mysqli_commit($link); // Commit se o agendamento for bem-sucedido
            $mensagemSucesso = "Agendamento realizado com sucesso!";
        } else {
            mysqli_rollback($link); // Rollback em caso de erro no agendamento
            $mensagemErro = "Erro ao agendar: " . mysqli_error($link);
        }
    }
}

$sql = "SELECT * FROM agendamentos";
$resultado = mysqli_query($link, $sql);

if (mysqli_num_rows($resultado) > 0) {
    echo "<table>";
    echo "<tr><th>Nome</th><th>Email</th><th>Telefone</th><th>Data</th><th>Horário</th><th>Serviço</th><th>Barbeiro</th><th>Mensagem</th></tr>";
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<tr>";
        echo "<td>" . $row["nome"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["telefone"] . "</td>";
        echo "<td>" . $row["data"] . "</td>";
        echo "<td>" . $row["horario"] . "</td>";
        echo "<td>" . $row["servico"] . "</td>";
        echo "<td>" . $row["barbeiro"] . "</td>";
        echo "<td>" . $row["mensagem"] . "</td>";
        echo "<td><form method='POST'><input type='hidden' name='id_agendamento' value='" . $row['id'] . "'>";
        echo "<input type='hidden' name='cliente_email' value='" . $row['email'] . "'>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Não há agendamentos.";
}

mysqli_close($link);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lista_servico.css">
    <title>Lista serviços</title>
</head>
<body>
    <form action="logout.php" method="post">
    </form>
</body>
</html>
