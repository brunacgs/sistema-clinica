<?php
require 'includes/conexao.php';

// Campo opcional vazio vira NULL no banco
function ouNulo($valor) {
    $valor = trim($valor ?? '');
    return $valor === '' ? null : $valor;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    try {
        switch ($tipo) {
            case 'convenio':
                $sql   = 'INSERT INTO convenio (nome, telefone, percentual_desconto) VALUES (?, ?, ?)';
                $tipos = 'ssd';
                $dados = [$_POST['nome'], ouNulo($_POST['telefone']), $_POST['percentual_desconto']];
                break;

            case 'paciente':
                $sql   = 'INSERT INTO paciente (nome, cpf, data_nascimento, telefone, email, endereco, cidade, id_convenio)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                $tipos = 'sssssssi';
                $dados = [$_POST['nome'], $_POST['cpf'], $_POST['data_nascimento'],
                          ouNulo($_POST['telefone']), ouNulo($_POST['email']), ouNulo($_POST['endereco']),
                          ouNulo($_POST['cidade']), ouNulo($_POST['id_convenio'])];
                break;

            case 'medico':
                // A foto é enviada como arquivo e salva em img/medicos/; o banco guarda só o nome
                $foto = null;
                if (!empty($_FILES['foto']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        throw new Exception('A foto precisa ser JPG, PNG ou WEBP.');
                    }
                    $foto = uniqid('medico-') . '.' . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/img/medicos/' . $foto);
                }
                $sql   = 'INSERT INTO medico (nome, crm, especialidade, telefone, email, valor_consulta, foto)
                          VALUES (?, ?, ?, ?, ?, ?, ?)';
                $tipos = 'sssssds';
                $dados = [$_POST['nome'], $_POST['crm'], $_POST['especialidade'], ouNulo($_POST['telefone']),
                          ouNulo($_POST['email']), $_POST['valor_consulta'], $foto];
                break;

            case 'consulta':
                $sql   = 'INSERT INTO consulta (id_paciente, id_medico, data_consulta, horario, motivo, observacoes, status)
                          VALUES (?, ?, ?, ?, ?, ?, ?)';
                $tipos = 'iisssss';
                $dados = [$_POST['id_paciente'], $_POST['id_medico'], $_POST['data_consulta'], $_POST['horario'],
                          $_POST['motivo'], ouNulo($_POST['observacoes']), $_POST['status']];
                break;

            case 'exame':
                $sql   = 'INSERT INTO exame (id_paciente, id_consulta, nome_exame, data_solicitacao,
                                             data_realizacao, status, resultado, valor)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                $tipos = 'iisssssd';
                $dados = [$_POST['id_paciente'], ouNulo($_POST['id_consulta']), $_POST['nome_exame'],
                          $_POST['data_solicitacao'], ouNulo($_POST['data_realizacao']), $_POST['status'],
                          ouNulo($_POST['resultado']), ouNulo($_POST['valor'])];
                break;

            default:
                throw new Exception('Formulário desconhecido.');
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($tipos, ...$dados);   // espalha o array, um valor para cada ?
        $stmt->execute();

        header("Location: cadastro.php?ok=$tipo#$tipo");
        exit;
    } catch (mysqli_sql_exception $ex) {
        if ($ex->getCode() == 1062) {
            $erro = 'Não foi possível salvar: CPF ou CRM já cadastrado.';
        } elseif ($ex->getCode() == 1452) {
            $erro = 'Não foi possível salvar: paciente, médico, convênio ou consulta inválido.';
        } else {
            $erro = 'Erro no banco: ' . $ex->getMessage();
        }
    } catch (Exception $ex) {
        $erro = $ex->getMessage();
    }
}

$titulo = 'Cadastro';
require 'includes/header.php';

