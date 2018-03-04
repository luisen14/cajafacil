package com.motus.taskhandler;

import com.motus.SocketWriter;
import com.motus.task.Task;
import com.motus.task.TaskDefinition;
import com.motus.util.ScaleReader;
import jssc.SerialPortException;
import org.json.simple.JSONObject;
import org.json.simple.JSONValue;

import java.util.logging.Level;
import java.util.logging.Logger;

@TaskDefinition(eventID = "Scale")
public class ScaleReaderTask extends Task {
    private static Logger LOG = Logger.getLogger(ScaleReaderTask.class.getName());
    private static ScaleReader reader;

    String tabId;
    String port;
    String braud;
    String stopBits;
    String parity;
    String flowControl;
    String dataBits;
    String dataAscii;
    boolean polling;

    public ScaleReaderTask(SocketWriter writer){
        super(writer);
    }

    @Override
    public void executeTask() {
        String data = this.getData();

        LOG.log(Level.INFO, "in scale reading task");
        try {
            JSONObject message = (JSONObject)JSONValue.parse(data);

            tabId = (String) message.get("sTabId");
            port = (String) message.get("sPort");
            braud = (String) message.get("sBraud");
            stopBits = (String) message.get("sStopBits");
            parity = (String) message.get("sParity");
            flowControl = (String) message.get("sFlowControl");
            dataBits = (String) message.get("sDataBits");
            dataAscii = (String) message.get("DataAssci");
            polling = Boolean.valueOf((String) message.get("ispolling"));
        } catch (Exception e){
            LOG.log(Level.SEVERE, "Exception parsing scale date " + e);
        }

        if (reader == null){

            try {
                reader = ScaleReader.getInstance(this.writer, tabId, port, braud, stopBits, parity, port, dataBits, dataAscii, polling);
                reader.setSenderTabId(tabId);
            } catch (SerialPortException e) {
                LOG.log(Level.SEVERE, "Exception while initializing scale reader " + e);
            }

        } else {
            reader.setSenderTabId(tabId);
        }

        if (!polling){
            LOG.log(Level.INFO, "requesting new reading");
            reader.requestScaleRead();
        }

    }
}
