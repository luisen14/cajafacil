/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.logging;

import static com.motus.Constants.APPLICATION_NAME;
import static com.motus.Constants.LOGS_DIRECTORY;
import java.io.IOException;
import java.util.logging.FileHandler;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.util.logging.SimpleFormatter;

/**
 *
 * @author lsencion
 */
public class ParentLogger {
    private final static Logger LOG = Logger.getLogger("com");
        
    public static void setupLogger(){
        try {
            FileHandler fh = new RollingFileHandler(LOGS_DIRECTORY + "/" + APPLICATION_NAME);
            
            LOG.addHandler(fh);
            fh.setFormatter(new SimpleFormatter());
            
            //Do not use the parent logger and hence, the console.
            LOG.setUseParentHandlers(false);
            LOG.info(APPLICATION_NAME + " parent logger booted up");            
        } catch (IOException ex) {
            LOG.log(Level.SEVERE, null, ex);
        }
    }

    /**
     * @return the LOG
     */
    public static Logger getLOG() {
        return LOG;
    }
}
