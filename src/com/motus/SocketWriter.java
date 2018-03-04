package com.motus;

import java.util.concurrent.BlockingQueue;
import java.util.concurrent.LinkedBlockingQueue;
import java.util.logging.Level;
import java.util.logging.Logger;
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
public final class SocketWriter extends Thread {
    private final BlockingQueue<String> queue;
    private final Session session;
    private final RemoteEndpoint.Async remote;
    private final Object lock;

    private SocketWriter(final Session session, final RemoteEndpoint.Async remote) {
        queue = new LinkedBlockingQueue<String>();
        this.session = session;
        this.remote = remote;
        lock = new Object();
    }
    
    public void enqueueMessage(String msg) {
        if (queue != null) {
            synchronized (lock) {
                try {
                    queue.put(msg);
                } catch (InterruptedException ex) {
                    Logger.getLogger(SocketWriter.class.getName()).log(Level.SEVERE, null, ex);
                }
            }
        }
    }
    
    @Override
    public void run() {
        boolean notEOLReceived = true;
        try {
            while (notEOLReceived) {
                if (queue != null) {
                    String msg = queue.take();
                    sendMessage(msg);
                }
            }
        } catch (InterruptedException ex) {
            Logger.getLogger(SocketWriter.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

    private void sendMessage(final String message){
        if (this.session != null && this.session.isOpen() && this.remote != null)
        {
            this.remote.sendText(message);
        }
    }

    public static class SocketWriterBuilder {        
        public static SocketWriter createSocketWriter(Session session,RemoteEndpoint.Async remote) {
            return new SocketWriter(session, remote);
        }
    }
}
