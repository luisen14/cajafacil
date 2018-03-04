package com.motus.util;

import java.util.Timer;
import java.util.TimerTask;

import com.motus.SocketWriter;
import jssc.SerialPort;
import jssc.SerialPortException;
import java.util.logging.Level;
import java.util.logging.Logger;

public class ScaleReader extends TimerTask implements Sleeper {
    private static Logger LOG = Logger.getLogger(ScaleReader.class.getName());
    private static  ScaleReader INSTANCE;
    private SerialPortParameters scaleConfig;
    private String keyWord;
    private SerialCommunicator scaleSerialComm;

    private final boolean scaleReadingArrived;
    private String scaleReading;
    private Timer timer;

    private static final int POLLING_INTERVAL = 2000;

    private final Object lock = new Object();
    private SocketWriter writer;

    private String senderTabId;

    private ScaleReader(){
        scaleReadingArrived = false;
    }

    public static ScaleReader getInstance(SocketWriter writer,
                                          String sTabId,
                                          String sPort,
                                          String sBraud,
                                          String sStopBits,
                                          String sParity,
                                          String sFlowControl,
                                          String sDataBits,
                                          String DataAscii,
                                          boolean ispolling) throws SerialPortException{
        LOG.log(Level.INFO, "Scale reader getting instance");

        if (INSTANCE == null){
            LOG.log(Level.INFO,"Setup of scale reader");
            INSTANCE = new ScaleReader();

            INSTANCE.setSenderTabId(sTabId);
            INSTANCE.setScaleConfig(init(sPort, sBraud, sStopBits, sParity, sDataBits));
            INSTANCE.setKeyWord(DataAscii);

            SerialCommunicator scaleSerialComm = SerialCommunicator.getInstance(INSTANCE);
            INSTANCE.setScaleSerialComm(scaleSerialComm);

            LOG.log(Level.INFO,"About to start serial comm");
            scaleSerialComm.initCommunication(INSTANCE.getScaleConfig());

            INSTANCE.setWriter(writer);
            if (ispolling){
                LOG.log(Level.INFO,"About to start polling");
                INSTANCE.setTimer(new Timer());
                INSTANCE.getTimer().scheduleAtFixedRate(INSTANCE, POLLING_INTERVAL, POLLING_INTERVAL);
            }
        }
        LOG.log(Level.INFO,"Returning instance of scale reader");
        return INSTANCE;
    }

    private static SerialPortParameters init(String sPort,
                                        String sBraud,
                                        String sStopBits,
                                        String sParity,
                                        String sDataBits){

        SerialPortParameters scaleConfig = new SerialPortParameters(sPort);

        scaleConfig.setDataBit(SerialPort.DATABITS_7);
        scaleConfig.setParity(SerialPort.PARITY_EVEN);

        scaleConfig.setStopBit(Integer.parseInt(sStopBits));
        scaleConfig.setBaudRate(Integer.parseInt(sBraud));
        scaleConfig.setDataBit(Integer.parseInt(sDataBits));

        int iParity = 0;
        switch (sParity) {
            case "Ninguna":
                iParity= SerialPort.PARITY_NONE;
                break;
            case "Impar":
                iParity= SerialPort.PARITY_ODD;
                break;
            case "Par":
                iParity= SerialPort.PARITY_EVEN;
                break;
            case "Marca":
                iParity= SerialPort.PARITY_MARK;
                break;
            case "Espacio":
                iParity= SerialPort.PARITY_SPACE;
                break;
        }

        scaleConfig.setParity(iParity);
        return scaleConfig;
    }

    public String readScale(){
        synchronized (lock){
            return scaleReading;
        }
    }

    public void requestScaleRead() {
        if (scaleSerialComm != null){
            try {
                getScaleSerialComm().sendMessage(keyWord);
            } catch (SerialPortException e) {
                LOG.log(Level.SEVERE,e.getMessage());
            }
        } else {
            String errMsg = "e: Scale reader is not connected to serial port";
            LOG.log(Level.SEVERE,errMsg);
        }
    }

    @Override
    public void wakeUp(String msg) {
        synchronized (lock) {
            if (msg != null && !msg.equals(scaleReading)){
                MessageHelper resMsg = new MessageHelper("scale");

                resMsg.appendStringData("result", String.valueOf(true));
                resMsg.appendStringData("tabId", senderTabId);
                resMsg.appendStringData("actionResponse", msg);
                resMsg.appendStringData("action", "scale");

                LOG.log(Level.INFO, "Enqued weight read");

                writer.enqueueMessage(resMsg.getJson());
            }
            scaleReading = msg;
        }
    }

    public void shutDown(){
        try {
            if (INSTANCE != null && INSTANCE.getTimer() != null)
                INSTANCE.getTimer().cancel();
            scaleSerialComm.terminateCommunication();
        } catch (SerialPortException e) {
            LOG.log(Level.SEVERE,"Exception caught in ScaleReader: " + e.getMessage());
        }
    }

    /**
     * @return the scaleConfig
     */
    public SerialPortParameters getScaleConfig() {
        return scaleConfig;
    }

    /**
     * @param scaleConfig the scaleConfig to set
     */
    public void setScaleConfig(SerialPortParameters scaleConfig) {
        this.scaleConfig = scaleConfig;
    }

    /**
     * @return the keyWord
     */
    public String getKeyWord() {
        return keyWord;
    }

    /**
     * @param keyWord the keyWord to set
     */
    public void setKeyWord(String keyWord) {
        this.keyWord = keyWord;
    }

    @Override
    public void run() {
        try {
            //HLog.msg("Polling...");
            getScaleSerialComm().sendMessage(keyWord);
        } catch (SerialPortException ex) {
            LOG.log(Level.SEVERE,"Exception caught in ScaleReader timer: " + ex.getMessage());
            //Logger.getLogger(ScaleReader.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

    /**
     * @return the scaleSerialComm
     */
    public SerialCommunicator getScaleSerialComm() {
        return scaleSerialComm;
    }

    /**
     * @param scaleSerialComm the scaleSerialComm to set
     */
    public void setScaleSerialComm(SerialCommunicator scaleSerialComm) {
        this.scaleSerialComm = scaleSerialComm;
    }

    /**
     * @return the timer
     */
    public Timer getTimer() {
        return timer;
    }

    /**
     * @param timer the timer to set
     */
    public void setTimer(Timer timer) {
        this.timer = timer;
    }

    private String makeResponse(String tabId, String action, String actionResponse, boolean result) {
        return String.format("{\"tabID\":\"%s\",\"action\":\"%s\",\"results\":%s,\"success\":\"%b\"}", tabId, action, actionResponse, result);
    }
    

    /**
     * @return the senderTabId
     */
    public String getSenderTabId() {
        return senderTabId;
    }

    /**
     * @param senderTabId the senderTabId to set
     */
    public void setSenderTabId(String senderTabId) {
        this.senderTabId = senderTabId;
    }

    public void setWriter(SocketWriter writer) {
        this.writer = writer;
    }
}
