<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />    
    <title>CAJA FACIL</title>
    <link rel="stylesheet" type="text/css" media="screen and (max-width: 750px)" href="includes/pv_chico.css"/>
    <link rel="stylesheet" type="text/css" media="screen and (min-width: 750px)" href="includes/pv_grande.css"/>
    <link rel="stylesheet" type="text/css" media="screen and (max-width: 750px)" href="includes/pv_chico_.css"/>
    <link rel="stylesheet" type="text/css" media="screen and (min-width: 750px)" href="includes/pv_grande_.css"/>
    <script src="includes/preload.js"></script>
    <link href="plugins/perfect-scrollbar-0.5.9/src/perfect-scrollbar.css" rel="stylesheet"/>
    <script src="includes/jquery.min.js"></script>
    <script src="plugins/perfect-scrollbar-0.5.9/src/perfect-scrollbar.js"></script>

    <script src="includes/elementosnavegables.js"></script>
    <script>
        //-- El url y path actual, util para abrir popups sin ser bloqueados...
        var sThisURLPath = '';
        sThisURLPath += window.location.protocol + "//" + window.location.host;
        var pathArray = window.location.pathname.split('/');
        for (i = 0; i < pathArray.length - 1; i++) {
            sThisURLPath += pathArray[i];
            sThisURLPath += "/";
        }
        console.log(sThisURLPath);



        var nFKCliente = '';
        var sTipoPago = "Ticket";

        var sURLopener = '';
        var bVentanaPopUp = true;
        if (opener != null)
            sURLopener = opener.location;
        if (sURLopener == '')
            bVentanaPopUp = false;

        var windowPagos; // ventana de pagos
        var bPantallaBloqueada = false;
        var bPidePrecio = false;
        var bAyudaActiva = false;
        var bSeleccionandoTicket = false;
        var bSeleccionandoTicketCancelar = false;
        var bSeleccionandoTicketSuspendido = false;
        var bFusionarTicketSuspendido = false;
        var bTicketsSuspender = false;
        var nIDseleccionado = 0;
        var nTicketIDseleccionado = 0;
        var nTicketSuspendidoIDseleccionado = 0;
        var nTicketSeleccionado = 0;
        var nTicketSuspendidoSeleccionado = 0;
        var nProdSeleccionado = 0;
		var nRenSeleccionado = 0;
        var nProds = 0;
        var nTickets = 0;
        var nTicketsSuspendidos = 0;
        var nFKListaPrecios = '';
        var bExistenciasSuficientes = 1;
		var bPrecioSuficiente=1;
        var bVendedor = false;
		// PERMISO USUARIO:
		var  bPagoCreditoAutorizado = false;
		var bAutorizado1=false;
		var bAutorizado2=false;
		var bAutorizado3=false;
		var bAutorizado4=false;
		var bAutorizado5=false;
		var bAutorizado6=false;
		var bAutorizado7=false;
		var bAutorizado8=false;
		var bAutorizado9=false;
		var bAutorizado10=false;
		var bAutorizado11=false;
		var bAutorizado12=false;
		var bAutorizado13=false;
		var bAutorizado14=false;
		var bAutorizado15=false;
		var bAutorizado16=false;
		var bAutorizado17=false;
		var bAutorizado18=false;
		var bAutorizado19=false;																		
		var bAutorizado20=false;
		var bAutorizado21=false;
        $(window).load(function () {

            ClienteAsigna();
            $("#CapturaCodigo").focus();
            listaProductos(false);
            var aImagesIn = {

            };
            var aImagesOut = {

            };

            $(".pvFK").mouseenter(FKhandlerIn).mouseleave(FKhandlerOut);		
            function FKhandlerIn() {
                var nFK = $(this).attr("rowID");
                $("#FK" + nFK).attr("src", aImagesOut[nFK]);
            }

            function FKhandlerOut() {
                var nFK = $(this).attr("rowID");
                $("#FK" + nFK).attr("src", aImagesIn[nFK]);
            }

            $(document).delegate('.ListaRow', 'click', function () {
                $(".ListaRow").css('backgroundColor', '#FFF');
                $(this).css('backgroundColor', 'yellow');
                nIDseleccionado = $(this).attr("rowID");
                nProdSeleccionado = $(this).attr("rowNum");
				nRenSeleccionado = $(this).attr("row2");
                nProdSeleccionado = eval(nProdSeleccionado) + 1;
                seleccionaProd(nProdSeleccionado);
            })
			/*//Seleccionar renglon si dan clic en e campo de cantidad
			function seleccionarrenglon(){
			  $(".ListaRow").css('backgroundColor', '#FFF');
                $(this).css('backgroundColor', 'yellow');
                nIDseleccionado = $(this).attr("rowID");
                nProdSeleccionado = $(this).attr("rowNum");
                nProdSeleccionado = eval(nProdSeleccionado) + 1;
                seleccionaProd(nProdSeleccionado);	
				
			}*/
            $("#PidePrecioCancelar").click(function () {
                PidePrecioCancelar();
            })

            $("#PidePrecioOK").click(function () {
                ProductoGuardaPrecio();
            })
            // SUSPENDER:
            $("#TicketsSuspenderCancelar").click(function () {
                TicketsSuspenderCancelar();
            })

            $("#TicketsSuspenderOK").click(function () {
                TicketsSuspenderGuardar();
            })

            // COMISION:
            $("#VendedorCancelar").click(function () {
                VendedorCancelar();
            })

            $("#VendedorOK").click(function () {
                VendedorGuardar();
            })

            $(window).bind('keydown', function (event) {
                // Si se presiona tecla ESC
                if (event.keyCode == 27) {
                    if (bSeleccionandoTicket) {
                        ListaTicketsCerrar();
                    } else if (bSeleccionandoTicketCancelar) {
                        ListaTicketsCancelarCerrar();
                    } else if (bSeleccionandoTicketSuspendido) {
                        ListaTicketsSuspendidosCerrar();
                    } else if (bAyudaActiva) {
                        AyudaCerrar();
                    } else if (bPidePrecio) {
                        PidePrecioCancelar();
                    } else if (bTicketsSuspender) {
                        TicketsSuspenderCancelar();
                    } else if (bVendedor) {
                        VendedorCancelar();
                    } else if (bAutorizando) {
                       PideLoginCerrar(false);
                    } else if (bPagando) {
                        // la logica se maneja en pv_pidepago.php
                    }
                    $("#CapturaCodigo").focus();
                }

                if (event.keyCode == 13) {
                    // en popup de pago, si se presiona ENTER, se realiza el pago
                    if (bPagando) {
                        // la logica se maneja en pv_pidepago.php
                    } else if (bSeleccionandoTicketSuspendido) {
						var Bloqueado= $("#Bloqueado").val();
					   if(Bloqueado!=1)
	                        ListaTicketsSuspendidosReanudar();
		           }else if (bAutorizando) 
                       PideLoginAceptar();
                }
                // si se presiona la barra espaciadora y el focus no está en un textbox, pasa el focus al input de CODIGO
                if (event.keyCode == 32) {
                    if (!$(document.activeElement).is("input")) {
                        event.preventDefault();
                        $("#CapturaCodigo").focus();
                    }
                }

                // Detecta teclas con CTRL...   
                if (event.ctrlKey || event.metaKey) {

                }
            });


            document.onkeypress = function (evt) {
                // solo hace caso a teclas si no se ha bloqueado la pantalla.
                if (bPantallaBloqueada)
                    return;

                evt = evt || window.event;
                var charCode = evt.which || evt.keyCode;
                var charStr = String.fromCharCode(charCode);
                if ('' == '1') {
                    if (charStr == "-") {
                        event.preventDefault();
                        $.post("pv_ajax.php",
                                {
                                    TicketID: '',
                                    fkCodigo: nIDseleccionado,
                                    Entidad: "ProductoDisminuye"
                                },
                        function (data, status) {
                            var json = $.parseJSON(data);
                            if (json.Error) {
                                alert("error: " + json.Error);
                            } else {
                                listaProductos(true);
                            }
                        })
                    }
                }

                if ('' == '1') {
                    if (charStr == "+") {
                        event.preventDefault();
                        $.post("pv_ajax.php",
                                {
                                    TicketID: '',
                                    fkCodigo: nIDseleccionado,
                                    Entidad: "ProductoAumenta"
                                },
                        function (data, status) {
                            //console.log(data);
							//alert(data);
                            var json = $.parseJSON(data);
							// VALIDA PRECIO
							if (json.Error=="Valida" ) {
								var mayoreo1=$('#mayoreo1').val();
								var mayoreo="";
								var mayoreo2="";
								if( mayoreo1.trim()=="")
								{
									var mayoreo=confirm('�Desea dar precio de mayoreo?');
									if(mayoreo) mayoreo2=1;
									else mayoreo2=0;
								}else{
								  mayoreo2=mayoreo1;	
								}
								
								if(mayoreo2==0)//si no permitio precio mayoreo, regresa a el precio de lista 1
								{
									$('#mayoreo1').val('0');
									$.post("pv_ajax.php",
								{
									  TicketID: '',
           							  fkCodigo: nIDseleccionado,
							          Entidad: "CambiarPrecio"
                                },
		                        function (data, status) { 
								  var json = $.parseJSON(data);
								  alert(json.Error);
								  });
								}
								else{//si permitio precio mayoreo, deja el precio de la lista correspondiente
									$('#mayoreo1').val('1');
								}
								
							listaProductos(true);
							}else if (json.Error) {
                                alert("error: " + json.Error);
                            } else {
                                listaProductos(true);
                            }
                        })
                    }
                }
            };

            // captura teclas de funcion
            $(document).keydown(function (event) {
                // ============== TECLAS ARRIBA Y ABAJO PARA NAVEGAR LISTAS ===================
                // con pantalla de ayuda, las teclas arriba y abajo no se usan
                // UP KEY
                if (event.keyCode == "38") {
                    event.preventDefault();
                    if (bSeleccionandoTicket) {
                        seleccionaTicketMueve(-1);
                    } else if (bSeleccionandoTicketCancelar) {
                        seleccionaTicketCancelarMueve(-1);
                    } else if (bSeleccionandoTicketSuspendido) {
                        seleccionaTicketSuspendidoMueve(-1);
                    } else if (bAyudaActiva) {
                        //console.log('...');
                    } else if (bPagando) {
                        //console.log('...');
                    } else if (bPidePrecio) {
                        //console.log('...');
                    } else {
                        seleccionaProdMueve(-1);
                    }
                }
                // DOWN KEY
                if (event.keyCode == "40") {
                    event.preventDefault();
                    if (bSeleccionandoTicket) {
                        seleccionaTicketMueve(+1);
                    } else if (bSeleccionandoTicketCancelar) {
                        seleccionaTicketCancelarMueve(+1);
                    } else if (bSeleccionandoTicketSuspendido) {
                        seleccionaTicketSuspendidoMueve(+1);
                    } else if (bAyudaActiva) {
                        //console.log('...');
                    } else if (bPagando) {
                        //console.log('...');
                    } else if (bPidePrecio) {
                        //console.log('...');
                    } else {
                        seleccionaProdMueve(+1);
                    }
                }

                // ============== TECLAS DE FUNCION QUE SE USAN POR LOS BOTONES ===================
                // solo hace caso a teclas si no se ha bloqueado la pantalla....
                // si esta bloqueda con la ayuda abierta, acepta las teclas.
                if ((bPantallaBloqueada) && (!bAyudaActiva))
                    return;
                // Previene la funcion nativa de la tecla predefinida por el navegador (ej: F5 para refrescar)

            });

            $(document).delegate('.TicketRow', 'click', function () {
                $(".TicketRow").css('backgroundColor', '#EFEFFF');
                $(this).css('backgroundColor', 'yellow');
                nTicketSeleccionado = $(this).attr("rowNum");
                nTicketIDseleccionado = $(this).attr("rowID");
            })

            $(document).delegate('.TicketSuspendidoRow', 'click', function () {
                $(".TicketSuspendidoRow").css('backgroundColor', '#EFEFFF');
                $(this).css('backgroundColor', 'yellow');
                nTicketSuspendidoSeleccionado = $(this).attr("rowNum");
                nTicketSuspendidoIDseleccionado = $(this).attr("rowID");
            })
        });

          function BloqueaPantalla(pBloqueo) {
            bPantallaBloqueada = pBloqueo;
             if (pBloqueo) {
                $("#ProductoLectorTable").hide();
                $("#BlockFullScreen").show();		
                $("#Bloqueado").val(11);				
            } else {
                $("#ProductoLectorTable").show();
                $("#BlockFullScreen").hide();
			    $("#Bloqueado").val(0);
            }
        }
		
        function PidePrecioCancelar() {
            $("#PidePrecioValor").val('');
            $("#PidePrecioDiv").hide();
            BloqueaPantalla(false);
            bPidePrecio = false;
            fCancelaRenglon();
        }
        // SUSPENDER
        function TicketsSuspenderCancelar() {
            $("#Comentario").val('');
            $("#TicketsSuspenderDiv").hide();
            BloqueaPantalla(false);
            bTicketsSuspender = false;
        }
        // COMISION
        function VendedorCancelar() {
            $("#Vendedor").val('');
            $("#VendedorDiv").hide();
            BloqueaPantalla(false);
            bVendedor = false;
        }

        function PideLoginAceptar() {		
		    var sLogin = $("#AutorizacionLogin").val();
            var sPassword = $("#AutorizacionPassword").val();
            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        data: $("#PagoForm").serialize(),
                        login: sLogin,
                        password: sPassword,
                        FKAtributo: nfkAtributo,
                        Entidad: "CreditoLoginVerifica"
                    },
            function (data, status) {
				//alert(data);
                var json = $.parseJSON(data);
                if (json.Error == "Login") {
                    alert('Los datos de acceso son incorrectos');
                    PideLoginCerrar(false);
                } else if (json.Error == "Permiso") {
					if(nfkAtributo=="1120")
	                    alert('El usuario no tiene permiso para autorizar cr\u00e9ditos.');
					else// PERMISO USUARIO:
					    alert('El usuario no tiene permiso para autorizar esta opci\u00f3n.');
                    PideLoginCerrar(false);
                } else {		
					if(nfkAtributo=="1104")
					{
						bAutorizado1=true;
						fRetirar();
					}
					else if(nfkAtributo=="1105")
					{
						bAutorizado2=true;
	                   fDepositar();
					}
					else if(nfkAtributo=="1106")   
					{
					   bAutorizado3=true;
					   fCorteX();
					}
					else if(nfkAtributo=="1107") 
					{
						bAutorizado4=true; 
					    fCorteZ();
					}
					else if(nfkAtributo=="1108")
					{
						bAutorizado5=true; 
						fArqueo();
					}
					else if(nfkAtributo=="1113")
					{
						bAutorizado6=true;
	                   fConfigurarCaja();
					}
					else if(nfkAtributo=="1117")
					{
						bAutorizado7=true;
					    fCancelarTicket();
					}
					else if(nfkAtributo=="1118")
					{
						bAutorizado8=true;
						fFacturarTicket();
					}
					else if(nfkAtributo=="1119")
					{
						bAutorizado9=true;
						fPrecuenta();
					}
					else if(nfkAtributo=="1120")
					{
						bPagoCreditoAutorizado = true;
	                   PidePagoRealiza(json.Autoriza);					   
					}
					else if(nfkAtributo=="1121")
					{
						bAutorizado10=true;
					   fCancelaRenglon();
					}
					else if(nfkAtributo=="1122")   
					{
						bAutorizado11=true;
					   fDevoluciones();
					}
					else if(nfkAtributo=="1123")   
					{
						bAutorizado12=true;
					    fPagarDevolucion();
					}
					else if(nfkAtributo=="1124")
					{bAutorizado13=true;
						fCancelarDevolucion();
					}
					else if(nfkAtributo=="1125")
					{bAutorizado14=true;
						fBuscarCliente();					
					}
					else if(nfkAtributo=="1126")
					{bAutorizado15=true;
						ClienteAsigna();
					}
					else if(nfkAtributo=="1127")
					{bAutorizado16=true;
						fAbonarCxC();
					}
					else if(nfkAtributo=="1128")
					{bAutorizado17=true;
						fPagos();
					}
					else if(nfkAtributo=="1129")	
					{bAutorizado18=true;
						fCancelaVenta(false);
					}
					else if(nfkAtributo=="1220")	
					{bAutorizado19=true;
						cambiarPrecio1();
					}else if(nfkAtributo=="1221")
					{
						bAutorizado20=true;
						ListaTicketsSuspendidosImprimirCopia();
					}else if(nfkAtributo=="1222")
					{
						bAutorizado21=true;
						fCancelaTicketE();
					}
					PideLoginCerrar(true);				
                }
            })
        };

        function ListaTicketsSuspendidosListaDeTickets() {
            var impresionWindow = window.open('', '_blank', 'width=600,height=500');
            impresionWindow.location.href = "reporte_suspendidos.php";
        }

        function ListaTicketsSuspendidosImprimirCopia() {
			// PERMISOS USUARIO
			nfkAtributo="1221";						
		            		
			 bAutorizado20= false;
			Bloqueado= $("#Bloqueado").val(0);
            if (nTicketSuspendidoIDseleccionado == 0) {
                alert('Debe seleccionar un ticket suspendido');
                return;
            }
            // PERMISOS CAJA: IMPRIME
            var Imprime = $('#Imprime').val();
            if (Imprime != 1)
            {
                alert('Esta caja no tiene activada la Impresi\u00f3n.');
                return;
            }
	         $.post("pv_imprime_ticket.php",
                                {
                                    TicketID:nTicketSuspendidoIDseleccionado,
                                    Reimpresion: 0, 
									Suspendido: 1,
									Folio:nTicketSuspendidoIDseleccionado, 
                                },
                        function (data, status) {
                            console.log(data);
                            var json = $.parseJSON(data);							
                            if (json.Error) {
                                alert("error: " + json.Error);
                            } else {
							var bImagen=json.Imagen;
							var bPublicidad=json.Publicidad;
							var bbarcode=json.bbarcode;
							var Folio=json.Folio;
                       		//toma las copias a imprimir
								var copias=parseInt(json.copias)+1;
								var i=0;
								var impresionWindow ="";
								//toma la impresora elegida
								var impresora1="";
								$.post("styles/get_printer.php",{},
								function (result) {
									//alert(result);
									impresora1=result;
									if(impresora1=="PDF")
									{
										impresionWindow = window.open('', '_blank', 'width=600,height=500');
										impresionWindow.location.href = sThisURLPath + "impresiones/ticket_" + nTicketSuspendidoIDseleccionado + ".pdf";
									}else{
									for (i = 0; i < copias; i++) { 									
									if(bbarcode=="1")
										var BarCode=Folio;
									else
									   	var BarCode="";	
									if(bImagen=="1")
										var imagen1=Imagen;
									else
									   	var imagen1="";	
									if(bPublicidad=="1")
										var publicidad1=Publicidad;
									else
									   	var publicidad1="";	
										
										console.log("Llamando impresora");
										window.postMessage({type: "PrintTicket", TICKET_URL:sThisURLPath+"impresiones/ticket_" + nTicketSuspendidoIDseleccionado + ".txt", PrinterLogicalName: impresora1,OpenDrawer: 0,BarCode:BarCode, CutPaper:"1",HeaderImg:imagen1,PublicityImg:publicidad1, TempFolder:"c:/cajafacil/impresiones"}, "*");
									
									}//for imprimir copias
										}//if pdf
								})//get printer							
                            }//else error
                        })//imprime ticket
        }
        function ListaTicketsSuspendidosReanudar() {
            if (nTicketSuspendidoIDseleccionado == 0) {
                alert('Debe seleccionar un ticket suspendido');
                return;
            }
			var Precios1=0;
			var reanudar= $('#reanudar').val();
			//si al reanudar tiene confirmar
			if(reanudar==3)
			{
		        if(confirm('Desea actualizar los precios (Se pondrian los precios de lista de hoy, quitando precios especiales capturados)?'))
					Precios1=1;
			}else if(reanudar==2)
				Precios1=1;
			$.post("pv_ajax.php",
                    {
                        TicketID: '',
                        TicketReanudar: nTicketSuspendidoIDseleccionado,
                        Fusionar: bFusionarTicketSuspendido,
						Precios1: Precios1,
                        Entidad: "TicketSuspendidoReanuda"
                    },
            function (data, status) {
				//alert(data);
                var json = $.parseJSON(data);
                if (json.Error) 
                    alert("error: " + json.Error);
                else {					
                    ListaTicketsSuspendidosCerrar();	
					location.reload();					
                }
            })
        }
      
        function ListaTicketsCerrar() {
            $("#TicketsDiv").hide();
            bSeleccionandoTicket = false;
            BloqueaPantalla(false);
        }
        // CANCELAR
        function ListaTicketsCancelarCerrar() {
            $("#TicketsCancelarDiv").hide();
            bSeleccionandoTicketCancelar = false;
            BloqueaPantalla(false);
        }
        function ListaTicketsSuspendidosCerrar() {
            $("#TicketsSuspendidosDiv").hide();
            bSeleccionandoTicketSuspendido = false;
            BloqueaPantalla(false);
        }

        function AyudaCerrar() {
            $("#AyudaDiv").hide();
            bAyudaActiva = false;
            BloqueaPantalla(false);
        }

        function PideLoginCerrar(bAutorizado) {
            bPagoCreditoAutorizado = bAutorizado;
            $("#PideLoginDiv").hide();

            // limpia campos previamente llenados
            $("#AutorizacionLogin").val("");
            $("#AutorizacionPassword").val("");
            bAutorizando = false;
        };

        function ProductoGuardaPrecio() {
            var nPrecioUnitario = $("#PidePrecioValor").val();
            if (nPrecioUnitario == 0) {
                alert("El precio unitario debe ser mayor que cero.");
                $("#CapturaCodigo").focus();
                return;
            }

            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        fkCodigo: nIDseleccionado,
                        PrecioUnitario: nPrecioUnitario,
                        Entidad: "ProductoGuardaPrecio"
                    },
            function (data, status) {
                var json = $.parseJSON(data);
                if (json.Error) {
                    alert("error: " + json.Error);
                } else {
                    //console.log(json.Debug);
                    $("#PidePrecioValor").val('');
                    $("#PidePrecioDiv").hide();
                    BloqueaPantalla(false);
                    bPidePrecio = false;
                    listaProductos(false);
                    $("#CapturaCodigo").focus();
                }
            })
        }
        //SUSPENDER
        function TicketsSuspenderGuardar() {
            var sComentario = $("#Comentario").val();
            if (sComentario == "") {
                alert("El texto no debe estar en blanco.");
                $("#CapturaCodigo").focus();
                return;
            }
            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        Comentario: sComentario,
                        Entidad: "TicketsSuspenderTexto"
                    },
            function (data, status) {
                //console.log(data);
                var json = $.parseJSON(data);
                if (json.Error) 
                    alert("error: " + json.Error);
                else {
                    $("#TicketsSuspenderDiv").hide();
                    BloqueaPantalla(false);
                    bTicketsSuspender = false;
                    listaProductos(false);
                    $("#CapturaCodigo").focus();
                    location.reload();
                }
            })
        }
        function VendedorGuardar() {
            var sVendedor = $("#Vendedor").val();
            if (sVendedor == "") {
                alert("El Vendedor no debe estar en blanco.");
                $("#CapturaCodigo").focus();
                return;
            } else
            {
                $('#Vendedor1').val(sVendedor);
                $("#VendedorDiv").hide();
                BloqueaPantalla(false);
                bVendedor = false;
                listaProductos(false);
                $("#CapturaCodigo").focus();
            }
        }
