<?php
include 'includes/db.php';
include 'templates/header.php';

$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

$primerDia = date('w', strtotime("$anio-$mes-01"));
$diasEnMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

$eventos = [];
$result = $conn->query("SELECT * FROM eventos WHERE MONTH(fecha) = $mes AND YEAR(fecha) = $anio");
while ($row = $result->fetch_assoc()) {
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
    $diasSemana = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];
    foreach ($diasSemana as $d) {
        echo "<div class='day'>$d</div>";
    }

    for ($i = 0; $i < $primerDia; $i++) {
        echo "<div class='empty'></div>";
    }

    for ($dia = 1; $dia <= $diasEnMes; $dia++) {
        $fechaActual = sprintf("%04d-%02d-%02d", $anio, $mes, $dia);
        $clase = (date('Y-m-d') == $fechaActual) ? "date today" : "date";
        echo "<div class='$clase'><strong>$dia</strong>";
        if (isset($eventos[$dia])) {
            foreach ($eventos[$dia] as $ev) {
                echo "<div class='event' 
                        data-id='{$ev['id']}'
                        data-titulo='" . htmlspecialchars($ev['titulo']) . "'
                        data-descripcion='" . htmlspecialchars($ev['descripcion']) . "'
                        data-fecha='" . htmlspecialchars($ev['fecha']) . "'>
                      " . htmlspecialchars($ev['titulo']) . "
                      </div>";
            }
        }
        echo "</div>";
    }
    ?>
  </div>
</div>

<!-- Modal de Detalles -->
<div id="eventModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3 id="modalTitle"></h3>
    <p id="modalDescription"></p>
    <p><strong>Fecha:</strong> <span id="modalDate"></span></p>
    <div class="modal-actions">
      <button id="editEvent">Editar</button>
      <button id="deleteEvent">Eliminar</button>
    </div>
  </div>
</div>

<!-- Modal de Edición -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-edit">&times;</span>
    <h3>Editar Evento</h3>
    <form id="editForm" method="POST">
      <input type="hidden" name="id" id="editId">
      <input type="text" name="titulo" id="editTitulo" placeholder="Título" required>
      <textarea name="descripcion" id="editDescripcion" placeholder="Descripción" required></textarea>
      <input type="date" name="fecha" id="editFecha" required>
      <button type="submit">Guardar Cambios</button>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modals = {
        details: document.getElementById('eventModal'),
        edit: document.getElementById('editModal')
    };
    
    const closeBtns = {
        details: document.querySelector('.close'),
        edit: document.querySelector('.close-edit')
    };

    // Manejar clic en eventos
    document.querySelectorAll('.event').forEach(event => {
        event.addEventListener('click', () => {
            modals.details.style.display = 'block';
            document.getElementById('modalTitle').textContent = event.dataset.titulo;
            document.getElementById('modalDescription').textContent = event.dataset.descripcion;
            document.getElementById('modalDate').textContent = event.dataset.fecha;

            // Configurar botones de acción
            document.getElementById('deleteEvent').onclick = () => {
                if(confirm('¿Eliminar este evento permanentemente?')) {
                    window.location = `delete_event.php?id=${event.dataset.id}`;
                }
            };

            document.getElementById('editEvent').onclick = () => {
                modals.details.style.display = 'none';
                modals.edit.style.display = 'block';
                
                // Llenar formulario de edición
                document.getElementById('editId').value = event.dataset.id;
                document.getElementById('editTitulo').value = event.dataset.titulo;
                document.getElementById('editDescripcion').value = event.dataset.descripcion;
                document.getElementById('editFecha').value = event.dataset.fecha;
            };
        });
    });

    // Manejar cierre de modales
    Object.entries(closeBtns).forEach(([key, btn]) => {
        btn.onclick = () => modals[key].style.display = 'none';
    });

    window.onclick = (e) => {
        if(e.target.classList.contains('modal')) {
            Object.values(modals).forEach(modal => modal.style.display = 'none');
        }
    }

    // Manejar envío de edición
    document.getElementById('editForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('edit_event.php', {
                method: 'POST',
                body: formData
            });
            
            if(response.ok) {
                window.location.reload();
            } else {
                alert('Error al actualizar el evento');
            }
        } catch(error) {
            console.error('Error:', error);
            alert('Error de conexión');
        }
    });
});
</script>

<?php include 'templates/footer.php'; ?>