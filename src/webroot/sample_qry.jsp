<%@ page contentType="text/html" %>
<%@ page import="java.util.logging.*" %>
<%@ page import="com.motus.query.CajaFacilQueries" %>
<%@ page import="com.motus.query.ResultadosQry" %>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SQL Query Demo</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>fkCliente</th>
                    <th>fkListaPrecios</th>
                </tr>
            </thead>
            <tbody>
            <%                
                ResultadosQry result = CajaFacilQueries.queryTicketClientes("1");
                
                if (result != null){
                    for (int i = 0; i < result.size(); i++){
                        out.println("<tr>");
                        out.println("<td>" + result.getInteger("fkCliente", i) + "</td>");
                        out.println("<td>" + result.getInteger("fkListaPrecios", i) + "</td>");
                        out.println("</tr>");                                                
                    }
                }
            %>
            <br>
                        <thead>
                <tr>
                    <th>BehaveVendor</th>
                    <th>LimAlert</th>
                    <th>LimOper</th>
                    <th>BloqLimOper</th>
                    <th>reanudar</th>
                </tr>
            </thead>
            <tbody>
            <%
                ResultadosQry resultCaja = CajaFacilQueries.queryConfigCaja(1);
                
                if (resultCaja != null){
                    for (int i = 0; i < resultCaja.size(); i++){
                        out.println("<tr>");
                        out.println("<td>" + resultCaja.getString("BehaveVendor", i) + "</td>");
                        out.println("<td>" + resultCaja.getInteger("LimAlert", i) + "</td>");
                        out.println("<td>" + resultCaja.getInteger("LimOper", i) + "</td>");
                        out.println("<td>" + resultCaja.getInteger("BloqLimOper", i) + "</td>");
                        out.println("<td>" + resultCaja.getInteger("reanudar", i) + "</td>");
                        out.println("</tr>");                                                
                    }
                }
            %>
            </tbody>
        </table>
    </body>
</html>

