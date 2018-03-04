package com.motus.util;

import jssc.SerialPort;
import jssc.SerialPortEvent;
import jssc.SerialPortEventListener;
import jssc.SerialPortException;

/**
 * @author Luis Sencion
 *
 */
public class SerialCommunicator {
    //private static SerialCommunicator INSTANCE = new SerialCommunicator();

    private SerialPort serialPort;
    private Sleeper delegate;

    private SerialCommunicator() {
    }

    private SerialCommunicator(Sleeper delegate) {
        this.delegate = delegate;
    }

    public static SerialCommunicator getInstance() {
        return new SerialCommunicator();
    }

    public static SerialCommunicator getInstance(Sleeper delegate) {
        //return INSTANCE;
        return new SerialCommunicator(delegate);
    }

    public void initCommunication(SerialPortParameters spSettings) throws SerialPortException {
        SerialPortParameters serialPortSettings = spSettings;

        serialPort = new SerialPort(serialPortSettings.getPortName());

        if (serialPort.openPort()) {
            if (serialPort.setParams(serialPortSettings.getBaudRate(),
                    serialPortSettings.getDataBit(),
                    serialPortSettings.getStopBit(),
                    serialPortSettings.getParity())) {
                serialPort.addEventListener(new Reader(),
                        SerialPort.MASK_RXCHAR
                                | SerialPort.MASK_RXFLAG
                                | SerialPort.MASK_CTS
                                | SerialPort.MASK_DSR
                                | SerialPort.MASK_RLSD);
            } else {
                serialPort.closePort();
            }
        }

    }

    public void terminateCommunication() throws SerialPortException {
        serialPort.closePort();
    }

    public void sendMessage(String msg) throws SerialPortException {
        serialPort.writeBytes(msg.getBytes());
    }

    private class Reader implements SerialPortEventListener {

        private String str = "";
        StringBuilder strBuffer = new StringBuilder();

        public void serialEvent(SerialPortEvent spe) {
            if (spe.isRXCHAR() || spe.isRXFLAG()) {
                if (spe.getEventValue() > 0) {
                    try {
                        str = "";

                        byte[] buffer = serialPort.readBytes(spe.getEventValue());
                        str = new String(buffer);
                        //str = str.replaceAll("[^0-9.,]+","");

                        //System.out.println("returned: '" + str + "' rxchar: " + spe.isRXCHAR() + " rxflag: " + spe.isRXFLAG() + " e. value: " + spe.getEventValue());
                        if (str != null && str.length() > 0) {
                            if (str.matches(".*[\r\n|\n|\r]+")) {
                                //We have reached the end of the message
                                //Print all
                                strBuffer.append(str);
                                str = strBuffer.toString();
                                str = str.replaceAll("[^A-Za-z0-9.,]+", ""); //Get rid of non number associated symbols.

                                strBuffer.setLength(0);
                                if (delegate != null) {
                                    delegate.wakeUp(str);
                                } /*else {
                                    System.out.println(str);
                                }*/

                            } else {
                                strBuffer.append(str);
                                //System.out.print(str);
                            }
                            //answerArrived(str);
                        }

                    } catch (Exception ex) {
                        //Do nothing
                    }
                }
                //System.out.print("\n\r");
            } else if (spe.isCTS()) {
                if (spe.getEventValue() == 1) {

                } else {

                }
            } else if (spe.isDSR()) {
                if (spe.getEventValue() == 1) {

                } else {

                }
            } else if (spe.isRLSD()) {
                if (spe.getEventValue() == 1) {

                } else {

                }
            }
        }
    }

}

