const mobileMenuBtn=document.querySelector("#mobile-menu"),cerrarMenuBtn=document.querySelector("#cerrar-menu"),sidebar=document.querySelector(".sidebar");mobileMenuBtn&&mobileMenuBtn.addEventListener("click",()=>{sidebar.classList.toggle("mostrar")}),cerrarMenuBtn&&cerrarMenuBtn.addEventListener("click",()=>{sidebar.classList.add("ocultar"),setTimeout(()=>{sidebar.classList.remove("mostrar"),sidebar.classList.remove("ocultar")},1e3)});const anchoPantalla=document.body.clientWidth;window.addEventListener("resize",()=>{anchoPantalla>=768&&sidebar.classList.remove("mostrar")});