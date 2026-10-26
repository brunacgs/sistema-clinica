<?php
$titulo = 'Área do Médico';
require 'includes/header.php';

$medicos  = $conn->query('SELECT id_medico, nome, especialidade FROM medico ORDER BY nome')->fetch_all(MYSQLI_ASSOC);
$idMedico = $_GET['id_medico'] ?? '';
$data = $_GET['data'] ?? date('Y-m-d');
?>

<h1>Agenda médica</h1>

<form method="get" class="cartao">
    <div class="linha">
        <div>
            <label for="id_medico">Médico</label>
            <select id="id_medico" name="id_medico" required>
                <option value="">Selecione...</option>
                <?php foreach ($medicos as $m): ?>
                    <option value="<?= $m['id_medico'] ?>" <?= $idMedico == $m['id_medico'] ? 'selected' : '' ?>>
                        <?= e($m['nome']) ?> – <?= e($m['especialidade']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="data">Data</label>
            <input type="date" id="data" name="data" value="<?= e($data) ?>" required>
        </div>
    </div>
    <button type="submit">Ver agenda</button>
</form>

<?php
if ($idMedico !== '') {
    $stmt = $conn->prepare(
        'SELECT co.horario, co.motivo, co.status, co.observacoes,
                p.nome AS paciente, p.telefone, p.email
           FROM consulta co
          INNER JOIN paciente p ON p.id_paciente = co.id_paciente
          WHERE co.id_medico = ?
            AND co.data_consulta = ?
          ORDER BY co.horario'
    );
    $stmt->bind_param('is', $idMedico, $data);
    $stmt->execute();
    $agenda = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Nome do médico escolhido, para o título
    $nomeMedico = '';
    foreach ($medicos as $m) {
        if ($m['id_medico'] == $idMedico) $nomeMedico = $m['nome'];
    }
    ?>
    <section class="cartao">
        <h2><?= e($nomeMedico) ?> – <?= dataBR($data) ?></h2>

        <?php if (!$agenda): ?>
            <p class="alerta">Não há consultas para este médico nesta data.</p>
        <?php else: ?>
            <p><?= count($agenda) ?> consulta(s) no dia.</p>
            <div class="tabela-rolavel">
            <table>
                <tr><th>Horário</th><th>Paciente</th><th>Motivo</th><th>Status</th><th>Contato</th></tr>
                <?php foreach ($agenda as $a): ?>
                    <tr>
                        <td><strong><?= substr($a['horario'], 0, 5) ?></strong></td>
                        <td><?= e($a['paciente']) ?></td>
                        <td><?= e($a['motivo']) ?></td>
                        <td><span class="status status-<?= e($a['status']) ?>"><?= e($a['status']) ?></span></td>
                        <td><?= e($a['telefone']) ?><br><small><?= e($a['email']) ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            </div>
        <?php endif; ?>
    </section>
    <?php
}

require 'includes/footer.php';
?>