    //====== ASEGURA NAVEGACION ENTRE ELEMENTOS DE INTERES ======
    // --- Se debe agregar la clase "pagosTabDeseado" a los elementos de inter�s
        var bNavegacionActiva=true;
		var bUp=false;//si la tecla de hacia arriba fue presionada no hace el comprotameinto default
        var aElemNavegables = [];
        $(window).load(function(){
            $(".pagosTabDeseado").each(function(i, obj) {
               aElemNavegables[i]=$(this).attr("id");
            });
            /*
            // DESPLIEGUE EN LOG DEL ORDEN DE LOS ELEMENTOS -----
                console.log("**********************************");
                for (i=0;i<aElemNavegables.length;i++) {
                    console.log(" ELEMENTO NAVEGABLE ["+i+"]: " + aElemNavegables[i]);
                }
                console.log("**********************************");
            */
            $(".pagosTabDeseado").click(function() {
                bNavegacionActiva=false;
                $(this).focus();
                bNavegacionActiva=true;
            })
        })    
        
       $(window).on( "focusout", function(event) {
		   if(!bUp)
		   {
	         // solo nos interesa monitorear el focus() cuando la pantalla de pago est� activa
             if (!bControlaNavegacion) return;
             if (!bNavegacionActiva) return;
             

             var sElementID = event.target.id;                    // el elemento de donde sale el focus()
             var iElement = aElemNavegables.indexOf(sElementID);  // la posicion del elemento en el arreglo
             var nMaxElement = aElemNavegables.length -1;         // el total de elementos en el arreglo
             var bSiguienteEsVisible=false;                       // bandera para saber si podemos navegar al siguiente elemento
             if (nMaxElement<=0) return;                          // si no hay elementos, no hacemos nada
             
             $("#" + sElementID).css("font-weight", "normal");
             

             var nBloqueo = 0;                                    // contamos iteraciones, para evitar ciclarnos...
             var iElemSiguiente = iElement;                       // apuntador al siguiente elemento
             while (!bSiguienteEsVisible) {
                 if (nBloqueo++>=20) break;

                 iElemSiguiente++;
                 if (iElemSiguiente>nMaxElement) iElemSiguiente=0;   // si el siguiente elemento llega al l�mite, vuelve a empezar

                 // si el siguiente elemento no es visible, repite con el que le sigue
                 bSiguienteEsVisible = $("#" + aElemNavegables[iElemSiguiente]).is(":visible"); 
                 if (!bSiguienteEsVisible) continue;

                 // si el siguiente elmento s� est� visible, le pone el focus
                 
                 $("#" + aElemNavegables[iElemSiguiente]).css("font-weight", "bold");
                 $("#" + aElemNavegables[iElemSiguiente]).focus();
             }
		   }
        });
    //==========================================================================

