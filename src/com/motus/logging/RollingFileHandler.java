/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.logging;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.logging.FileHandler;

/**
 *
 * @author lsencion
 */
public class RollingFileHandler extends FileHandler {
    public RollingFileHandler(String appName) throws IOException {        
        super(appName+"_"+getFileTimeStampName()+".log",0,1,true);        
    }
    
    private static String getFileTimeStampName(){
        return new SimpleDateFormat("yyyyMMdd_HHmmss").format(new Date(System.currentTimeMillis()));
    }
}