// Listas para os <select> (carregadas depois do INSERT, então já incluem o que acabou de ser cadastrado)
$convenios = $conn->query('SELECT id_convenio, nome FROM convenio ORDER BY nome')->fetch_all(MYSQLI_ASSOC);
$pacientes = $conn->query('SELECT id_paciente, nome, cpf FROM paciente ORDER BY nome')->fetch_all(MYSQLI_ASSOC);
$medicos   = $conn->query('SELECT id_medico, nome, especialidade FROM medico ORDER BY nome')->fetch_all(MYSQLI_ASSOC);
$consultas = $conn->query(
    'SELECT co.id_consulta, co.data_consulta, co.horario, p.nome AS paciente, m.nome AS medico
       FROM consulta co
       JOIN paciente p ON p.id_paciente = co.id_paciente
       JOIN medico m   ON m.id_medico   = co.id_medico
      ORDER BY co.data_consulta DESC, co.horario'
)->fetch_all(MYSQLI_ASSOC);

$nomes = ['convenio' => 'convênio', 'paciente' => 'paciente', 'medico' => 'médico',
          'consulta' => 'consulta', 'exame' => 'exame'];
?>

<h1>Cadastro</h1>
<p>Ir para: 
<a href="#convenio">Convênio</a> · 
<a href="#paciente">Paciente</a> · 
<a href="#medico">Médico</a> · 
<a href="#consulta">Consulta</a> · 
<a href="#exame">Exame</a></p>

<?php if (isset($_GET['ok'], $nomes[$_GET['ok']])): ?>
    <p class="alerta sucesso">Cadastro de <?= $nomes[$_GET['ok']] ?> realizado com sucesso!</p>
<?php endif; ?>
<?php if ($erro): ?>
    <p class="alerta erro"><?= e($erro) ?></p>
<?php endif; ?>

<!-- CONVÊNIO -->
<section id="convenio" class="cartao">
    <h2>Novo convênio</h2>
    <form method="post">
        <input type="hidden" name="tipo" value="convenio">
        <label for="cv_nome">Nome do convênio</label>
        <input id="cv_nome" name="nome" required>
        <div class="linha">
            <div><label for="cv_tel">Telefone</label><input id="cv_tel" name="telefone"></div>
            <div><label for="cv_desc">Desconto (%)</label>
                 <input type="number" id="cv_desc" name="percentual_desconto" min="0" max="100" step="0.01" required></div>
        </div>
        <button type="submit">Cadastrar convênio</button>
    </form>
</section>