// CAMBIAR CANTIDAD
function cambiarcantidad(nRenSeleccionado1){
	var Cantidad=$('#Cantidad_'+nRenSeleccionado1).val();
	  $.post("pv_ajax.php",
       {
           TicketID: '',
           nRenSeleccionado: nRenSeleccionado1,
		   Cantidad: Cantidad,
           Entidad: "ProductoCambia"
        },
        function (data, status) {
	   var json = $.parseJSON(data);
		 // VALIDA PRECIO
		if (json.Error=="Valida" ) {
			var mayoreo1=$('#mayoreo1').val();
			var mayoreo="";
			var mayoreo2="";
			if( mayoreo1.trim()=="")
			{
				var mayoreo=confirm('�Desea dar precio de mayoreo?');
				if(mayoreo) mayoreo2=1;
					else mayoreo2=0;
				}else{
					mayoreo2=mayoreo1;	
				}
								
				if(mayoreo2==0)//si no permitio precio mayoreo, regresa a el precio de lista 1
				{
					$('#mayoreo1').val('0');
					$.post("pv_ajax.php",
					{
						  TicketID: '',
           				  fkCodigo: nIDseleccionado,
					      Entidad: "CambiarPrecio"
                    },
		            function (data, status) { 
					  var json = $.parseJSON(data);
					  alert(json.Error);
					});
					}
					else{//si permitio precio mayoreo, deja el precio de la lista correspondiente
						$('#mayoreo1').val('1');
					}
					listaProductos(true);
				}else if (json.Error) {
		           alert("error: " + json.Error);
        	} else {
           	listaProductos(true);
        	}
      });
	}
	//cambiar precio manualmente
		function cambiarPrecio1(fkCodigo){
			 // PERMISOS USUARIO
			nfkAtributo="1220";	
	
			}
		function cambiarPrecio2(fkCodigo){
	         var Precio=$('#Precio2_'+nIDseleccionado).val();
			 $.post("pv_ajax.php",
       		{	
        	    TicketID: '',
                fkCodigo: nIDseleccionado,
                PrecioUnitario: Precio,
                Entidad: "ProductoGuardaPrecio"
            },
            function (data, status) {
			console.log(data);
                var json = $.parseJSON(data);
                if (json.Error) {
                    alert("error: " + json.Error);             
		        } else {
        	  	listaProductos(true);
        	}
      		});
			}
        function ClienteAsigna() {     
		  nFKCliente = $("#FKCliente").val();      
			crestringido=$("#crestringido").val();			
            if (nFKCliente == '')
                return;
				
			// PERMISO USUARIO:	
			if(crestringido=="1")
			{
			nfkAtributo="1126";						
		            
			}			
			bAutorizado15= false;
			  nFKCliente = $("#FKCliente").val();
	       $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        FKCliente: nFKCliente,
                        Entidad: "ClienteAsigna"
                    },
            function (data, status) {
			//alert(data);
                var json = $.parseJSON(data);
                if (json.Error) {
                    alert("error: " + json.Error);
                } else {
                    $("#ClienteCreditoDisponible").val(json.CreditoDisponible);
                    $("#ClienteDiasPlazo").val(json.DiasPlazo);
                    $("#ClienteNombre").html(json.Nombre);
                    nFKListaPrecios = json.fkListaPrecios;
					//cambia nombre de promocion si la hay
					 $("#PromocionNombre").html(json.PromocionNombre);				
                    listaProductos(false);
                }
            })
        }

        function ProductoObtenCodigo() {
            var nPrCodigoID = $("#FKProducto").val();
            if (nPrCodigoID == '')
                return;

            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        ProdCodigoID: nPrCodigoID,
                        Entidad: "ProductoObtenPorCodigo"
                    },
            function (data, status) {
                var json = $.parseJSON(data);
                if (json.Error) {
                    alert("error: " + json.Error);
                } else {
                    $("#CapturaCodigo").val(json.NombreCorto);
                    $("#CapturaCantidad").focus();
                }
            })
        }

        function agregaProducto() {
	       var sCapturaCodigo = $("#CapturaCodigo").val();
            var nCodigoBarraID = $("#FKProducto").val();
	        var nCapturaCantidad = $("#CapturaCantidad").val();
            nCapturaCantidad = nCapturaCantidad || 0;
            $("#CapturaCodigo").val('');            
            $("#CapturaCantidad").val(1);
			// REGRESA LA PREGUNTA DE VALIDAR MAYOREO A VACIO
			$('#mayoreo1').val('');
            if (parseFloat(nCapturaCantidad) < 0) {
                /*alert('Debe especificar una cantidad positiva de producto.');
                return;*/
				nCapturaCantidad=nCapturaCantidad*-1;
            }else  if (parseFloat(nCapturaCantidad) ==0) {
				nCapturaCantidad=1;
            }
			
		  $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        Codigo: escape(sCapturaCodigo),
                        CodigoBarraID: nCodigoBarraID,
                        FKListaPrecios: nFKListaPrecios,
                        Cantidad: nCapturaCantidad,
                        Entidad: "ProductoAgrega"
                    },
            function (data, status) {
	            var json = $.parseJSON(data);
				// VALIDA PRECIO
				//alert(json.Debug);
				if (json.Error=="Valida" ) {
					var mayoreo1=$('#mayoreo1').val();
					var mayoreo="";
					var mayoreo2="";
					if( mayoreo1.trim()=="")
					{
						var mayoreo=confirm('�Desea dar precio de mayoreo?');
						if(mayoreo) mayoreo2=1;
						else mayoreo2=0;
						}else{
						  mayoreo2=mayoreo1;	
						}						
							fkCodigo=json.fkCodigo;
						if(mayoreo2==0)//si no permitio precio mayoreo, regresa a el precio de lista 1
						{
							
							$('#mayoreo1').val('0');
							$.post("pv_ajax.php",
							{
								TicketID: '',
	           					fkCodigo: fkCodigo,
							    Entidad: "CambiarPrecio"
        	                },
		    	            function (data, status) { 
								  var json = $.parseJSON(data);
							      alert(json.Error);
							});
						}
						else{//si permitio precio mayoreo, deja el precio de la lista correspondiente
							$('#mayoreo1').val('1');
						}
						//cambia nombre de promocion si la hay
						 $("#PromocionNombre").html(json.PromocionNombre);
						listaProductos(true);
				}else if (json.Error) {
	                    alert("error: " + json.Error);
    	        } else {
					 if (json.Warning.trim()!="") {// AGREGUE WARNING, QUE AVISE PERO QUE CONTINUE, EL ERROR INTERRUMPE
	                    alert("Aviso: " + json.Warning);
    	            } 
					$("#FKProducto").val('');
                    // si el producto no tiene precio, lo solicita
                    if (json.PidePrecio == 1) {
                        pidePrecio();
                    }
                    // COMISION: si es el primer producto y esta en forzar seleccion de vendedor para comisiones, lo pide
                    if ($('#BehaveVendor').val() == "Forzar Seleccion" && $('#Vendedor1').val() == "")
                    {
                        bVendedor = true;
                        BloqueaPantalla(true);
                        $("#VendedorDiv").show();
                        $("#Vendedor").focus();
                    }
					//cambia nombre de promocion
					$("#PromocionNombre").html(json.PromocionNombre);

                    listaProductos(false);
                    if (!bPidePrecio)
                        $("#CapturaCodigo").focus();
                }
            })
            if (!bPidePrecio)
                $("#CapturaCodigo").focus();
        }

        function listaProductos(bMantenSeleccion) {
            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        bBloqOnExistZero: '',
                        sBloqCosto: '',
                        Entidad: "listaProductos"
                    },
            function (data, status) {
		         //console.log(data);
                var json = $.parseJSON(data);
                if (json.Error) {
                    alert("error: " + json.Error);
                } else {
                    bExistenciasSuficientes = json.ExistenciasSuficientes;
					bPrecioSuficiente = json.PrecioSuficiente;
                    $("#TablaListaProductos > tbody").html(json.Lista);
                    $("#articulosTD").html(json.Articulos);
                    $("#subtotalTD").html("$" + json.Subtotal);
                    $("#ivaTD").html("$" + json.IVA);
                    $("#totalTD").html("<B>$" + json.Total + "</B>");
                    $("#TicketTotal").val(json.Total);

                    if (!bMantenSeleccion)
                        nProdSeleccionado = json.LineasProductos;
                    nProds = json.LineasProductos;
                    seleccionaProd(nProdSeleccionado);
                    ActualizaBotones();
                }
            })
        }

        function seleccionaTicketMueve(nSentido) {
            nTicketSeleccionado = eval(parseInt(nSentido) + parseInt(nTicketSeleccionado));
            if (nTicketSeleccionado > nTickets)
                nTicketSeleccionado = nTickets;
            if (nTicketSeleccionado < 1)
                nTicketSeleccionado = 1;
            seleccionaTicket(nTicketSeleccionado);
        }
        function seleccionaTicketCancelarMueve(nSentido) {
            nTicketSeleccionado = eval(parseInt(nSentido) + parseInt(nTicketSeleccionado));
            if (nTicketSeleccionado > nTickets)
                nTicketSeleccionado = nTickets;
            if (nTicketSeleccionado < 1)
                nTicketSeleccionado = 1;
            seleccionaTicketCancelar(nTicketSeleccionado);
        }
        function seleccionaTicketSuspendidoMueve(nSentido) {
            nTicketSuspendidoSeleccionado = eval(parseInt(nSentido) + parseInt(nTicketSuspendidoSeleccionado));
            if (nTicketSuspendidoSeleccionado > nTicketsSuspendidos)
                nTicketSuspendidoSeleccionado = nTicketsSuspendidos;
            if (nTicketSuspendidoSeleccionado < 1)
                nTicketSuspendidoSeleccionado = 1;
            seleccionaTicketSuspendido(nTicketSuspendidoSeleccionado);
        }

        function seleccionaProdMueve(nSentido) {
            nProdSeleccionado = eval(nSentido + nProdSeleccionado);
            if (nProdSeleccionado > nProds)
                nProdSeleccionado = nProds;
            if (nProdSeleccionado < 1)
                nProdSeleccionado = 1;
            seleccionaProd(nProdSeleccionado)
        }

        function seleccionaTicket(nTicketID) {
            $(".TicketRow").css('backgroundColor', '#EFEFFF');
            $("#TicketLista_" + nTicketID).css('backgroundColor', 'yellow');
            nTicketIDseleccionado = $("#TicketLista_" + nTicketID).attr("rowID");
        }
        function seleccionaTicketCancelar(nTicketID) {
            $(".TicketRow").css('backgroundColor', '#EFEFFF');
            $("#TicketLista_" + nTicketID).css('backgroundColor', 'yellow');
            nTicketIDseleccionado = $("#TicketLista_" + nTicketID).attr("rowID");
        }
        function seleccionaTicketSuspendido(nTicketSuspendidoID) {
            $(".TicketSuspendidoRow").css('backgroundColor', '#EFEFFF');
            $("#TicketSuspendidoLista_" + nTicketSuspendidoID).css('backgroundColor', 'yellow');
            nTicketSuspendidoIDseleccionado = $("#TicketSuspendidoLista_" + nTicketSuspendidoID).attr("rowID");
        }

        function seleccionaProd(nProdID) {
            nProdID = eval(nProdID - 1);
            $(".ListaRow").css('backgroundColor', '#FFF');
            $(".ListaRow td").css('border', '0px');
            $("#ProdLista_" + nProdID).css('backgroundColor', 'yellow');
            $("#ProdLista_" + nProdID + " td").css('border-top', '5px solid yellow');
            $("#ProdLista_" + nProdID + " td").css('border-bottom', '5px solid yellow');
            nIDseleccionado = $("#ProdLista_" + nProdID).attr("rowID");
			nRenSeleccionado =  $("#ProdLista_" + nProdID).attr("row2");
            //obtiene foto
            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        fkCodigo: nIDseleccionado,
                        Entidad: "ProductoObtenFoto"
                    },
            function (data, status) {
                var json = $.parseJSON(data);
                if (json.Error) {
                    alert("error: " + json.Error);
                } else {
                    $("#producto_imagen").attr("src", json.archivo);
                }
            })
        }

        function ActualizaBotones() {
            // Activa o Desactiva botones que dependen de que haya productos:
            ActualizaBtn_FUNCTIONKEY("fPrecuenta");
            ActualizaBtn_FUNCTIONKEY("fPagos");
            ActualizaBtn_FUNCTIONKEY("fCancelaVenta");
            ActualizaBtn_FUNCTIONKEY("fCancelaRenglon");
            ActualizaBtn_FUNCTIONKEY("fSuspendeVenta");
        }

        function ActualizaBtn_FUNCTIONKEY(pActionName) {

            // Busca el boton en el menu de accesso rápido
            $('.pvFK').each(function (i, obj) {
                if ($(this).attr("onClick") == "javascript:" + pActionName + "();") {
                    if (nProds > 0) {
                        $(this).addClass("pv_functionkey");
                        $(this).removeClass("pv_functionkey_apagado");
                    } else {
                        $(this).addClass("pv_functionkey_apagado");
                        $(this).removeClass("pv_functionkey");
                    }

                }
            })

            // Busca el boton en el menu principal de ayuda
            $('.pvAyudaFK').each(function (i, obj) {
                if ($(this).attr("onClick") == "javascript:" + pActionName + "();") {
                    if (nProds > 0) {
                        $(this).addClass("pv_functionkey");
                        $(this).removeClass("pv_functionkey_apagado");
                    } else {
                        $(this).addClass("pv_functionkey_apagado");
                        $(this).removeClass("pv_functionkey");
                    }

                }
            })
        }

        function pidePrecio() {
            bPidePrecio = true;
            BloqueaPantalla(true);
            $("#PidePrecioDiv").show();
            $("#PidePrecioValor").focus();
        }

        function fArqueo() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
             window.open('arqueom.php?fkCaja=&Nombre=&A=1', 'arqueo', 'width=450,height=285,toolbar=no,scrollbar=no');
        }

        function fAbonarCxC() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1127";						
		            		
			 bAutorizado16= false;
            var nHeight = screen.height;// - 150;
            if (nHeight > 660)
                nHeight = 660;

            window.open('pv_cpc.php?A=1', 'abonarcpc', 'width=700,height=' + nHeight + ',toolbar=no,scrollbar=no');
        }

        function fDevoluciones() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1122";						
		            		
			 bAutorizado11=false;
            window.open('devoluciones.php?A=1', 'devoluciones', 'width=465,height=430,toolbar=no,scrollbar=no');
        }

        function fDepositar() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1105";						
		            		
			 bAutorizado2=false;
            window.open('depositar.php?fkCaja=<?= $nFKCaja ?>&Nombre=<?= $_SESSION["Caja"] ?>&A=1', 'retirar', 'width=450,height=285,toolbar=no,scrollbar=no');
        }

        function fConfigurarCaja() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1113";						
		            		
			bAutorizado6= false;
            window.open('cajasyperifericos.php?A=1', 'configurarcaja', 'width=800,height=600,toolbar=no,scrollbar=no');
        }

        function fCancelarDevolucion() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1124";						
		            		
			  bAutorizado13= false;
            window.open('cancelardevolucion.php?A=1', 'devoluciones', 'width=465,height=430,toolbar=no,scrollbar=no');
        }

		function fCancelaTicketE() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1222";						
		            		
			  bAutorizado21= false;
             window.open('cancelatickete.php?fkSucursal=A=1', 'Cancelar', 'width=465,height=430,toolbar=no,scrollbar=no');
        }
		
        function fRetirar() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1104";						
	            		
			bAutorizado1=false;
            window.open('retirar.php?fkCaja=Nombre=A=1', 'retirar', 'width=450,height=285,toolbar=no,scrollbar=no');
        }

        function fBonificaciones() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();

            window.open('bonificaciones.php?fkCaja', 'retirar', 'width=450,height=285,toolbar=no,scrollbar=no');

        }

        function fCorteX() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1106";						
		            		
			 bAutorizado3= false;
            window.open('cortex.php?A=1', 'cortex', 'width=750,height=500,toolbar=no');
        }

        function fCambiarCajero() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();

            var win = window.open('cambiarcajero.php', 'cajero', 'width=600,height=575,toolbar=no');
        }


        function fBascula() {
		     if (bPantallaBloqueada)
                return;
            AyudaCerrar();
            console.log("Llamando a bascula al puerto: ");
            window.postMessage({type: "Scale", Port: "", "Braud":"", "StopBits":"","Parity":"","FlowControl":"","DataBits":"" , "DataAssci":"", "Automatico":"true"}, "*");
        }

        function fCobranzaEnRuta() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
            window.open('pv_cobranzaenruta.php', 'cobranzaenruta', 'width=750,height=500,toolbar=no');
        }

        function fCorteZ() {			
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1107";						
		            		
			bAutorizado4=false;
            window.open('cortez.php?A=1', 'cortez', 'width=750,height=500,toolbar=no');
        }

        function fReimprimirTicket() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();            
			window.open('pv_tickets_reimprimir.php?A=1', 'Cancelar Tickets', 'width=465,height=430,toolbar=no,scrollbar=no');
        }

        function fCotizar() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();

            var nFKCliente = parseFloat($("#FKCliente").val());
            nFKCliente = nFKCliente || 0;

            var win = window.open('cotizar.php?FKCliente=' + nFKCliente + '&fkTicket=', 'cotizar', 'width=400,height=350,toolbar=no');
            var pollTimer = window.setInterval(function () {
                if (win.closed !== false) { // !== is required for compatibility with Opera
                    window.clearInterval(pollTimer);
                    ClienteAsigna();
                }
            }, 300);
        }

        function fPagarDevolucion() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1123";						
		            		
			  bAutorizado12= false;
            window.open('pagardevolucion.php?A=1', 'devoluciones', 'width=465,height=430,toolbar=no,scrollbar=no');
        }

        function fCancelarTicket() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1117";						
		            		
			bAutorizado7=false;
            window.open('pv_tickets_cancelar.php?A=1', 'Cancelar Tickets', 'width=465,height=430,toolbar=no,scrollbar=no');
        }

        function fFacturarTicket() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1118";						
		            		
			 bAutorizado8=false;
            var nHeight = screen.height;// - 150;
            if (nHeight > 660)
                nHeight = 660;
            window.open('pv_facturas.php?A=1', 'facturas', 'width=700,height=' + nHeight + ',toolbar=no,scrollbar=no');
        }

        function fPagos() {
            if (bPantallaBloqueada)
                return;
            if (nProds <= 0)
                return;

		// PERMISOS USUARIO
			nfkAtributo="1128";						
		            		
			bAutorizado17= false;
            AyudaCerrar();


            var nFKCliente = parseFloat($("#FKCliente").val());
            nFKCliente = nFKCliente || 0;
            var bPermiteCredito = 0;
            if (nFKCliente > 1)
                bPermiteCredito = 1;
            // PERMISOS CAJA: COBRA: si no tiene activado cobra, no permite cobrar           
            var Cobra = $('#Cobra').val();
            if (Cobra != 1)
            {
                alert('Esta caja no tiene activados los Cobros.');
                return;
            }
	            PidePagoPantalla(, $("#TicketTotal").val(), bPermiteCredito);			
        }

        function fCancelaVenta(bSalirPV) {
            if (bPantallaBloqueada)
                return;
            if ((nProds <= 0) && (!bSalirPV))
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1129";			
		            		
			 bAutorizado18= false;
            if (nProds > 0)
                if (!confirm("Seguro de cancelar la venta?")) {
                    return false;
                }
            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        Entidad: "CancelarVenta"
                    },
            function (data, status) {	
			//alert(data);		
                if (bSalirPV) {
                    if (bVentanaPopUp)
                        window.close();
                    else
                        window.open("inicio.php", '_self');
                } else {
                    location.reload();
                }
            })
        }

        function fSuspendeVenta() {
            if (bPantallaBloqueada)
                return;
            if (nProds <= 0)
                return;
            AyudaCerrar();
            // PERMISOS CAJA: SuspendeTicket
            var SuspendeTicket = $('#SuspendeTicket').val();
	       if (SuspendeTicket != 1)
            {
                alert('Esta caja no tiene activado el Suspender Ticket.');
                return;
            }


            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        Entidad: "SuspendeVenta"
                    },
            function (data, status) {
                var json = $.parseJSON(data);
                if (json.Error) {
                    alert("error: " + json.Error);
                } else {

                }
            })
        }

        function fAyuda() {
            if (bPantallaBloqueada)
                return;

            $("#AyudaDiv").show();
            bAyudaActiva = true;
            BloqueaPantalla(true);
            bPantallaBloqueada = false; // forza a que la pantalla no se considere bloqueada, para permitir teclas de funcion.
        }

        function fReanudarVenta() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
            // PERMISOS CAJA: ABRE TICKET SUSPENDIDO:
            var AbreTicketSuspendido = $('#AbreTicketSuspendido').val();
            if (AbreTicketSuspendido != 1)
            {
                alert('Esta caja no tiene activado el Abrir Tickets Suspendidos.');
                return;
            }

            bFusionarTicketSuspendido = false;
            if (nProds > 0) {
                if (confirm('Existe una venta en proceso. �Desea fusionar la cuenta reanudada a esta venta?')) {
                    bFusionarTicketSuspendido = true;
                } else {
				   bFusionarTicketSuspendido = false;
                }
            }

            $.post("pv_ajax.php",
                    {
                        TicketID: '',
                        Entidad: "listaTicketsSuspendidos"
                    },
            function (data, status) {
                var json = $.parseJSON(data);
                if (json.Error) {
                    alert("error: " + json.Error);
                } else {
                    $("#TablaListaTicketsSuspendidos > tbody").html(json.Lista);
                    nTicketsSuspendidos = json.Tickets;
                    nTicketSuspendidoIDseleccionado = json.PrimerTicket;
                    nTicketSuspendidoSeleccionado = 1;
                    seleccionaTicketSuspendido(nTicketSuspendidoSeleccionado);

                    $("#TicketsSuspendidosDiv").show();
                    bSeleccionandoTicketSuspendido = true;
                    BloqueaPantalla(true);

                }
            })
        }

        function fPrecuenta() {
            if (bPantallaBloqueada)
                return;
            if (nProds <= 0)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1119";						
		            		
			 bAutorizado9= false;
            if (confirm("\u00BFSuspender Ticket?\nSi es pedido a domicilio se recomienda suspender el ticket hasta el pago del mismo.")) {
			 var SuspendeTicket = $('#SuspendeTicket').val();
		     if (SuspendeTicket != 1)
            {
                alert('Esta caja no tiene activado el Suspender Ticket.');
                return;
            }


                $.post("pv_ajax.php",
                        {
                            TicketID: '',
                            Entidad: "SuspendeVenta"
                        },
                function (data, status) {
                    var json = $.parseJSON(data);
                    if (json.Error) {
                        alert("error: " + json.Error);
                    } else {

                    }
                })
            } else {
                // PERMISOS CAJA: IMPRIME
                var Imprime = $('#Imprime').val();
                if (Imprime != 1)
                {
                    alert('Esta caja no tiene activada la Impresi\u00f3n. ');
                    return;
                }
            }		
							 $.post("precuenta_imprime.php",
                                {
                                    TicketID: '',
                                },
                        function (data2, status) {
							//alert(data2);
                            console.log(data2);
                            var json2 = $.parseJSON(data2);
                            if (json2.Error) {
                                alert("error: " + json.Error);
                            } else {
								var bbarcode=json2.bbarcode;
								var Folio=json2.Folio;	
						    	//toma las copias a imprimir
								var copias=parseInt(json2.copias);
								var i=0;
								var impresionWindow ="";
								//toma la impresora elegida
								var impresora1="";
								$.post("styles/get_printer.php",{},
								function (result) {
								//	alert(result);
									impresora1=result;
									//si esta en pdf, muestra pdf
									if(impresora1=="PDF")
									{
										impresionWindow = window.open('', '_blank', 'width=600,height=500');
										impresionWindow.location.href = sThisURLPath + "impresiones/precuenta_.pdf";
										 location.reload();
									}else{//si no imrpime el ticket con sus copias
									for (i = 0; i < copias; i++) { 									
										if(bbarcode=="1")
											var BarCode=Folio;
										else
										   	var BarCode="";	
										var imagen1="";	
									   	var publicidad1="";		
										console.log("Llamando impresora");
										window.postMessage({type: "PrintTicket", TICKET_URL:sThisURLPath+"impresiones/precuenta_.txt", PrinterLogicalName: impresora1,OpenDrawer: 0,BarCode:BarCode, CutPaper:"1",HeaderImg:imagen1,PublicityImg:publicidad1, TempFolder:"c:/cajafacil/impresiones"}, "*");		
										 location.reload();
									}//for imprimir copias
										}//if pdf
								})//get printer							
                            }
                        })
        }

        function fBuscarCliente() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO			
			nfkAtributo="1125";						
		            		
			 bAutorizado14= false;
			 
            // abre ventana de seleccion de clientes y monitorea el cierre para asignarlo
            var win = window.open('clientes2.php', 'productos', 'width=1050,height=570,toolbar=no');
            var pollTimer = window.setInterval(function () {
                if (win.closed !== false) { // !== is required for compatibility with Opera
                    window.clearInterval(pollTimer);
                    ClienteAsigna();
                }
            }, 300);
        }

        function fBuscarProducto() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
            // abre ventana de seleccion de producto y monitorea el cierre para buscar el nombre de producto
            var win = window.open('productos3.php', 'productos', 'width=880,height=490,toolbar=no,scrollbar=no');
            var pollTimer = window.setInterval(function () {
                if (win.closed !== false) { // !== is required for compatibility with Opera
                    window.clearInterval(pollTimer);
                    ProductoObtenCodigo();
                }
            }, 300);
        }

        function fAbrirCaja() {
	        if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			
			$.post("styles/get_printer.php",{},
				function (result) {
				//alert(result);
		     console.log("*************************");
            console.log("Llamando impresora para abrir cajon");
            console.log("*************************");
            console.log(result);
            window.postMessage({type: "OpenCashDrawer", PrinterLogicalName: result}, "*");
			})
        }

        function fSalirPV() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();

            if (!confirm('Salir del Punto de Venta?'))
                return;
            fCancelaVenta(true);
        }


        function fCancelaRenglon() {
            if (bPantallaBloqueada)
                return;
            AyudaCerrar();
			// PERMISOS USUARIO
			nfkAtributo="1121";	
		            		
			 bAutorizado10 = false;
	      if (nIDseleccionado > 0) {
	            $.post("pv_ajax.php",
                        {
                            TicketID: '',
                            renglon: nRenSeleccionado,
                            Entidad: "ProductoElimina"
                        },
                function (data, status) {
                    var json = $.parseJSON(data);
                    if (json.Error) {
                        alert("error: " + json.Error);
                    } else {
						// REGRESA LA PREGUNTA DE VALIDAR MAYOREO A VACIO
						$('#mayoreo1').val('');
                        listaProductos(false);
                    }
                })
            }
        }
    </script>
