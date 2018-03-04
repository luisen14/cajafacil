/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.motus.util;

import com.motus.MessageProcessor;
import java.util.HashMap;
import java.util.List;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;

/**
 *
 * @author lsencion
 */
public class MessageHelper {
    private final String eventID;
    private HashMap<String, Object> dataMap;    
    
    public MessageHelper(String messageId){
        eventID = messageId;
        dataMap = new HashMap<String, Object>();
    }
    
    public MessageHelper appendStringData(String key, String data){
        dataMap.put(key, data);
        return this;
    }
    
    public MessageHelper appendArrayData(String key, List<String> data){                
        dataMap.put(key,data);        
        return this;
    }    
    
    public String getJson(){
        JSONObject responseObj = new JSONObject();
        
        responseObj.put(MessageProcessor.EVENT_TAG,eventID);                
        responseObj.put(MessageProcessor.DATA_TAG, JSONObject.toJSONString(dataMap));
        
        return responseObj.toString();
    }
}
