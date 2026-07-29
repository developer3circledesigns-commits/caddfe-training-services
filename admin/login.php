<?php
require_once __DIR__ . '/../config/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['admin_id'])) {
    header('Location: index');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        try {
            if ($pdo === null) {
                throw new \RuntimeException('Database connection not available');
            }
            $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, role, is_active FROM admin_users WHERE username = :username AND is_active = 1 LIMIT 1');
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = (int)$admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];

                $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = :id')
                    ->execute([':id' => $admin['id']]);

                header('Location: index');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (\Throwable $e) {
            $error = 'Something went wrong. Please try again.';
            error_log('Admin login failed: ' . $e->getMessage());
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - CADDFE Training Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Inter', system-ui, sans-serif; }
    body {
      min-height: 100vh; display: flex; align-items: center; justify-content: center;
      background: #f8fafc;
    }
    .login-card {
      background: #fff; border-radius: 20px; padding: 2.5rem; width: 100%; max-width: 420px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    }
    .login-card .logo { text-align: center; margin-bottom: 2rem; }
    .login-card .logo h4 { font-weight: 700; color: #1e293b; }
    .login-card .logo p { color: #64748b; font-size: 0.85rem; margin-top: 0.25rem; }
    .login-card .form-control {
      border-radius: 10px; padding: 0.65rem 1rem; font-size: 0.9rem;
      border-color: #e2e8f0;
    }
    .login-card .form-control:focus {
      border-color: #d8000d; box-shadow: 0 0 0 3px rgba(216,0,13,0.12);
    }
    .login-card .btn {
      border-radius: 10px; padding: 0.65rem; font-weight: 600;
    }
    .login-card .back-link { text-align: center; margin-top: 1.25rem; font-size: 0.85rem; }
    .login-card .back-link a { color: #d8000d; text-decoration: none; }
    .login-card .back-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="logo">
      <i class="bi bi-building fs-1 text-danger"></i>
      <h4>CADDFE Admin</h4>
      <p>Sign in to manage enrollments</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label small fw-semibold" style="color:#1e293b;">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
      </div>
      <div class="mb-4">
        <label class="form-label small fw-semibold" style="color:#1e293b;">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>
      <button type="submit" class="btn w-100 text-white" style="background:#d8000d;border-color:#d8000d;">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
      </button>
    </form>

    <div class="back-link">
      <a href="../index.php"><i class="bi bi-arrow-left me-1"></i>Back to Website</a>
    </div>
  </div>
</body>
</html>