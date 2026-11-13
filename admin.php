<?php
session_start();
$ADMIN_PASSWORD = getenv('COLETA_ADMIN_PASSWORD') ?: 'troque-esta-senha';
if (isset($_GET['logout'])) { session_destroy(); header('Location: admin.php'); exit; }
if (!isset($_SESSION['admin'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (hash_equals($ADMIN_PASSWORD, (string)($_POST['password'] ?? ''))) {
            $_SESSION['admin'] = true;
            header('Location: admin.php'); exit;
        }
        $error = 'Senha incorreta.';
    }
    ?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin • Coleta Expressa</title><style>body{font-family:Arial,sans-serif;background:#f3f4f6;display:grid;place-items:center;min-height:100vh;margin:0}.box{background:#fff;padding:32px;border-radius:16px;width:min(380px,90%);box-shadow:0 10px 30px #0001}input,button{width:100%;box-sizing:border-box;padding:13px;margin-top:10px;border:1px solid #ddd;border-radius:9px}button{background:#111;color:#fff;cursor:pointer}.err{color:#b00}</style></head><body><div class="box"><h2>Painel Coleta Expressa</h2><p>Entre para alterar o aplicativo e o WhatsApp.</p><?php if(!empty($error)) echo '<p class="err">'.htmlspecialchars($error).'</p>'; ?><form method="post"><input type="password" name="password" placeholder="Senha" required><button>Entrar</button></form></div></body></html><?php exit; }
$config = require __DIR__ . '/config.php';
$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $app = trim((string)($_POST['app_url'] ?? ''));
    $wa = preg_replace('/\D+/', '', (string)($_POST['whatsapp'] ?? ''));
    $msg = trim((string)($_POST['whatsapp_message'] ?? ''));
    if ($app !== '' && filter_var($app, FILTER_VALIDATE_URL) && $wa !== '') {
        $data = ['app_url'=>$app,'whatsapp'=>$wa,'whatsapp_message'=>$msg];
        $php = "<?php\nreturn " . var_export($data, true) . ";\n";
        file_put_contents(__DIR__ . '/config.php', $php, LOCK_EX);
        $config = $data; $saved = true;
    }
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Painel • Coleta Expressa</title><style>body{font-family:Arial,sans-serif;background:#f3f4f6;margin:0}.wrap{max-width:760px;margin:40px auto;padding:0 18px}.card{background:#fff;border-radius:16px;padding:26px;box-shadow:0 10px 30px #0001}label{display:block;font-weight:700;margin:18px 0 7px}input,textarea{width:100%;box-sizing:border-box;padding:13px;border:1px solid #d7d7d7;border-radius:9px;font-size:15px}textarea{min-height:100px;resize:vertical}.row{display:flex;gap:12px;margin-top:22px}.btn{border:0;border-radius:9px;padding:13px 18px;cursor:pointer;background:#111;color:#fff}.out{background:#eee;color:#111;text-decoration:none}.ok{padding:12px;background:#e8f7e8;border:1px solid #b8dfb8;border-radius:9px;margin-bottom:16px}.hint{color:#666;font-size:13px}.top{display:flex;justify-content:space-between;align-items:center}</style></head><body><div class="wrap"><div class="card"><div class="top"><div><h1>Painel administrativo</h1><p>Coleta Expressa</p></div><a href="?logout=1">Sair</a></div><?php if($saved): ?><div class="ok">Configurações salvas com sucesso.</div><?php endif; ?><form method="post"><label>Link do aplicativo</label><input type="url" name="app_url" value="<?=htmlspecialchars($config['app_url']??'')?>" required><div class="hint">Você pode trocar este link sempre que quiser. O site passa a usar o novo link após salvar.</div><label>WhatsApp</label><input type="text" name="whatsapp" value="<?=htmlspecialchars($config['whatsapp']??'')?>" placeholder="5511999999999" required><div class="hint">Informe o número com DDI e DDD, somente números.</div><label>Mensagem do WhatsApp</label><textarea name="whatsapp_message"><?=htmlspecialchars($config['whatsapp_message']??'')?></textarea><div class="row"><button class="btn" type="submit" name="save" value="1">Salvar alterações</button><a class="btn out" href="index.php" target="_blank">Abrir site</a></div></form><hr style="margin:28px 0;border:0;border-top:1px solid #eee"><p class="hint"><b>Importante:</b> troque a senha padrão antes de publicar. Para maior segurança, defina a variável de ambiente <code>COLETA_ADMIN_PASSWORD</code> no servidor.</p></div></div></body></html>
