<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';


// Datos del usuario
$stmt = $pdo->prepare("
    SELECT
        username,
        puntos
    FROM usuarios
    WHERE id = ?
");

$stmt->execute([
    $_SESSION['usuario_id']
]);

$usuario = $stmt->fetch();


// Total de retos activos
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM retos
    WHERE activo = 1
");

$stmt->execute();

$totalRetos = (int) $stmt->fetchColumn();


// Retos completados por el usuario
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM progreso
    WHERE usuario_id = ?
    AND completado = 1
");

$stmt->execute([
    $_SESSION['usuario_id']
]);

$retosCompletados = (int) $stmt->fetchColumn();


// Porcentaje de progreso
$porcentaje = 0;

if ($totalRetos > 0) {

    $porcentaje = round(
        ($retosCompletados / $totalRetos) * 100
    );
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Dashboard | CTF Manager
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>

<header class="topbar">

    <strong>
        CTF Manager
    </strong>

    <div>

        <?= htmlspecialchars($usuario['username']) ?>

        ·

        <a href="logout.php">
            Salir
        </a>

    </div>

</header>


<main class="dashboard">

    <h1>

        Bienvenido,

        <?= htmlspecialchars($usuario['username']) ?>

    </h1>


    <section class="cards">


        <!-- PUNTUACIÓN -->

        <div class="card">

            <h2>
                Puntuación
            </h2>

            <div class="score">

                <?= (int) $usuario['puntos'] ?>

            </div>

            <p>
                puntos
            </p>

        </div>


        <!-- PROGRESO -->

        <div class="card">

            <h2>
                Progreso
            </h2>

            <div class="score">

                <?= $porcentaje ?> %

            </div>

            <p>

                <?= $retosCompletados ?>

                de

                <?= $totalRetos ?>

                retos completados

            </p>

            <progress
                value="<?= $porcentaje ?>"
                max="100">
            </progress>

            <br><br>

            <a href="retos.php">
                Ver retos
            </a>

        </div>


        <!-- LABORATORIO -->

        <div class="card">

            <h2>
                Laboratorio
            </h2>

            <p>

                Estado:

                <strong>
                    ⚫ Detenido
                </strong>

            </p>

            <button disabled>

                Iniciar laboratorio

            </button>

            <p class="small">

                La integración automática
                con Docker se añadirá
                en la siguiente fase.

            </p>

        </div>

    </section>

</main>

</body>

</html>