package cnc.sosbeacon;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.PendingIntent;
import android.app.ProgressDialog;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.telephony.SmsManager;
import android.telephony.TelephonyManager;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

public class OffLineModeActivity extends Activity{
	String latitude = "n/a";
	String longitude = "n/a";
	EditText etMessage;
	TextView tvCharRemain;
	Button btnCancel;
	Spinner spnBroadCast;
	Spinner spnCall;
	protected String mImei;	
	SharedPreferences preferences;
	
	private ProgressDialog pro;
	private String messResult;
	int type = 0;
	int maxLength = 75;
	ProgressDialog proReturn;
	@Override
	public boolean dispatchTouchEvent(MotionEvent ev) {
		TelephonyManager telephonyManager = (TelephonyManager)getSystemService(Context.TELEPHONY_SERVICE);
		mImei = telephonyManager.getDeviceId();
		proReturn = new ProgressDialog(this);
		proReturn.setCancelable(false);
		proReturn.setMessage(getString(R.string.offline_return_main_screen));
		/*
		 * Check coordinate of touch on screen
		 * If it outside edit text, we will hide soft keyboard
		 */
		if(ev.getAction() == 0){
			int[] location = new int[2];
			etMessage.getLocationInWindow(location);
			if(ev.getRawY() < location[1]){
				InputMethodManager imm = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
				imm.hideSoftInputFromWindow(etMessage.getApplicationWindowToken(), 0);
			}
		}
		return super.dispatchTouchEvent(ev);
	}
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.offline);
		getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
		btnCancel = (Button) findViewById(R.id.btnCancel);
		preferences = PreferenceManager.getDefaultSharedPreferences(this);
		final String[] broadCast = getResources().getStringArray(R.array.broadCast);
		final String[] selectGroup = getResources().getStringArray(R.array.selectGroup);
		spnBroadCast = (Spinner) findViewById(R.id.spnSelectBroadcast);
		
		try{
			LocationManager mlocManager = (LocationManager)getSystemService(Context.LOCATION_SERVICE);
			LocationListener mlocListener = new MyLocationListener();
			mlocManager.requestLocationUpdates( LocationManager.GPS_PROVIDER, 0, 0, mlocListener);
			Location curloc = mlocManager.getLastKnownLocation( LocationManager.GPS_PROVIDER);
			latitude = String.valueOf(curloc.getLatitude());
			longitude = String.valueOf(curloc.getLongitude());
		}catch(NullPointerException e){
			try{
				LocationManager mlocManager = (LocationManager)getSystemService(Context.LOCATION_SERVICE);
				LocationListener mlocListener = new MyLocationListener();
				mlocManager.requestLocationUpdates( LocationManager.NETWORK_PROVIDER, 0, 0, mlocListener);
				Location curloc = mlocManager.getLastKnownLocation( LocationManager.NETWORK_PROVIDER);
				latitude = String.valueOf(curloc.getLatitude());
				longitude = String.valueOf(curloc.getLongitude());
			}catch (Exception ex) {
				Toast.makeText(this, getString(R.string.offline_error_pgs), Toast.LENGTH_SHORT).show();
				Log.d("SOSbeacon","Error : Can't detect location");
			}
		}
		ArrayAdapter<String> adapterSend = new ArrayAdapter<String>(this,
				R.layout.spinner_layout, broadCast);
		adapterSend.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		spnBroadCast.setAdapter(adapterSend);
		
		spnCall = (Spinner) findViewById(R.id.spnCall);
		ArrayAdapter<String> adapterCall = new ArrayAdapter<String>(this,
				R.layout.spinner_layout, selectGroup);
		adapterCall.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		spnCall.setAdapter(adapterCall);
		
		etMessage = (EditText) findViewById(R.id.edtMessage);
		tvCharRemain = (TextView) findViewById(R.id.tvCharRemain);
		tvCharRemain.setText(String.format(getString(R.string.offline_sms_char_remain), maxLength));
		etMessage.addTextChangedListener(new TextWatcher() {
			@Override
			public void onTextChanged(CharSequence s, int start, int before, int count) {
			}
			
			@Override
			public void beforeTextChanged(CharSequence s, int start, int count,
					int after) {
			}
			
			@Override
			public void afterTextChanged(Editable s) {
				int count = maxLength - s.length();
				tvCharRemain.setText(String.format(getString(R.string.offline_sms_char_remain), count));
			}
		});
		Button btnSend = (Button) findViewById(R.id.offline_btn_send);
		btnSend.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View arg0) {
				// {Prefix}|{PhoneId}|{Alert Type}|{Group}|{Latitude}|{Longitude}|{Mess}
				int broadcast = 2;
				if(spnBroadCast.getSelectedItemId() == 1){
					broadcast = 0;
				}
				
				int group = 0;
				if(spnCall.getSelectedItemId() == 2){
					group = 3;
				}else if(spnCall.getSelectedItemId() == 3){
					group = 2;
				}else{
					group = (int) spnCall.getSelectedItemId();
				}
				String mess = "OF|";
				//mess += preferences.getString("id", "")+"|";
				mess += mImei+"|";
				mess += broadcast+"|";
				mess += group+"|";
				mess += latitude+"|";
				mess += longitude+"|";
				mess += etMessage.getText().toString()+"|";
				//tel : +14156898484;
				//etMessage.setText(mess);
				type = 0;
				//test tel : 0974072386
				new AutoSendSMS("+14156898484",mess).execute();
			}
		});
		
		Button btnCall = (Button) findViewById(R.id.offline_btn_call);
		btnCall.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View arg0) {
				int broadcast = 1;
				int group = 0;
				if(spnCall.getSelectedItemId() == 2){
					group = 3;
				}else if(spnCall.getSelectedItemId() == 3){
					group = 2;
				}else{
					group = (int) spnCall.getSelectedItemId();
				}
				String mess = "OF|";
				//mess += preferences.getString("id", "")+"|";
				mess += mImei+"|";				
				mess += broadcast+"|";
				mess += group+"|";
				mess += latitude+"|";
				mess += longitude+"|";
				mess += etMessage.getText().toString()+"|";
				//tel : +14156898484;
				//etMessage.setText(mess);
				type = 1;
				new AutoSendSMS("+14156898484",mess).execute();
			}
		});
		
		btnCancel.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				finish();
			}
		});
	}
	
	/* Class My Location Listener */

	public class MyLocationListener implements LocationListener{
		@Override
		public void onLocationChanged(Location loc)	{
			latitude = String.valueOf(loc.getLatitude());
			longitude = String.valueOf(loc.getLongitude());
		}

		@Override
		public void onProviderDisabled(String provider)	{
		}

		@Override
		public void onProviderEnabled(String provider)	{
		}

		@Override
		public void onStatusChanged(String provider, int status, Bundle extras)	{

		}

	}/* End of Class MyLocationListener */

	class AutoSendSMS extends AsyncTask<Void, Void, Void>{

		private String phoneNumber;
		private String message;
		public AutoSendSMS(String phoneNumber, String message){
			this.phoneNumber = phoneNumber;
			this.message = message;
			pro = new ProgressDialog(OffLineModeActivity.this);
			pro.setCancelable(false);
			pro.setMessage(getString(R.string.offline_sms_sending));
		}
		
		@Override
		protected void onPreExecute() {
			pro.show();
			super.onPreExecute();
		}
		
		@Override
		protected Void doInBackground(Void... params) {
			String SENT = "SMS_SENT";			
			PendingIntent sentPI = PendingIntent.getBroadcast(OffLineModeActivity.this, 0,
		            new Intent(SENT), 0);
		    SmsManager sms = SmsManager.getDefault();
		    Log.d("SOSbeacon","MESS : "+phoneNumber+" "+message);
		    sms.sendTextMessage(phoneNumber, null, message, sentPI, null); 			
			return null;
		}
		
		
	}
	
	@Override
	protected void onResume() {
		String SENT = "SMS_SENT";
		//---when the SMS has been sent---
        registerReceiver(smsSent, new IntentFilter(SENT));		
		super.onResume();
	}
	
	BroadcastReceiver smsSent = new BroadcastReceiver(){
        @Override
        public void onReceive(Context arg0, Intent arg1) {
        	if(pro != null){
        		pro.dismiss();       		
        	}
        	AlertDialog.Builder alertBuilder = new AlertDialog.Builder(OffLineModeActivity.this);
        	alertBuilder.setCancelable(false);
            switch (getResultCode())
            {
                case Activity.RESULT_OK:
        			if(type == 1){
                		etMessage.setText("");
                		spnBroadCast.setSelection(0);
                		spnCall.setSelection(0);
                        String tel = preferences.getString("emergencyNumber", "0");
                        Intent intent = new Intent(Intent.ACTION_CALL, Uri.parse("tel:"+tel));
                        startActivity(intent);                        
                	}else{
                    	new DelayScreen().execute();
                	}			
                    break;
                case SmsManager.RESULT_ERROR_GENERIC_FAILURE:
                   	alertBuilder.setPositiveButton(android.R.string.ok, null);
                	messResult = getString(R.string.offline_sms_error_unknown);
                    alertBuilder.setMessage(messResult);
                    alertBuilder.create().show();
                    break;
                case SmsManager.RESULT_ERROR_NO_SERVICE:
                	alertBuilder.setPositiveButton(android.R.string.ok, null);
                	messResult = getString(R.string.offline_sms_error_no_service);
                    alertBuilder.setMessage(messResult);
                    alertBuilder.create().show();
                    break;
                case SmsManager.RESULT_ERROR_NULL_PDU:
                	alertBuilder.setPositiveButton(android.R.string.ok, null);
                	messResult = getString(R.string.offline_sms_error_unknown);
                    alertBuilder.setMessage(messResult);
                    alertBuilder.create().show();
                    break;
                case SmsManager.RESULT_ERROR_RADIO_OFF:
                	//"SOSbeacon communication failed, no wireless signal, try again later"
                	//"sms transmission failed, no text service available, try again later"
                	//"sms transmission failed, reasons unknown, try again later"
                	alertBuilder.setPositiveButton(android.R.string.ok, null);
                	messResult = getString(R.string.offline_sms_error_radio_off);
                    alertBuilder.setMessage(messResult);
                    alertBuilder.create().show();
                    break;
            }
        }
    };
	@Override
	protected void onPause() {
		unregisterReceiver(smsSent);
		super.onPause();
	}
	
	class DelayScreen extends AsyncTask<Void, Void, Void>{
		@Override
		protected void onPreExecute() {
			proReturn.show();
			super.onPreExecute();
		}

		@Override
		protected Void doInBackground(Void... params) {
			try {
				Thread.sleep(10000);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
			return null;
		}
		
		@Override
		protected void onPostExecute(Void result) {
			try {
				proReturn.dismiss();
			} catch (Exception e) {
			}
//			if(type == 1){
//        		etMessage.setText("");
//        		spnBroadCast.setSelection(0);
//        		spnCall.setSelection(0);
//                String tel = preferences.getString("emergencyNumber", "0");
//                Intent intent = new Intent(Intent.ACTION_CALL, Uri.parse("tel:"+tel));
//                startActivity(intent);                        
//        	}else{
            	Intent i = new Intent(getBaseContext(), SplashScreenActivity.class);
            	startActivity(i);
            	finish();
//        	}							
			super.onPostExecute(result);
		}
		
	}
	
}