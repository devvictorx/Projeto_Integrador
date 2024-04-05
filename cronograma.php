<?php
session_start();

include("conectadb.php");

// Seleciona todos os agendamentos da tabela, ordenados por data e hora
$sql = "SELECT * FROM agendamentos ORDER BY data ASC, horario ASC";
$resultado = mysqli_query($link, $sql);

// Verifica se há agendamentos
if (mysqli_num_rows($resultado) > 0) {
    echo "<table>";
    echo "<tr><th>Cliente</th><th>Serviço</th><th>Data</th><th>Hora</th></tr>";
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<tr>";
        echo "<td>" . $row["nome"] . "</td>";
        echo "<td>" . $row["servico"] . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($row["data"])) . "</td>"; // Formatando a data
        echo "<td>" . date('H:i', strtotime($row["horario"])) . "</td>"; // Formatando a hora
        echo "</tr>";
    }
    echo "</table>";
}

mysqli_close($link);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cronograma.css">
    <title>Cronograma</title>
</head>
<body>
</body>
</html>
