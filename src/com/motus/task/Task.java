package com.motus.task;

import com.motus.SocketWriter;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author lsencion
 */
public abstract class Task {
    protected final SocketWriter writer;
    private String data;
    
    public Task(SocketWriter writer){
        this.writer = writer;
    }
    
    protected void write(String msg){
        writer.enqueueMessage(msg);
    }
    
    public abstract void executeTask();

    /**
     * @return the data
     */
    public String getData() {
        return data;
    }

    /**
     * @param data the data to set
     */
    public void setData(String data) {
        this.data = data;
    }
}
