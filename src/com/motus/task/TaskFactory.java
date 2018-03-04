/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.task;

import com.motus.SocketWriter;
import java.lang.annotation.Annotation;
import java.lang.reflect.AnnotatedElement;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.HashMap;
import java.util.Map;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author lsencion
 */
public class TaskFactory {
    private static final Logger LOG = Logger.getLogger(TaskFactory.class.getName());
    private static final TaskFactory INSTANCE = new TaskFactory();
    private final Map<String, Constructor<? extends Task>> tasks;
    private SocketWriter writer;
        
    static {
        LOG.log(Level.INFO, "Feeding task factory ...");
        //TaskFactory.getInstance().addTask(com.motus.taskhandler.TestTask.class);
        //TaskFactory.getInstance().addTask(com.motus.taskhandler.AnagramTask.class);
        TaskFactory.getInstance().addTask(com.motus.taskhandler.HeartBeatTask.class);
        //TaskFactory.getInstance().addTask(com.motus.taskhandler.QueryPrinterTask.class);
        //TaskFactory.getInstance().addTask(com.motus.taskhandler.AddWordTask.class);

        TaskFactory.getInstance().addTask(com.motus.taskhandler.MacAddressFinderTask.class);
        TaskFactory.getInstance().addTask(com.motus.taskhandler.ScaleReaderTask.class);
    }
    
    private TaskFactory(){
        tasks = new HashMap<String, Constructor<? extends Task>>();
    }
    
    public static TaskFactory getInstance(){
       return INSTANCE; 
    }
    
    public void addTask(Task tsk) {
        try {
            AnnotatedElement element = (AnnotatedElement) tsk;
            if (element.isAnnotationPresent(TaskDefinition.class)) {
                // getAnnotation returns Annotation type
                Annotation singleAnnotation = element.getAnnotation(TaskDefinition.class);    
                TaskDefinition definition = (TaskDefinition) singleAnnotation;
                definition.eventID();
            }
        } catch (ClassCastException ex) {
            LOG.log(Level.SEVERE, null, ex);
        }
    }
    
    public void addTask(Class<? extends Task> taskClass ) {
        try {
            if (taskClass.isAnnotationPresent(TaskDefinition.class)){
                Annotation singleAnnotation = taskClass.getAnnotation(TaskDefinition.class);
                TaskDefinition definition = (TaskDefinition) singleAnnotation;
                if (!definition.eventID().equalsIgnoreCase("Undefined")){                
                    Constructor<? extends Task> ctor = taskClass.getConstructor(SocketWriter.class);
                    //Object object = ctor.newInstance(new Object[] { writer });                
                    tasks.put(definition.eventID(), ctor);
                }            
            }
        } catch (NoSuchMethodException ex) {
            LOG.log(Level.SEVERE, null, ex);
        } catch (IllegalArgumentException ex) {
            LOG.log(Level.SEVERE, null, ex);
        } 
    }
    
    public Task makeTask(String taskID){
        if (tasks.containsKey(taskID)){
            Constructor<? extends Task> ctor = tasks.get(taskID);
            try {   
                Task tObj = ctor.newInstance(new Object[] { writer });
                return tObj;
            } catch (InstantiationException ex) {
                LOG.log(Level.SEVERE, null, ex);
                return null;
            } catch (IllegalAccessException ex) {
                LOG.log(Level.SEVERE, null, ex);
                return null;
            } catch (IllegalArgumentException ex) {
                LOG.log(Level.SEVERE, null, ex);
                return null;
            } catch (InvocationTargetException ex) {
                LOG.log(Level.SEVERE, null, ex);
                return null;
            }
        } else 
            return null;
    }

    /**
     * @param writer the writer to set
     */
    public void setWriter(SocketWriter writer) {
        this.writer = writer;
    }
    
}
