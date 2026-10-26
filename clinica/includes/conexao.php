<?php
$host    = 'localhost';
$usuario = 'root';
$senha   = '';          
$banco   = 'clinica';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $usuario, $senha, $banco);
    $conn->set_charset('utf8mb4');
} 
catch (mysqli_sql_exception $erro) {
    die('Erro ao conectar ao banco: ' . $erro->getMessage());
}

// Troca < por &lt; caracteres aparecem como texto para não rodar como código
function e($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

// strtotime transforma o texto em data, e date escreve no formato brasileiro
function dataBR($data) {
    return $data ? date('d/m/Y', strtotime($data)) : '-';
}

//  2 casas, vírgula nos centavos e ponto nos milhares
function dinheiro($valor) {
    return $valor !== null ? 'R$ ' . number_format($valor, 2, ',', '.') : '-';
}