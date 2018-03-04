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
                    <th>Name</th>
                    <th>Identifier</th>
                </tr>
            </thead>
            <tbody>
            <%
                Logger LOG = Logger.getLogger("sample_write.jsp");
                
                Connection connection = null;
                try {
                    // create a database connection
                    connection = DriverManager.getConnection("jdbc:sqlite:sample.db");
                    Statement statement = connection.createStatement();
                    statement.setQueryTimeout(30);  // set timeout to 30 sec.

                    statement.executeUpdate("drop table if exists anagram");

                    statement.executeUpdate("create table anagram (ID INTEGER PRIMARY KEY ASC, NAME STRING)");

                    statement.execute("insert into anagram values(NULL,'abstraction')");
                    statement.execute("insert into anagram values(NULL,'ambiguous')");
                    statement.execute("insert into anagram values(NULL,'arithmetic')");
                    statement.execute("insert into anagram values(NULL,'backslash')");
                    statement.execute("insert into anagram values(NULL,'bitmap')");
                    statement.execute("insert into anagram values(NULL,'circumstance')");
                    statement.execute("insert into anagram values(NULL,'combination')");
                    statement.execute("insert into anagram values(NULL,'consequently')");
                    statement.execute("insert into anagram values(NULL,'consortium')");
                    statement.execute("insert into anagram values(NULL,'decrementing')");
                    statement.execute("insert into anagram values(NULL,'dependency')");
                    statement.execute("insert into anagram values(NULL,'disambiguate')");
                    statement.execute("insert into anagram values(NULL,'dynamic')");
                    statement.execute("insert into anagram values(NULL,'encapsulation')");
                    statement.execute("insert into anagram values(NULL,'equivalent')");
                    statement.execute("insert into anagram values(NULL,'expression')");
                    statement.execute("insert into anagram values(NULL,'facilitate')");
                    statement.execute("insert into anagram values(NULL,'fragment')");
                    statement.execute("insert into anagram values(NULL,'hexadecimal')");
                    statement.execute("insert into anagram values(NULL,'implementation')");
                    statement.execute("insert into anagram values(NULL,'indistinguishable')");
                    statement.execute("insert into anagram values(NULL,'inheritance')");
                    statement.execute("insert into anagram values(NULL,'internet')");
                    statement.execute("insert into anagram values(NULL,'java')");
                    statement.execute("insert into anagram values(NULL,'localization')");
                    statement.execute("insert into anagram values(NULL,'microprocessor')");
                    statement.execute("insert into anagram values(NULL,'navigation')");
                    statement.execute("insert into anagram values(NULL,'optimization')");
                    statement.execute("insert into anagram values(NULL,'parameter')");
                    statement.execute("insert into anagram values(NULL,'patrick')");
                    statement.execute("insert into anagram values(NULL,'pickle')");
                    statement.execute("insert into anagram values(NULL,'polymorphic')");
                    statement.execute("insert into anagram values(NULL,'rigorously')");
                    statement.execute("insert into anagram values(NULL,'simultaneously')");
                    statement.execute("insert into anagram values(NULL,'specification')");
                    statement.execute("insert into anagram values(NULL,'structure')");
                    statement.execute("insert into anagram values(NULL,'lexical')");
                    statement.execute("insert into anagram values(NULL,'likewise')");
                    statement.execute("insert into anagram values(NULL,'management')");
                    statement.execute("insert into anagram values(NULL,'manipulate')");
                    statement.execute("insert into anagram values(NULL,'mathematics')");
                    statement.execute("insert into anagram values(NULL,'hotjava')");
                    statement.execute("insert into anagram values(NULL,'vertex')");
                    statement.execute("insert into anagram values(NULL,'unsigned')");
                    statement.execute("insert into anagram values(NULL,'traditional')");

                    ResultSet rs = statement.executeQuery("select * from anagram");

                    while (rs.next()) {
                            out.println("<tr>");
                            out.println("<td>" + rs.getString("name") + "</td>");
                            out.println("<td>" + rs.getInt("id") + "</td>");
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

