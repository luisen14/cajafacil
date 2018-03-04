/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus;

import static com.motus.Constants.APPLICATION_NAME;
import static com.motus.Constants.LOCAL_URL_KEY;
import static com.motus.Constants.PORT_KEY;
import static com.motus.Constants.PRODUCTION_MODE_KEY;
import static com.motus.Constants.PROPERTIES_FILE;
import static com.motus.Constants.REMOTE_URL_KEY;
import static com.motus.Constants.VERSION_KEY;
import static com.motus.Constants.WEBROOT_INDEX_KEY;
import static com.motus.Constants.WSPATH_KEY;
import com.motus.logging.JettyLog;
import com.motus.logging.ParentLogger;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.URLClassLoader;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.servlet.ServletException;
import javax.websocket.DeploymentException;
import javax.websocket.server.ServerContainer;
import javax.websocket.server.ServerEndpointConfig;
import org.apache.tomcat.InstanceManager;
import org.apache.tomcat.SimpleInstanceManager;
import org.eclipse.jetty.annotations.ServletContainerInitializersStarter;
import org.eclipse.jetty.apache.jsp.JettyJasperInitializer;
import org.eclipse.jetty.jsp.JettyJspServlet;
import org.eclipse.jetty.plus.annotation.ContainerInitializer;
import org.eclipse.jetty.server.ConnectionFactory;
import org.eclipse.jetty.server.Server;
import org.eclipse.jetty.server.ServerConnector;
import org.eclipse.jetty.servlet.DefaultServlet;
import org.eclipse.jetty.servlet.ServletHolder;
import org.eclipse.jetty.webapp.WebAppContext;
import org.eclipse.jetty.websocket.jsr356.server.deploy.WebSocketServerContainerInitializer;

/**
 *
 * @author lsencion
 */
public class PortableServer {
    private static final Logger LOG = Logger.getLogger(PortableServer.class.getName());
    private static Properties properties;
    
    private int port;
    private Server server;
    private URI serverURI;
    
    public static void main(String[] args) throws Exception{
        org.eclipse.jetty.util.log.Log.setLog(new JettyLog());
        ParentLogger.setupLogger(); 
        
        loadProperties();        
        ParentLogger.getLOG().setUseParentHandlers(!isProductionMode());
        
        LOG.log(Level.INFO, "Starting: {0} Version: {1} in production mode: {2}",new Object[]{APPLICATION_NAME,properties.getProperty(VERSION_KEY),properties.getProperty(PRODUCTION_MODE_KEY)});
        LOG.log(Level.INFO, "Starting listener in port: {0}",properties.getProperty(PORT_KEY));
        int port = Integer.parseInt(properties.getProperty(PORT_KEY));
        
        PortableServer pServer = new PortableServer(port);
        pServer.start();
        pServer.waitForInterrupt();
    }
    
    public PortableServer(int port){
        this.port = port;
    }
    
    public void start() throws Exception
    {
        server = new Server();
        ServerConnector connector = connector();
        server.addConnector(connector);

        URI baseUri = getWebRootResourceUri();

        // Set JSP to use Standard JavaC always
        System.setProperty("org.apache.jasper.compiler.disablejsr199", "false");

        WebAppContext webAppContext = getWebAppContext(baseUri, getScratchDir());

        server.setHandler(webAppContext);                
        enableWebSockets(webAppContext);

        // Start Server
        server.start();

        // Show server state
        if (LOG.isLoggable(Level.FINE))
        {
            LOG.fine(server.dump());
        }
        this.serverURI = getServerUri(connector);
    }   
    
    private void enableWebSockets(WebAppContext webAppContext) throws ServletException, DeploymentException{
        ServerContainer container = WebSocketServerContainerInitializer.configureContext(webAppContext);
        String wspath = properties.getProperty(WSPATH_KEY);
        LOG.log(Level.INFO, "WS Path: {0}",wspath);
        
        ServerEndpointConfig echoConfig = ServerEndpointConfig.Builder.create(OmniSocket.class,wspath).build();
        container.addEndpoint(echoConfig);
    }
    
    private ServerConnector connector()
    {
        ServerConnector connector = new ServerConnector(server);
        connector.setPort(port);
        return connector;
    }    
    
    private URI getWebRootResourceUri() throws FileNotFoundException, URISyntaxException
    {
        String webRootIndex = "/" + properties.getProperty(WEBROOT_INDEX_KEY) + "/";
        URL indexUri = this.getClass().getResource(webRootIndex);
        if (indexUri == null)
        {
            throw new FileNotFoundException("Unable to find resource " + webRootIndex);
        }
        // Points to wherever /webroot/ (the resource) is
        return indexUri.toURI();
    }   
    
    /**
     * Establish Scratch directory for the servlet context (used by JSP compilation)
     */
    private File getScratchDir() throws IOException
    {
        File tempDir = new File(System.getProperty("java.io.tmpdir"));
        File scratchDir = new File(tempDir.toString(), "embedded-jetty-jsp");

        if (!scratchDir.exists())
        {
            if (!scratchDir.mkdirs())
            {
                throw new IOException("Unable to create scratch directory: " + scratchDir);
            }
        }
        return scratchDir;
    }    
    
