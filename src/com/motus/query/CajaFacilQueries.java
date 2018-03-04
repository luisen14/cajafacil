/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.query;

import static com.motus.Constants.DB_CONN_STR;
import com.motus.PortableServer;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.logging.Level;
import java.util.logging.Logger;


/**
 *
 * @author lsencion
 */
public class CajaFacilQueries {
    private static final Logger LOG = Logger.getLogger(CajaFacilQueries.class.getName());
    
    private final static String TicketsPreciosQry = "SELECT fkCliente, fkListaPrecios FROM tickets t LEFT JOIN clientes c ON (t.fkCliente=c.ID) WHERE t.ID='{0}'";
            
    public static ResultadosQry queryTicketClientes(final String ticketId){        
        String qry = String.format(TicketsPreciosQry, ticketId);
        LOG.log(Level.INFO, qry);
        return query(qry);        
    }
    
    private final static String ConfigCajaQry = "SELECT ifnull(BehaveVendor,0) BehaveVendor,LimAlert, LimOper, BloqLimOper, reanudar FROM configcaja WHERE fkCaja=%d";

    public static ResultadosQry queryConfigCaja(final int cajaKey){        
        String qry = String.format(ConfigCajaQry, cajaKey);
        LOG.log(Level.INFO, qry);
        return query(qry);        
    }    
    
    
    public static ResultadosQry query(final String qry){
        Connection connection = null;
        ResultadosQry result = null;
        try {
            // create a database connection
            connection = DriverManager.getConnection(DB_CONN_STR);
            Statement statement = connection.createStatement();

            ResultSet rs = statement.executeQuery(qry);
            result = new ResultadosQry(rs);
            
        } catch (SQLException e) {
            LOG.log(Level.SEVERE, qry, e);
        } finally {
            try {
                if (connection != null) {
                    connection.close();
                }
            } catch (SQLException e) {
                // connection close failed.
                LOG.log(Level.INFO, qry, e);
            }
        } 
        return result;
    }
    
}
