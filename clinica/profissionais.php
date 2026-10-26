<?php
$titulo = 'Profissionais';
require 'includes/header.php';

$medicos = $conn->query('SELECT * FROM medico ORDER BY especialidade, nome')->fetch_all(MYSQLI_ASSOC);
?>

<h1>Nossos profissionais</h1>

<?php if (!$medicos): ?>
    <p class="alerta">Nenhum profissional cadastrado ainda.</p>

<?php else: ?>
    <div class="grade-medicos">
        <?php foreach ($medicos as $m):
            $foto = 'img/medicos/' . $m['foto'];
            if (empty($m['foto']) || !file_exists($foto)) {
                $foto = 'img/medicos/sem-foto.png';
            }
        ?>
            <article class="card-medico">
                <img src="<?= e($foto) ?>" alt="Foto de <?= e($m['nome']) ?>">
                <div class="info">
                    <h3><?= e($m['nome']) ?></h3>
                    <p class="especialidade"><?= e($m['especialidade']) ?></p>
                    <p class="crm">CRM <?= e($m['crm']) ?></p>
                    <p><?= e($m['email']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>