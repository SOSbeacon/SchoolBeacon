package cnc.sosbeacon;

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
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.ImageButton;
import cnc.sosbeacon.http.HttpSolution;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class CheckInOkActivity extends GeneralActivity {
	private ProgressDialog mProgressDialog;
	
	protected final Logger logger = LoggerFactory.getLogger(CheckInOkActivity.class); 
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.check_in_im_ok);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		
		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		leftBtn.setVisibility(View.INVISIBLE);
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		rightBtn.setVisibility(View.INVISIBLE);
		
		ImageButton btOk = (ImageButton) findViewById(R.id.btOkEasy);
		ImageButton btCheckIn = (ImageButton) findViewById(R.id.btCheckIn);
		ImageButton btCancel = (ImageButton) findViewById(R.id.btCancel);
		
		final AlertDialog.Builder confirmAlert = new AlertDialog.Builder(this);
		confirmAlert.setTitle(R.string.confirm);
		
		confirmAlert.setNegativeButton(R.string.btnCancel, null);
		
		btOk.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				sendCheckInOK();
			}
		});
		btCheckIn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
				CheckInActivity.show(CheckInOkActivity.this);
			}
		});
		btCancel.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
				SosBeaconActivity.show(CheckInOkActivity.this, false);
			}
		});
	}
	
	private  void sendCheckInOK() {
		try {
			mProgressDialog = ProgressDialog.show(this, "", getString(R.string.lblCheckingIn) + "...");
		} catch (Exception e) {
			e.printStackTrace();
		}
		
		final Handler checkInHandler = new Handler() {
			@Override
			public void handleMessage(Message msg) {
				super.handleMessage(msg);
				try {
					  mProgressDialog.hide();
				}  
				catch (Exception e) {}
				
				String checkInMessage = "";
				if (msg.what == 1) {
					checkInMessage = getString(R.string.check_in_success);
				} else if (msg.what == 2) {
					checkInMessage = msg.obj.toString();
				}
				AlertDialog.Builder mCheckInFinish = new AlertDialog.Builder(CheckInOkActivity.this);
				mCheckInFinish.setPositiveButton(R.string.btnOK, null);
				mCheckInFinish.setMessage(checkInMessage);
				mCheckInFinish.show();
			}
		};
		
		new Thread(new Runnable() {
			public void run() {
				Looper.prepare();
				String message = getString(R.string.checkInOk);
				logger.log(Level.INFO, "start sending check-in message");
				getLocation();
				try {
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
					//alertlogType
					httpObject.put(TO_GROUP, mAlertSendToGroup); // Send to default group		
					httpObject.put(MESSAGE, message);
					httpObject.getParams();
					List<NameValuePair> params = httpObject.getParams();
					
					logger.log(Level.INFO, "- URL: " + postURL + ", request params: " + params.toString());
					
					// Request server and receive result
					UrlEncodedFormEntity ent = new UrlEncodedFormEntity(params, HTTP.UTF_8);
					post.setEntity(ent);
					HttpResponse responsePOST = client.execute(post);			
					HttpEntity resEntity = responsePOST.getEntity();
					//get info from server
				    String responseString = new String(EntityUtils.toString(resEntity));
					JSONObject jsonResponse = new JSONObject(responseString);
					httpObject.setJsonObject(jsonResponse);		
					httpObject.getResponeAlert();
					Message msg = new Message();
					msg.what = 1;
					
					logger.log(Level.INFO, "- server response: " + responseString);
					
					checkInHandler.sendMessage(msg);
				} catch (Exception e) {
					Message msg = new Message();
					msg.what = 2;
					msg.obj = e.getMessage();
					checkInHandler.sendMessage(msg);
					e.printStackTrace();
					logger.log(Level.FATAL, "- exception error: " + e.getMessage());
				}
			}
		}).start();
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
			SosBeaconActivity.show(CheckInOkActivity.this, false);
			return true;
		}
		return super.onKeyDown(keyCode, event);
	}
	
	public static void show(Context context) {
		final Intent intent = new Intent(context, CheckInOkActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
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
