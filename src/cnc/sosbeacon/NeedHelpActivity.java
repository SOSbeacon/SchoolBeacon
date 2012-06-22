package cnc.sosbeacon;


import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.media.MediaPlayer;
import android.net.Uri;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.util.Log;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.Toast;
import cnc.sosbeacon.dialog.UploadDialog;
import cnc.sosbeacon.http.HttpSolution;
import cnc.sosbeacon.recorder.Callrecorder;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class NeedHelpActivity extends GeneralActivity {
	
	private final Logger logger = LoggerFactory.getLogger(NeedHelpActivity.class); 
	private boolean  _recordInUse = false;
	private Callrecorder  callRecord;
	private List<String> lstFileVoice = new ArrayList<String>();
	private List<String> lstFileImage = new ArrayList<String>();
	private Handler mHandler;
	private static final int RECORD = 0;
	private static final int CALLPANIC = 1;
	private Boolean callPanic = false;
	private Thread mCheckUpdate;
	private static final int UPDATE_CALL_PANIC_SUCCESS = 5;
	private String alertId = "";
	private Boolean state;
	private String TAG = "NeedHelpActivity";
	private ProgressDialog mProgressDialog;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.call_help);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		
		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		leftBtn.setVisibility(View.INVISIBLE);
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		rightBtn.setVisibility(View.INVISIBLE);
		
		Button btCallOriginal = (Button) findViewById(R.id.btCallOriginal);
		Button btNeedHelpEasy = (Button) findViewById(R.id.btHelpEasy);
		Button btCancel = (Button) findViewById(R.id.btCancel);
		
		mProgressDialog = new ProgressDialog(this);
		
		btCallOriginal.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				if (mEmergencyNumber.trim().equalsIgnoreCase("") || mEmergencyNumber.trim().equalsIgnoreCase("0")) {
					//Toast.makeText(NeedHelpActivity.this, getString(R.string.set_emergency_number), Toast.LENGTH_LONG).show();
					AlertDialog.Builder adEmergencyNumber = new AlertDialog.Builder(NeedHelpActivity.this);
					adEmergencyNumber.setMessage(R.string.set_emergency_number_prompt);
					adEmergencyNumber.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							finish();
							AlertSettingActivity.show(NeedHelpActivity.this);
						}
					});
					adEmergencyNumber.setNegativeButton(R.string.btnNotNow, null);
					adEmergencyNumber.show();
				} else {
					confirmCallEmergencyPhone();
				}
			}
		});
		btNeedHelpEasy.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				sendAlertSever(RECORD);
			}
		});
		btCancel.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				SosBeaconActivity.show(NeedHelpActivity.this, false);
				finish();
			}
		});
		
		if (getIntent().getBooleanExtra(BACK, false)) {
			new AlertDialog.Builder(this)
			.setMessage(R.string.alert_sent)
			.setNeutralButton(R.string.btnOK, null)
			.show();
		}
	}
	
	private void confirmCallEmergencyPhone() {
		final AlertDialog.Builder callAlertDialog = new AlertDialog.Builder(this);
		final AlertDialog callDialog = callAlertDialog.create();
		final CountDownTimer callCoutDownTime = new CountDownTimer(CALL_COUNTDOWN_TIME, 1000) {
			@Override
			public void onTick(long millisUntilFinished) {
				String callTimeDown = getString(R.string.confirm_call) + "\n" + String.valueOf((millisUntilFinished) / 1000) + " seconds left";
				callDialog.setMessage(callTimeDown);
			}
			@Override
			public void onFinish() {
				callDialog.hide();
				sendAlertPanic();
			}
		};
		
		callDialog.setTitle(R.string.confirm);
		callDialog.setMessage(getString(R.string.confirm_call));
		callDialog.setOnCancelListener(new DialogInterface.OnCancelListener() {
			public void onCancel(DialogInterface dialog) {
				callCoutDownTime.cancel();
			}
		});
		callDialog.setButton2(getString(R.string.btnCancelCap), new DialogInterface.OnClickListener() {
			public void onClick(DialogInterface dialog, int which) {
				dialog.cancel();
			}
		});
		callDialog.setButton3(getString(R.string.btnCall), new DialogInterface.OnClickListener() {
			public void onClick(DialogInterface dialog, int which) {
				callCoutDownTime.cancel();
				sendAlertPanic();
			}
		});
		callDialog.show();
		callCoutDownTime.start();
	}
	
	private void sendAlertPanic() {
		try {   
			callPanic = true;
			if (!mGoodSamaritanStatus.equals("1")) {
				startActivity(new Intent(Intent.ACTION_CALL, Uri.parse("tel:"+ mEmergencyNumber)));
				recordVoice();
			} else {
				startActivity(new Intent(Intent.ACTION_CALL, Uri.parse("tel:"+ mEmergencyNumber)));
				recordVoice();
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	private void recordVoice(){
		callRecord = new Callrecorder(AUDIO_FILE);
		try {
			MediaPlayer player = MediaPlayer.create(this,R.raw.bip);
			player.start();
			callRecord.start();
		} catch (IOException e) {
			e.printStackTrace();
		}
		_recordInUse = true;
	}
	
	private  void sendAlertSever(int type) {
		if (type == CALLPANIC) {
			mProgressDialog =  ProgressDialog.show(this, "", getString(R.string.sending_alert), true);
		} else {
			mProgressDialog =  ProgressDialog.show(this, "", getString(R.string.starting_alert), true);
		}
		
		mHandler = new Handler() {
			@Override
			public void handleMessage(Message msg) {
				super.handleMessage(msg);
				if (msg.what == MESSAGE_CONNECT_EXCEPTION) {
					if(mProgressDialog.isShowing()) {
						mProgressDialog.hide();
					}
					Toast.makeText(NeedHelpActivity.this, getString(R.string.get_info_fail), Toast.LENGTH_LONG).show();
				}
				if (msg.what == UPDATE_CALL_PANIC_SUCCESS) {
					try {
						if(mProgressDialog.isShowing()) {
							mProgressDialog.hide();
						}
					} catch (Exception e) {
						e.printStackTrace();
					}
					uploadCallRecord();
				}
			}
		};
		mCheckUpdate = new CheckUpdate(mHandler,type);
		mCheckUpdate.start();
	}
	
	// Send server in call emergency phone
	private void uploadCallRecord() {
		Handler callBackHandler = new Handler() {
			@Override
			public void handleMessage(Message msg) {
				super.handleMessage(msg);
			}
		};
		if (lstFileImage.size() > 0 || lstFileVoice.size() > 0) {
			try {
				UploadDialog uploadDialog  = new UploadDialog(NeedHelpActivity.this, getApiUrl(UPLOAD_URL), callBackHandler, lstFileImage, lstFileVoice, String.valueOf(alertId), "1", getPhoneId(), getToken());
				uploadDialog.setCancelable(false);
				try {
					uploadDialog.show();
				} catch (Exception e) {
					e.printStackTrace();
				}
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		//reset list	 
		lstFileImage = new ArrayList<String>();
		lstFileVoice = new ArrayList<String>();
	}
	
	class CheckUpdate extends Thread {
		Handler handler;
		int type;
		private Runnable thSendalert = new Runnable() {
	        public void run(){
	        	try {   
	        		if(mProgressDialog.isShowing()) {
	        			mProgressDialog.hide();
	        		}
	        		if(state) {
	    				finish();
	    				RecordActivity.show(NeedHelpActivity.this, TAG, alertId);
	    			}
	        	} catch (Exception e) {
	        		e.printStackTrace();
				}
	        }
		};
        public CheckUpdate(Handler handler, int type) {
			super();
			this.handler = handler;
			this.type    = type;
		}
		public void run() {
			Looper.prepare();
			String responseString = "";
			logger.log(Level.INFO, "start sending alert, type: " + (type == CALLPANIC ? "CALL EMERGECY PHONE" : "SEND ALERT"));
			getLocation();
			try {
				HttpClient client = new DefaultHttpClient();
				String postURL = getApiUrl(ALERT_URL);
				HttpPost post = new HttpPost(postURL);
				HttpSolution httpObject = new HttpSolution();
				httpObject.put(FORMAT, "json");
				httpObject.put(PHONE_ID, getPhoneId());
				if(type == RECORD){
					httpObject.put(TYPE, "0");
				}
				if(type == CALLPANIC){
					httpObject.put(TYPE, "1");
				}
				httpObject.put(LATITUDE, getLatitude());
				httpObject.put(LONGITUDE,getLongtitude());
				httpObject.put(TOKEN, getToken());
				httpObject.getParams();
				List<NameValuePair> params = httpObject.getParams();
				
				logger.log(Level.INFO, "- URL: " + postURL + ", request params: " + params.toString());
				
				// Request server and receive result
				UrlEncodedFormEntity ent = new UrlEncodedFormEntity(params,HTTP.UTF_8);
				post.setEntity(ent);
				HttpResponse responsePOST = client.execute(post);
				HttpEntity resEntity = responsePOST.getEntity();
				//get info from server
			    responseString = new String(EntityUtils.toString(resEntity));
				JSONObject jsonResponse = new JSONObject(responseString);
				httpObject.setJsonObject(jsonResponse);		
				httpObject.getResponeAlert();
				state   = httpObject.getState();
				alertId = httpObject.getAlertId();
				
				logger.log(Level.INFO, "- server response: " + responseString);
				
				if(type == RECORD) {
					mHandler.post(thSendalert);
				}
				if(type == CALLPANIC) {
					mHandler.sendEmptyMessage(UPDATE_CALL_PANIC_SUCCESS);
				}
				
				if (resEntity != null) {
					 Log.i("SOSBeacon HttpResponse", responseString);
				}
			} catch (Exception e) {
				handler.sendEmptyMessage(MESSAGE_CONNECT_EXCEPTION);
				e.printStackTrace();
				logger.log(Level.FATAL, "- exception error: " + e.getMessage());
			}
		}
	}
	
	public static void show(Context context) {
		show(context, false);
	}
	
	public static void show(Context context, Boolean back) {
		final Intent intent = new Intent(context, NeedHelpActivity.class);
		intent.putExtra(BACK, back);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.home, menu);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		finish();
		callActivity(item.getItemId(), this);		
		return super.onMenuItemSelected(featureId, item);
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			finish();
			SosBeaconActivity.show(NeedHelpActivity.this, false);
			return true;
		}
		return super.onKeyDown(keyCode, event);
	}
	
	@Override
	protected void onResume() {
		super.onResume();
		if(_recordInUse) {
			try {
				 lstFileVoice =  callRecord.stop();
				 _recordInUse = false;
			} catch (IOException e) {
				e.printStackTrace();
			}
			if (callPanic) {
			    //update and send alert to server
				callPanic = false;
				sendAlertSever(CALLPANIC);
			}
		}
	}
	
	public void onStart() {
	   super.onStart();
	   FlurryAgent.onStartSession(this, getPrefs(FLURRY_API_KEY));
	}
	
	public void onStop() {
	   super.onStop();
	   FlurryAgent.onEndSession(this);
	}
	
	@Override
	protected void onDestroy() {
		super.onDestroy();
		try {
			if (mProgressDialog != null) mProgressDialog.dismiss();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
}