</head>
<body class="maindocpv">
<div id="BlockFullScreen" class="BlockFullScreen"> </div>
<div id="PidePrecioDiv" class="PidePrecioDiv">
    <form method="post" name="PidePrecioForm" id="PidePrecioForm" action="javascript: ProductoGuardaPrecio()">
        <TABLE class="editTable" width="100%">
            <TR><TD>Precio Unitario:</TD></TR>
            <TR><TD><input type="number" id="PidePrecioValor" min="0" step="0.01"></TD></TR>
            <TR><TD><input type="button" value="OK" id="PidePrecioOK"><input type="button" value="Cancelar" id="PidePrecioCancelar"></TD></TR>
        </TABLE>
    </form>
</div>
<div id="PidePagoDiv" class="PidePagoDiv">

</div>

<div id="AyudaDiv" class="AyudaDiv">

</div>
<div id="TicketsSuspendidosDiv" class="TicketsSuspendidosDiv">

</div>
<div id="TicketsDiv" class="TicketsSuspendidosDiv">

</div>
<div id="TicketsSuspenderDiv" class="PidePrecioDiv">
    <form method="post" name="tick" id="TicketsSuspendider" action="javascript: TicketsSuspenderGuardar()">
        <TABLE class="editTable" width="100%">
            <TR><TD>Escriba texto descriptivo del ticket:</TD></TR>
            <TR><TD><input type="text" id="Comentario" /></TD></TR>
            <TR><TD><input type="button" value="OK" id="TicketsSuspenderOK"><input type="button" value="Cancelar" id="TicketsSuspenderCancelar"></TD></TR>
        </TABLE>
    </form>
