package com.motus;

import com.motus.task.Task;
import com.motus.task.TaskFactory;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedHashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.logging.Level;
import java.util.logging.Logger;
import org.json.simple.JSONObject;
import org.json.simple.JSONValue;
import org.json.simple.parser.ContainerFactory;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author lsencion
 */
public final class MessageProcessor {
    private static Logger LOG = Logger.getLogger(MessageProcessor.class.getName());
    
    private static final MessageProcessor INSTANCE = new MessageProcessor();
    private final ExecutorService executor;
    private final int MAX_WORKERS = 5;
    
    public static final String EVENT_TAG = "event";
    public static final String DATA_TAG = "data";
    
    private MessageProcessor(){
        executor = Executors.newFixedThreadPool(MAX_WORKERS);
    }
    
    public static final MessageProcessor getInstance(){
        return INSTANCE;
    }
    
    public final void processMessage(final String message){
        TaskProcessor task = new TaskProcessor(message);
        executor.execute(task);
    }
    
    final class TaskProcessor implements Runnable {           
        private final String message;
        private Map<String, String> taskMap;
                
        /**
         * 
         * @param message 
         */
        public TaskProcessor(final String message){
            this.message = message;
        }

        @Override
        public void run() {
            parseRequestMessage();
            processRequest();
        }

        /**
         * 
         */
        private void parseRequestMessage() {
            try {
                Object obj = JSONValue.parse(message);
                String sKey = (String) ((JSONObject)obj).get(EVENT_TAG);
                //JSONObject obj2 = (JSONObject) ((JSONObject)obj).get(EVENT_TAG);
                
                JSONObject oData = (JSONObject) ((JSONObject)obj).get(DATA_TAG);
                //System.out.println("data -> " + oData.toJSONString());
                taskMap = new HashMap<>();
                taskMap.put(EVENT_TAG, sKey);
                taskMap.put(DATA_TAG, oData.toJSONString());
                
                /*
                I don't know why someone would use this complicated form:
                
                JSONParser parser = new JSONParser();
                
                Map json = (Map) parser.parse(message, new ContainerFactory() {
                    public List creatArrayContainer() {
                        return new LinkedList();
                    }

                    public Map createObjectContainer() {
                        return new LinkedHashMap();
                    }
                });
                Iterator iter = json.entrySet().iterator();
                taskMap = new HashMap<>();
                while (iter.hasNext()) {
                    Map.Entry entry = (Map.Entry) iter.next();
                    taskMap.put(entry.getKey().toString(), entry.getValue().toString());
                }
                */
            } catch (Exception ex) {
                Logger.getLogger(TaskProcessor.class.getName()).log(Level.SEVERE, null, ex);
                //Something is wrong, signal to skip processing the bogus message.
                taskMap = null;
            }
        }

        /**
         * 
         */
        private final void processRequest() {
            if (taskMap != null && taskMap.containsKey(EVENT_TAG)){
                String event_id = taskMap.get(EVENT_TAG);
                
                Task task = TaskFactory.getInstance().makeTask(event_id);
                if (task != null){
                    if (taskMap.containsKey(DATA_TAG)){                        
                        Logger.getLogger(TaskProcessor.class.getName()).log(Level.INFO, "Setting data in task");
                        task.setData(taskMap.get(DATA_TAG));
                    }
                    task.executeTask();
                } else {
                    Logger.getLogger(TaskProcessor.class.getName()).log(Level.WARNING, "event ID not found");
                }
            } else {
                Logger.getLogger(TaskProcessor.class.getName()).log(Level.WARNING, "event field not found");
            }
        }        
    }
    

}
