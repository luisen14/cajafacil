package com.motus.taskhandler;

import com.motus.SocketWriter;
import com.motus.task.Task;
import com.motus.task.TaskDefinition;
import com.motus.util.MessageHelper;

import java.net.InetAddress;
import java.net.NetworkInterface;
import java.net.SocketException;
import java.net.UnknownHostException;
import java.util.Enumeration;

@TaskDefinition(eventID = "Identify")
public class MacAddressFinderTask extends Task {

    public MacAddressFinderTask(SocketWriter writer){
        super(writer);
    }

    @Override
    public void executeTask() {
        MessageHelper msg = new MessageHelper("mac_address");
        msg.appendStringData("value", resolveMacAddress());
        writer.enqueueMessage(msg.getJson());
    }

    private String resolveMacAddress(){
        String singleMAC = null;

        try {
            Enumeration<NetworkInterface> networks = NetworkInterface.getNetworkInterfaces();

            while (networks.hasMoreElements()) {
                NetworkInterface network = networks.nextElement();
                byte[] mac = network.getHardwareAddress();

                if (mac != null) {
                    StringBuilder sb = new StringBuilder();
                    for (int i = 0; i < mac.length; i++) {
                        sb.append(String.format("%02X%s", mac[i], (i < mac.length - 1) ? "-" : ""));
                    }

                    if (singleMAC == null || singleMAC.toString().contains("00-00-00-00-00-00-00-E0"))
                        singleMAC=sb.toString();
                }
            }

        } catch (SocketException e) {
            e.printStackTrace();
        }
        return singleMAC;
    }
}
