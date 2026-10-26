<?php require_once __DIR__ . '/conexao.php';
// require_once evita abrir a conexão duas vezes, __DIR__ é a pasta do arquivo atual

$pagina = basename($_SERVER['PHP_SELF']);

$menu = [
    'index.php' => 'Início',
    'cliente.php' => 'Área do Cliente',
    'exames.php' => 'Exames',
    'medico.php' => 'Área do Médico',
    'profissionais.php' => 'Profissionais',
    'cadastro.php' => 'Cadastro',
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Clínica Vida Plena') ?> | Clínica Vida Plena</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topo">
    <a href="index.php" class="logo">Clínica Vida Plena</a>
    <nav class="menu">
        <?php foreach ($menu as $arquivo => $nome): ?>
            <a href="<?= $arquivo ?>" class="<?= $pagina === $arquivo ? 'ativo' : '' ?>"><?= $nome ?></a>
        <?php endforeach; ?>
    </nav>
</header>
<main class="container">