<?php
/**
 * api_avaliacoes.php — Endpoint para submissão e consulta de avaliações do site
 */
require_once __DIR__ . '/php/config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'get';
$pdo = get_db();

if ($action === 'add') {
    $nome = clean_input($_POST['nome'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $nota = (int)($_POST['nota'] ?? 5);
    $categoria = clean_input($_POST['categoria'] ?? 'geral');
    $titulo = clean_input($_POST['titulo'] ?? '');
    $comentario = clean_input($_POST['comentario'] ?? '');
    $tipoUsuario = clean_input($_POST['tipo_usuario'] ?? 'comprador');

    // Validações
    if ($nome === '' || $comentario === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nome e comentário são obrigatórios.']);
        exit;
    }

    if ($nota < 1 || $nota > 5) {
        $nota = 5;
    }

    $validCats = ['geral', 'atendimento', 'midia_fisica', 'entrega'];
    if (!in_array($categoria, $validCats)) {
        $categoria = 'geral';
    }

    $validTipos = ['comprador', 'vendedor', 'colecionador'];
    if (!in_array($tipoUsuario, $validTipos)) {
        $tipoUsuario = 'comprador';
    }

    $logged = get_logged_user();
    $usuarioId = $logged ? $logged['id'] : null;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO avaliacoes (usuario_id, nome, email, nota, categoria, titulo, comentario, tipo_usuario, criado_em)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$usuarioId, $nome, $email, $nota, $categoria, $titulo, $comentario, $tipoUsuario]);
        $newId = (int)$pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Avaliação publicada com sucesso! Obrigado pelo seu feedback.',
            'review' => [
                'id' => $newId,
                'nome' => $nome,
                'nota' => $nota,
                'categoria' => $categoria,
                'titulo' => $titulo,
                'comentario' => $comentario,
                'tipo_usuario' => $tipoUsuario,
                'data' => date('d/m/Y')
            ]
        ]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar avaliação: ' . $e->getMessage()]);
        exit;
    }
}

// Obter avaliações
if ($action === 'get') {
    $cat = clean_input($_GET['categoria'] ?? '');
    $sql = "SELECT id, nome, nota, categoria, titulo, comentario, tipo_usuario, criado_em FROM avaliacoes ";
    $params = [];

    if ($cat !== '' && $cat !== 'todas') {
        $sql .= "WHERE categoria = ? ";
        $params[] = $cat;
    }

    $sql .= "ORDER BY criado_em DESC LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $reviews = [];
    foreach ($rows as $r) {
        $reviews[] = [
            'id' => (int)$r['id'],
            'nome' => $r['nome'],
            'nota' => (int)$r['nota'],
            'categoria' => $r['categoria'],
            'titulo' => $r['titulo'],
            'comentario' => $r['comentario'],
            'tipo_usuario' => $r['tipo_usuario'],
            'data' => date('d/m/Y', strtotime($r['criado_em']))
        ];
    }

    echo json_encode(['success' => true, 'reviews' => $reviews]);
    exit;
}
