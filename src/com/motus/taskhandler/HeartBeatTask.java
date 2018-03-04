/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.taskhandler;

import com.motus.SocketWriter;
import com.motus.task.Task;
import com.motus.task.TaskDefinition;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author lsencion
 */
@TaskDefinition(eventID = "heart_beat")
public class HeartBeatTask extends Task {
    private static Logger LOG = Logger.getLogger(HeartBeatTask.class.getName());

    public HeartBeatTask(SocketWriter writer) {
        super(writer);
    }

    @Override
    public void executeTask() {
        LOG.log(Level.INFO, "Heart beat received ...");
    }
    
}
