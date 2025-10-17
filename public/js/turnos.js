
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

function actualizarTiempoEspera() {
  fetch("tiempo-espera")
    .then((res) => res.json())
    .then((data) => {
      const contenedor = document.getElementById("tiempo-espera");
      if (!contenedor) return; // si alguien borró el div, no explotes

      if (data.promedio) {
        contenedor.textContent = data.promedio;
      } else {
        contenedor.textContent = "No disponible";
      }
    })
    .catch((err) => {
      console.error("Error obteniendo tiempo:", err);
    });
}

// Ejecutar cuando la página termine de cargar
document.addEventListener("DOMContentLoaded", () => {
  actualizarTiempoEspera(); // primera carga
  setInterval(actualizarTiempoEspera, 30000); // cada 10 segundos

  const imprimirDiv = document.getElementById("imprimir-turno");
  if (imprimirDiv && imprimirDiv.dataset.turnoId) {
    imprimirTurno(imprimirDiv.dataset.turnoId);
  }
});