</div>
<div id="VendedorDiv" class="PidePrecioDiv">
    <form method="post" name="tick" id="VendedorDiv" action="javascript: VendedorGuardar()">
        <TABLE class="editTable" width="100%">
            <TR><TD>Vendedor:</TD></TR>
            <TR><TD><select name="Vendedor" id="Vendedor"  class="catalog_edit_field" >

            </select></TD></TR>
            <TR><TD><input type="button" value="OK" id="VendedorOK"><input type="button" value="Cancelar" id="VendedorCancelar"></TD></TR>
        </TABLE>
    </form>
</div>
<div id="MuestraCambioDiv" class="MuestraCambioDiv">
    <TABLE class="PagoCambioTable" width="100%">
        <TR><TD>Su cambio es de:</TD></TR>
        <TR><TD id="PagoCambioTD">0.00</TD></TR>
        <TR><TD><input type="button" value="Aceptar" id="PagoCambioAceptarBtn"></TD></TR>
    </TABLE>
</div>
<div id="PideLoginDiv" class="PideLoginDiv">
    <TABLE border="0" align="center"  cellpadding="5" cellspacing="0" width="200" class="login">
        <TR>
            <TD><span class="etiqueta">Usuario:  </span></TD>
            <TD><input type="text" name="AutorizacionLogin" id="AutorizacionLogin" class="pagosTabDeseado" tabindex="1"></TD>
        </TR>
        <TR>
            <TD><span class="etiqueta">Contrase&ntilde;a:  </span></TD>
            <TD><input type="password" name="AutorizacionPassword" id="AutorizacionPassword" class="pagosTabDeseado" tabindex="2"/></TD>
        </TR>
        <TR>
            <TD align="right">
                <table width="80" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="20"><img src="images/bkg_barramenu_izq_.png" width="20" height="36" /></td>
                        <td background="images/bkg_barramenu_centro_.png" class="boton"><a href="javascript: PideLoginAceptar();" class="pagosTabDeseado" tabindex="3">Aceptar</a></td>
                        <td width="20"><img src="images/bkg_barramenu_der_.png" width="20" height="36" /></td>
                    </tr>
                </table>
            </TD>
            <TD align="right">
                <table width="80" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="20"><img src="images/bkg_barramenu_izq_.png" width="20" height="36" /></td>
                        <td background="images/bkg_barramenu_centro_.png" class="boton"><a href="javascript: PideLoginCerrar(false);">Cancelar</a></td>
                        <td width="20"><img src="images/bkg_barramenu_der_.png" width="20" height="36" /></td>
                    </tr>
                </table>
            </TD>
        </TR>
    </TABLE>
