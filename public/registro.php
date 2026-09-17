<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '') {
        $errores[] = 'El nombre de usuario es obligatorio.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    }

    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if ($password !== $password2) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    if (!$errores) {
        $stmt = $pdo->prepare(
            'SELECT id FROM usuarios WHERE username = ? OR email = ?'
        );
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $errores[] = 'El usuario o el correo ya existen.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (username, email, password_hash)
                 VALUES (?, ?, ?)'
            );

            $stmt->execute([$username, $email, $passwordHash]);

            header('Location: login.php?registro=ok');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | CTF Manager</title>
</head>
<body>

<h1>Crear cuenta</h1>

<?php foreach ($errores as $error): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endforeach; ?>

<form method="POST">
    <label>Usuario</label><br>
    <input type="text" name="username" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña</label><br>
    <input type="password" name="password" required><br><br>

    <label>Repetir contraseña</label><br>
    <input type="password" name="password2" required><br><br>

    <button type="submit">Registrarse</button>
</form>

<p>
    <a href="login.php">Ya tengo cuenta</a>
</p>

</body>
</html>