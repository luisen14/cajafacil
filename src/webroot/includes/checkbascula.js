var checkBasculaTimer;
document.Scale = '';

$(document).ready(function () {
    checkBasculaTimer = window.setInterval(function () {
       // console.log(".");
        var sScaleValue = document.Scale;
        if (sScaleValue !== "") {
			if (sScaleValue.indexOf("e:")>=0) {
		        alert("Ha habido un error de comunicaci�n con el dispositivo.");
				clearInterval(checkBasculaTimer);
       		 return;
    	}
            verifica(sScaleValue);
            document.Scale='';
        }
    }, 50);
});

function verifica(sScaleValue) {
    console.log("verificando: " + sScaleValue);		
    var validScaleChars = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.'];
    var sNewScaleValue = '';
	
    for (var i = 0, len = sScaleValue.length; i < len; i++) {
        if (validScaleChars.indexOf(sScaleValue[i]) >= 0) {
            sNewScaleValue += sScaleValue[i];
        }
    }
    sNewScaleValue = sNewScaleValue.replace(/\.$/, '');
   
    var xScaleCantidad = document.getElementById("CapturaCantidad");
    if (xScaleCantidad != null) {
        xScaleCantidad.value = sNewScaleValue;
    } else {
        console.log('Scale input not found');
    }

}
