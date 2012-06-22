package cnc.sosbeacon;

import java.io.IOException;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpParams;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.ContentResolver;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.DialogInterface.OnClickListener;
import android.content.Intent;
import android.database.Cursor;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.telephony.TelephonyManager;
import cnc.sosbeacon.constants.Constants;
import cnc.sosbeacon.http.HttpRequest;
import cnc.sosbeacon.http.HttpSolution;
import cnc.sosbeacon.provider.SosBeaconCheckinMessageProvider;
import cnc.sosbeacon.util.ContactInfo;
import cnc.sosbeacon.util.GroupInfo;
import cnc.sosbeacon.util.Preferences;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;
import com.google.code.microlog4android.config.PropertyConfigurator;

public class GeneralActivity extends Activity implements Constants {
	
	protected Preferences preferences;
	protected int sdkVersion = Integer.parseInt(Build.VERSION.SDK);
	protected String mImei;
	protected String mPhoneId = "";
	protected String mSettingId;
	protected String mToken = "";
	protected String mPhoneNumber = "";
	protected String mUserName = "";
	protected String mPassword = "";
	protected String mEmail = "";
	protected String mRecordDuration = ""; 
	protected String mAlertSendToGroup = "";
	protected String mEmergencyNumber = "";
	protected String mPanicRange = "";
	protected String mPanicStatus = "";
	protected String mGoodSamaritanStatus = ""; 
	protected String mGoodSamaritanRange = "";
	protected String mIncomingGovernmentAlert = "";
	protected String mPhoneStatus = "";
	protected String mRegisterType = "" ;
	protected Integer mCountContact;
	protected Integer mResponseCode = 0;
	protected String mMessage = "";
	private static boolean isStartTimer = false;
	protected JSONObject settingJson;
	protected JSONObject phoneJson;
	protected Location mLocation;
	protected String mLatitude;
	protected String mLongtitude;
	protected ProgressDialog mProgressDialog;
	protected AlertDialog.Builder mAlertDialog;
	
	protected final Logger logger = LoggerFactory.getLogger(GeneralActivity.class); 
	Thread checkConnect;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		//Log to file configure
		PropertyConfigurator.getConfigurator(this).configure();
		
