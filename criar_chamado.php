<?php
include 'db_connect.php';

// Adicionar novo chamado
if (isset($_POST["adicionar"])) {
    $cliente = $_POST['fk_cliente'];
    $descricao = $_POST['descricao_chamado'];
    $criticidade = $_POST['criticidade_chamado'];
    $status = $_POST['status_chamado'];

    $sql = "INSERT INTO chamados (fk_cliente, descricao_chamado, criticidade_chamado, status_chamado) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $cliente, $descricao, $criticidade, $status);

    if ($stmt->execute()) {
        echo "Novo chamado registrado com sucesso!";
    } else {
        echo "Erro ao registrar chamado.";
    }
}

// Exibir todos os chamados
$sql = "SELECT ch.fk_cliente, ch.id_chamado, ch.descricao_chamado, ch.criticidade_chamado, ch.status_chamado, cl.nome_cliente
        FROM chamados AS ch 
        INNER JOIN clientes AS cl ON cl.id_cliente = ch.fk_cliente";
$result = $conn->query($sql);

//exibindo tabela chamados
if ($result->num_rows > 0) {
    echo "<div class='column'>
            <div class='row'>
            <div class='column'>
            <table border='1'>
            <tr>
                <th>ID do Cliente</th>
                <th>ID do Chamado</th>
                <th>Descrição</th>
                <th>Criticidade</th>
                <th>Status</th>
                <th>Nome do Cliente</th>
                <th>Ações</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['fk_cliente']}</td>
                <td>{$row['id_chamado']}</td>
                <td>{$row['descricao_chamado']}</td>
                <td>{$row['criticidade_chamado']}</td>
                <td>{$row['status_chamado']}</td>
                <td>{$row['nome_cliente']}</td>
                <td>
                    <form method='POST' action=''>
                        <input type='hidden' name='id_chamado' value='{$row['id_chamado']}'>
                        <input type='submit' name='delete' value='Deletar Dados'>
                    </form>
                    <form method='POST' action=''>
                        <input type='hidden' name='id_chamado' value='{$row['id_chamado']}'>
                        <input type='submit' name='alterar' value='Alterar Dados'>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Nenhum registro encontrado.";
}

// Deletar chamado
if (isset($_POST["delete"])) {
    $id_chamado = $_POST['id_chamado'];

    $sql_delete = "DELETE FROM chamados WHERE id_chamado = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param('i', $id_chamado);

    if ($stmt_delete->execute()) {
        echo "Registro deletado com sucesso!";
    } else {
        echo "Erro ao deletar o registro.";
    }

    header("Location: criar_chamado.php");
    exit();
}

// Alterar chamado (exibir formulário)
if (isset($_POST["alterar"])) {
    echo "<br>
    <br>===/ALTERANDO VALORES\===
    <br>
    <br>";
    $id_update = $_POST["id_chamado"];

    $sql_select = "SELECT * FROM chamados WHERE id_chamado = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param('i', $id_update);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($row = $result_select->fetch_assoc()) {
        echo "
        <form method='POST' action=''>
            <input type='hidden' name='id_chamado' value='{$row['id_chamado']}'>

            <label for='fk_cliente'>Id do Cliente: </label>
            <input type='number' name='fk_cliente' value='{$row['fk_cliente']}' required><br>

            <label for='descricao_chamado'>Descrição: </label>
            <input type='text' name='descricao_chamado' value='{$row['descricao_chamado']}' required><br>

            <label for='criticidade_chamado'>Criticidade: </label>
            <select name='criticidade_chamado' required>
                <option value='baixa'>baixa</option>
                <option value='média'>média</option>
                <option value='alta'>alta</option>
            </select><br>

            <label for='status_chamado'>Status: </label>
            <select name='status_chamado' required>
                <option value='aberto'>aberto</option>
                <option value='em andamento'>em andamento</option>
                <option value='resolvido'>resolvido</option>
            </select><br>

            <input type='submit' name='salvar_alteracoes' value='Salvar Alterações'>
        </form>";
        
        echo "<br>
        <br>
        <br>
        ===/INSERINDO VALORES NOVOS\===
        <br>
        <br>
        <br>";
    }
}

// Salvar alterações do cliente
if (isset($_POST["salvar_alteracoes"])) {
    $id_chamado = $_POST['id_chamado'];
    $cliente = $_POST['fk_cliente'];
    $descricao = $_POST['descricao_chamado'];
    $criticidade = $_POST['criticidade_chamado'];
    $status = $_POST['status_chamado'];

    $sql_update = "UPDATE chamados SET fk_cliente = ?, descricao_chamado = ?, criticidade_chamado = ?, status_chamado = ? WHERE id_chamado = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('isssi', $cliente, $descricao, $criticidade, $status, $id_chamado);

    if ($stmt_update->execute()) {
        echo "Dados do chamado atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar os dados do chamado.";
    }

    header("Location: criar_chamado.php");
    exit();
}

// Consulta SQL para buscar os clientes
$sql = "SELECT id_cliente, nome_cliente FROM clientes";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();

?>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!--o começo das divs se encontra na parte do php -->    
<form method="POST" action="criar_chamado.php">
    Cliente id: <input type="number" name="fk_cliente" required>

    Descrição: <input type="text" name="descricao_chamado" required>

    <label for="criticidade_chamado">Criticidade: </label>
    <select name="criticidade_chamado" required>
        <option value="baixa">baixa</option>
        <option value="média">média</option>
        <option value="alta">alta</option>
    </select>
    
    <label for="status_chamado">Status: </label>
    <select name="status_chamado" required>
        <option value="aberto">aberto</option>
        <option value="em andamento">em andamento</option>
        <option value="resolvido">resolvido</option>
    </select>
    <input type="submit" name="adicionar">
</form>
<div class='row'>
<a href="criar_cliente.php"><button>Adicionar cliente</button></a>
</div>
</div>
</div>
</div>
</body>
</html>