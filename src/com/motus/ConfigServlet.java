package com.motus;


import static com.motus.Constants.LOCAL_URL_KEY;
import static com.motus.Constants.REMOTE_URL_KEY;
import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author lsencion
 */
public class ConfigServlet extends HttpServlet {        
    @Override
    public void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
    {
        response.setContentType("text/html");
        response.setStatus(HttpServletResponse.SC_OK);
        //response.getWriter().println("var test = '1000';");
        
        response.getWriter().printf("var localAddress = '%s';",getServletConfig().getInitParameter(LOCAL_URL_KEY));
        response.getWriter().printf("var remoteAddress = '%s';",getServletConfig().getInitParameter(REMOTE_URL_KEY));
        
        //response.getWriter().println("session=" + request.getSession(true).getId());
    }
}
