<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT id, username, password_hash, rol, puntos
         FROM usuarios
         WHERE username = ?'
    );

    $stmt->execute([$username]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['rol'] = $usuario['rol'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Usuario o contraseña incorrectos.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | CTF Manager</title>
</head>
<body>

<h1>CTF Manager</h1>

<?php if (isset($_GET['registro']) && $_GET['registro'] === 'ok'): ?>
    <p style="color:green;">Cuenta creada correctamente.</p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="POST">
    <label>Usuario</label><br>
    <input type="text" name="username" required><br><br>

    <label>Contraseña</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Entrar</button>
</form>

<p>
    <a href="registro.php">Crear una cuenta</a>
</p>

</body>
</html>