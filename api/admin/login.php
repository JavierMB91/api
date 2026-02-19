<?php
$skipAuth = true;
require_once 'conexion.php';
include 'header.php';

$error = '';
if (!empty($_GET['expired'])) {
    $error = 'Sesion expirada. Por favor inicia sesion otra vez.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = trim($_POST['mail'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($mail === '' || $password === '') {
        $error = 'Mail y password son obligatorios.';
    } else {
        $respuesta = callAPI('POST', 'auth/login', [
            'mail' => $mail,
            'password' => $password
        ]);

        if (isset($respuesta['success']) && $respuesta['success']) {
            $_SESSION['jwt_token'] = $respuesta['token'] ?? '';
            header('Location: index.php');
            exit;
        }

        $error = $respuesta['error'] ?? 'Credenciales invalidas.';
    }
}
?>

<div class="container mt-4" style="max-width: 480px;">
    <h2 class="mb-3">Login Admin</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label for="mail" class="form-label">Mail</label>
            <input type="email" class="form-control" id="mail" name="mail" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</div>

<?php include 'footer.php'; ?>
