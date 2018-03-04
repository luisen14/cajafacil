<script src="includes/elementosnavegables.js"></script>
<script>
    var bPagoCheque = false;
    var bPagoCredito = false;
    var bPagando = false;
    var bControlaNavegacion = false;
    var bSugiriendoPagoDefault = true;
    var bAutorizando = false;
    var bPagoCreditoAutorizado = false;
	var bsumar = false;
	var bimprime = false;	
    var bimprime2 = false;	
    var nTicketID = 0;
    $(window).bind('keydown', function (event) {
        if (!bPagando)
            return;
			
        if (event.keyCode == 27) {
            bPagoCreditoAutorizado = false;
            // en popup de autorizacion de credito, si se presiona cancel, se cierra el popup
            if (bAutorizando) 
                PideLoginCerrar(false);
            else 
                PidePagoCancelar();            
        }
        if (event.keyCode == 13) {
            // en popup de autorizacion de credito, si se presiona ENTER, se validan login y password
            if (bAutorizando) 
                PideLoginAceptar();
            else {
			    if(!bsumar && !bimprime)
				{
					bimprime=true;
		            PidePagoRealiza();				
				}
            }
        }
		
		 // UP KEY
        if (event.keyCode == "38") {
			bUp=true;
			// si el usuario sale de la casilla sin modificarla, se quita la sugerencia
            if (bSugiriendoPagoDefault) {
				var $focused = $(':focus');
		        $focused.val('');              
                bSugiriendoPagoDefault = false;
			 }
			
			var sElementID = event.target.id;                    // el elemento de donde sale el focus()
             var iElement = aElemNavegables.indexOf(sElementID);  // la posicion del elemento en el arreglo
             var nMaxElement = aElemNavegables.length -1;         // el total de elementos en el arreglo
             var bAnteriorEsVisible=false;                       // bandera para saber si podemos navegar al siguiente elemento
             if (nMaxElement<=0) return;                          // si no hay elementos, no hacemos nada
             
             $("#" + sElementID).css("font-weight", "normal");            

             var nBloqueo = 0;                                    // contamos iteraciones, para evitar ciclarnos...
             var iElemAnterior = iElement;                       // apuntador al siguiente elemento
             while (!bAnteriorEsVisible) {
                 if (nBloqueo-1>=20) break;
                 iElemAnterior=iElemAnterior-1;
                 if (iElemAnterior<0) iElemAnterior=nMaxElement;   // si el siguiente elemento llega al límite, vuelve a empezar
                 // si el siguiente elemento no es visible, repite con el que le sigue
                 bAnteriorEsVisible = $("#" + aElemNavegables[iElemAnterior]).is(":visible"); 
                 if (!bAnteriorEsVisible) continue;
                 // si el siguiente elmento sí está visible, le pone el focus
		        $("#" + aElemNavegables[iElemAnterior]).css("font-weight", "bold");
                 $("#" + aElemNavegables[iElemAnterior]).focus();
			 }				
		}
		
		 // Down KEY
        if (event.keyCode == "40") {
			bUp=false;
			// si el usuario sale de la casilla sin modificarla, se quita la sugerencia
            if (bSugiriendoPagoDefault) {
				var $focused = $(':focus');
		        $focused.val('');
                bSugiriendoPagoDefault = false;
			 }
				/**/
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
                 if (iElemSiguiente>nMaxElement) iElemSiguiente=0;   // si el siguiente elemento llega al límite, vuelve a empezar

                 // si el siguiente elemento no es visible, repite con el que le sigue
                 bSiguienteEsVisible = $("#" + aElemNavegables[iElemSiguiente]).is(":visible"); 
                 if (!bSiguienteEsVisible) continue;

                 // si el siguiente elmento sí está visible, le pone el focus                 
                 $("#" + aElemNavegables[iElemSiguiente]).css("font-weight", "bold");
                 $("#" + aElemNavegables[iElemSiguiente]).focus();
			 }
		}
    })
	//si se mueve del pago default quitar sugerencia
	function quitar(){
			// si el usuario sale de la casilla sin modificarla, se quita la sugerencia
            if (bSugiriendoPagoDefault) {				
				var focused1 = document.getElementsByClassName("tipoPagoDefault")[0];
		        focused1.value='';
                bSugiriendoPagoDefault = false;
			 }		
	}
	//si no es el tipo de pago default y ya se completo el total se da aceptar
