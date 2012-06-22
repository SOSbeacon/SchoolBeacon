package cnc.sosbeacon;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.preference.PreferenceManager;
import android.view.KeyEvent;
import android.widget.Toast;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class SplashScreenActivity extends GeneralActivity {

	private final Logger logger = LoggerFactory.getLogger(SplashScreenActivity.class);  
    @Override
    public void onCreate(Bundle savedInstanceState) {
    	super.onCreate(savedInstanceState);
    	setContentView(R.layout.splashscreen);
    	
    	
    	SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);
    	if(!prefs.getBoolean("firstRun", false)){
    		prefs.edit().putBoolean("shutter", true).commit();
    		prefs.edit().putBoolean("firstRun", true).commit();
    	}
    	logger.log(Level.INFO, ">>>>>>>>>> SOSbeacon START <<<<<<<<<<");
    	
        new Thread(new Runnable() {
        	public void run() {
        		requestPhoneData();
        		handler.sendEmptyMessage(0);
            }
    	}).start();
        mProgressDialog.setOnCancelListener(new DialogInterface.OnCancelListener() {
			public void onCancel(DialogInterface dialog) {
				finish();
			}
		});
        mAlertDialog.setOnCancelListener(new DialogInterface.OnCancelListener() {
			public void onCancel(DialogInterface dialog) {
				finish();
			}
		});
        preprocess();
    }
    
    private Handler handler = new Handler() {
    	@Override
    	public void handleMessage(Message msg) {
    		super.handleMessage(msg);
        	if (mResponseCode == CODE_SUCCESS) { // If login is OK
        		finish();
        		SosBeaconActivity.show(SplashScreenActivity.this, true);
    			overridePendingTransition(android.R.anim.fade_in, android.R.anim.fade_out);
        	}
        	if (mResponseCode == CODE_ERROR) { // Login fail
        		finish();
        		LoginActivity.show(SplashScreenActivity.this);
        		//Toast.makeText(SplashScreenActivity.this, mMessage, Toast.LENGTH_LONG).show();
        	}
        	if (mResponseCode == CODE_ACCOUNT_NOT_ACTIVATED) { // Phone has not activated
        		finish();
        		LoginActivity.show(SplashScreenActivity.this, ACTIVATE, mMessage);
    		}
        	if (mResponseCode == CODE_NEW_ACCOUNT) { // Phone is not exist then register new phone
        		finish();
        		TermActivity.show(SplashScreenActivity.this);
        	}
        	if (mResponseCode == 0) { // App exception error
        		//Toast.makeText(SplashScreenActivity.this, mMessage, Toast.LENGTH_LONG).show();
        	}
    	}
    };
    
    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
    	if (keyCode == KeyEvent.KEYCODE_BACK) {
    		finish();
    	}
    	return super.onKeyDown(keyCode, event);
    }
    
    public static void show(Context context) {
		final Intent intent = new Intent(context, SplashScreenActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		context.startActivity(intent);
	}
}