<!-- PACIENTE -->
<section id="paciente" class="cartao">
    <h2>Novo paciente</h2>
    <form method="post">
        <input type="hidden" name="tipo" value="paciente">
        <label for="p_nome">Nome completo</label>
        <input id="p_nome" name="nome" required>
        <div class="linha">
            <div><label for="p_cpf">CPF</label>
                 <input id="p_cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required></div>
            <div><label for="p_nasc">Data de nascimento</label>
                 <input type="date" id="p_nasc" name="data_nascimento" required></div>
        </div>
        <div class="linha">
            <div><label for="p_tel">Telefone</label><input id="p_tel" name="telefone"></div>
            <div><label for="p_email">E-mail</label><input type="email" id="p_email" name="email"></div>
        </div>
        <div class="linha">
            <div><label for="p_end">Endereço</label><input id="p_end" name="endereco"></div>
            <div><label for="p_cid">Cidade</label><input id="p_cid" name="cidade"></div>
        </div>
        <label for="p_conv">Convênio</label>
        <select id="p_conv" name="id_convenio">
            <option value="">Particular (sem convênio)</option>
            <?php foreach ($convenios as $c): ?>
                <option value="<?= $c['id_convenio'] ?>"><?= e($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Cadastrar paciente</button>
    </form>
</section>

<!-- MÉDICO (enctype obrigatório para enviar arquivo) -->
<section id="medico" class="cartao">
    <h2>Novo médico</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="tipo" value="medico">
        <label for="m_nome">Nome</label>
        <input id="m_nome" name="nome" required>
        <div class="linha">
            <div><label for="m_crm">CRM</label><input id="m_crm" name="crm" placeholder="123456-SP" required></div>
            <div><label for="m_esp">Especialidade</label><input id="m_esp" name="especialidade" required></div>
        </div>
        <div class="linha">
            <div><label for="m_tel">Telefone</label><input id="m_tel" name="telefone"></div>
            <div><label for="m_email">E-mail</label><input type="email" id="m_email" name="email"></div>
        </div>
        <div class="linha">
            <div><label for="m_valor">Valor da consulta (R$)</label>
                 <input type="number" id="m_valor" name="valor_consulta" min="0" step="0.01" required></div>
            <div><label for="m_foto">Foto</label>
                 <input type="file" id="m_foto" name="foto" accept="image/*"></div>
        </div>
        <button type="submit">Cadastrar médico</button>
    </form>
</section>

<!-- CONSULTA -->
<section id="consulta" class="cartao">
    <h2>Nova consulta</h2>
    <form method="post">
        <input type="hidden" name="tipo" value="consulta">
        <div class="linha">
            <div><label for="c_pac">Paciente</label>
                <select id="c_pac" name="id_paciente" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?= $p['id_paciente'] ?>"><?= e($p['nome']) ?> (CPF <?= e($p['cpf']) ?>)</option>
                    <?php endforeach; ?>
                </select></div>
            <div><label for="c_med">Médico</label>
                <select id="c_med" name="id_medico" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($medicos as $m): ?>
                        <option value="<?= $m['id_medico'] ?>"><?= e($m['nome']) ?> – <?= e($m['especialidade']) ?></option>
                    <?php endforeach; ?>
                </select></div>
        </div>
        <div class="linha">
            <div><label for="c_data">Data</label><input type="date" id="c_data" name="data_consulta" required></div>
            <div><label for="c_hora">Horário</label><input type="time" id="c_hora" name="horario" required></div>
            <div><label for="c_status">Status</label>
                <select id="c_status" name="status">
                    <option>Agendada</option><option>Confirmada</option>
                    <option>Realizada</option><option>Cancelada</option>
                </select></div>
        </div>
        <label for="c_motivo">Motivo</label>
        <input id="c_motivo" name="motivo" required>
        <label for="c_obs">Observações</label>
        <textarea id="c_obs" name="observacoes" rows="3"></textarea>
        <button type="submit">Agendar consulta</button>
    </form>
</section>

<!-- EXAME -->
<section id="exame" class="cartao">
    <h2>Novo exame</h2>
    <form method="post">
        <input type="hidden" name="tipo" value="exame">
        <div class="linha">
            <div><label for="x_pac">Paciente</label>
                <select id="x_pac" name="id_paciente" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?= $p['id_paciente'] ?>"><?= e($p['nome']) ?> (CPF <?= e($p['cpf']) ?>)</option>
                    <?php endforeach; ?>
                </select></div>
            <div><label for="x_cons">Consulta que pediu o exame (opcional)</label>
                <select id="x_cons" name="id_consulta">
                    <option value="">Nenhuma</option>
                    <?php foreach ($consultas as $c): ?>
                        <option value="<?= $c['id_consulta'] ?>">
                            <?= dataBR($c['data_consulta']) ?> – <?= e($c['paciente']) ?> com <?= e($c['medico']) ?>
                        </option>
                    <?php endforeach; ?>
                </select></div>
        </div>
        <label for="x_nome">Nome do exame</label>
        <input id="x_nome" name="nome_exame" required>
        <div class="linha">
            <div><label for="x_sol">Data da solicitação</label>
                 <input type="date" id="x_sol" name="data_solicitacao" required></div>
            <div><label for="x_real">Data de realização</label>
                 <input type="date" id="x_real" name="data_realizacao"></div>
            <div><label for="x_status">Status</label>
                <select id="x_status" name="status">
                    <option>Solicitado</option><option>Agendado</option>
                    <option>Realizado</option><option>Cancelado</option>
                </select></div>
            <div><label for="x_valor">Valor (R$)</label>
                 <input type="number" id="x_valor" name="valor" min="0" step="0.01"></div>
        </div>
        <label for="x_res">Resultado / observação</label>
        <textarea id="x_res" name="resultado" rows="3"></textarea>
        <button type="submit">Cadastrar exame</button>
    </form>
</section>

<?php require 'includes/footer.php'; ?>