</div>
        <form name="FormaCaptura" id="FormaCaptura" action="javascript:agregaProducto();">
            <input type="hidden" id="BehaveVendor" name="BehaveVendor" value="" /> 
             <input type="hidden" id="reanudar" name="reanudar" value="" />   
            <input type="hidden" id="Imprime" name="Imprime" value="" /> 
            <input type="hidden" id="Cobra" name="Cobra" value="" /> 
            <input type="hidden" id="AbreTicketSuspendido" name="AbreTicketSuspendido" value="" /> 
            <input type="hidden" id="SuspendeTicket" name="SuspendeTicket" value="" />   
            <input type="hidden" id="Vendedor1" name="Vendedor1" value="" />
            <input type="hidden" id="LimAlert" name="LimAlert" value="" />   
            <input type="hidden" id="LimOper" name="LimOper" value="" />   
            <input type="hidden" id="BloqLimOper" name="BloqLimOper" value="" />  
            <input type="hidden" id="crestringido" name="crestringido" value="0" />
            <input type="hidden" id="mayoreo1" name="mayoreo1" value="" />
            <input type="hidden" id="Bloqueado" name="Bloqueado" value="0" />
            <div class="BodyContainer">  
                <div align="center">
                    <div class="pv_mainrow">   
                        <div class="pv_maincol">
                            <div class="pv_header_top_menu">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="32"><img src="images/bkg_basegde_latizq.png" width="32" height="57" /></td>
                                        <td width="100%" background="images/bkg_basegde_centro.png">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="20%"><span class="etiqueta2">Caja </span></td>
                                                    <td width="40%"><span class="etiqueta2">Cajero: </span></td>
                                                    <td width="28%"><span class="etiqueta2">Sucursal: </span></td>
                                                    <td width="12%"><span class="etiqueta2"></span></td>
                                                </tr>
                                            </table></td>
                                        <td width="25"><img src="images/bkg_basegde_der.png" width="30" height="57" /></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="pv_prodlist_div1">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="pv_prodlist_table1">
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0"  height="100%">
                                                <tr valign="top">
                                                    <td width="30" bgcolor="white"><img src="images/bkg_barratit_izq_.png" width="30" height="42" /></td>
                                                    <td width="100%" bgcolor="white">
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="pv_tablaproductos"  id="TablaListaProductos" >
                                                            <thead heigh="42" background="images/bkg_barratit_centro_.png">
                                                                <tr heigh="42" >
                                                                    <th width="2%" align="left"><img src="images/linea_bca.png" width="1" height="42" /></th>
                                                                    <th width="56%" align="left"><span class="tituloLista">Descripci&oacute;n</span></th>
                                                                    <th width="2%" falign="left"><img src="images/linea_bca.png" width="1" height="42" /></th>
                                                                    <th width="14%" align="left"><span class="tituloLista">Cantidad</span></th>
                                                                    <th width="2%" align="left"><img src="images/linea_bca.png" width="1" height="42" /></th>
                                                                    <th width="12%" align="left"><span class="tituloLista">Precio</span></th>
                                                                    <th width="2%" align="left"><img src="images/linea_bca.png" width="1" height="42" /></th>
                                                                    <th width="8%" align="left"><span class="tituloLista">Importe</span></th>
                                                                </tr> 
                                                            </thead>
                                                            <tbody>
                                                                <!--ULTIMO RENGLON PERMITE DEFINIR LOS MISMOS TAMA�OS QUE EL ENCABEZADO --->
                                                                <tr height="100%">
                                                                    <td widtd="2%"></td>
                                                                    <td widtd="56%"></td>
                                                                    <td widtd="2%"></td>
                                                                    <td widtd="14%"></td>
                                                                    <td widtd="2%"></td>
                                                                    <td widtd="14%"></td>
                                                                    <td widtd="2%"></td>
                                                                    <td widtd="8%"></td>
                                                                </tr>
                                                            </tbody>   
                                                        </table></td>
                                                    <td width="31" bgcolor="white"><img src="images/bkg_barratit_der_.png" width="30" height="42" /></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="10"><img src="images/bkg_bcobajo_izq.png" width="10" height="10" /></td>
                                                    <td  height="10" background="images/bkg_bcobajo_centro.png"><img src="images/bkg_bcobajo_centro.png" width="3" height="10" /></td>
                                                    <td width="9"><img src="images/bkg_bcobajo_der.png" width="9" height="10" /></td>
                                                </tr>
                                            </table></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="pv_totaldiv1">
                                <table cellspacing="0" cellpadding="0" class="pv_totaldiv1a">
                                    <tr>
                                        <td width="20" height="36"><img src="images/bkg_barramenu_izq_.png" width="20" height="36" /></td>
                                        <td  height="36" background="images/bkg_barramenu_centro_.png"><table width="100%" border="0" cellspacing="0" cellpadding="2" id="ProductoLectorTable">
                                                <tr>
                                                    <td ><img src="images/separador.png" width="1" height="15" /></td>
                                                    <td ><span class="etiqueta">C&oacute;digo</span></td>
                                                    <td ><input type="text" name="CapturaCodigo" id="CapturaCodigo" list="Productos"/><datalist id="Productos">

                                                                <option value="">

                                                        </datalist>
                                                        <input type="hidden" id="FKProducto" name="FKProducto">
                                                    </td>
                                                    <td ><img src="images/separador.png" width="1" height="15" /></td>
                                                    <td ><span class="etiqueta">Cantidad</span></td>
                                                    <td ><input type="number" name="CapturaCantidad" id="CapturaCantidad" min="0" value="1" step="0.001"/></td>
                                                    <td ><img src="images/separador.png" width="1" height="15" /></td>
                                                    <td ><input type="submit" name="CapturaOK" id="CapturaOK" value="OK"/></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="20" height="36"><img src="images/bkg_barramenu_der_.png" width="16" height="36" /></td>
                                    </tr>
                                </table>               
                            </div>
                        </div>
                        <div class="pv_maincol_der">
                            <div class="pv_maincol_der_arriba">
                                <div class="pv_header_logo">
                                    <a href="inicio.php"></a>
                                </div>
                                <div class="pv_prod_image">
                                    <img src="images/shim.gif"  id="producto_imagen">
                                </div>
                            </div>
                            <div class="pv_maincol_der_abajo">
                                <table width="199" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td><table width="199" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="22"><img src="images/bkg_grissupizq.png" width="22" height="21" /></td>
                                                    <td width="156" background="images/bkg_grissupcentro.png">&nbsp;</td>
                                                    <td width="21"><img src="images/bkg_grissupder.png" width="21" height="21" /></td>
                                                </tr>
                                            </table></td>
                                    </tr>
                                    <tr>
                                        <td><table width="199" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="22" background="images/bkg_griscentroizq.png">&nbsp;</td>
                                                    <td width="156" align="left" valign="top" bgcolor="#DEDEDD">
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td height="38"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                          <td width="23%"><!--<img src="images/ico_promocion_.png" width="23" height="23" />--></td>
                                                                            <td width="77%"><span class="etiqueta2" id="PromocionNombre" style="font-size:11px;">Sin promoci&oacute;n</span></td>
                                                                        </tr>
                                                                    </table></td>
                                                            </tr>
                                                            <tr>
                                                                <td><img src="images/linea_sombra.png" width="154" height="2" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="38"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                          <td width="23%" align="center"><!--<img src="images/ico_publico_.png" width="9" height="21" />--></td>
                                                                            <td width="77%"><span class="etiqueta2" id="ClienteNombre">P&uacute;blico general</span>
                                                                                <input type="hidden" name="FKCliente" id="FKCliente" value=""/>
                                                                                <input type="hidden" name="ClienteCreditoDisponible" id="ClienteCreditoDisponible" value="0"/>
                                                                                <input type="hidden" name="ClienteDiasPlazo" id="ClienteDiasPlazo" value="0"/>
                                                                            </td>
                                                                        </tr>
                                                                        </table></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><img src="images/linea_sombra.png" width="154" height="2" /></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                <tr>
                                                                                    <td class="etiqueta2">Art&iacute;culos</td>
                                                                                    <td id="articulosTD" align="right"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="etiqueta2">Sub Total</td>
                                                                                    <td id="subtotalTD" align="right"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="etiqueta2">IVA</td>
                                                                                    <td id="ivaTD" align="right"></td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                    </table></td>
                                                                    <td width="22" background="images/bkg_griscentroder.png">&nbsp;</td>
                                                                    </tr>
                                                                    </table></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><table width="199" border="0" cellspacing="0" cellpadding="0">
                                                                                <tr>
                                                                                    <td width="22"><img src="images/bkg_grisinfizq.png" width="22" height="22" /></td>
                                                                                    <td width="156" background="images/bkg_grisinfcentro.png">&nbsp;</td>
                                                                                    <td width="21"><img src="images/bkg_grisinfder.png" width="21" height="22" /></td>
                                                                                </tr>
                                                                            </table></td>
                                                                    </tr>
                                                                    </table>

                            <div class="pv_totaldiv2">
                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tr>
                                        <td >
                                            <span class="total">Total</span>
                                        </td>
                                        <td>
                                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                <tr>
                                                    <td width="20" height="36"><img src="images/bkg_barramenu_izq.png" width="20" height="36" /></td>
                                                    <td height="36" align="right" background="images/bkg_barramenu_centro.png" id="totalTD"></td>
                                                    <td width="20" height="36"><img src="images/bkg_barramenu_der.png" width="16" height="36" /></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>  
                        </div>
                    </div>
                    <div class="pv_mainrow">    
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="39"><img src="images/bkg_grisredizq.png" width="45" height="97" /></td>
                            <td width="938" align="left" valign="middle" background="images/bkg_grisredcentro.png">
                                <!---INICIA BOTONES CONFIGURABLES--->
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width:650px;">
                                    <tr height="55" >

                                    </tr>
                                </table>
                            <!---TERMINA BOTONES CONFIGURABLES--->
                            </td>
                            <td width="47"><img src="images/bkg_grisredder.png" width="47" height="97" /></td>
                        </tr>
                        </table>
                    </div>

                <p>&nbsp;</p>
            </div>
        </div>
        <div id="DebugDiv"> </div>
    </form>
</body>
<script type="text/javascript">
var myInput = document.getElementById("CapturaCantidad");
if (myInput.addEventListener) 
    myInput.addEventListener('keydown', this.keyHandler, false);
 else if (myInput.attachEvent) 
    myInput.attachEvent('onkeydown', this.keyHandler); /* damn IE hack */

function keyHandler(e) {
    var TABKEY = 9;
    if (e.keyCode == TABKEY) {
        //this.value += "    ";
        if (e.preventDefault) {
            e.preventDefault();
        }
        this.value = this.value.substring(0, this.value.length - 1);
        $("#CapturaCodigo").focus();
        return false;
    }
}

</script>
</html>