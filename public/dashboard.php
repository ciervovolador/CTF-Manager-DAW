<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->prepare(
    'SELECT username, puntos
     FROM usuarios
     WHERE id = ?'
);

$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | CTF Manager</title>
</head>
<body>

<h1>
    Bienvenido, <?= htmlspecialchars($usuario['username']) ?>
</h1>

<p>
    Puntuación: <?= (int) $usuario['puntos'] ?> puntos
</p>

<p>
    Estado del laboratorio: detenido
</p>

<button disabled>
    Iniciar laboratorio
</button>

<p>
    <a href="logout.php">Cerrar sesión</a>
</p>

</body>
</html>