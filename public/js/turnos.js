
function imprimirTurno(turnoId) {
  window.open(BASE_URL + `/turno/pdf?id=${turnoId}`, "_blank");
  const iframe = document.createElement("iframe");
  iframe.style.display = "none";
  iframe.src = BASE_URL + `/turno/pdf?id=${turnoId}`;
  document.body.appendChild(iframe);

  iframe.onload = function () {
    iframe.contentWindow.print();
    setTimeout(() => iframe.remove(), 2000);
  };
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
    <p>Tiempo estimado: ${formatearTiempo(tiempoRestanteSegundos)}</p>
  `;
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
      const segundosTranscurridos = ahora - ultimaActualizacionServidor;

      if (ultimaActualizacionServidor === 0 || Math.abs(data.tiempo_estimado_segundos - (tiempoRestanteSegundos + segundosTranscurridos)) > 10) {
        tiempoRestanteSegundos = data.tiempo_estimado_segundos;
      }

      turnosPendientes = data.pendientes;
      ultimaActualizacionServidor = ahora;

      actualizarDisplay();
    })
    .catch(() => {
      const tiempoDiv = document.getElementById("tiempo-espera");
      tiempoDiv.innerHTML = "<p>Error al cargar</p>";
    });
}

// Ejecutar cuando la página termine de cargar
document.addEventListener("DOMContentLoaded", () => {
  const imprimirDiv = document.getElementById("imprimir-turno");

  if (imprimirDiv && imprimirDiv.dataset.turnoId) {
    const turnoId = imprimirDiv.dataset.turnoId;

    actualizarTiempo(turnoId);
    setInterval(() => actualizarTiempo(turnoId), 30000);

    intervaloCuentaRegresiva = setInterval(actualizarDisplay, 1000);

    imprimirTurno(turnoId);
  }
});