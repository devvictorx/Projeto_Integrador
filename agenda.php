<?php
include("conectadb.php");

// Consultar horários já agendados para a data selecionada
if(isset($_POST['data'])) {
    $data_selecionada = $_POST['data'];
    $sql_horarios_agendados = "SELECT horario FROM agendamentos WHERE data = '$data_selecionada'";
    $result_horarios_agendados = mysqli_query($link, $sql_horarios_agendados);
    $horarios_agendados = array();
    while ($row = mysqli_fetch_assoc($result_horarios_agendados)) {
        $horarios_agendados[] = $row['horario'];
    }
} else {
    $horarios_agendados = array();
}

// Função para obter todos os horários disponíveis
function getHorariosDisponiveis($horarios_agendados) {
    $horarios_disponiveis = array();
    $horario_inicio = strtotime("09:00");
    $horario_fim = strtotime("18:00");
    $intervalo = 30 * 60; // Intervalo de 30 minutos
    for ($i = $horario_inicio; $i < $horario_fim; $i += $intervalo) {
        $horario = date("H:i", $i);
        if (!in_array($horario, $horarios_agendados)) {
            $horarios_disponiveis[] = $horario;
        }
    }
    return $horarios_disponiveis;
}

// Verificação do envio do formulário e processamento do agendamento
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
    } elseif (in_array($horario, $horarios_agendados)) {
        $mensagemErro = "Desculpe, este horário já está agendado. Por favor, selecione outro horário.";
    } else {
        // Iniciar a transação
        mysqli_begin_transaction($link);
        
        // Inserir os dados na tabela dentro da transação
        $sql = "INSERT INTO agendamentos (nome, email, telefone, data, horario, servico, mensagem, barbeiro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssss", $nome, $email, $telefone, $data, $horario, $servico, $mensagem, $barbeiro);
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

mysqli_close($link);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="agenda.css">
    <title>Agenda</title>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Bloquear os horários já agendados
            const horariosAgendados = <?php echo json_encode($horarios_agendados); ?>;
            const horarioSelect = document.getElementById('horario');
            horarioSelect.addEventListener('change', () => {
                const selectedHorario = horarioSelect.value;
                if (horariosAgendados.includes(selectedHorario)) {
                    alert('Desculpe, este horário já está agendado. Por favor, selecione outro horário.');
                    horarioSelect.value = '';
                }
            });
        });
    </script>
</head>
<body>
<header>
    <nav class="menu">
        <a href="home.html">Início</a>
        <a href="servicos.html">Serviços</a>
        <a href="equipe.html">Equipe</a>
        <a href="#"></a>
        </nav>
</header>
<div class="container">
    <form action="" method="POST">
        <?php if(isset($mensagemSucesso)) { ?>
            <p class="sucesso"><?php echo $mensagemSucesso; ?></p>
        <?php } ?>
        <?php if(isset($mensagemErro)) { ?>
            <p class="erro"><?php echo $mensagemErro; ?></p>
        <?php } ?>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
        <label for="telefone">Telefone:</label>
        <input type="tel" id="telefone" name="telefone" required>
        <label for="data">Data:</label>
        <input type="date" id="data" name="data" required>
        <label for="horario">Horário:</label>
        <select id="horario" name="horario" required>
            <option value="">Selecione um horário</option>
            <?php
            $horarios_disponiveis = getHorariosDisponiveis($horarios_agendados);
            foreach ($horarios_disponiveis as $horario) {
                echo "<option value='$horario'>$horario</option>";
            }
            ?>
        </select>
        <label for="servico">Serviço:</label>
        <select id="servico" name="servico" required>
            <option value="">Selecione um serviço</option>
            <option value="Corte de Cabelo">Corte de Cabelo</option>
            <option value="Design de Sobrancelhas">Design de Sobrancelhas</option>
            <option value="Hidratação Capilar">Hidratação Capilar</option>
            <option value="Coloração">Coloração</option>
            <option value="Penteado">Penteado</option>
            <option value="Outros">Outros</option>
        </select>
            <label for="barbeiro">Barbeiro:</label>
            <select id="barbeiro" name="barbeiro" required>
            <option value="">Selecione um barbeiro</option>
            <option value="Eduard">Eduard</option>
            <option value="Taylor">Taylor</option>
        </select>
            <label for="mensagem">Mensagem Adicional:</label>
            <textarea id="mensagem" name="mensagem" rows="4"></textarea>
            <input type="submit" name="submit" value="Agendar">
</form>

</div>
</body>
</html>
