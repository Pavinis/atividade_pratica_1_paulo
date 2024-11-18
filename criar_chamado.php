<?php
include 'db_connect.php';

// Adicionar novo cliente
if (isset($_POST["adicionar"])) {
    $cliente = $_POST['fk_cliente'];
    $descricao = $_POST['descricao_chamado'];
    $criticidade = $_POST['criticidade_chamado'];
    $status = $_POST['status_chamado'];

    $sql = "INSERT INTO chamados (fk_cliente, descricao_chamado, criticidade_chamado, status_chamado) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $cliente, $descricao, $criticidade, $status);

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
    echo "<table border='1'>
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
                        <input type='hidden' name='id_cliente' value='{$row['id_cliente']}'>
                        <input type='submit' name='delete' value='Deletar Dados'>
                    </form>
                    <form method='POST' action=''>
                        <input type='hidden' name='id_cliente' value='{$row['id_cliente']}'>
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
    $id_update = $_POST["id_cliente"];

    $sql_select = "SELECT * FROM clientes WHERE id_cliente = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param('i', $id_update);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($row = $result_select->fetch_assoc()) {
        echo "
        <form method='POST' action=''>
            <input type='hidden' name='id_cliente' value='{$row['id_cliente']}'>
            <label for='nome'>Nome do Cliente: </label>
            <input type='text' name='nome' value='{$row['nome_cliente']}' required><br>

            <label for='email'>E-Mail: </label>
            <input type='email' name='email' value='{$row['email_cliente']}' required><br>

            <label for='telefone'>Telefone: </label>
            <input type='text' name='telefone' value='{$row['telefone_cliente']}' required><br>

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
    $id_cliente = $_POST['id_cliente'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];

    $sql_update = "UPDATE clientes SET nome_cliente = ?, email_cliente = ?, telefone_cliente = ? WHERE id_cliente = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('sssi', $nome, $email, $telefone, $id_cliente);

    if ($stmt_update->execute()) {
        echo "Dados do cliente atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar os dados do cliente.";
    }

    header("Location: criar_cliente.php");
    exit();
}

// Consulta SQL para buscar os clientes
$sql = "SELECT id_cliente, nome_cliente FROM clientes";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();

?>

<form method="POST" action="criar_cliente.php">
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

<a href="criar_cliente"><button>Adicionar cliente</button></a>