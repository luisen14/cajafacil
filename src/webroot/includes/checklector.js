var checkBasculaTimer;
document.Scanner = '';

$(document).ready(function () {
    checkBasculaTimer = window.setInterval(function () {
        var sScannerValue = document.Scanner;
        var Bloqueado= document.getElementById("Bloqueado").value;
        if (sScannerValue !== "" && Bloqueado!="11") {
		 if (sScannerValue.indexOf("e:")>=0 ) {
       			 alert("Ha habido un error de comunicaci�n con el dispositivo.");
				 clearInterval(checkBasculaTimer);
		        return;
    			}else
		            obtiene(sScannerValue);
            document.Scanner='';
        }
		else if(Bloqueado=="11")
			document.Scanner='';
    }, 50);
});

function obtiene(sScannerValue) {
    console.log("obtiene: " + sScannerValue);
  
  var xScannerCodigo = document.getElementById("CapturaCodigo");
    if (xScannerCodigo != null && xScannerCodigo != "null" && xScannerCodigo != "e:") {
         xScannerCodigo.value = sScannerValue;
		 document.getElementById("FormaCaptura").submit();
    } else {
        console.log('Scanner input not found');
    }
}