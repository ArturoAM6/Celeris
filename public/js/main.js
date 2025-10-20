// Restaurar la pestaña activa al cargar la página
document.addEventListener('DOMContentLoaded', function() {
  const activeTab = localStorage.getItem('activeTab');
  
  if (activeTab) {
    // Si hay una pestaña guardada, abrirla
    const tabButtons = document.getElementsByClassName('tab-links');
    for (let i = 0; i < tabButtons.length; i++) {
      const buttonText = tabButtons[i].textContent.trim().toLowerCase();
      if (buttonText === activeTab.toLowerCase()) {
        tabButtons[i].click();
        return;
      }
    }
  }
  
  // Si no hay pestaña guardada, abrir la primera por defecto
  document.getElementById("defaultOpen").click();
});

// Guardar la pestaña activa antes de enviar cualquier formulario
document.addEventListener('DOMContentLoaded', function() {
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    form.addEventListener('submit', function() {
      const activeTabContent = document.querySelector('.tab-content[style*="display: block"]');
      if (activeTabContent) {
        localStorage.setItem('activeTab', activeTabContent.id);
      }
    });
  });
});

function openTab(evt, tabName) {
  // Declarar las variables
  var i, tabcontent, tablinks;

  // Obtener todos los elementos con class="tab-content" y ocultarlos
  tabcontent = document.getElementsByClassName("tab-content");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Obtener todos los elementos con class="tab-links" y quitar la clase "active"
  tablinks = document.getElementsByClassName("tab-links");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Mostrar la pestaña actual y añadir "active" en la clase del boton que abrio la pestaña
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
  
  // Guardar la pestaña activa en localStorage
  localStorage.setItem('activeTab', tabName);
}

function abrirModal(modalName) {
  document.getElementById(modalName).style.display = "block";
}

function cerrarModal(modalName) {
  document.getElementById(modalName).style.display = "none";
}

function abrirModalEditar(empleado) {
  document.getElementById("edit_id").value = empleado.id;
  document.getElementById("edit_nombre").value = empleado.nombre;
  document.getElementById("edit_apellido_paterno").value = empleado.apellido_paterno;
  document.getElementById("edit_apellido_materno").value = empleado.apellido_materno || "";
  document.getElementById("edit_password").value = empleado.password_hash;
  document.getElementById("edit_email").value = empleado.email;
  document.getElementById("edit_id_rol").value = empleado.id_rol;
  document.getElementById("edit_id_departamento").value = empleado.id_departamento;
  document.getElementById("edit_id_tipo_turno").value = empleado.id_tipo_turno;
  abrirModal("modalEditar");
}

function abrirModalAsignar(caja) {
  
  document.getElementById("asign_id").value = caja.id;
  document.getElementById("asign_numero").value = caja.numero;
  document.getElementById("asign_id_departamento").value = caja.id_departamento;
  document.getElementById("asign_id_estado").value = caja.id_estado;
  if (caja.empleado) {
    document.getElementById("asign_id_empleado").value = caja.id_empleado;
  }

  // Función que retorna una promesa cuando el fetch termina de cargar los empleados
  function cargarEmpleados(idDepartamento) {
      return fetch(`${BASE_URL}/admin/empleados/filtrar`, {
          method: 'POST',
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `id_departamento=${encodeURIComponent(idDepartamento)}`
      })
      .then(res => res.json())
      .then(data => {
          selectEmpleado.innerHTML = '';
          if (!data || data.length === 0) {
              const option = document.createElement('option');
              option.textContent = 'Sin empleados disponibles';
              option.disabled = true;
              selectEmpleado.appendChild(option);
          } else {
              data.forEach(emp => {
                  const option = document.createElement('option');
                  option.value = emp.id;
                  option.textContent = emp.nombre;
                  selectEmpleado.appendChild(option);
              });
          }
          return data;
      });
  }

  // Carga los empleados y luego asigna el empleado actual
  cargarEmpleados(caja.id_departamento).then(() => {
      if (caja.empleado) {
          selectEmpleado.value = caja.id_empleado;
      }
  });

  abrirModal("modalAsignar");
}

const selectDepartamento = document.getElementById("asign_id_departamento");
const selectEmpleado = document.getElementById("asign_id_empleado");

if (selectDepartamento && selectEmpleado) {
  selectDepartamento.addEventListener("change", () => {
    const idDepartamento = selectDepartamento.value;
    selectEmpleado.innerHTML = "<option>Cargando...</option>";

    fetch(`${BASE_URL}/admin/empleados/filtrar`, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id_departamento=${encodeURIComponent(idDepartamento)}`,
    })
      .then((res) => res.json())
      .then((data) => {
        selectEmpleado.innerHTML = "";

        if (!data || data.length === 0) {
          const option = document.createElement("option");
          option.textContent = "Sin empleados disponibles";
          option.disabled = true;
          selectEmpleado.appendChild(option);
        } else {
          data.forEach((emp) => {
            const option = document.createElement("option");
            option.value = emp.id;
            option.textContent = emp.nombre;
            selectEmpleado.appendChild(option);
          });
        }
      })
      .catch((err) => {
        console.error("Error al cargar empleados:", err);
        selectEmpleado.innerHTML = "<option>Error al cargar empleados</option>";
      });
  });
}