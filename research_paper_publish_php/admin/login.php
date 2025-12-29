<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $stmt = $pdo->prepare('SELECT id,password,role FROM users WHERE email=?');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if ($row && password_verify($pass, $row['password']) && $row['role'] === 'admin') {
        $_SESSION['user_id'] = $row['id'];
        header('Location: dashboard.php'); exit;
    } else $err = 'Invalid admin credentials';
}
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center"><div class="col-md-4">
    <div class="card"><div class="card-body">
      <h4>Admin Login</h4>
      <?php if ($err): ?><div class="alert alert-danger"><?= esc($err) ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3"><input name="email" class="form-control" placeholder="Email"></div>
        <div class="mb-3"><input name="password" class="form-control" type="password" placeholder="Password"></div>
        <button class="btn btn-primary">Login</button>
      </form>
    </div></div>
  </div></div>
</div>
</body></html>