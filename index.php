<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de pistas deportivas</title>
</head>
<body>
    <?php
    require './comunes/auxiliar.php';

    const DIAS = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    $hoy = new DateTime();
    $lunes = clone $hoy;
    $unDia = new DateInterval('P1D');

    while ($lunes->format('w') != 1) {
        $lunes->add($unDia);
    }

    $pista_id = recoger_get('pista_id');

    $pdo = conectar();
    $sent = $pdo->query('SELECT id, denominacion FROM pistas');
    $filas = $sent->fetchAll();

    $sent = $pdo->prepare("SELECT *,
                                  fecha_hora::date AS fecha,
                                  fecha_hora::time(0) AS hora
                             FROM reservas
                            WHERE fecha_hora::date BETWEEN :lunes AND :viernes
                              AND pista_id = :pista_id");
    $sent->execute([
        'lunes' => $lunes->format('Y-m-d'),
        'viernes' => (clone $lunes)->add(new DateInterval('P4D'))->format('Y-m-d'),
        'pista_id' => $pista_id,
    ]);

    $reservas = $sent->fetchAll();
    ?>
    <form action="" method="get">
        <label for="pista_id">Selecciona la pista:</label>
        <select name="pista_id" id="pista_id">
            <?php foreach ($filas as $f): ?>
                <option value="<?= hh($f['id']) ?>" <?= selected($f['id'], $pista_id) ?> >
                    <?= hh($f['denominacion']) ?>
                </option>
            <?php endforeach ?>
        </select>
        <button type="submit">Seleccionar</button>
    </form>
    <table border="1">
        <thead>
            <th>Horas</th>
            <?php for ($dia = clone $lunes, $i = 0; $i < count(DIAS); $i++, $dia->add($unDia)): ?>
                <?php $fecha = $dia->format('d-m-Y') ?>
                <th>
                    <?= DIAS[$i] . " ($fecha)" ?>
                </th>
            <?php endfor ?>
        </thead>
        <tbody>
            <?php for ($ho = 10; $ho < 20; $ho++): ?>
                <?php $hora = "$ho:00:00" ?>
                <tr>
                    <td><?= $hora ?></td>
                    <?php for ($dia = clone $lunes, $i = 0; $i < count(DIAS); $i++, $dia->add($unDia)): ?>
                        <?php $fecha = $dia->format('Y-m-d') ?>
                        <?php if ($usu_res_id = existe($reservas, $fecha, $hora) !== false): ?>
                            <td>Reservado</td>
                        <?php else: ?>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="fecha_hora" value="<?= "$fecha $hora" ?>">
                                    <input type="hidden" name="pista_id" value="<?= $pista_id ?>">
                                    <button type="submit">Reservar</button>
                                </form>
                            </td>
                        <?php endif ?>
                    <?php endfor ?>
                </tr>
            <?php endfor ?>
        </tbody>
    </table>
</body>
</html>