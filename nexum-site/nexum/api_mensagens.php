<?php
/**
 * api_mensagens.php — API assíncrona para o chat entre vendedor e comprador
 */
require_once __DIR__ . '/php/config.php';

header('Content-Type: application/json; charset=utf-8');

$user = get_logged_user();
$action = $_REQUEST['action'] ?? '';

// Troca rápida de usuário para testes
if ($action === 'switch_user') {
    $target = $_REQUEST['target'] ?? 'vendedor';
    $targetEmail = $target === 'vendedor' ? 'vendedor@nexum.com' : 'comprador@nexum.com';
    try {
        $pdo = get_db();
        $stmt = $pdo->prepare('SELECT id, nome, email, tipo FROM usuarios WHERE email = ?');
        $stmt->execute([$targetEmail]);
        $found = $stmt->fetch();
        if ($found) {
            $_SESSION['user_id'] = $found['id'];
            $_SESSION['user_nome'] = $found['nome'];
            $_SESSION['user_email'] = $found['email'];
            $_SESSION['user_tipo'] = $found['tipo'];
            echo json_encode(['success' => true, 'user' => $found]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

$pdo = get_db();
$userId = $user['id'];

switch ($action) {
    // Obter mensagens de uma conversa
    case 'get_messages':
        $conversaId = (int)($_GET['conversa_id'] ?? 0);
        if ($conversaId <= 0) {
            echo json_encode(['error' => 'Conversa inválida']);
            exit;
        }

        // Verificar permissão
        $stmt = $pdo->prepare("SELECT id, comprador_id, vendedor_id, produto_titulo FROM conversas WHERE id = ?");
        $stmt->execute([$conversaId]);
        $conv = $stmt->fetch();

        if (!$conv || ($conv['comprador_id'] != $userId && $conv['vendedor_id'] != $userId)) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado a esta conversa']);
            exit;
        }

        // Marcar mensagens recebidas como lidas
        $stmtRead = $pdo->prepare("UPDATE mensagens SET lida = 1 WHERE conversa_id = ? AND remetente_id != ? AND lida = 0");
        $stmtRead->execute([$conversaId, $userId]);

        // Buscar mensagens
        $stmtMsg = $pdo->prepare("
            SELECT m.id, m.remetente_id, m.mensagem, m.lida, m.enviado_em,
                   u.nome as remetente_nome, u.tipo as remetente_tipo
            FROM mensagens m
            INNER JOIN usuarios u ON m.remetente_id = u.id
            WHERE m.conversa_id = ?
            ORDER BY m.enviado_em ASC, m.id ASC
        ");
        $stmtMsg->execute([$conversaId]);
        $rawMsgs = $stmtMsg->fetchAll();

        $messages = [];
        foreach ($rawMsgs as $m) {
            $timestamp = strtotime($m['enviado_em']);
            $messages[] = [
                'id'             => (int)$m['id'],
                'remetente_id'   => (int)$m['remetente_id'],
                'remetente_nome' => $m['remetente_nome'],
                'remetente_tipo' => $m['remetente_tipo'],
                'mensagem'       => $m['mensagem'],
                'lida'           => (bool)$m['lida'],
                'is_me'          => ((int)$m['remetente_id'] === $userId),
                'horario'        => date('H:i', $timestamp),
                'data'           => date('d/m/Y', $timestamp)
            ];
        }

        echo json_encode(['success' => true, 'messages' => $messages]);
        break;

    // Enviar nova mensagem
    case 'send_message':
        $conversaId = (int)($_POST['conversa_id'] ?? 0);
        $texto = trim($_POST['mensagem'] ?? '');

        if ($conversaId <= 0 || $texto === '') {
            echo json_encode(['error' => 'Mensagem ou conversa inválida']);
            exit;
        }

        // Verificar permissão
        $stmt = $pdo->prepare("SELECT id, comprador_id, vendedor_id FROM conversas WHERE id = ?");
        $stmt->execute([$conversaId]);
        $conv = $stmt->fetch();

        if (!$conv || ($conv['comprador_id'] != $userId && $conv['vendedor_id'] != $userId)) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado']);
            exit;
        }

        // Inserir mensagem
        $stmtInsert = $pdo->prepare("
            INSERT INTO mensagens (conversa_id, remetente_id, mensagem, lida, enviado_em)
            VALUES (?, ?, ?, 0, NOW())
        ");
        $stmtInsert->execute([$conversaId, $userId, $texto]);
        $newId = (int)$pdo->lastInsertId();

        // Atualizar conversa
        $stmtUpdate = $pdo->prepare("
            UPDATE conversas
            SET ultimo_remetente_id = ?, ultima_mensagem = ?, atualizado_em = NOW()
            WHERE id = ?
        ");
        $stmtUpdate->execute([$userId, $texto, $conversaId]);

        echo json_encode([
            'success' => true,
            'message' => [
                'id'             => $newId,
                'remetente_id'   => $userId,
                'remetente_nome' => $user['nome'],
                'remetente_tipo' => $user['tipo'],
                'mensagem'       => $texto,
                'lida'           => false,
                'is_me'          => true,
                'horario'        => date('H:i'),
                'data'           => date('d/m/Y')
            ]
        ]);
        break;

    // Iniciar ou buscar conversa existente
    case 'start_conversation':
        $produto = trim($_POST['produto_titulo'] ?? 'Dúvidas sobre produto');
        $outroId = (int)($_POST['destinatario_id'] ?? 0);

        // Se outroId não for passado, pegar o primeiro vendedor (se for comprador) ou primeiro comprador (se for vendedor)
        if ($outroId <= 0) {
            $tipoBuscado = ($user['tipo'] === 'vendedor') ? 'comprador' : 'vendedor';
            $stmtUser = $pdo->prepare("SELECT id FROM usuarios WHERE tipo = ? AND status = 'ativo' AND id != ? LIMIT 1");
            $stmtUser->execute([$tipoBuscado, $userId]);
            $outroId = (int)$stmtUser->fetchColumn();
        }

        if ($outroId <= 0) {
            echo json_encode(['error' => 'Destinatário não encontrado']);
            exit;
        }

        $compradorId = ($user['tipo'] === 'comprador') ? $userId : $outroId;
        $vendedorId  = ($user['tipo'] === 'vendedor') ? $userId : $outroId;

        // Verificar se já existe conversa com esse mesmo produto ou entre essas duas partes
        $stmtConv = $pdo->prepare("
            SELECT id FROM conversas 
            WHERE comprador_id = ? AND vendedor_id = ? AND produto_titulo = ?
            LIMIT 1
        ");
        $stmtConv->execute([$compradorId, $vendedorId, $produto]);
        $existente = $stmtConv->fetch();

        if ($existente) {
            $conversaId = (int)$existente['id'];
        } else {
            // Criar conversa
            $stmtCreate = $pdo->prepare("
                INSERT INTO conversas (comprador_id, vendedor_id, produto_titulo, atualizado_em)
                VALUES (?, ?, ?, NOW())
            ");
            $stmtCreate->execute([$compradorId, $vendedorId, $produto]);
            $conversaId = (int)$pdo->lastInsertId();
        }

        // Se veio mensagem inicial
        $msgInicial = trim($_POST['mensagem_inicial'] ?? '');
        if ($msgInicial !== '') {
            $stmtMsg = $pdo->prepare("
                INSERT INTO mensagens (conversa_id, remetente_id, mensagem, lida, enviado_em)
                VALUES (?, ?, ?, 0, NOW())
            ");
            $stmtMsg->execute([$conversaId, $userId, $msgInicial]);
            $pdo->prepare("UPDATE conversas SET ultimo_remetente_id = ?, ultima_mensagem = ?, atualizado_em = NOW() WHERE id = ?")
                ->execute([$userId, $msgInicial, $conversaId]);
        }

        echo json_encode(['success' => true, 'conversa_id' => $conversaId]);
        break;

    default:
        echo json_encode(['error' => 'Ação não reconhecida']);
        break;
}