		TelephonyManager telephonyManager = (TelephonyManager)getSystemService(Context.TELEPHONY_SERVICE);
		mImei = telephonyManager.getDeviceId();
		// PreferenceManager.setDefaultValues(this, R.xml.preferences, true);
		preferences =  new Preferences(this);// PreferenceManager.getDefaultSharedPreferences(this);
		mProgressDialog = new ProgressDialog(this);
		mAlertDialog = new AlertDialog.Builder(this);
		getSavedInfor();
		runCheckConnect();
		//updateLocation();
	}
	
	protected void preprocess() {
		preferences.edit().remove(CONTACT_GROUPS).commit(); // Clear group cache
	}

	protected String getPrefs(String key) {
		return preferences.getString(key, "");
	}
	
	protected String getApiUrl(String key) {
		if (key.equalsIgnoreCase(API_URL)) {
			return preferences.get(API_URL);
		}
		return preferences.get(API_URL) + preferences.get(key);
	}
	
	private void getSavedInfor() {
		mPhoneId				 = getPrefs(ID);
		mSettingId				 = getPrefs(SETTING_ID);
		mPhoneNumber 			 = getPrefs(PHONE_NUMBER);
		mUserName 				 = getPrefs(NAME);
		mPassword 				 = getPrefs(PASSWORD);
		mEmail					 = getPrefs(EMAIL);
		mToken 					 = getPrefs(TOKEN);
		mPhoneStatus 			 = getPrefs(PHONE_STATUS);
		mRegisterType 			 = getPrefs(REGISTER_TYPE);
		mCountContact 			 = preferences.getInt(COUNT_CONTACT, 0);
		
		mRecordDuration			 = getPrefs(RECORD_DURATION);
		mAlertSendToGroup		 = getPrefs(ALERT_SEND_TO_GROUP);
		mEmergencyNumber 		 = getPrefs(EMERGENCY_NUMBER);
		mPanicStatus			 = getPrefs(PANIC_STATUS);
		mPanicRange				 = getPrefs(PANIC_RANGE);
		mGoodSamaritanStatus 	 = getPrefs(GOOD_SAMARITAN_STATUS);
		mGoodSamaritanRange		 = getPrefs(GOOD_SAMARITAN_RANGE);
		mIncomingGovernmentAlert = getPrefs(INCOMING_GOVERNMENT_ALERT);
	}
	
	protected void savePhoneInfor() {
    	preferences.edit()
    	.putString(ID, mPhoneId)
    	.putString(SETTING_ID, mSettingId)
		.putString(PHONE_NUMBER, mPhoneNumber)
		.putString(NAME, mUserName)
		.putString(PASSWORD, mPassword)
		.putString(EMAIL, mEmail)
		.putString(TOKEN, mToken)
		.putString(PHONE_STATUS, mPhoneStatus)
		.putString(REGISTER_TYPE, mRegisterType)
		.putInt(COUNT_CONTACT, mCountContact)
		.putString(RECORD_DURATION, mRecordDuration)
		.putString(ALERT_SEND_TO_GROUP, mAlertSendToGroup)
		.putString(EMERGENCY_NUMBER, mEmergencyNumber)
		.putString(PANIC_STATUS, mPanicStatus)
		.putString(PANIC_RANGE, mPanicRange)
		.putString(GOOD_SAMARITAN_STATUS, mGoodSamaritanStatus)
		.putString(GOOD_SAMARITAN_RANGE, mGoodSamaritanRange)
		.putString(INCOMING_GOVERNMENT_ALERT, mIncomingGovernmentAlert)
		.commit();
	}
	
	protected void initPhoneData(JSONObject json) {
		try {
			mPhoneId = json.has(ID) ? json.getString(ID) : mPhoneId;
			mSettingId = json.has(SETTING_ID) ? json.getString(SETTING_ID) : mSettingId;
			mPhoneNumber = json.has(PHONE_NUMBER) ? json.getString(PHONE_NUMBER) : mPhoneNumber;
			mUserName = json.has(NAME) ? json.getString(NAME) : mUserName;
			mPassword = json.has(PASSWORD) ? json.getString(PASSWORD) : mPassword;
	    	mEmail = json.has(EMAIL) ? json.getString(EMAIL) : mEmail;
	    	mToken = json.has(TOKEN) ? json.getString(TOKEN) : mToken;
	    	mPhoneStatus = json.has(PHONE_STATUS) ? json.getString(PHONE_STATUS) : mPhoneStatus;
	    	mCountContact = json.has(COUNT_CONTACT) ? json.getInt(COUNT_CONTACT) : mCountContact;
	    	if (!mSettingId.equalsIgnoreCase("")) {
		    	initPhoneSettings(json);
	    	}
		} catch (JSONException e) {
			e.printStackTrace();
			logger.log(Level.FATAL, e.getMessage());
		}
	}
	
	protected void initPhoneSettings(JSONObject json) {
		try {
			mRecordDuration = json.getString(RECORD_DURATION);
			mAlertSendToGroup = json.getString(ALERT_SEND_TO_GROUP);
	    	mEmergencyNumber = json.getString(EMERGENCY_NUMBER);
			mPanicStatus = json.getString(PANIC_STATUS);
			mPanicRange = json.getString(PANIC_RANGE);
			mGoodSamaritanStatus = json.getString(GOOD_SAMARITAN_STATUS);
			mGoodSamaritanRange = json.getString(GOOD_SAMARITAN_RANGE);
			mIncomingGovernmentAlert = json.getString(INCOMING_GOVERNMENT_ALERT);
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}
	
	protected String getDeviceInfor() {
		String phoneInfo = "";
		TelephonyManager telephonyManager = (TelephonyManager)getSystemService(Context.TELEPHONY_SERVICE);
		try {
			int phoneType  = telephonyManager.getPhoneType();
			phoneInfo = "Version:" + getString(R.string.app_version);
			phoneInfo += ";Brand:" + android.os.Build.BRAND;
			phoneInfo += ";Model:" + android.os.Build.MODEL;
			phoneInfo += ";AndroidOS:" + android.os.Build.VERSION.SDK;
			phoneInfo += ";PhoneType:" + (phoneType == TelephonyManager.PHONE_TYPE_CDMA ? "CDMA": (phoneType == TelephonyManager.PHONE_TYPE_GSM ? "GSM": "NONE"));			
		} catch (Exception e) {
			e.printStackTrace();
		}
		return phoneInfo;
	}
	
	/**
	 * Get phone information by IMEI
	 */
	protected void requestPhoneData() {
		String phoneInfo = getDeviceInfor();
		HttpClient client = new DefaultHttpClient();
		String getUrl = String.format(getApiUrl(PHONE_GET_URL), mImei, mToken, mPhoneNumber, mPassword, URLEncoder.encode(phoneInfo));
		HttpGet httpGet = new HttpGet(getUrl);
		HttpResponse response;
		logger.log(Level.INFO, "request login: " + getUrl);
		try {
			response = client.execute(httpGet);
			String responseContent = HttpRequest.GetText(response);
			JSONObject responseJson = new JSONObject(responseContent);
			responseJson = responseJson.getJSONObject(RESPONSE);
			mResponseCode = responseJson.getInt(RESPONSE_CODE);
			mMessage = responseJson.getString(MESSAGE);
			if (mResponseCode == CODE_SUCCESS || mResponseCode == CODE_ACCOUNT_NOT_ACTIVATED) {
				initPhoneData(responseJson);
				savePhoneInfor();
			}
			logger.log(Level.INFO, "- server response: " + responseJson);
		} catch (Exception e) {
			e.printStackTrace();
			mResponseCode = 0;
			mMessage = e.getMessage();
			logger.log(Level.ERROR, e.getMessage());
		}
	}
	
	/**
	 * Register new phone
	 */
	protected void regsiterNewPhone(String phoneNumber, String action) {
		String phoneInfo = getDeviceInfor();
		HttpClient client = new DefaultHttpClient();
		HttpPost httpPost = new HttpPost(getApiUrl(PHONE_URL));
		List<NameValuePair> params = new ArrayList<NameValuePair>();
		params.add(new BasicNameValuePair(FORMAT, "json"));
		params.add(new BasicNameValuePair(IMEI, mImei));
		params.add(new BasicNameValuePair(PHONE_NUMBER, phoneNumber));
		params.add(new BasicNameValuePair(PHONE_TYPE, PHONE_TYPE_ID));
		params.add(new BasicNameValuePair(PHONE_INFO, phoneInfo));
		if (!action.equalsIgnoreCase("")) {
			params.add(new BasicNameValuePair(DO, action));
		}
		try {
			UrlEncodedFormEntity urlEncodedFormEntity = new UrlEncodedFormEntity(params);
			httpPost.setEntity(urlEncodedFormEntity);
		} catch (Exception e) {
				e.printStackTrace();
		}
		HttpResponse response;
		try {
			response = client.execute(httpPost);
			String responseContent = HttpRequest.GetText(response);
			JSONObject responseJson = new JSONObject(responseContent);
			responseJson = responseJson.getJSONObject(RESPONSE);
			mResponseCode = responseJson.getInt(RESPONSE_CODE);
			mMessage = responseJson.getString(MESSAGE);
			if (mResponseCode == CODE_SUCCESS || mResponseCode == CODE_ACCOUNT_NOT_ACTIVATED) {
				initPhoneData(responseJson);
				savePhoneInfor();
			}
		} catch (Exception e) {
			e.printStackTrace();
			mResponseCode = 0;
			mMessage = e.getMessage();
		}
	}

	protected void updatePhone(String requestType, String name, String email, String password) {
		HttpClient client = new DefaultHttpClient();
		HttpPost httpPut = new HttpPost(getApiUrl(PHONE_URL) + mPhoneId);
		List<NameValuePair> params = new ArrayList<NameValuePair>();
		params.add(new BasicNameValuePair(FORMAT, "json"));
		params.add(new BasicNameValuePair(METHOD, "PUT"));
		params.add(new BasicNameValuePair(DO, requestType)); // update, request password
		params.add(new BasicNameValuePair(IMEI, mImei));
		params.add(new BasicNameValuePair(TOKEN, mToken));
		params.add(new BasicNameValuePair(PHONE_NUMBER, mPhoneNumber));
		params.add(new BasicNameValuePair(NAME, name));
		params.add(new BasicNameValuePair(EMAIL, email));
		if (!password.equalsIgnoreCase("")) {
			params.add(new BasicNameValuePair(PASSWORD, password));
		}
		
		try {
			UrlEncodedFormEntity urlEncodedFormEntity = new UrlEncodedFormEntity(params, HTTP.UTF_8);
			httpPut.setEntity(urlEncodedFormEntity);
		} catch (Exception e) {
				e.printStackTrace();
		}
		HttpResponse response;
		try {
			response = client.execute(httpPut);
			String responseContent = HttpRequest.GetText(response);
			JSONObject responseJson = new JSONObject(responseContent);
			responseJson = responseJson.getJSONObject(RESPONSE);
			mResponseCode = responseJson.getInt(RESPONSE_CODE);
			mMessage = responseJson.getString(MESSAGE);	
			if (mResponseCode == CODE_SUCCESS) {
				initPhoneData(responseJson);
				savePhoneInfor();
			}
		} catch (Exception e) {
			e.printStackTrace();
			mResponseCode = 0;
			mMessage = e.getMessage();
		}
	}
	
	protected ArrayList<GroupInfo> getContactGroups() throws ClientProtocolException, IOException, JSONException {
		ArrayList<GroupInfo> category = new ArrayList<GroupInfo>();
		HttpClient client = new DefaultHttpClient();		
		String getURL = String.format(getApiUrl(GROUP_GET_URL), getPhoneId(), getToken());
		HttpGet httpGet = new HttpGet(getURL);	
		HttpParams httpParams = new BasicHttpParams();
		httpParams.setParameter(FORMAT, JSON);
        httpParams.setParameter("http.protocol.content-charset", "UTF-8");
		httpGet.setParams(httpParams);
		HttpResponse response = client.execute(httpGet);
		String content = HttpRequest.GetText(response);
		JSONObject responseJson = new JSONObject(content);
		JSONArray categoryJSON = responseJson.getJSONObject(RESPONSE).getJSONArray(DATA);
		category = ContactInfo.getGroupsFromJson(categoryJSON);
		return category;
	}
	
	protected void updateLocation() {
		if (mGoodSamaritanStatus.equalsIgnoreCase("1")) {
			if(!isStartTimer) {
				CountDownTimer timer = new CountDownTimer(UPDATE_LOCATION_INTERVAL, 1000) {
					@Override
					public void onTick(long millisUntilFinished) {}
					@Override
					public void onFinish() {
						sendCurrentLocation();
						this.start();
					}
				}; 
				timer.start();
				isStartTimer = true;
			}
		}
	}
	
	private Handler connectHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			super.handleMessage(msg);
			if(msg.what == 2){
				checkConnect = new Thread(new Runnable() {
					public void run() {
						check = true;
						while (check) {
							try {
								if(!checkConnect()){
									connectHandler.sendEmptyMessage(0);
									check = false;								
								}else{
									Intent i = new Intent(getBaseContext(), SplashScreenActivity.class);
									i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
									startActivity(i);
									check = false;
									finish();
								};
								Thread.sleep(3000);
							} catch (Exception e) {
								e.printStackTrace();
							}
						}
					}
				});
				checkConnect.start();
			}else{
				try {
					mAlertDialog.setMessage(getString(R.string.offline_error_switch))
					.setNegativeButton(android.R.string.cancel, new OnClickListener() {
						@Override
						public void onClick(DialogInterface dialog, int which) {
							Intent i = getIntent();
							String component = i.getComponent().toShortString();
							String cls[] = component.split("/");
							if(cls[1].contains("SplashScreenActivity")){
								checkConnect.stop();
								checkConnect.interrupt();
								sendEmptyMessageDelayed(2, 10000);
							}
						}
					})
					.setPositiveButton(android.R.string.ok, new OnClickListener() {
						@Override
						public void onClick(DialogInterface dialog, int which) {
							Intent i = new Intent(getBaseContext(), OffLineModeActivity.class);
							i.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
							i.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
							startActivity(i);
							finish();		
						}
					});
					mAlertDialog.show();
				}
				catch (Exception e) {
					e.printStackTrace();
				}
			}
		}
	};
	
	protected void runCheckConnect() {
		checkConnect = new Thread(new Runnable() {
			public void run() {
				while (check) {
					try {
						if(!checkConnect()){
							connectHandler.sendEmptyMessage(0);
							check = false;								
						};
						Thread.sleep(3000);
					} catch (Exception e) {
						e.printStackTrace();
					}
				}
			}
		});
		checkConnect.start();
	}
	
	boolean check = true;
	@Override
	protected void onResume() {
		super.onResume();
	}
	
	@Override
	protected void onPause() {
		super.onPause();
	}
	
	@Override
	protected void onDestroy() {
		try {
			//checkConnect.stop();
			checkConnect.interrupt();
		} catch (Exception e) {
			// TODO: handle exception
		}
		super.onDestroy();
	}
	
	protected boolean checkConnect() {
		boolean isConnect = false;
		try {
			URL url = new URL(getApiUrl(API_URL));
			HttpURLConnection httpConn;
			httpConn = (HttpURLConnection) url.openConnection();
			httpConn.setConnectTimeout(30000); //  second for timeout
			httpConn.connect();
			int response = httpConn.getResponseCode();
			if (response == HttpURLConnection.HTTP_OK) {
				isConnect = true;
			} 
			/*
			else {
				connectHandler.sendEmptyMessage(MESSAGE_CONNECT_FAIL);
			}*/
		} catch (Exception e) {
			/*Message m = new Message();
			m.what = MESSAGE_CONNECT_EXCEPTION;
			m.obj = e.getMessage();
			connectHandler.sendMessage(m);*/
		}
		return isConnect; 
	}
	
	protected String getLocation() {
		Boolean gpsEnabled = false;
		Boolean networkEnabled = false;
		LocationManager lm = (LocationManager) getSystemService(LOCATION_SERVICE);
		String lastKnownLocationService = "";
		String message = "";
		try {
			gpsEnabled = lm.isProviderEnabled(LocationManager.GPS_PROVIDER);
		} catch (Exception e) {
			e.printStackTrace();
		}
		try {
			networkEnabled = lm.isProviderEnabled(LocationManager.NETWORK_PROVIDER);
		} catch (Exception e) {
			e.printStackTrace();
		}
		if (networkEnabled) {
			lastKnownLocationService = LocationManager.NETWORK_PROVIDER;
		}
		if (gpsEnabled) {
			lastKnownLocationService = LocationManager.GPS_PROVIDER;
		}
		if (gpsEnabled || networkEnabled) {
			try {
				lm.requestLocationUpdates(LocationManager.NETWORK_PROVIDER, 0, 0, new LocationListener() {
					public void onStatusChanged(String provider, int status, Bundle extras) {}
					public void onProviderEnabled(String provider) {}
					public void onProviderDisabled(String provider) {}
					public void onLocationChanged(Location location) {
						mLocation = location;
					}
				});
				mLocation = lm.getLastKnownLocation(LocationManager.GPS_PROVIDER);
				if (mLocation == null) {
					mLocation = lm.getLastKnownLocation(LocationManager.NETWORK_PROVIDER);
				}
				if (mLocation == null) {
					message += String.format(getString(R.string.locationServiceProblem), lastKnownLocationService.toUpperCase());
				}
				if (mLocation != null) {
					mLatitude = Double.toString(mLocation.getLatitude());
					mLongtitude = Double.toString(mLocation.getLongitude());
				}
			} catch (Exception e) {
				e.printStackTrace();
			}
		} else {
			message += getString(R.string.locationServiceNotEnabled);
		}
		return message;
	}
	
	protected void sendCurrentLocation() {
		Thread sendLocation = new Thread(new Runnable() {
			public void run() {
				Looper.prepare();
				try {
					getLocation();
					HttpClient client = new DefaultHttpClient();
					String postURL = getApiUrl(LOCATION_URL);
					HttpPost post = new HttpPost(postURL);
					HttpSolution httpObject = new HttpSolution();
					httpObject.put(FORMAT, JSON);
					httpObject.put(PHONE_ID, mPhoneId);
					httpObject.put(TOKEN, mToken);
					httpObject.put(LATITUDE, mLatitude);
					httpObject.put(LONGITUDE, mLongtitude);
					List<NameValuePair> params = httpObject.getParams();
					// Request server and receive result
					UrlEncodedFormEntity ent = new UrlEncodedFormEntity(params, HTTP.UTF_8);
					post.setEntity(ent);
					HttpResponse responsePOST = client.execute(post);			
					HttpEntity resEntity = responsePOST.getEntity();			
					String responseString= new String(EntityUtils.toString(resEntity));
					logger.log(Level.INFO, "sendCurrentLocation: responseString=" + responseString);
					JSONObject jsonResponse = new JSONObject(responseString);
					httpObject.setJsonObject(jsonResponse);	
				} catch (Exception e) {
					logger.log(Level.ERROR, "sendCurrentLocation: " + e.getMessage());
				}
			}
		});
		sendLocation.start();
	}
	
	protected  void sendCheckIn(final Handler checkinHandler,  final Integer groupId, final String contact, final String message) {
		new Thread(new Runnable() {
			public void run() {
				Integer alertId = 0;
				Message msg = new Message();
				logger.log(Level.INFO, "start sending check-in message");
				try {
					Looper.prepare();
					getLocation();
					HttpClient client = new DefaultHttpClient();
					String postURL = getApiUrl(ALERT_URL);
					HttpPost post = new HttpPost(postURL);
					HttpSolution httpObject = new HttpSolution();
					httpObject.put(FORMAT, JSON);
					httpObject.put(PHONE_ID, getPhoneId());			
					httpObject.put(LATITUDE, getLatitude());
					httpObject.put(LONGITUDE, getLongtitude());
					httpObject.put(TOKEN, getToken());			
					httpObject.put(TYPE, "2");		
					httpObject.put(ALERT_LOG_TYPE, "2");
					httpObject.put(TO_GROUP, String.valueOf(groupId));						
					if (!contact.equalsIgnoreCase("")) {
						httpObject.put(SINGLE_CONTACT, contact);
					}			
					httpObject.put(MESSAGE, message);
					httpObject.getParams();
					List<NameValuePair> params = httpObject.getParams();
					
					logger.log(Level.INFO, "- URL: " + postURL + ", request params: " + params.toString());
					
					UrlEncodedFormEntity ent = new UrlEncodedFormEntity(params, HTTP.UTF_8);
					post.setEntity(ent);
					HttpResponse responsePOST = client.execute(post);
					HttpEntity resEntity = responsePOST.getEntity();
				    String responseString = new String(EntityUtils.toString(resEntity));
					JSONObject jsonResponse = new JSONObject(responseString);
					httpObject.setJsonObject(jsonResponse);		
					httpObject.getResponeAlert();
					alertId = Integer.valueOf(httpObject.getAlertId());
					msg.obj = getString(R.string.check_in_success);
					
					logger.log(Level.INFO, "- server response: " + responseString);
				}
				catch (Exception e) {
					msg.obj = e.getMessage();
					logger.log(Level.FATAL, "- exception error: " + e.getMessage());
				}
				if (alertId != null) msg.arg1 = alertId;
				checkinHandler.sendMessage(msg);
			}
		}).start();
	}
	
	protected void saveCheckinMessage(String message) {
		try {
			message = message.trim();
			Uri contentUri = SosBeaconCheckinMessageProvider.CONTENT_URI;
			if (!message.equalsIgnoreCase(getString(R.string.checkInOk))) {
				ContentResolver cr = this.getContentResolver();
				ContentValues values;
				// check if message is exist 
				Cursor c = cr.query(contentUri, null, "lower(message)=\"" + message.toLowerCase() + "\"" , null, null);
				Integer count = 0;
				if (c != null) {
					if (c.moveToFirst()) {
						count = c.getCount();
					}
				}
				if (count > 0) {
					Integer usageCount = c.getInt(c.getColumnIndex("count"));
					Integer _id = c.getInt(c.getColumnIndex("_id"));
					usageCount ++;
					values = new ContentValues();
					values.put("count", usageCount);
					cr.update(contentUri, values, "_id=" + _id, null);
					c.close();
				} else { // update new message to least used message
					if (c != null) if (!c.isClosed()) c.close();
					c = cr.query(contentUri, null, null, null, "count ASC, _id DESC");
					if (c != null) {
						if (c.moveToFirst()) {
							Integer _id = c.getInt(c.getColumnIndex("_id"));
							values = new ContentValues();
							values.put("message", message);
							values.put("count", 1);
							cr.update(contentUri, values, "_id=" + _id, null);
						}
						c.close();
					}
				}
			}
		} catch (Exception e) {
			logger.log(Level.FATAL, "save recent check-in message: " + e.getMessage());
		}
	}
	
	protected void callActivity(int menuId, Context context) {
		logger.log(Level.INFO, "callActivity, context: " + context.getClass().getName());
		switch (menuId) {
			case R.id.menu_item_home:
				SosBeaconActivity.show(context, false);
				break;
			case R.id.menu_item_review:
				ReviewActivity.show(context);
				break;
			/*case R.id.menu_item_groups:
				ContactCategory.show(context);
				break;*/
			case R.id.menu_item_more:
				ManagerActivity.show(context);
				break;
		}
	}
	
	protected String getPhoneId() {
		return mPhoneId;
	}

	protected void setPhoneId(String phoneId) {
		this.mPhoneId = phoneId;
	}

	protected String getToken() {
		return mToken;
	}

	protected void setToken(String token) {
		this.mToken = token;
	}
	
	protected String getLatitude() {
		return mLatitude;
	}

	protected String getLongtitude() {
		return mLongtitude;
	}
}
