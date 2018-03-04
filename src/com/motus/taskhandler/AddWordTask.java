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
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.logging.Level;
import java.util.logging.Logger;
import org.json.simple.JSONObject;
import org.json.simple.JSONValue;

/**
 *
 * @author lsencion
 */
@TaskDefinition(eventID = "add_word")
public class AddWordTask extends Task {
    private static Logger LOG = Logger.getLogger(AddWordTask.class.getName());
    
    public AddWordTask(SocketWriter writer) {
        super(writer);
    }

    @Override
    public void executeTask() {
        String ldata = this.getData();
        Object obj = JSONValue.parse(ldata);
        String sWord = (String) ((JSONObject)obj).get("word");
        LOG.log(Level.INFO, "WORD -> " + sWord);
        
        boolean res = storeWord(sWord);
        
        MessageHelper msg = new MessageHelper("add_word_result");
        msg.appendStringData("result", String.valueOf(res));
        LOG.log(Level.INFO, "Sending result of storing word");
        writer.enqueueMessage(msg.getJson());
    }
    
    private boolean storeWord(String sWord){
        Connection connection = null;
        boolean result = false;
        try {
            // create a database connection
            connection = DriverManager.getConnection("jdbc:sqlite:sample.db");
            Statement statement = connection.createStatement();
            statement.setQueryTimeout(30);  // set timeout to 30 sec.

            statement.execute("insert into anagram values(NULL,'"+sWord+"')");
            result = true;
        } catch (SQLException e) {
            // if the error message is "out of memory", 
            // it probably means no database file is found
            LOG.log(Level.SEVERE, "Exception trapped", e);
        } finally {
            try {
                if (connection != null) {
                    connection.close();
                }
            } catch (SQLException e) {
                // connection close failed.
                LOG.log(Level.SEVERE, "Exception trapped", e);                
            }
        }
        return result;
    }
    
}
