package com.motus;


import com.motus.task.TaskFactory;
import com.motus.util.MessageHelper;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.websocket.CloseReason;
import javax.websocket.Endpoint;
import javax.websocket.EndpointConfig;
import javax.websocket.MessageHandler;
import javax.websocket.RemoteEndpoint;
import javax.websocket.Session;


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author lsencion
 */
public class OmniSocket extends Endpoint implements MessageHandler.Whole<String>
{
    private static final Logger LOG = Logger.getLogger(OmniSocket.class.getName());
    private Session session;
    private RemoteEndpoint.Async remote;    
    private SocketWriter writer;

    @Override
    public void onOpen(Session session, EndpointConfig config) {
        this.session = session;
        this.remote = this.session.getAsyncRemote();
        LOG.info("WebSocket Connect: " + session);
        
        MessageHelper msg = new MessageHelper("conn_ack");
        msg.appendStringData("message", "You are now connected to " + this.getClass().getName());  
        this.remote.sendText(msg.getJson());
        //this.remote.sendText("You are now connected to " + this.getClass().getName());
        // attach echo message handler
        session.addMessageHandler(this);
                
        writer = SocketWriter.SocketWriterBuilder.createSocketWriter(session, remote);
        TaskFactory.getInstance().setWriter(writer);
        writer.start();
        
    }
    
    @Override
    public void onClose(Session session, CloseReason close)
    {
        super.onClose(session,close);
        this.session = null;
        this.remote = null;
        //MessageProcessor.getInstance(null);
        if (writer != null){
            writer.interrupt();
        }
        
        LOG.log(Level.INFO,"WebSocket Close: {0} - {1}",new Object[] {close.getCloseCode(),close.getReasonPhrase()});
    }
    
    @Override
    public void onError(Session session, Throwable cause)
    {
        super.onError(session,cause);
        LOG.log(Level.WARNING, "WebSocket Error {0}",cause);
    }    

    @Override
    public void onMessage(String message) {
        LOG.log(Level.INFO, "message received: [{}]",message);
                
        MessageProcessor.getInstance().processMessage(message);
        /*
        if (this.session != null && this.session.isOpen() && this.remote != null)
        {
            this.remote.sendText(message);
        }
        */
    }
    
}
