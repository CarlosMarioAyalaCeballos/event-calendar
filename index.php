<?php
include 'includes/db.php';
include 'templates/header.php';

// Obtén el mes y año actuales o los que se pasen por GET
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

// Calcula el primer día de la semana del mes y la cantidad de días
$primerDia = date('w', strtotime("$anio-$mes-01"));
$diasEnMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

// Consulta los eventos para el mes y año indicados
$eventos = [];
$result = $conn->query("SELECT * FROM eventos WHERE MONTH(fecha) = $mes AND YEAR(fecha) = $anio");
while ($row = $result->fetch_assoc()) {
    // Extrae el día (sin ceros a la izquierda)
    $diaEvento = date('j', strtotime($row['fecha']));
    $eventos[$diaEvento][] = $row;
}
?>

<div class="container">
  <h2>📅 Calendario de <?php echo strftime('%B %Y', strtotime("$anio-$mes-01")); ?></h2>

  <form action="add_event.php" method="POST" class="event-form">
    <input type="text" name="titulo" placeholder="Título del evento" required>
    <textarea name="descripcion" placeholder="Descripción" required></textarea>
    <input type="date" name="fecha" required>
    <button type="submit">Agregar evento</button>
  </form>

  <div class="calendar">
    <?php
    // Imprime los encabezados de los días
    $diasSemana = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];
    foreach ($diasSemana as $d) {
        echo "<div class='day'>$d</div>";
    }

    // Celdas vacías antes del primer día del mes
    for ($i = 0; $i < $primerDia; $i++) {
        echo "<div class='empty'></div>";
    }

    // Imprime cada día del mes
    for ($dia = 1; $dia <= $diasEnMes; $dia++) {
        // Si es la fecha actual, agrega la clase 'today'
        $fechaActual = sprintf("%04d-%02d-%02d", $anio, $mes, $dia);
        $clase = (date('Y-m-d') == $fechaActual) ? "date today" : "date";
        echo "<div class='$clase'><strong>$dia</strong>";
        if (isset($eventos[$dia])) {
            foreach ($eventos[$dia] as $ev) {
                echo "<div class='event'>" . htmlspecialchars($ev['titulo']) . "</div>";
            }
        }
        echo "</div>";
    }
    ?>
  </div>
</div>

<?php include 'templates/footer.php'; ?>
