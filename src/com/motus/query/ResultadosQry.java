/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.query;

import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 *
 * @author lsencion
 */
    public class ResultadosQry {
        private Map<String, Integer> columnMap;
        private List<List<Object>> rowData;
        private int totCols; 
        
        private ResultadosQry(){
            columnMap = new HashMap<String, Integer>();
            rowData = new ArrayList<List <Object>>();
        }
        
        public ResultadosQry(final ResultSet rs) throws SQLException{
            this();
            ResultSetMetaData rsmd = rs.getMetaData();
            int i = 0;            
            for (i = 1; i <= rsmd.getColumnCount(); i++) 
                columnMap.put(rsmd.getColumnName(i),i-1);            
            totCols = i;
            while (rs.next()) {
                List<Object> data = new ArrayList<>();
                for (int j = 1; j <= rsmd.getColumnCount(); j++) 
                    data.add(rs.getObject(j));
                rowData.add(data);
            }
        }
        
        public int size(){
            return rowData.size();
        }
        
        public Object getData(final int col, final int row){
            if (row < 0 || col < 0)
                throw new IllegalArgumentException("Row or col can not be negative");
            if (row > rowData.size())
                throw new IllegalArgumentException("Row number exceeds existing rows");
            
            if (col > totCols)
                throw new IllegalArgumentException("Col number exceeds existing cols");
            
            ArrayList<Object> rowObject = (ArrayList<Object>) rowData.get(row);
            return rowObject.get(col);
        }
        
        public Object getData(final String colName, final int row){
            if (columnMap.containsKey(colName))
                return getData(columnMap.get(colName), row);
            else
                throw new IllegalArgumentException("Column name given doesn't exist");
        }  
        
        public String getString(final String colName, final int row){
            return getData(colName, row).toString();
        }
        
        public int getInteger(final String colName, final int row){                        
            return Integer.parseInt(getData(colName, row).toString());
        }     
        
        public double getDouble(final String colName, final int row){                        
            return Double.parseDouble(getData(colName, row).toString());
        }        
        
    }
