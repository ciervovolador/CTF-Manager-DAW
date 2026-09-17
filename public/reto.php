<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$retoId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$retoId) {
    exit('Reto no válido.');
}

$stmt = $pdo->prepare("
    SELECT *
    FROM retos
    WHERE id = ?
    AND activo = 1
");

$stmt->execute([$retoId]);

$reto = $stmt->fetch();

if (!$reto) {
    exit('Reto no encontrado.');
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $flag = trim($_POST['flag'] ?? '');

    $stmt = $pdo->prepare("
        SELECT flag_hash
        FROM flags
        WHERE reto_id = ?
    ");

    $stmt->execute([$retoId]);

    $flags = $stmt->fetchAll();

    $correcta = false;

    foreach ($flags as $fila) {

        if (
            password_verify(
                $flag,
                $fila['flag_hash']
            )
        ) {
            $correcta = true;
            break;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO intentos
        (
            usuario_id,
            reto_id,
            correcto
        )
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $_SESSION['usuario_id'],
        $retoId,
        $correcta ? 1 : 0
    ]);

    if ($correcta) {

        $stmt = $pdo->prepare("
            SELECT completado
            FROM progreso
            WHERE usuario_id = ?
            AND reto_id = ?
        ");

        $stmt->execute([
            $_SESSION['usuario_id'],
            $retoId
        ]);

        $progreso = $stmt->fetch();

        if (!$progreso || !$progreso['completado']) {

            $pdo->beginTransaction();

            try {

                $stmt = $pdo->prepare("
                    INSERT INTO progreso
                    (
                        usuario_id,
                        reto_id,
                        completado,
                        puntos_obtenidos,
                        fecha_completado
                    )
                    VALUES (?, ?, 1, ?, NOW())

                    ON DUPLICATE KEY UPDATE
                        completado = 1,
                        puntos_obtenidos = VALUES(puntos_obtenidos),
                        fecha_completado = NOW()
                ");

                $stmt->execute([
                    $_SESSION['usuario_id'],
                    $retoId,
                    $reto['puntos']
                ]);

                $stmt = $pdo->prepare("
                    UPDATE usuarios
                    SET puntos = puntos + ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $reto['puntos'],
                    $_SESSION['usuario_id']
                ]);

                $pdo->commit();

                $mensaje =
                    'Flag correcta. +' .
                    (int)$reto['puntos'] .
                    ' puntos';

            } catch (Exception $e) {

                $pdo->rollBack();

                $mensaje =
                    'Se produjo un error al registrar el progreso.';
            }

        } else {

            $mensaje =
                'Este reto ya estaba completado.';
        }

    } else {

        $mensaje =
            'Flag incorrecta.';
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>
        <?= htmlspecialchars($reto['titulo']) ?>
    </title>
</head>

<body>

<h1>
    <?= htmlspecialchars($reto['titulo']) ?>
</h1>

<p>
    <?= htmlspecialchars($reto['descripcion']) ?>
</p>

<p>
    <strong>
        <?= (int)$reto['puntos'] ?> puntos
    </strong>
</p>

<?php if ($mensaje): ?>

    <p>
        <?= htmlspecialchars($mensaje) ?>
    </p>

<?php endif; ?>

<form method="POST">

    <label>
        Introduce la flag:
    </label>

    <br><br>

    <input
        type="text"
        name="flag"
        placeholder="CTF{...}"
        required
    >

    <button type="submit">
        Comprobar
    </button>

</form>

<p>
    <a href="retos.php">
        Volver a los retos
    </a>
</p>

</body>

</html>