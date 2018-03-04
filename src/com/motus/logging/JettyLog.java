/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.logging;

import java.util.logging.Level;
import org.eclipse.jetty.util.log.Logger;
/**
 *
 * @author lsencion
 */
public class JettyLog implements Logger {
    private static java.util.logging.Logger LOG = java.util.logging.Logger.getLogger(JettyLog.class.getName());
    
    @Override public String getName() { return "JettyLog"; }
    
    @Override public void warn(String msg, Object... args) { 
        LOG.log(Level.WARNING, msg, args);
    }
    @Override public void warn(Throwable thrown) { 
        LOG.log(Level.WARNING, "", thrown);
    }
    @Override public void warn(String msg, Throwable thrown) { 
        LOG.log(Level.WARNING, msg, thrown);
    }
    @Override public void info(String msg, Object... args) { 
        LOG.log(Level.INFO, msg, args);
    }
    @Override public void info(Throwable thrown) { 
        LOG.log(Level.INFO, "", thrown);
    }
    @Override public void info(String msg, Throwable thrown) { 
        LOG.log(Level.INFO, msg, thrown);
    }
    @Override public boolean isDebugEnabled() { return false; }
    @Override public void setDebugEnabled(boolean enabled) { }
    @Override public void debug(String msg, Object... args) { 
        LOG.log(Level.FINER, msg, args);
    }
    @Override public void debug(Throwable thrown) { 
        LOG.log(Level.FINER, "", thrown);
    }
    @Override public void debug(String msg, Throwable thrown) { 
        LOG.log(Level.FINER, msg, thrown);
    }
    @Override public Logger getLogger(String name) { return this; }
    @Override public void ignore(Throwable ignored) { }
    @Override public void debug(String string, long l) { }
}