function sumar()
{
 var v = 0;
    $("input[id^='Total_']").each(function(i, el){
		var v1=el.value;
		if(v1=="") v1=0;
        v += parseFloat(v1, 10);
    });
	var Total=$('#TicketTotal').val();
	if(v>=Total)
	{
		if(!bimprime)
			bsumar = true;
	}
}
    function PidePagoPantalla(pTicketID, pTotalPorPagar, pPermiteCredito) {
        pTotalPorPagar = parseFloat(limpiaCantidad(pTotalPorPagar)) || 0;
        pTotalPorPagar = pTotalPorPagar.toFixed(2);
        $(".OtraMoneda").each(function (i, obj) {
            var nTipoCambio = $(this).attr("TipoCambio");
            var nPagoOtraMoneda = parseFloat(pTotalPorPagar / nTipoCambio);
            $(this).html('$' + nPagoOtraMoneda.toFixed(4) + ' (' + nTipoCambio + ')');
        });

        $("#PagoTicketID").val(pTicketID);
        if (pPermiteCredito > 0) 
            $(".TipoPagoCredito").show();
        else {
            $(".TipoPagoCredito").hide();
            $("#Total_6").val(0);
        }

        $("#Saldo").val(pTotalPorPagar);

        // si el tipo default es credito y no hay cliente, usa efectivo como default
        if (($(".tipoPagoDefault").attr("id") == "Total_6") && (pPermiteCredito <= 0)) {
            $("#Total_6").val(0);
            $("#Total_1").val(pTotalPorPagar);
            $("#Total_1").data('valorSugerido', pTotalPorPagar);
        } else {
            $(".tipoPagoDefault").val(pTotalPorPagar);
            $(".tipoPagoDefault").data('valorSugerido', pTotalPorPagar);
        }

        bSugiriendoPagoDefault = true;
        BloqueaPantalla(true);
        $("#PidePagoDiv").show();
        $("#FaltaPago").val(0);

        // si el tipo default es credito y no hay cliente, usa efectivo como default
        if (($(".tipoPagoDefault").attr("id") == "Total_6") && (pPermiteCredito <= 0)) {
            $('#Total_1').val(pTotalPorPagar);
            $('#Total_1').focus();
            $('#Total_1').select();
        } else {
            $('.tipoPagoDefault').val(pTotalPorPagar);
            $('.tipoPagoDefault').focus();
            $('.tipoPagoDefault').select();
        }
        bPagando = true;
        bControlaNavegacion = true;
		bimprime2=false;
		aplicaabono2=false;		
		if($('#Total_8').val())
		$('#Total_8').val('');		
    }

    function PidePagoCancelar() {
        $("#PidePagoDiv").hide();
        // limpia campos previamente llenados
        $("#NumCheque").val("");
        $("#Banco").val("");
        $('.pagoPorTipo').each(function (i, obj) {
            $(this).val('');
        });

        BloqueaPantalla(false);
        bPagando = false;
        bControlaNavegacion = false;
    }

    function PidePagoRealiza(nUsuarioAutoriza) {	
	    var bCancelaPago = false;
        var nClienteDiasPlazo = 0;
        recalculaPorPagar();
        var nFaltaPago = limpiaCantidad($("#FaltaPago").val());
        var nTotalTicket = limpiaCantidad($("#Saldo").val());
        nFaltaPago = parseFloat(nFaltaPago);
        nFaltaPago = nFaltaPago || 0;
        nUsuarioAutoriza = nUsuarioAutoriza || 0;
        $('.pagoPorTipo').each(function (i, obj) {
            var nEstePago = parseFloat($(this).val());
            var nExactPay = $(this).attr("exactPay");
            if (nExactPay == 1) {
                if (nTotalTicket < nEstePago) {
                    alert('El pago de ' + $(this).attr("Nombre") + ' máximo es $' + nTotalTicket.toFixed(2));
                    bCancelaPago = true;				
                }
            }
			//Si el pago es mas de 2 digitos mayor que el total no aceptarlo
			if(nEstePago=='NaN')
				nEstePago=0;
			var nEstePago2=parseInt(nEstePago).toString().length;
			var nTotalTicket2=parseInt(nTotalTicket).toString().length+2;
			if(nEstePago2>nTotalTicket2)
			{
			   alert('El pago máximo es de 2 digitos más que el Total');
               bCancelaPago = true;
			}
        })
       
        if ((sTipoPago == "Ticket") && (parseFloat(nFaltaPago) > 0)) {
            alert("El pago total debe ser igual o mayor al total de la venta.");
			bCancelaPago = true;	
        }

        if (bPagoCheque) {
            if (($("#NumCheque").val() == "") || ($("#Banco").val() == "")) {
                alert('Debe especificar los datos del cheque.');
				bCancelaPago = true;	
            }
        }

 		if (bCancelaPago)
		{
	bPagando = true;
    bControlaNavegacion = true;
     bSugiriendoPagoDefault = false;
     bAutorizando = false;
     bPagoCreditoAutorizado = false;
	 bsumar = false;
	 bimprime = false;	
     bimprime2 = false;
            return;
		}
        if ((bPagoCredito) && (!bPagoCreditoAutorizado)) {		
            var nPagoCredito = parseFloat($(".tipoPagoCredito6").val()) || 0;
            var nClienteCreditoDisponible = parseFloat($("#ClienteCreditoDisponible").val()) || 0;
            nClienteDiasPlazo = $("#ClienteDiasPlazo").val();

            if (nClienteCreditoDisponible < nPagoCredito) {
                if (confirm('Sobrepasa el límite de crédito\nEl crédito posible es: ' + nClienteCreditoDisponible.toFixed(2) + '\n ¿Autorizar venta?')) {
<?
#---si tiene permiso de: "Cajas - Autoriza Venta Credito"...
if ($xUsuario->obtenPermiso(1120)) {
    ?>
                        nUsuarioAutoriza = '<?= $nUsuarioID ?>';
<? } else { ?>
                        $("#PideLoginDiv").show();
                       $("#AutorizacionLogin").focus();
					   $('#AutorizacionLogin').select();
                        bAutorizando = true;
                        return;
<? } ?>
                } else {
                    return;
                }
            }
        }

        nTicketID = $("#PagoTicketID").val();
        if (sTipoPago == "Ticket") {
            RegistraPagoDB(nUsuarioAutoriza, nClienteDiasPlazo, false);			
        } else if (sTipoPago == "TicketCxC") {
            nAbonoPorAplicar = parseFloat(limpiaCantidad($("#TotalAbono").val())) || 0;
            //console.log("*** nAbonoPorAplicar: " + nAbonoPorAplicar);
            if (nAbonoPorAplicar == 0) {
                sRazonNoAbona = prompt("Raz\u00f3n por la cual no abona:");
		          $.post("pv_cobranzaenruta_ajax.php",
                        {
                            Orden: nOrdenSeleccionada,
                            Ticket: nTicketID,
                            RazonNoAbona: escape(sRazonNoAbona),
                            Entidad: "RazonNoAbona"
                        },
                function (data, status) {
				//alert(data);
                    console.log(data);
                    var json = $.parseJSON(data);
                    if (json.Error) 
                        alert("error: " + json.Error);
                    else 
                        bRazonPorAplicar = true;
                }
                )
            }
            proponeAbonoATicket(nTicketID, nAbonoPorAplicar);
            $("#PidePagoDiv").hide();
            BloqueaPantalla(false);
            bPagando = false;
            bControlaNavegacion = false;
        }
    }

    function RegistraPagoDB(nUsuarioAutoriza, nClienteDiasPlazo, bCxC) {
	    // COMISION: TOMAR VENDEDOR 
        var Vendedor = $('#Vendedor1').val();
        // LIMITES , TOMAR LIMITEs, si esta marcado bloquear operacion no deja hacer el pago
        var LimAlert = $('#LimAlert').val();
        var LimOper = $('#LimOper').val();
        var BloqLimOper = $('#BloqLimOper').val();
		var nCambio = $("#FaltaPago").val();
                    nCambio = parseFloat(nCambio);
                    nCambio = nCambio || 0;
                    nCambio = nCambio * -1;
		var nTotalAbono=$("#TotalAbono").val();
        $.post("pv_ajax.php",
                {
                    Entidad: "Limites"
                }, function (data, status) {
            console.log(data);
			// alert(data);
	        if (parseFloat(data) >= LimOper && BloqLimOper == "1")
                alert("No se puede procesar la venta. El Limite de Operaciones de Caja ha sido Sobrepasado. Es recomendable llamar a su supervisor para generar un retiro de caja.");
            else {
                if (parseFloat(data) >= LimOper)
                    alert("El Limite de Operaciones de Caja ha sido Sobrepasado. Es recomendable llamar a su supervisor para generar un retiro de caja.");
                else if (parseFloat(data) >= LimAlert)
                    alert("El Limite de Caja para alerta ha sido Sobrepasado. Es recomendable llamar a su supervisor para generar un retiro de caja.");
               if(!bimprime2)
				{				
				$.post("pv_ajax.php",
                        {
                            TipoTransaccion: sTipoPago,
                            TicketID: nTicketID,
                            data: $("#PagoForm").serialize(),
                            UsuarioAutoriza: nUsuarioAutoriza,
                            ClienteDiasPlazo: nClienteDiasPlazo,
                            Vendedor: Vendedor,
							Cambio: nCambio.toFixed(2),
                            Entidad: "PagoRealiza"
                        },
                function (data, status) {
					console.log(data);
				  // alert(data);
                    if (data != "") {
                        alert('ha habido un error al registrar el pago.');
                        return;
                    }                   
					$("#PidePagoDiv").hide();
					// BORRA TICKET ELECTRONICO SI SE USO
					 $.post("pv_ajax.php",
                        {
                            TicketID: nTicketID,
					         Entidad: "BorrarTicketE"
                        },
               			function (data, status) {
					    console.log(data);
    	            	});
                    if (bCxC) {
                        // PERMISOS CAJA: IMPRIME
                        var Imprime = $('#Imprime').val();
                        if (Imprime != 1)
                        {
                            alert('Esta caja no tiene activada la Impresi\u00f3n.');
                            return;
                        }        
                    } else {
						
                        $.post("pv_imprime_ticket.php",
                                {
                                    TicketID: nTicketID,
                                    Reimpresion: 0
                                },
                        function (data, status) {
						//alert(data);
							bimprime2=true;
                            console.log(data);
                            var json = $.parseJSON(data);							
                            if (json.Error) {
                                alert("error: " + json.Error);
                            } else {
							var bImagen=json.bImagen;
							var bPublicidad=json.bPublicidad;
							var bbarcode=json.bbarcode;
							var Folio=json.Folio;
							var Imagen=json.Imagen;
							var Publicidad=json.Publicidad;
                                //if (parseFloat(nCambio) > 0) {
                                    $("#PagoCambioTD").html(nCambio.toFixed(2) + "<br/>Su total de:<br/>" + nTotalAbono);
                                    $("#MuestraCambioDiv").show();
                                    $("#PagoCambioAceptarBtn").focus();
                                bPagando = false;
                                bControlaNavegacion = false;
                                // PERMISOS CAJA: IMPRIME
                                var Imprime = $('#Imprime').val();
                                if (Imprime != 1)
                                {
                                    alert('Esta caja no tiene activada la Impresi\u00f3n.');
                                    return;
                                }
								//toma las copias a imprimir
								var copias=parseInt(json.copias)+1;
								var abrir=parseInt(json.abrir);
								//alert(abrir);
								var i=0;
								var impresionWindow ="";
								//toma la impresora elegida
								var impresora1="";
								$.post("includes/get_printer.php",{},
								function (result) {
								//	alert(result);
									impresora1=result;
									if(impresora1=="PDF")
									{
										impresionWindow = window.open('', '_blank', 'width=600,height=500');
										impresionWindow.location.href = sThisURLPath + "impresiones/ticket_" + nTicketID + ".pdf";
									}else{
										var TicketTexto1=json.TicketTexto;
									for (i = 0; i < copias; i++) { 																			
									if(bbarcode)
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
										window.postMessage({type: "PrintTicket", TICKET_URL:sThisURLPath+"impresiones/ticket_" + nTicketID + ".txt", PrinterLogicalName: impresora1,OpenDrawer: abrir,BarCode:BarCode, CutPaper:"1",HeaderImg:imagen1,PublicityImg:publicidad1, TempFolder:"c:/cajafacil/impresiones"}, "*");
									}//for imprimir copias
										}//if pdf
								})//get printer							
                            }//else error
                        })//imprime ticket					
                    }
                }
                )
					}//si no ha impreso bimprime2
            }
        })//else limtalert
    }

    function recalculaPorPagar() {
        var nTotal = $("#Saldo").val();
        nTotal = limpiaCantidad(nTotal);
        var nSumaPagos = 0;

        $('.pagoPorTipo').each(function (i, obj) {
            var nValorEsteTipo = parseFloat(limpiaCantidad($(this).val()));
            var nTipoCambio = parseFloat(limpiaCantidad($(this).attr("TipoCambio")));
            nValorEsteTipo = nValorEsteTipo || 0;
            nTipoCambio = nTipoCambio || 1;
            nSumaPagos = parseFloat(nSumaPagos + nValorEsteTipo * nTipoCambio);
        });
        var nFalta = parseFloat(nTotal - nSumaPagos);
        $("#FaltaPago").val(nFalta.toFixed(2));
        $("#TicketSaldo").val(nFalta.toFixed(2));
        $("#TotalAbono").val(nSumaPagos);

        var nPagoCredito = $(".tipoPagoCredito6").val();
	    nPagoCredito = parseFloat(nPagoCredito);
        nPagoCredito = nPagoCredito || 0;
        if (nPagoCredito > 0)
            bPagoCredito = true;
        else
            bPagoCredito = false;
        var nPagoCheque = $(".tipoPagoCheque").val();
        nPagoCheque = parseFloat(nPagoCheque);
        nPagoCheque = nPagoCheque || 0;
        if (nPagoCheque > 0)
            bPagoCheque = true;
        else
            bPagoCheque = false;
    }

    function limpiaCantidad(pCantidad) {
        //quita coma
        pCantidad = pCantidad.toString();
        pCantidad = pCantidad.replace(/\,/g, '');
        return parseFloat(pCantidad) || 0;
    }

    function MuestraCambioCerrar() {
        BloqueaPantalla(false);
        bPagando = false;
        bControlaNavegacion = false;
        $("#MuestraCambioDiv").hide();

        if (sTipoPago == "Ticket")
            location.reload();
    }

    $(window).load(function () {

        $(".tipoPagoDefault").keydown(function (event) {
            if ((bSugiriendoPagoDefault) && ($(this).data("valorSugerido") != $(this).val()))
                bSugiriendoPagoDefault = false;
        })

        // si se especifica pago por cheque, pide numero de cheque y banco
        $(".tipoPagoCheque").change(function () {
            if (($(this).val() == '') || ($(this).val() == '0')) {
                bPagoCheque = false;
                $("#DatosCheque").hide();
            } else {
                bPagoCheque = true;
                $("#DatosCheque").show();
            }
        });

        // si esta permitido pago a Crédito y se cambia el valor en la casilla, prende o apaga bandera...
        $(".tipoPagoCredito6").change(function () {			
            if (($(this).val() == '') || ($(this).val() == '0')) {
                bPagoCredito = false;
            } else {
                bPagoCredito = true;
            }
			
        });

        $(".pagoPorTipo").change(function () {
            recalculaPorPagar();
        })

        $("#PagoCambioAceptarBtn").click(function () {
            MuestraCambioCerrar();
        })
    });
