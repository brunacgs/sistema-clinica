<?php
$titulo = 'Exames';
require 'includes/header.php';

$busca = trim($_GET['nome'] ?? '');
?>

<h1>Área de Exames</h1>

<form method="get" class="cartao">
    <label for="nome">Nome do paciente</label>
    <input type="text" id="nome" name="nome" value="<?= e($busca) ?>" required>
    <button type="submit">Ver exames</button>
</form>

<?php
if ($busca !== '') {
    $stmt = $conn->prepare(
        'SELECT p.id_paciente, p.nome, p.cpf, c.nome AS convenio, c.percentual_desconto
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

    $stmtExames = $conn->prepare(
        'SELECT nome_exame, data_solicitacao, data_realizacao, status, resultado, valor
           FROM exame
          WHERE id_paciente = ?
          ORDER BY data_solicitacao DESC'
    );

    foreach ($pacientes as $p) {
        $idPaciente = $p['id_paciente'];
        $stmtExames->bind_param('i', $idPaciente);
        $stmtExames->execute();
        $exames = $stmtExames->get_result()->fetch_all(MYSQLI_ASSOC);
        ?>
        <section class="cartao">
            <h2><?= e($p['nome']) ?> <small>(CPF <?= e($p['cpf']) ?>)</small></h2>

            <?php if ($p['convenio']): ?>
                <p class="alerta sucesso">Possui convênio: <strong><?= e($p['convenio']) ?></strong>
                   (<?= (float) $p['percentual_desconto'] ?>% de desconto)</p>
            <?php else: ?>
                <p class="alerta">Paciente sem convênio (atendimento particular).</p>
            <?php endif; ?>

            <h3>Exames (<?= count($exames) ?>)</h3>
            <?php if (!$exames): ?>
                <p class="alerta">Nenhum exame solicitado para este paciente.</p>
            <?php else: ?>
                <div class="tabela-rolavel">
                <table>
                    <tr><th>Exame</th><th>Solicitado em</th><th>Realizado em</th><th>Status</th><th>Resultado / observação</th><th>Valor</th></tr>
                    <?php foreach ($exames as $x): ?>
                        <tr>
                            <td><?= e($x['nome_exame']) ?></td>
                            <td><?= dataBR($x['data_solicitacao']) ?></td>
                            <td><?= $x['data_realizacao'] ? dataBR($x['data_realizacao']) : 'Ainda não realizado' ?></td>
                            <td><span class="status status-<?= e($x['status']) ?>"><?= e($x['status']) ?></span></td>
                            <td><?= $x['resultado'] ? e($x['resultado']) : 'Aguardando resultado' ?></td>
                            <td><?= dinheiro($x['valor']) ?></td>
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