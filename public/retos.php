<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->prepare("
    SELECT
        r.id,
        r.titulo,
        r.descripcion,
        r.categoria,
        r.dificultad,
        r.puntos,
        COALESCE(p.completado, 0) AS completado
    FROM retos r
    LEFT JOIN progreso p
        ON p.reto_id = r.id
        AND p.usuario_id = ?
    WHERE r.activo = 1
    ORDER BY r.orden ASC
");

$stmt->execute([
    $_SESSION['usuario_id']
]);

$retos = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Retos | CTF Manager</title>
</head>

<body>

<h1>Retos disponibles</h1>

<?php foreach ($retos as $reto): ?>

    <div style="
        border:1px solid #ccc;
        padding:15px;
        margin-bottom:15px;
    ">

        <h2>
            <?= htmlspecialchars($reto['titulo']) ?>
        </h2>

        <p>
            <?= htmlspecialchars($reto['descripcion']) ?>
        </p>

        <p>
            <strong>Categoría:</strong>
            <?= htmlspecialchars($reto['categoria']) ?>
        </p>

        <p>
            <strong>Dificultad:</strong>
            <?= htmlspecialchars($reto['dificultad']) ?>
        </p>

        <p>
            <strong>Puntos:</strong>
            <?= (int)$reto['puntos'] ?>
        </p>

        <?php if ($reto['completado']): ?>

            <p style="color:green;">
                ✓ Completado
            </p>

        <?php else: ?>

            <a href="reto.php?id=<?= (int)$reto['id'] ?>">
                Abrir reto
            </a>

        <?php endif; ?>

    </div>

<?php endforeach; ?>

<p>
    <a href="dashboard.php">
        Volver al dashboard
    </a>
</p>

</body>

</html>