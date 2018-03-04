/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.taskhandler;

import com.motus.SocketWriter;
import com.motus.task.Task;
import com.motus.task.TaskDefinition;
import com.motus.util.MessageHelper;

/**
 *
 * @author lsencion
 */
@TaskDefinition(eventID = "ping")
public class TestTask extends Task {

    public TestTask(SocketWriter writer) {
        super(writer);
    }

    @Override
    public void executeTask() {
        MessageHelper msg = new MessageHelper("pong");
        msg.appendStringData("data", "pong message").appendStringData("res", "true");        
        writer.enqueueMessage(msg.getJson());
    }
    
}
