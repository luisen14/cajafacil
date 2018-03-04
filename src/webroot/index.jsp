<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Caja Facil OmniSocket</title>
        <script type="text/javascript" src="js/jquery-2.2.0.min.js"></script>
        <script type="text/javascript" src="js/ws_events_dispatcher.js"></script>
        <script type="text/javascript" src="js/spin.min.js"></script>
        <script type="text/javascript" src="js/js.cookie.js"></script>
        <script type="text/javascript" src="js/notify.js"></script>
        <script type="text/javascript" src="js/config/settings"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { font-family: sans-serif; }
            
            div#console {
                clear: both;
                width: 40em;
                height: 20em;
                overflow: auto;
                background-color: #f0f0f0;
                padding: 4px;
                border: 1px solid black;
                display: none; 
            }

            div#console .info {
                    color: black;
            }

            div#console .client {
                    color: blue;
            }

            div#console .server {
                color: magenta;
            }
            
            #div_controls 
            {
                display: none;                
            }
            
            #id_confrmdiv
            {
                display: none;
                background-color: #eee;
                border-radius: 5px;
                border: 1px solid #aaa;
                position: fixed;
                width: 480px;
                left: 50%;
                margin-left: -150px;
                padding: 6px 8px 8px;
                box-sizing: border-box;
                text-align: center;
            }
            
            #id_confrmdiv .button {
                background-color: #ccc;
                display: inline-block;
                border-radius: 3px;
                border: 1px solid #aaa;
                padding: 2px;
                text-align: center;
                width: 80px;
                cursor: pointer;
            }
            
            #conn-wait-msg{
                display:none;
                position: absolute;
                top: 0;
                left: 0;
                z-index:1000;
                width: 100%;
                height: 100%;
                min-height: 100%;
                
                background: #000;
                opacity: 0.8;
                color: #fff;
                
                /*background: rgba(255,255,255,.9);*/
                text-align: center;
                line-height: 100px;
            }
            
            #loading_anim {
                position: absolute;
                left:50%;
                top:50%;
                z-index: 1010;
            }
        </style>    
    </head>
    <body>
        <div id="top_spacer"></div>
        <div id="id_confrmdiv"><div id="id_confrmdiv_msg">Esta usted conectado a una instancia local, quiere usted?:</div>
            <button id="localRBtn">Continuar trabajando localmente</button>
            <button id="remoteRBtn">Conectarse a servidor remoto</button>
        </div>
        <div id="conn-wait-msg"><div id="loading_anim"></div><div id="wait-msg"></div></div>
        <div id="console"></div>
        <div id="div_controls">
            <input id="connect" class="button" type="submit" name="connect" value="Connect"/>
            <input id="disconnect" class="button" type="button" name="disconnect" value="Disconnect" disabled="disabled"/>
            <input id="anagrams" class="button" type="button" name="anagrams" value="Get Anagrams" disabled="disabled"/>
            <input id="printers" class="button" type="button" name="printers" value="Get Printers" disabled="disabled"/><br>
            <input id="newWord" type="text" value="" /><input id="addWord" class="button" type="button" name="addWord" value="Add Word" disabled="disabled"/><br>
            <input id="switchToLocal" class="button" type="button" name="switchToLocal" value="Switch To Local" disabled="disabled"/><input id="switchToRemote" class="button" type="button" name="switchToRemote" value="Switch To Remote" disabled="disabled"/>
        </div>
        <script type="text/javascript">
            if (!window.WebSocket && window.MozWebSocket) {
                window.WebSocket = window.MozWebSocket;
            }

            if (!window.WebSocket) {
                alert("WebSocket not supported by this browser");
            }
            
            function showSpinner() {
                var opts = {
                  lines: 15, // The number of lines to draw
                  length: 3, // The length of each line
                  width: 4, // The line thickness
                  radius: 30, // The radius of the inner circle
                  rotate: 0, // The rotation offset
                  color: '#fff', // #rgb or #rrggbb
                  speed: 2, // Rounds per second
                  trail: 70, // Afterglow percentage
                  shadow: false, // Whether to render a shadow
                  hwaccel: false, // Whether to use hardware acceleration
                  className: 'spinner', // The CSS class to assign to the spinner
                  zIndex: 2e9, // The z-index (defaults to 2000000000)
                  top: 'auto', // Top position relative to parent in px
                  left: 'auto' // Left position relative to parent in px
                };
                $('#loading_anim').each(function() {
                    spinner = new Spinner(opts).spin(this);
                });
            }
            
            function setPreloaderMessage(msg){
                $('#wait-msg').text(msg);
            }
            
            function showPreloader(msg){
               if (msg !== null)                   
                   setPreloaderMessage(msg);
               $('#conn-wait-msg').show();               
               setTimeout(function() {
                    setTimeout(function() {showSpinner();},30);                    
                },0);
            }
            
            function removePreloader()
            {   
                // Fade it out
                $('#conn-wait-msg').animate({opacity:0},3000,function(){
                    $(this).remove();
                });
            }
            
            function selectInstance(fnRemote, fnLocal){                
                $('#id_confrmdiv').show();   
                
                $('#localRBtn').off();
                $('#remoteRBtn').off();
                
                
                $('#localRBtn').on("click", function() {                    
                    $('#id_confrmdiv').remove();
                    fnLocal();
                });
                $('#remoteRBtn').on("click", function() {                    
                    $('#id_confrmdiv').remove();                    
                    fnRemote();
                });
                                
            }
                        
            //var remoteAddress = 'cajafacil.net:8080';
            //var localAddress = 'localhost:8080';
            
            function notifyGlobal(message, status){
                var cls = (status !== null)?status:'success';
                $('#top_spacer').notify(message,  
                    { position:"bottom center",className: cls });
            }
            
            function connectedToLocal(localAddr){
                var localURL = 'ws://' + localAddr + '/echo';
                console.log("locat host url: " + localURL);
                
                var socket = new OmniWebSocket(localURL);
                        
                socket.bind('open', function(event){
                    console.log('WebSocket connected ');
                    socket.close();
                    removePreloader();
                    notifyGlobal("Conectado a instancia local");
                });
                socket.bind('error', function(error){
                    notifyGlobal("Conexion fallida a instancia local","error");
                    console.log('WebSocket Error ' + error);
                });                
            }            
            
            function resolveInstance(){
                
                //Where are we?                
                var curLocation = document.location.toString().replace('http://', '');
                console.log("Current location: " + curLocation);
                var status = Cookies.get('status')||false;
                
                if (curLocation.lastIndexOf(localAddress,0) === 0){
                    //We are in local let's see if remote is up and running
                    //if so, swith to remote
                    //try connecting to remote ...                    
                    if (!status){
                        selectInstance(
                                function(){
                                    showPreloader("Espere un momento. Verificando si servidor remoto esta disponible ...");
                                    var remoteURL = 'ws://' + remoteAddress + '/echo';
                                    console.log("remote location: " + remoteURL);
                                    var socket = new OmniWebSocket(remoteURL);

                                    socket.bind('open', function(event){
                                        console.log('WebSocket connected ');
                                        
                                        Cookies.set('status',  { workLocally : false, connected: true });
                                        removePreloader();
                                        notifyGlobal("Conectado a instancia remota");
                                        work();
                                    });
                                    socket.bind('error', function(error){
                                        setPreloaderMessage("No se pudo conectar al servidor remoto");
                                        Cookies.set('status',  { workLocally : true, connected: false });
                                        removePreloader();
                                        console.log('WebSocket Error ' + error);
                                        var useLocal = confirm("No se pudo conectar al servidor remoto, continuar usando el servidor local?");
                                        if (useLocal){
                                            showPreloader("Conectandose a servidor local...");
                                            connectedToLocal(localAddress);
                                            work();
                                        } else {
                                            notifyGlobal("Conexion a instancia remota fallida","error");
                                            Cookies.remove('status');
                                        }
                                    });

                                },
                                function(){Cookies.set('status', { workLocally : true, connected: false });work();});
                    } else {
                        work();
                    }                                                                                            
                } else {
                    //We are in remote
                    showPreloader("Conectandose a servidor remoto...");
                    
                    var remoteURL = 'ws://' + remoteAddress + '/echo';
                    console.log("remote location: " + remoteURL);
                    
                    var socket = new OmniWebSocket(remoteURL);

                    socket.bind('open', function(event){
                        console.log('WebSocket connected ');
                        Cookies.set('status',  { workLocally : false, connected: true });
                        removePreloader();
                        notifyGlobal("Conectado a instancia remota");
                        work();
                    });
                    socket.bind('error', function(error){
                        console.log('WebSocket Error ' + error);   
                        setPreloaderMessage("No se pudo conectar al servidor remoto");
                        removePreloader();
                        var useLocal = confirm("No se pudo conectar al servidor remoto, continuar usando el servidor local?");
                        if (useLocal){
                            showPreloader("Conectandose a servidor local...");
                            connectedToLocal(localAddress);
                            work();
                        } else {
                            notifyGlobal("Conexion a instancia fallida","error");
                            Cookies.remove('status');
                        }
                    });
                    
                    console.log('wait conn result ...');
                    
                }
                    
            }
            
            function out(css, message) {
                var console = document.getElementById('console');//$('#console');                                
                var spanText = document.createElement('span');                
                spanText.className = 'text ' + css;
                spanText.innerHTML = message;
                var lineBreak = document.createElement('br');
                console.appendChild(spanText);
                console.appendChild(lineBreak);
                console.scrollTop = console.scrollHeight - console.clientHeight;
            }            
            
            function work()
            {
                function getWSURL(){
                    var statusObj = null;
                    if (Cookies.get('status')||false){
                        statusObj = Cookies.getJSON('status'); 
                    }
                    return (statusObj.workLocally)?'ws://' + localAddress + '/echo':'ws://' + remoteAddress + '/echo';
                };
                var socket = null;
                var intervalID = 0;
                                
                $('#connect').on("click", function() {                    
                    var urlAddr = getWSURL();
                    socket = new OmniWebSocket(urlAddr);
                    
                    socket.bind('open', function(event){
                        console.log('WebSocket connected ');
                        $('#connect').prop("disabled",true);
                        $('#disconnect').prop("disabled",false);
                        $('#printers').prop("disabled",false);
                        $('#anagrams').prop("disabled",false);
                        
                        $('#newWord').prop("disabled",false);
                        $('#addWord').prop("disabled",false);
                        
                        $('#switchToLocal').prop("disabled",false);
                        $('#switchToRemote').prop("disabled",false);                        
                        
                        if (Cookies.get('status')||false){
                            var statusObj = Cookies.getJSON('status'); 
                        }
                        
                        var locStr = '';
                                                
                        if (statusObj.workLocally){
                           $('#switchToRemote').prop("disabled",false);
                           $('#switchToLocal').prop("disabled",true);
                            locStr = 'local';
                        } else {
                           $('#switchToRemote').prop("disabled",true);
                           $('#switchToLocal').prop("disabled",false);                            
                            locStr = 'remota';
                        }
                                                
                        var msg = 'Conectado a instancia ' + locStr;
                        
                        notifyGlobal(msg,"info");
                        
                        intervalID = setInterval(function(){socket.send( 'heart_beat', {val: '1'} );}, 30000);
                        
                        $('#switchToRemote').on("click", function(){
                            if (socket !== null){
                                socket.close();
                                Cookies.set('status',  { workLocally : false, connected: false });
                                work();
                            }
                        });
                        
                        $('#switchToLocal').on("click", function(){
                            if (socket !== null){
                                socket.close();
                                Cookies.set('status',  { workLocally : true, connected: false });
                                work();
                            }
                        });
                                                
                        $('#switchToLocal').on("click", function(){});
                        
                        $('#anagrams').on("click", function(){
                            if (socket !== null){
                                socket.send( 'get_anagrams', {val: '1'} );

                                socket.bind('anagrams', function(data){
                                    console.log('Anagrams received: ' + data);
                                    /*
                                    var console = document.getElementById('console');//$('#console');
                                    console.innerHTML = "";
                                    */
                                    var div = document.getElementById('console');
                                    while(div.firstChild){
                                        div.removeChild(div.firstChild);
                                    }
                                                                     
                                    var anagramsCol = null;
                                    try {
                                        anagramsCol = JSON.parse(data);                                        
                                        var index;
                                        for (index = 0; index < anagramsCol.values.length; ++index) {
                                            out('info',anagramsCol.values[index]);
                                        }
                                    }catch(exception){
                                        console.log('Exception parsing anagrams: ' + exception);
                                    }
                                });   
                            }
                        });
                        
                        $('#printers').on('click',function(){
                            if (socket !== null){
                                socket.send( 'get_printers', {val: '1'} );

                                socket.bind('printers', function(data){
                                    console.log('Printers received: ' + data);

                                    var div = document.getElementById('console');
                                    while(div.firstChild){
                                        div.removeChild(div.firstChild);
                                    }
                                                                     
                                    var anagramsCol = null;
                                    try {
                                        anagramsCol = JSON.parse(data);                                        
                                        var index;
                                        for (index = 0; index < anagramsCol.values.length; ++index) {
                                            out('info',anagramsCol.values[index]);
                                        }
                                    }catch(exception){
                                        console.log('Exception parsing printers: ' + exception);
                                    }
                                });   
                            }
                        });
                        
                        $('#addWord').on('click', function(){
                            if (socket !== null){
                                var word2add = $('#newWord').val();
                                if (word2add !== null){
                                    socket.send( 'add_word', {'word': word2add} );
                                    
                                    socket.bind('add_word_result', function(data){
                                        $('#newWord').val("");
                                        console.log('Adding word result received: ' + data);
                                        var objRes = JSON.parse(data);
                                        if (objRes !== null && objRes.result)
                                            $('#addWord').notify("Palabra agregada", { position:"right",className: 'success' });
                                        else 
                                            $('#addWord').notify("Problema al agregar palabra nueva", { position:"right",className: 'error' });
                                    });   
                                }
                            }
                        });
                    });
                    
                    socket.bind('close', function(event){
                        console.log('WebSocket disconnected ');
                        $('#connect').prop("disabled",false);
                        $('#disconnect').prop("disabled",true);
                        $('#printers').prop("disabled",true);
                        $('#anagrams').prop("disabled",true);   
                        
                        $('#newWord').prop("disabled",true);
                        $('#addWord').prop("disabled",true);                                                                         
                        
                        clearInterval(intervalID);
                        if (Cookies.get('status')||false){
                            var statusObj = Cookies.getJSON('status'); 
                            Cookies.set('status',  { workLocally :statusObj.workLocally, connected: false });
                        }
                        
                        var locStr = '';
                        
                        $('#switchToRemote').prop("disabled",true);
                        $('#switchToLocal').prop("disabled",true);
                        
                        if (statusObj.workLocally){
                            locStr = 'local';
                        } else {
                            locStr = 'remota';
                        }
                        
                        var msg = 'Desconectado de instancia ' + locStr;
                        notifyGlobal(msg,"warning");
                    });                    
                });
                
                $('#disconnect').on("click",function(){
                    if (socket !== null){
                        socket.close();
                    }
                });

                $('#div_controls').show();
                $('#console').show();
            }            
            
            resolveInstance();
            

            

        </script>
    </body>
</html>