</script>caja
Cobro al P&uacute;blico
<table align="center" bgcolor="#EFEFFF" width="100%" height="100%">
    <tr>
        <td align="center" >
            <FORM ID="PagoForm" NAME="PagoForm" METHOD="POST" ACTION="javascript:return false;">
                <input type="hidden" id="PagoTicketID" name="PagoTicketID" value="<?= $nTicketID ?>">
                <input type="hidden" id="TicketTotal" name="TicketTotal" value="">
                <input type="hidden" id="TicketSaldo" name="TicketSaldo" value="">
                <TABLE  class="ScrollTable">
                    <THEAD>
                        <TR>
                            <TH>Nombre</TH>
                            <TH>Referencia</TH>
                            <TH>Total</TH>
                        </TR>
                    </THEAD>
                    <tbody>							
                        <?
                        $iTipoPago = 0;
                        $iTabIndex = 0;
                        $SQL_query = "SELECT * FROM tipospago order by Nombre ASC";
                        #print "<P>".$SQL_query;
                        $xTiposPagosRS = mysql_query($SQL_query) or die(mysql_error());
                        while ($xTiposPagosRow = mysql_fetch_array($xTiposPagosRS)) {
                            $sTipoNombre = $xTiposPagosRow["Nombre"];
                            $sClasePagoDefault = "";
                            if ($xTiposPagosRow["ID"] == $xConfigPDV->fkTipoPago)
							{
                                $sClasePagoDefault = "tipoPagoDefault";
								$sonchange=' ';
							}else
								$sonchange=' onchange="javascript: sumar();" onclick="javascript: quitar();"';

                            $sClasePagoEspecial = "NoDefault";
                            if ($xTiposPagosRow["Nombre"] == "Cheque")
                                $sClasePagoEspecial = "tipoPagoCheque";
                            if ($xTiposPagosRow["ID"] == "6")
                                $sClasePagoEspecial = "tipoPagoCredito6";
                            ?>
                            <TR Rowid="<?= $xTiposPagosRow["ID"] ?>" class="ListaRow <?= ($xTiposPagosRow["ID"] == "6" ? "TipoPagoCredito" : "") ?>">
                                <TD NOWRAP align="left"><?= $sTipoNombre ?></TD>
                                <? if ($xTiposPagosRow["TipoCambio"] != 1) { ?>
                                    <TD NOWRAP align="left" class="OtraMoneda" TipoCambio="<?= $xTiposPagosRow["TipoCambio"] ?>"></TD>
                            <? } else { ?>
                                    <TD NOWRAP align="left">&nbsp;</TD>
                            <? } ?>
                                <TD data-bind="" NOWRAP align="left"><input type="text" id="Total_<?= $xTiposPagosRow["ID"] ?>" name="SubTotal[<?= $xTiposPagosRow["ID"] ?>]" value=" "  size="10"  class="money pagoPorTipo pagosTabDeseado <?= $sClasePagoDefault ?> <?= $sClasePagoEspecial ?>"  numTipoPago="<?= $iTipoPago++ ?>"  tabindex="<?= $iTabIndex ?>" exactPay="<?= $xTiposPagosRow["ExactPay"] ?>" Nombre="<?= $sTipoNombre ?>" TipoCambio="<?= floatval($xTiposPagosRow["TipoCambio"]) ?>" <?=$sonchange?> onfocus="this.select();"/></TD>
                            </TR>
<? 
$iTabIndex=$iTabIndex+1;
}
mysql_free_result($xTiposPagosRS);
?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td NOWRAP align="left">Total a Pagar:</td><td NOWRAP align="left" >
                                <input type="hidden" id="TotalAbono" name="TotalAbono" value="" disabled="disabled"  size="10" class="money"/>
                                <input type="text" id="Saldo" name="Saldo" value="" disabled="disabled"  size="10" class="money"/></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td NOWRAP align="left">Falta Pagar:</td><td NOWRAP align="left"><input type="text" id="FaltaPago" name="FaltaPago" value="" disabled="disabled" size="10" class="money"/></td>
                        </tr>
                        <TR ID="DatosCheque" style="display:none">
                            <TD NOWRAP align="left"></TD>
                            <TD NOWRAP align="right" colspan="2">
                                Num. Cheque: <input type="text" id="NumCheque" name="NumCheque" value="" size="20" class="pagosTabDeseado" tabindex="<?= $iTabIndex++ ?>"/>
                                <BR>
                                Banco: <input type="text" id="Banco" name="Banco" value="" size="20" class="pagosTabDeseado" tabindex="<?= $iTabIndex++ ?>"/>&nbsp;
                            </TD>
                        </TR>
                    </tfoot>
                </TABLE>
            </FORM>
        </td>
    </tr> 
    <tr><td  ><br /></td>
    </tr>
    <tr><td >
            <table width="80" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="20"><img src="images/bkg_barramenu_izq_<?= $color ?>.png" width="20" height="36" /></td>
                                <td background="images/bkg_barramenu_centro_<?= $color ?>.png"  ><span class="boton" id="Aceptar"><a href="javascript:" onclick="javascript:PidePagoRealiza();" id="Aceptar1" class="pagosTabDeseado" tabindex="<?= $iTabIndex++ ?>">Aceptar</a></span>
                                <td width="20"><img src="images/bkg_barramenu_der_<?= $color ?>.png" width="20" height="36" /></td>
                            </tr>
                        </table></td><td>&nbsp;&nbsp;&nbsp;</td><td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="20"><img src="images/bkg_barramenu_izq_<?= $color ?>.png" width="20" height="36" /></td>
                                <td background="images/bkg_barramenu_centro_<?= $color ?>.png"><span class="boton" id="Cancelar"><a href="javascript:" onclick="javascript:PidePagoCancelar();" id="Cancelar1" class="pagosTabDeseado" tabindex="<?= $iTabIndex++ ?>">Cancelar</a></span></td>
                                <td width="20"><img src="images/bkg_barramenu_der_<?= $color ?>.png" width="20" height="36" /></td>
                            </tr>
                        </table></td><td></td></tr>
            </table>
        </td></tr>
</table>
