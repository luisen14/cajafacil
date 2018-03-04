/*
The MIT License (MIT)

Copyright (c) 2014 Ismael Celis

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
-------------------------------*/
/*
Simplified WebSocket events dispatcher (no channels, no users)

var socket = new FancyWebSocket();

// bind to server events
socket.bind('some_event', function(data){
  alert(data.name + ' says: ' + data.message)
});

// broadcast events to all connected users
socket.send( 'some_event', {name: 'ismael', message : 'Hello world'} );
*/

var OmniWebSocket = function(url){
    var codeMap = {};
        codeMap[1000] = "(NORMAL)";
        codeMap[1001] = "(ENDPOINT_GOING_AWAY)";
        codeMap[1002] = "(PROTOCOL_ERROR)";
        codeMap[1003] = "(UNSUPPORTED_DATA)";
        codeMap[1004] = "(UNUSED/RESERVED)";
        codeMap[1005] = "(INTERNAL/NO_CODE_PRESENT)";
        codeMap[1006] = "(INTERNAL/ABNORMAL_CLOSE)";
        codeMap[1007] = "(BAD_DATA)";
        codeMap[1008] = "(POLICY_VIOLATION)";
        codeMap[1009] = "(MESSAGE_TOO_BIG)";
        codeMap[1010] = "(HANDSHAKE/EXT_FAILURE)";
        codeMap[1011] = "(SERVER/UNEXPECTED_CONDITION)";
        codeMap[1015] = "(INTERNAL/TLS_ERROR)";
        
  var conn = null;
  
  try {
    conn = new WebSocket(url);
  } catch(exception){
      console.log('exception caught during instantiation');
  }

  var callbacks = {};

  this.bind = function(event_name, callback){
    callbacks[event_name] = callbacks[event_name] || [];
    callbacks[event_name].push(callback);
    return this;// chainable
  };

  this.send = function(event_name, event_data){
    if(conn.readyState == 1){
        var payload = JSON.stringify({event:event_name, data: event_data});
        conn.send( payload ); // <= send JSON data to socket server
    } else {
        console.log("exception trying to send...");
        dispatch('error',"trying to communicate with closed socket");
    }

    return this;
  };

  // dispatch to the right handlers
  conn.onmessage = function(evt){
    var json = JSON.parse(evt.data);
    dispatch(json.event, json.data);
  };
  
  this.close = function(){
    if (conn !== null)
        conn.close();     
  };

  //conn.onclose = function(){dispatch('close',closedConnHandler);};
  
  conn.onclose = function (closedConnHandler) {
      dispatch('close',closedConnHandler);
  };
  
  conn.onopen = function(event){
      dispatch('open',event);
  };
  //conn.onerror = function(){dispatch('error',null);};
  
  conn.onerror = function (error) {
      console.log('dispatch error ' + error);
      dispatch('error',error);      
  };

  var dispatch = function(event_name, message){
    var chain = callbacks[event_name];
    if (typeof chain === 'undefined') return; // no callbacks for this event
    for(var i = 0; i < chain.length; i++){
      chain[i]( message );
    }
  };
    
  function closedConnHandler(closeEvent) {
    this.conn = null;
    //wstool.setState(false);
    console.log("Websocket Closed");
    console.log("  .wasClean = " + closeEvent.wasClean);

    var codeStr = codeMap[closeEvent.code];

    console.log("  .code = " + closeEvent.code + "  " + codeStr);
    console.log("  .reason = " + closeEvent.reason);
  }
};


