async function imprimirTurno(turnoId) {
  try {
      // Conectar con QZ Tray
    if (!qz.websocket.isActive()) {
        await qz.websocket.connect();
    }

    // Obtener impresora predeterminada
    const printer = await qz.printers.getDefault();
    
    // Configurar impresión PDF
    const config = qz.configs.create(printer, {
        size: { width: 2, height: 2.2 }, // pulgadas (50mm x 55mm aprox)
        units: 'in'
    });

    // URL del PDF
    const pdfUrl = BASE_URL + `/turno/pdf?id=${turnoId}`;
    
    // Imprimir
    const data = [{
        type: 'pdf',
        data: pdfUrl
    }];
    
    await qz.print(config, data);
    console.log('Impresión enviada correctamente');
      
  } catch (err) {
      console.error('Error al imprimir:', err);
  }
}

async function subirADrive(turnoId) {
  try {
    const response = await fetch(BASE_URL + `/turno/subir-drive?id=${turnoId}`);
    const result = await response.json();
    if (result.success) {
      console.log("Subido a Drive:", result.fileId);
    }
  } catch (err) {
    console.error("Error al subir a Drive:", err);
    await fetch(BASE_URL + `/auth.php`)
  }
}

let tiempoRestanteSegundos = 0;
let turnosPendientes = 0;
let intervaloCuentaRegresiva = null;
let ultimaActualizacionServidor = 0;

function formatearTiempo(segundos) {
  if (segundos <= 0) return "0:00";
  const mins = Math.floor(segundos / 60);
  const secs = segundos % 60;
  return `${mins}:${secs.toString().padStart(2, "0")}`;
}

function actualizarDisplay() {
  const tiempoDiv = document.getElementById("tiempo-espera");

  if (tiempoRestanteSegundos > 0) {
    tiempoRestanteSegundos--;
  }

  tiempoDiv.innerHTML = `
    <p>Turnos adelante: ${turnosPendientes}</p>
    <p>Tiempo de espera estimado: ${formatearTiempo(tiempoRestanteSegundos)}</p>
  `;
}

function obtenerEstadoGuardado() {
  const guardado = localStorage.getItem("tiempoEspera");
  if (guardado) {
    const estado = JSON.parse(guardado);
    tiempoRestanteSegundos = estado.tiempoRestante;
    ultimaActualizacionServidor = estado.ultimaActualizacion;
    turnosPendientes = estado.pendientes;
  }
}

function actualizarTiempo(turnoId) {
  fetch(BASE_URL + `/turno/tiempo-espera?id=${turnoId}`)
    .then((res) => res.json())
    .then((data) => {
      const tiempoDiv = document.getElementById("tiempo-espera");

      if (data.error) {
        tiempoDiv.innerHTML = "<p>No disponible</p>";
        return;
      }

      const ahora = Math.floor(Date.now() / 1000);

      if (ultimaActualizacionServidor === 0) {
        tiempoRestanteSegundos = data.tiempo_estimado_segundos;
      } else {
        const segundosTranscurridos = ahora - ultimaActualizacionServidor;
        const tiempoEsperadoLocal = Math.max(0, tiempoRestanteSegundos - segundosTranscurridos);
      
        tiempoRestanteSegundos = tiempoEsperadoLocal;
      }

      turnosPendientes = data.pendientes;
      ultimaActualizacionServidor = ahora;

      localStorage.setItem(
        "tiempoEspera",
        JSON.stringify({
          tiempoRestante: tiempoRestanteSegundos,
          ultimaActualizacion: ultimaActualizacionServidor,
          pendientes: turnosPendientes,
        })
      );

      actualizarDisplay();
    })
    .catch(() => {
      const tiempoDiv = document.getElementById("tiempo-espera");
      tiempoDiv.innerHTML = "<p>Error al cargar</p>";
    });
}

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function goBackAfter($path, $ms) {
  await sleep($ms);
  window.location.href = $path;
}

// Ejecutar cuando la página termine de cargar
document.addEventListener("DOMContentLoaded", () => {
  const imprimirDiv = document.getElementById("imprimir-turno");

  if (imprimirDiv && imprimirDiv.dataset.turnoId) {
    const turnoId = imprimirDiv.dataset.turnoId;

    obtenerEstadoGuardado();

    actualizarTiempo(turnoId);
    setInterval(() => actualizarTiempo(turnoId), 30000);

    intervaloCuentaRegresiva = setInterval(actualizarDisplay, 1000);

    imprimirTurno(turnoId);
    subirADrive(turnoId);

    // goBackAfter(BASE_URL, 10000);
  }
});