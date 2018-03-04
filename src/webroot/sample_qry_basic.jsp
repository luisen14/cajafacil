<%@ page contentType="text/html" %>
<%@ page import="java.sql.*" %>
<%@ page import="java.util.logging.*" %>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SQLite Demo</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>fkCaja</th>
                    <th>fkTipoPago</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
            <%
                Logger LOG = Logger.getLogger("sample_write.jsp");
                
                Connection connection = null;
                try {
                    // create a database connection
                    connection = DriverManager.getConnection("jdbc:sqlite:cajafacil.db");
                    Statement statement = connection.createStatement();

                    ResultSet rs = statement.executeQuery("select * from contenidocaja");

                    while (rs.next()) {
                            out.println("<tr>");
                            out.println("<td>" + rs.getInt("fkCaja") + "</td>");
                            out.println("<td>" + rs.getInt("fkTipoPago") + "</td>");
                            out.println("<td>" + rs.getDouble("Valor") + "</td>");
                            out.println("</tr>");
                    }
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
            %>
            </tbody>
        </table>
    </body>
</html>
