/**
 * 
 */
package cnc.sosbeacon;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import cnc.sosbeacon.http.HttpRequest;

import com.flurry.android.FlurryAgent;

public class TellUsActivity extends GeneralActivity implements Runnable {
	EditText etSendTo;
	EditText etCC;
	EditText etSubject;
	EditText etMessage;
	TextView tvTo;
	String sendTo, sendCC, subject, message;
	Integer type = 2; // 1 = contact, 2 = tell friends
	String host = "";
	String mailAccount = "";
	String mailPass = "";
	String mailDelivery = "";
	String port = "";
	String smtpSecure = "";
	Boolean sendToEnable;
	Button leftBtn;
	Button rightBtn;
	AlertDialog.Builder alSend;
	private ProgressDialog mProgressDialog;
	private AlertDialog.Builder mSendResult;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.tell_us);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		
		leftBtn = (Button) findViewById(R.id.title_left_btn);
		rightBtn = (Button) findViewById(R.id.title_right_btn);
		leftBtn.setText(R.string.btnCancel);
		rightBtn.setText(R.string.tellus_send);
		leftBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				ManagerActivity.show(TellUsActivity.this);
				finish();
			}
		});
		rightBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				sendMessagePrepare();
			}
		});
		
		TextView title = (TextView) findViewById(R.id.title);
		
		
		tvTo = (TextView) findViewById(R.id.tvSendTo);
		etSendTo = (EditText) findViewById(R.id.send_to);
		etCC = (EditText) findViewById(R.id.send_cc);
		etSubject = (EditText) findViewById(R.id.subject);
		etMessage = (EditText) findViewById(R.id.message);
		mProgressDialog =  new ProgressDialog(this);
		mProgressDialog.setMessage(getString(R.string.tellus_sending));
		
		mSendResult = new AlertDialog.Builder(this);
		mSendResult.setPositiveButton(R.string.btnOK, new DialogInterface.OnClickListener() {
			public void onClick(DialogInterface dialog, int which) {
				resetForm();
			}
		});
		
		sendToEnable = getIntent().getBooleanExtra(TO, false);
		if (sendToEnable) {
			title.setText(R.string.menu_item_send_friend);
			tvTo.setVisibility(View.GONE);
			etSendTo.setVisibility(View.VISIBLE);
		} else {
			title.setText(R.string.menu_item_contact);
			tvTo.setVisibility(View.VISIBLE);
	    	etSendTo.setVisibility(View.GONE);
		}
		
		getEmailAccountInfo();
	}
	
	private void resetForm() {
		etMessage.setText("");
		etSubject.setText("");
		finish();
		ManagerActivity.show(TellUsActivity.this);
	}
	
	public static void show(Context context, boolean sendToEnable) {
		final Intent intent = new Intent(context, TellUsActivity.class);
		intent.putExtra(TO, sendToEnable);
		intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		context.startActivity(intent);
	}

	private boolean checkValidate() {
		sendTo = etSendTo.getText().toString().trim();
		sendCC = etCC.getText().toString().trim();
		subject = etSubject.getText().toString().trim();
		message = etMessage.getText().toString().trim();
		if (sendToEnable) {
			if (sendTo.equalsIgnoreCase("")) {
				Toast.makeText(this, getString(R.string.validate_send_to), Toast.LENGTH_SHORT).show();
				return false;
			}
			if (!sendCC.equalsIgnoreCase("")) {
				sendTo = sendTo + "," + sendCC;
			}
		} else {
			sendTo = mailDelivery;
			if (!sendCC.equalsIgnoreCase("")) {
				sendTo = mailDelivery + "," + sendCC;
			}
		}
		if (subject.equalsIgnoreCase("") || message.equalsIgnoreCase("")) {
			Toast.makeText(this, getString(R.string.tellus_require), 100).show();
			return false;
		}
		return true;
	}
	
	private void sendMessagePrepare() {
		if (checkValidate()) {
			mProgressDialog.show();
			rightBtn.setEnabled(false);
			Thread threadCheckIn = new Thread(this);
			threadCheckIn.start();
		}
	}
	
	Handler sendUsHandle = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			if (mProgressDialog.isShowing()) {
				mProgressDialog.hide();
			}
			if (msg.what == 1) {
				mSendResult.setMessage(mMessage);
				mSendResult.show();
			}
			if (msg.what == 2) {
				Toast.makeText(TellUsActivity.this, mMessage, Toast.LENGTH_LONG).show();
			}
			rightBtn.setEnabled(true);
		};
	};
	
	private void sendMessage() {
		/*GMailSender sender = new GMailSender(host, mailAccount, mailPass, port, smtpSecure);
		String fromName = getString(R.string.phoneNumber) + " " + mPhoneNumber;
		if (!mUserName.equalsIgnoreCase("")) {
		 fromName = mUserName + " - " + fromName;
		}
		if (!mEmail.equalsIgnoreCase("")) {
		 fromName += " - " + mEmail;
		}
		String mailSubject = String.format(MAIL_SUBJECT, fromName, etSubject.getText().toString());
		String mailMessage = String.format(MAIL_MESSAGE, fromName, etMessage.getText().toString());
		sender.sendMail(mailSubject, mailMessage, mailAccount, sendTo);
		*/
    	HttpClient client = new DefaultHttpClient();
 		HttpPost httpPost = new HttpPost(getApiUrl(MAIL_ACCOUNT_URL));
 		List<NameValuePair> params = new ArrayList<NameValuePair>();
 		params.add(new BasicNameValuePair(FORMAT, "json"));
 		params.add(new BasicNameValuePair(PHONE_ID, mPhoneId));
 		params.add(new BasicNameValuePair(IMEI, mImei));
 		params.add(new BasicNameValuePair(TOKEN, mToken));
 		params.add(new BasicNameValuePair(EMAILS, sendTo));
 		params.add(new BasicNameValuePair(SUBJECT, subject));
 		params.add(new BasicNameValuePair(MESSAGE, message));
 		params.add(new BasicNameValuePair(TYPE, String.valueOf(type)));
 		try {
 			UrlEncodedFormEntity urlEncodedFormEntity = new UrlEncodedFormEntity(params);
 			httpPost.setEntity(urlEncodedFormEntity);
 		} catch (Exception e) {
 				e.printStackTrace();
 		}
 		HttpResponse response;
 		Message msg = new Message();
 		try {
 			response = client.execute(httpPost);
 			String responseContent = HttpRequest.GetText(response);
 			JSONObject responseJson = new JSONObject(responseContent);
 			responseJson = responseJson.getJSONObject(RESPONSE);
 			Boolean success = responseJson.getBoolean(STATE);
 			mMessage = responseJson.getString(MESSAGE);
 			msg.what = (success ?  1 :  2);
 		} catch (Exception e) {
 			e.printStackTrace();
 			mMessage = e.getMessage();
 			msg.what = 2;
 		}
 		sendUsHandle.sendMessage(msg);
	}

	public void run() {
		sendMessage();
	} 
	
	public void getEmailAccountInfo() {
		HttpClient client = new DefaultHttpClient();
		HttpGet get = new HttpGet(getApiUrl(MAIL_ACCOUNT_URL));
		try {
			HttpResponse response = client.execute(get);
			JSONObject object = null;
			try {
				object = new JSONObject(HttpRequest.GetText(response));
			} catch (JSONException e) {
				e.printStackTrace();
			}
			if (object != null) {
				host =  object.getJSONObject("response").getString("server");
			    mailAccount =  object.getJSONObject("response").getString("email");
			    mailPass =  object.getJSONObject("response").getString("password");
			    mailDelivery =  object.getJSONObject("response").getString("delivery");
			    port =  object.getJSONObject("response").getString("port");
			    smtpSecure =  object.getJSONObject("response").getString("SMTPSecure");
			    if (!sendToEnable) {
			    	tvTo.setText(mailDelivery);
			    }
			}
		} catch (Exception e) {
			mMessage = e.getMessage();
			sendUsHandle.sendEmptyMessage(2);
		}  
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
			ManagerActivity.show(TellUsActivity.this);
			return true;
		}
		return super.onKeyDown(keyCode, event);
	}
	
	public void onStart() {
	   super.onStart();
	   FlurryAgent.onStartSession(this, getPrefs(FLURRY_API_KEY));
	}
	
	public void onStop() {
	   super.onStop();
	   FlurryAgent.onEndSession(this);
	}
}