<?php
include 'db_connect.php';

// Adicionar novo cliente
if (isset($_POST["adicionar"])) {
    $nome = $_POST['nome_cliente'];
    $email = $_POST['email_cliente'];
    $telefone = $_POST['telefone_cliente'];

    $sql = "INSERT INTO clientes (nome_cliente, email_cliente, telefone_cliente) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $nome, $email, $telefone);

    if ($stmt->execute()) {
        echo "Novo cliente registrado com sucesso!";
    } else {
        echo "Erro ao registrar cliente.";
    }
}

// Exibir todos os clientes
$sql = "SELECT * FROM clientes";
$result = $conn->query($sql);

//exibindo tabela clientes
if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID do Cliente</th>
                <th>Nome do Cliente</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id_cliente']}</td>
                <td>{$row['nome_cliente']}</td>
                <td>{$row['email_cliente']}</td>
                <td>{$row['telefone_cliente']}</td>
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

// Deletar cliente
if (isset($_POST["delete"])) {
    $id_cliente = $_POST['id_cliente'];

    $sql_delete = "DELETE FROM clientes WHERE id_cliente = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param('i', $id_cliente);

    if ($stmt_delete->execute()) {
        echo "Registro deletado com sucesso!";
    } else {
        echo "Erro ao deletar o registro.";
    }

    header("Location: criar_cliente.php");
    exit();
}

// Alterar cliente (exibir formulário)
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

$conn->close();


?>

<form method="POST" action="criar_cliente.php">
    Nome: <input type="text" name="nome_cliente" required>
    email: <input type="email" name="email_cliente" required>
    telefone: <input type="text" name="telefone_cliente" required>
    <input type="submit" name="adicionar">
</form>

<a href="criar_chamado"><button>Adicionar chamado</button></a>