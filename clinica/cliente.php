<?php
$titulo = 'Área do Cliente';
require 'includes/header.php';

// trim() tira os espaços do começo e fim
// na primeira visita ainda não há nome, então ?? define texto vazio
$busca = trim($_GET['nome'] ?? '');
?>

<h1>Área do Cliente</h1>

<form method="get" class="cartao">
    <label for="nome">Nome do paciente</label>
    <input type="text" id="nome" name="nome" value="<?= e($busca) ?>" placeholder="Ex.: Maria Oliveira" required>
    <button type="submit">Pesquisar</button>
</form>

<?php
if ($busca !== '') {
    // 1) Pacientes cujo nome contém o texto digitado
    $stmt = $conn->prepare(
        'SELECT p.*, c.nome AS convenio, c.percentual_desconto
           FROM paciente p
           LEFT JOIN convenio c ON c.id_convenio = p.id_convenio
          WHERE p.nome LIKE ?
          ORDER BY p.nome'
    );
    $termo = "%$busca%";
    $stmt->bind_param('s', $termo);
    $stmt->execute();
    $pacientes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!$pacientes) {
        echo '<p class="alerta">Nenhum paciente encontrado com o nome "' . e($busca) . '".</p>';
    }

    // 2) Para cada paciente encontrado, mostra os dados e as consultas
    $stmtConsultas = $conn->prepare(
        'SELECT co.data_consulta, co.horario, co.motivo, co.status,
                m.nome AS medico, m.especialidade
           FROM consulta co
          INNER JOIN medico m ON m.id_medico = co.id_medico
          WHERE co.id_paciente = ?
          ORDER BY co.data_consulta DESC, co.horario'
    );

    foreach ($pacientes as $p) {
        $idPaciente = $p['id_paciente'];
        $stmtConsultas->bind_param('i', $idPaciente);
        $stmtConsultas->execute();
        $consultas = $stmtConsultas->get_result()->fetch_all(MYSQLI_ASSOC);
        ?>
        <section class="cartao">
            <h2><?= e($p['nome']) ?></h2>
            <div class="dados">
                <p><strong>CPF:</strong> <?= e($p['cpf']) ?></p>
                <p><strong>Nascimento:</strong> <?= dataBR($p['data_nascimento']) ?></p>
                <p><strong>Telefone:</strong> <?= e($p['telefone']) ?></p>
                <p><strong>E-mail:</strong> <?= e($p['email']) ?></p>
                <p><strong>Endereço:</strong> <?= e($p['endereco']) ?> – <?= e($p['cidade']) ?></p>
                <p><strong>Convênio:</strong>
                    <?= $p['convenio'] ? e($p['convenio']) : 'Particular (sem convênio)' ?></p>
            </div>

            <h3>Consultas (<?= count($consultas) ?>)</h3>
            <?php if (!$consultas): ?>
                <p class="alerta">Este paciente ainda não possui consultas.</p>
            <?php else: ?>
                <div class="tabela-rolavel">
                <table>
                    <tr><th>Data</th><th>Horário</th><th>Médico</th><th>Especialidade</th><th>Motivo</th><th>Status</th></tr>
                    <?php foreach ($consultas as $c): ?>
                        <tr>
                            <td><?= dataBR($c['data_consulta']) ?></td>
                            <td><?= substr($c['horario'], 0, 5) ?></td>
                            <td><?= e($c['medico']) ?></td>
                            <td><?= e($c['especialidade']) ?></td>
                            <td><?= e($c['motivo']) ?></td>
                            <td><span class="status status-<?= e($c['status']) ?>"><?= e($c['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}

require 'includes/footer.php';
?>