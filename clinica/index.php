
<?php
// $titulo é definido antes do require porque o header.php usa essa variável na tag title
$titulo = 'Início';
require 'includes/header.php';

$totalMedicos = $conn->query('SELECT COUNT(*) AS total FROM medico')->fetch_assoc()['total'];
$especialidades = $conn->query('SELECT COUNT(DISTINCT especialidade) AS total FROM medico')->fetch_assoc()['total'];
?>

<section class="destaque">
    <h1>Clínica Vida Plena</h1>
    <p>Cuidado completo para você e sua família, com <?= $totalMedicos ?> médicos em <?= $especialidades ?> especialidades, exames no local e atendimento por convênio ou particular.</p>
    <a href="cliente.php" class="botao">Ver minhas consultas</a>
</section>

<div class="atalhos">
    <a class="atalho" href="cliente.php"><strong>Área do Cliente</strong>Consulte seus dados e agendamentos.</a>
    <a class="atalho" href="exames.php"><strong>Exames</strong>Acompanhe seus exames e resultados.</a>
    <a class="atalho" href="medico.php"><strong>Área do Médico</strong>Veja a agenda do dia de cada médico.</a>
    <a class="atalho" href="profissionais.php"><strong>Profissionais</strong>Conheça nossa equipe.</a>
    <a class="atalho" href="cadastro.php"><strong>Cadastro</strong>Cadastre pacientes, médicos, consultas e mais.</a>
</div>

<?php require 'includes/footer.php'; ?>