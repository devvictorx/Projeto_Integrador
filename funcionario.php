<?php
// Inclui o arquivo de conexão com o banco de dados
include("conectadb.php");

// Variável para armazenar a saudação personalizada
$saudacao = "";

// Verifica se os campos do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o campo de nome de usuário e senha não estão vazios
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        // Obtém as credenciais do formulário
        $username = mysqli_real_escape_string($link, $_POST['username']);
        $password = mysqli_real_escape_string($link, $_POST['password']);

        // Consulta SQL para verificar as credenciais do funcionário
        $sql = "SELECT id, nome FROM funcionarios WHERE nome = '$username' AND senha = '$password'";
        $result = mysqli_query($link, $sql);

        // Verifica se a consulta foi bem sucedida e se encontrou algum funcionário com as credenciais fornecidas
        if ($result && mysqli_num_rows($result) > 0) {
            // Obtém os dados do funcionário autenticado
            $row = mysqli_fetch_assoc($result);
            $funcionario_id = $row['id'];
            $funcionario_nome = $row['nome'];

            // Inicia a sessão
            session_start();
            // Armazena o ID e o nome do funcionário autenticado na sessão
            $_SESSION['funcionario_id'] = $funcionario_id;
            $_SESSION['funcionario_nome'] = $funcionario_nome;

            // Redireciona para a página de visualização do cronograma
            header("Location: cronograma.php");
            exit;
        } else {
            // Se as credenciais estiverem incorretas, define a saudação de erro
            $saudacao = "<p>Credenciais inválidas. Por favor, tente novamente.</p>";
        }
    } else {
        // Se algum campo estiver vazio, define a saudação de erro
        $saudacao = "<p>Por favor, preencha todos os campos.</p>";
    }
}

// Fecha a conexão com o banco de dados (se estiver aberta)
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionário</title>
    <link rel="stylesheet" href="funcionario.css">
</head>
<body>
</body>
</html>
