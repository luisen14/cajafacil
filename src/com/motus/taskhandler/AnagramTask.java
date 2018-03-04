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
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author lsencion
 */
@TaskDefinition(eventID = "get_anagrams")
public class AnagramTask extends Task {
    private static Logger LOG = Logger.getLogger(AnagramTask.class.getName());
    
    public AnagramTask(SocketWriter writer) {
        super(writer);
    }

    @Override
    public void executeTask() {
        MessageHelper msg = new MessageHelper("anagrams");
        msg.appendArrayData("values", fetchAnagrams());
        LOG.log(Level.INFO, "Sending anagrams fetched from DB");
        writer.enqueueMessage(msg.getJson());
    }
    
    private List<String> fetchAnagrams(){
        ArrayList<String> anagrams = new ArrayList<>();
        
        Connection connection = null;
        try
        {
          // create a database connection
          connection = DriverManager.getConnection("jdbc:sqlite:sample.db");
          Statement statement =  connection.createStatement();
          statement.setQueryTimeout(30);  // set timeout to 30 sec.
          
          ResultSet rs = statement.executeQuery("select * from anagram");
          while(rs.next())
          {
              anagrams.add(rs.getString("name"));
          }
        }
        catch(SQLException e)
        {
          // if the error message is "out of memory", 
          // it probably means no database file is found
          System.err.println(e.getMessage());
        }
        finally
        {
          try
          {
            if(connection != null)
              connection.close();
          }
          catch(SQLException e)
          {
            // connection close failed.
            System.err.println(e);
          }
        }

        
        return anagrams;
    }
    
}
