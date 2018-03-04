/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus;

/**
 *
 * @author lsencion
 */
public final class Constants {
    private Constants() {throw new AssertionError("Thou shall not instantiate me");};
    public final static String APPLICATION_NAME = "cajafacil";
    public final static String PORT_KEY = "PORT";
    public final static String WSPATH_KEY = "WSPATH";
    public final static String CLIENT_KEY = "CLIENT";
    public final static String RUN_BROWSER_CMD_KEY = "RUN_BROWSER_CMD";
    public final static String RUN_BROWSER_CMD = "\"\" http://localhost:8080/index.html";
    public final static String PROPERTIES_FILE = "config.properties";
    public final static String LOCAL_URL_KEY = "LOCAL_URL";
    public final static String REMOTE_URL_KEY = "REMOTE_URL";
    public final static String PRODUCTION_MODE_KEY = "PRODUCTION_MODE";
    public final static String VERSION_KEY = "VERSION";
    public final static String WEBROOT_INDEX_KEY = "WEBROOT_INDEX";
    
    public final static String LOGS_DIRECTORY = "logs";
    
    public final static String DB_CONN_STR = "jdbc:sqlite:cajafacil.db";
}
