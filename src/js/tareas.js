(function (){

    obtenerTareas();
    let tareas = [];
    let filtradas = [];

    //Boton para mostrar el Modal de agregar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', () => {
        mostrarFormulario();
    });

    //Filtros de búsqueda
    const filtros = document.querySelectorAll('#filtros')
    filtros.forEach( radio => {
        radio.addEventListener('input', filtrarTareas);
    });

    function filtrarTareas(e){
        const filtro = e.target.value;

        if(filtro !== ''){
            filtradas = tareas.filter(tarea => tarea.estado === filtro);
        } else{
            filtradas = [];
        }
        mostrarTareas();
    }

    async function obtenerTareas(){
        try {
            const proyectoURL = obtenerProyecto();
            const url = `api/tareas?id=${proyectoURL}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            tareas = resultado.tareas;
            mostrarTareas();

        } catch (error) {
            console.log(error);
        }
    }

    function mostrarTareas(){

        limpiarTareas();
        totalPendientes();
        totalCompletas();
        const arrayTareas = filtradas.length ? filtradas : tareas;

        if(arrayTareas.length == 0){
            const contenedorTareas = document.querySelector('#listado-tareas');
            const textoNoTareas= document.createElement("LI");
            textoNoTareas.textContent = "Aún no tienes tareas..";
            textoNoTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNoTareas);
            return;
        }

        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        }

        arrayTareas.forEach(tarea => { 
            //Generamos el contenedor para cada tarea
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');

            //Un parrafo con el nombre de cada tarea
            const nombreTarea = document.createElement("P");
            nombreTarea.textContent = tarea.nombre;
            nombreTarea.ondblclick = () => {
                mostrarFormulario(true, {...tarea});
            }

            //Un div para colocar la barra de opciones de cada tarea
            const opcionesDiv = document.createElement("DIV");
            opcionesDiv.classList.add('opciones');

            //Botones del DIV
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`)
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.ondblclick = () => {
                cambiarEstadoTarea({...tarea});
            }

            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar'
            btnEliminarTarea.ondblclick = function(){
                confirmarEliminarTarea({...tarea});
            }

            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);
            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);
        });
    }

    function totalPendientes(){
        const totalPendientes = tareas.filter(tarea => tarea.estado === "0");
        const pendientesRadio = document.querySelector("#pendientes");
        if (totalPendientes.length == 0){
            pendientesRadio.disabled = true;
        } else{
            pendientesRadio.disabled = false;
        }
    }

    function totalCompletas(){
        const totalCompletas = tareas.filter(tarea=> tarea.estado === "1");
        const completasRadio = document.querySelector("#completadas");
        if (totalCompletas.length == 0){
            completasRadio.disabled = true;
        } else{
            completasRadio.disabled = false;
        }

    }

    function mostrarFormulario(editar = false, tarea ={}){
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
        <form class="formulario nueva-tarea">
        <legend>${editar ? 'Edite la tarea' : 'Añade una tarea'}</legend>
        <div class="campo">
            <label>Tarea</label>
            <input type="text" name="tarea" placeholder="${editar ? '': 'Añadir tarea al proyecto actual'}" id="tarea" value="${tarea.nombre ? tarea.nombre : ''}"/>
        </div>
        <div class="opciones">
            <input type="submit" class="submit-nueva-tarea" value="${editar ? 'Editar tarea' : 'Añadir tarea'}">
            <button type="button" class="cerrar-modal">Cancelar</button>
        </div>
        </form>
        `;
   
    setTimeout(() => {
        const formulario = document.querySelector('.formulario');
        formulario.classList.add('animar');
    }, 10);

    modal.addEventListener('click', function(e){
        e.preventDefault();
        if(e.target.classList.contains('cerrar-modal')){
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('cerrar');
            
            setTimeout(() => {
                modal.remove(); 
            }, 300);
        }

        if(e.target.classList.contains('submit-nueva-tarea')){
            const nombreTarea = document.querySelector('#tarea').value.trim();

            if(nombreTarea === ''){
                //Mostrar una alerta
                referencia = document.querySelector('.formulario legend');
                mostrarAlerta('El nombre de la tarea es obligatorio', 'error', referencia);
                return
            }
            document.querySelector('body').classList.add('loading');
            
            if(editar){
                tarea.nombre = nombreTarea;
                actualizarTarea(tarea);
            } else{
                agregarTarea(nombreTarea);
            }
        }
    });

    document.querySelector('.dashboard').appendChild(modal);
 }

    

    //Muestra un mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia){
        //Previene la creación de múltiples alertas
        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia){
            alertaPrevia.remove();
        }
        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        //Inserta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia);

        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

    //Consultar el servidor para añadir una tarea al proyecto actual
    async function agregarTarea(tarea){
        //Construir la petición
        const datos = new FormData();
        datos.append('nombre', tarea);
        datos.append('proyectoURL', obtenerProyecto());

        try {
            const url = 'http://localhost:3000/api/tarea';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            console.log(resultado);

            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));

            if(resultado.tipo === 'exito'){
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    const formulario = document.querySelector('.formulario');
                    formulario.classList.add('cerrar');
                    setTimeout(() => {
                        formulario.classList.remove('cerrar');
                        modal.remove();
                        document.querySelector('body').classList.remove('loading');
                    }, 500);
                }, 3000);
                //Agregar el objeto de tareas al global de tareas
                const tareaObj={
                    id:String(resultado.id),
                    nombre:tarea,
                    estado:"0",
                    proyectoId: resultado.proyectoId
                }

                tareas = [...tareas, tareaObj];
                mostrarTareas(); 
            }
        } catch (error) {
            console.log('error');
        }
    }

    function cambiarEstadoTarea(tarea){
        console.log(tarea);

        tarea.estado = tarea.estado === "1" ? "0" : "1";
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea){
        const {estado, id, nombre, proyectoId} = tarea;
        const datos =  new FormData()
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoURL', obtenerProyecto());

        try {
            const url = 'http://localhost:3000/api/tarea/actualizar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            
            if(resultado.respuesta.tipo === 'exito'){
                Swal.fire(
                    resultado.respuesta.mensaje,
                    resultado.respuesta.mensaje,
                    'success'
                );
            const modal = document.querySelector('.modal');
            if(modal){
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    formulario.classList.remove('cerrar');
                    modal.remove();
                    document.querySelector('body').classList.remove('loading');
                }, 500);
            }
                tareas = tareas.map(tareaMemoria => {
                    if (tareaMemoria.id === id){
                        tareaMemoria.estado = estado;
                        tareaMemoria.nombre = nombre;
                    }
                    return tareaMemoria;
                });
                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
    }

    function confirmarEliminarTarea(tarea){
        Swal.fire({
            title: '¿Eliminar tarea?',
            text: "No podrás revertir este cambio!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, eliminar'
          }).then((result) => {
            if (result.isConfirmed) {
              eliminarTarea(tarea);
            }
          })
    }

    async function eliminarTarea(tarea){
        const datos = new FormData();
        const {estado, id, nombre} = tarea;
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoURL', obtenerProyecto());
        try {
            const url = 'http://localhost:3000/api/tarea/eliminar';
            const respuesta = await fetch(url,{
                method:'POST',
                body:datos
            });
            const resultado = await respuesta.json();
            
            if(resultado.resultado){
                Swal.fire(
                    'Eliminado!',
                    resultado.mensaje,
                    'success'
                  )

                tareas = tareas.filter(tareaMemoria => tareaMemoria.id !== tarea.id);
                mostrarTareas();
            }

        } catch (error) {
            
        }
    }

    function obtenerProyecto(){
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    function limpiarTareas(){
        const listadoTareas = document.querySelector('#listado-tareas');
        
        while(listadoTareas.firstChild){
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
})();