    /**
     * Setup the basic application "context" for this application at "/"
     * This is also known as the handler tree (in jetty speak)
     */
    private WebAppContext getWebAppContext(URI baseUri, File scratchDir) throws ServletException, DeploymentException
    {
        WebAppContext context = new WebAppContext();
        context.setContextPath("/");
        context.setAttribute("javax.servlet.context.tempdir", scratchDir);
        context.setAttribute("org.eclipse.jetty.server.webapp.ContainerIncludeJarPattern",
          ".*/[^/]*servlet-api-[^/]*\\.jar$|.*/javax.servlet.jsp.jstl-.*\\.jar$|.*/.*taglibs.*\\.jar$");
        context.setResourceBase(baseUri.toASCIIString());
        context.setAttribute("org.eclipse.jetty.containerInitializers", jspInitializers());
        context.setAttribute(InstanceManager.class.getName(), new SimpleInstanceManager());
        context.addBean(new ServletContainerInitializersStarter(context), true);
        context.setClassLoader(getUrlClassLoader());
                                
        //Add default servlet
        context.addServlet(jspServletHolder(), "*.jsp");
        // Add Application Servlets
        
        context.addServlet(configServletHolder(), "/js/config/settings");
        
        //context.addServlet(DateServlet.class, "/date/");

        //context.addServlet(exampleJspFileMappedServletHolder(), "/test/foo/");
        context.addServlet(defaultServletHolder(baseUri), "/");
        return context;
    }  
    
    /**
     * Ensure the jsp engine is initialized correctly
     */
    private List<ContainerInitializer> jspInitializers()
    {
        JettyJasperInitializer sci = new JettyJasperInitializer();
        ContainerInitializer initializer = new ContainerInitializer(sci, null);
        List<ContainerInitializer> initializers = new ArrayList<ContainerInitializer>();
        initializers.add(initializer);
        return initializers;
    }    
    
    private ServletHolder configServletHolder(){
        ServletHolder configServletHolder = new ServletHolder("config",new ConfigServlet());
        configServletHolder.setInitParameter(LOCAL_URL_KEY, properties.getProperty(LOCAL_URL_KEY));
        configServletHolder.setInitParameter(REMOTE_URL_KEY, properties.getProperty(REMOTE_URL_KEY));
        return configServletHolder;
    }
    
    /**
     * Set Classloader of Context to be sane (needed for JSTL)
     * JSP requires a non-System classloader, this simply wraps the
     * embedded System classloader in a way that makes it suitable
     * for JSP to use
     */
    private ClassLoader getUrlClassLoader()
    {
        ClassLoader jspClassLoader = new URLClassLoader(new URL[0], this.getClass().getClassLoader());
        return jspClassLoader;
    }    
    
    /**
     * Create JSP Servlet (must be named "jsp")
     */
    private ServletHolder jspServletHolder()
    {
        ServletHolder holderJsp = new ServletHolder("jsp", JettyJspServlet.class);
        holderJsp.setInitOrder(0);
        holderJsp.setInitParameter("logVerbosityLevel", "DEBUG");
        holderJsp.setInitParameter("fork", "false");
        holderJsp.setInitParameter("xpoweredBy", "false");
        holderJsp.setInitParameter("compilerTargetVM", "1.8");
        holderJsp.setInitParameter("compilerSourceVM", "1.8");
        holderJsp.setInitParameter("keepgenerated", "true");
        return holderJsp;
    }    
    

    /**
     * Create Default Servlet (must be named "default")
     */
    private ServletHolder defaultServletHolder(URI baseUri)
    {
        ServletHolder holderDefault = new ServletHolder("default", DefaultServlet.class);
        LOG.info("Base URI: " + baseUri);
        holderDefault.setInitParameter("resourceBase", baseUri.toASCIIString());
        holderDefault.setInitParameter("dirAllowed", "true");
        return holderDefault;
    }    
    
    /**
     * Establish the Server URI
     */
    private URI getServerUri(ServerConnector connector) throws URISyntaxException
    {
        String scheme = "http";
        for (ConnectionFactory connectFactory : connector.getConnectionFactories())
        {
            if (connectFactory.getProtocol().equals("SSL-http"))
            {
                scheme = "https";
            }
        }
        String host = connector.getHost();
        if (host == null)
        {
            host = "localhost";
        }
        int port = connector.getLocalPort();
        serverURI = new URI(String.format("%s://%s:%d/", scheme, host, port));
        LOG.info("Server URI: " + serverURI);
        return serverURI;
    }   
    
    public void stop() throws Exception
    {
        server.stop();
    }

    /**
     * Cause server to keep running until it receives a Interrupt.
     * <p>
     * Interrupt Signal, or SIGINT (Unix Signal), is typically seen as a result of a kill -TERM {pid} or Ctrl+C
     * @throws InterruptedException if interrupted
     */
    public void waitForInterrupt() throws InterruptedException
    {
        server.join();
    }    
    
    private static boolean isProductionMode(){
        return Boolean.parseBoolean(properties.getProperty(PRODUCTION_MODE_KEY));        
    }
    
    private static void loadProperties(){
        properties = new Properties();
        
        LOG.log(Level.INFO, "Loading properties file ",PROPERTIES_FILE);
        InputStream configFileStream = null;
        try {
            configFileStream = new FileInputStream(PROPERTIES_FILE); 
        } catch (FileNotFoundException ex) {
            Logger.getLogger(PortableServer.class.getName()).log(Level.SEVERE, null, ex);
            System.exit(1);
        }
        
        if (configFileStream != null){
            try {
                properties.load(configFileStream);                
            } catch (IOException ex) {
                Logger.getLogger(PortableServer.class.getName()).log(Level.SEVERE, null, ex);
                System.exit(1);
            } finally {
                try {
                    configFileStream.close();
                } catch (IOException ex) {
                    Logger.getLogger(PortableServer.class.getName()).log(Level.SEVERE, null, ex);
                }
            }
        }        
    }    
    
    
}
