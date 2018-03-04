/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.taskhandler;

import com.motus.SocketWriter;
import com.motus.task.Task;
import com.motus.task.TaskDefinition;
import com.motus.util.MessageHelper;
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.print.PrintService;
import javax.print.PrintServiceLookup;

/**
 *
 * @author lsencion
 */
@TaskDefinition(eventID = "get_printers")
public class QueryPrinterTask extends Task {
    private static Logger LOG = Logger.getLogger(QueryPrinterTask.class.getName());

    public QueryPrinterTask(SocketWriter writer) {
        super(writer);
    }

    @Override
    public void executeTask() {
        List<String> aPrinters = new ArrayList<>();
        PrintService[] allPrintServices = PrintServiceLookup.lookupPrintServices(null, null);
        String[] printerNames = (allPrintServices != null && allPrintServices.length > 0)
                ? new String[allPrintServices.length] : new String[0];
        for (int i = 0; i < allPrintServices.length; i++) {
            printerNames[i] = allPrintServices[i].getName();
            aPrinters.add(printerNames[i]);
        }
        
        MessageHelper msg = new MessageHelper("printers");
        msg.appendArrayData("values", aPrinters);
        LOG.log(Level.INFO, "Sending printers name");
        writer.enqueueMessage(msg.getJson());
    }
    
}
