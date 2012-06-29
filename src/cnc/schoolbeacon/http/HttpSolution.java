package cnc.schoolbeacon.http;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import cnc.schoolbeacon.constants.Constants;

import android.util.Log;

public class HttpSolution {
	private List<NameValuePair> params = new ArrayList<NameValuePair>();
	private String TAG = "HpptSolution";
	private JSONObject jsonObject;
	private String phoneId;
	private Boolean state;
	private String imei;
	private String token;
	private String settingId;
	private String message = "";
	private String numberPanic;
	private String alertId;
	private String responseCode = "";

	public void put(String name, String value) {
		params.add(new BasicNameValuePair(name, value));
	}

	public void setJsonObject(JSONObject jsonObject) {
		try {
			this.jsonObject = (JSONObject) jsonObject.get(Constants.RESPONSE);
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			Log.d(TAG, e.toString());
		}
	}

	public void getResponeLogin() {

		try {
			setState((String) jsonObject.get(Constants.STATE));
			if (this.state) {
				setPhoneId(jsonObject.getString(Constants.ID));
				setImei((String) jsonObject.get(Constants.IMEI));
				setToken((String) jsonObject.get(Constants.TOKEN));				
			} else {				
				if (!jsonObject.isNull(Constants.MESSAGE)) {
				  setMessage((String) jsonObject.get(Constants.MESSAGE));
				}
				if (!jsonObject.isNull(Constants.RESPONSE_CODE)) {
					setResponseCode((String) jsonObject.get(Constants.RESPONSE_CODE));
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}

	}
	/*
	 * Get Response after send alert
	 */
	public  void getResponeAlert(){
		try {
//			setState((String) jsonObject.getString(Constants.STATE));
			setAlertId((String) jsonObject.getString(Constants.ALERT_ID));
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}
	
	public void getRAddContact(){
		try {
			setState((String) jsonObject.get(Constants.STATE));
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}
	
	public void  getInfoPhone() {
		try {
			setState((String) jsonObject.get(Constants.STATE));
			if (this.state) {
				setSettingId(jsonObject.getString(Constants.SETTING_ID));
			}
			
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}
	

	public List<NameValuePair> getParams() {
		return params;
	}

	public String getPhoneId() {
		return phoneId;
	}

	public void setPhoneId(String phoneId) {
		this.phoneId = phoneId;
	}

	public boolean getState() {
		return state;
	}

	public void setState(String state) {
		this.state=false;
		if(state.equals("true"))
		 this.state =true;
	}

	public String getImei() {
		return imei;
	}

	public void setImei(String imei) {
		this.imei = imei;
	}

	public String getToken() {
		return token;
	}

	public void setToken(String token) {
		this.token = token;
	}
	
	public String getSettingId() {
		return settingId;
	}

	public void setSettingId(String settingId) {
		this.settingId = settingId;
	}
	
	public String getMessage() {
		return message;
	}

	public void setMessage(String message) {
		this.message = message;
	}
	
	public String getNumberPanic() {
		return numberPanic;
	}

	public void setNumberPanic(String numberPanic) {
		this.numberPanic = numberPanic;
	}

	public String getAlertId() {
		return alertId;
	}

	public void setAlertId(String alertId) {
		this.alertId = alertId;
	}
	
	public String getResponseCode() {
		return responseCode;
	}

	public void setResponseCode(String responseCode) {
		this.responseCode = responseCode;
	}

	/**
	 *  Funtion use Method GET
	 * @param endpoint
	 * @param requestParameters
	 * @return
	 */
	public static String sendGetRequest(String endpoint,
			String requestParameters) {
		String result = null;
		if (endpoint.startsWith("http://")) {
			// Send a GET request to the servlet
			try {
				// Send data
				String urlStr = endpoint;
				if (requestParameters != null && requestParameters.length() > 0) {
					urlStr += "?" + requestParameters;
				}
				Log.d("Login",urlStr);
				URL url = new URL(urlStr);
				URLConnection conn = url.openConnection();
				Log.d("Login","Connect server");
				// Get the response
				BufferedReader rd = new BufferedReader(new InputStreamReader(
						conn.getInputStream()));
				StringBuffer sb = new StringBuffer();
				String line;
				while ((line = rd.readLine()) != null) {
					sb.append(line);
				}
				rd.close();
				result = sb.toString();
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		return result;
	}
}
