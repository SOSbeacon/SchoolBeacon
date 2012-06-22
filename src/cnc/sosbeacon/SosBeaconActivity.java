package cnc.sosbeacon;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class SosBeaconActivity extends GeneralActivity {
	
	private final Logger logger = LoggerFactory.getLogger(SosBeaconActivity.class); 
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.main);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		
		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		leftBtn.setVisibility(View.INVISIBLE);
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		rightBtn.setVisibility(View.INVISIBLE);
		
		Button okButton = (Button) findViewById(R.id.btOk);
		okButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
				CheckInOkActivity.show(SosBeaconActivity.this);
			}
		});
		Button helpButton = (Button) findViewById(R.id.btHelp);
		helpButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
				NeedHelpActivity.show(SosBeaconActivity.this);
			}
		});
		int usageCount = preferences.getInt(USAGE_COUNT, 0);
		Boolean phoneFirstActivated = preferences.getBoolean(PHONE_FIRST_ACTIVATED, false);
		
		sendCurrentLocation();
		String configProblem = "";
		Boolean promptToConfig = false;
		Boolean appFirstLoad = getIntent().getBooleanExtra(APP_FIRST_LOAD, false);
		
		// Check location services
		try {
			String locationMessage = getLocation();
			if (mLocation == null) {
				configProblem += locationMessage;
				promptToConfig = true;
			}
		}
		catch (Exception e) {
			e.printStackTrace();
		}
		if (mEmergencyNumber.toString().trim().equalsIgnoreCase("0") || mEmergencyNumber.toString().trim().equalsIgnoreCase("")) {
			configProblem += "\n" + getString(R.string.set_emergency_number);
			promptToConfig = true;
		}
		// if normal contact is empty 
		if (appFirstLoad && mCountContact <= 0) {
			AlertDialog.Builder adContact = new AlertDialog.Builder(this);
			adContact.setMessage(R.string.updateContactPrompt);
			adContact.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					finish();
					ContactCategory.show(SosBeaconActivity.this);
				}
			});
			adContact.setNegativeButton(R.string.btnNotNow, null);
			adContact.show();
		}
		// if password is not set
		if (phoneFirstActivated && mPassword.equalsIgnoreCase("")) {
			AlertDialog.Builder adPassword = new AlertDialog.Builder(this);
			adPassword.setMessage(R.string.updateInforPrompt);
			adPassword.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					finish();
					AccountActivity.show(SosBeaconActivity.this);
				}
			});
			adPassword.setNegativeButton(R.string.btnNotNow, null);
			adPassword.show();
		}
		if (appFirstLoad && promptToConfig) {
			AlertDialog.Builder adProblem = new AlertDialog.Builder(this);
			adProblem.setMessage(configProblem);
			adProblem.setNeutralButton(R.string.btnOK, null);
			adProblem.show();
		}
		// show video demo if user first active phone
		if (appFirstLoad && phoneFirstActivated) {
			preferences.edit().putBoolean(PHONE_FIRST_ACTIVATED, false).commit();
			AlertDialog.Builder askViewVideo = new AlertDialog.Builder(this);
			askViewVideo.setMessage(R.string.ask_view_video);
			askViewVideo.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					dialog.dismiss();
					Intent videoIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(getApiUrl(DEMO_URL)));
					videoIntent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
					startActivity(videoIntent);
				}
			});
			askViewVideo.setNegativeButton(R.string.btnNotNow, null);
			askViewVideo.show();
		}
		usageCount ++;
		preferences.edit().putInt(USAGE_COUNT, usageCount).commit();
	}
	
	public static void show(Context context, Boolean isFirstLoad) {
		final Intent intent = new Intent(context, SosBeaconActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		intent.putExtra(APP_FIRST_LOAD, isFirstLoad); // true if first call when app load, false if call from menu
		context.startActivity(intent);
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.home, menu);
		MenuItem item = menu.findItem(R.id.menu_item_home);
	    item.setChecked(true);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		finish();
		callActivity(item.getItemId(), this);
		return super.onMenuItemSelected(featureId, item);
	}
	
	@Override
	protected void onResume() {
		if (getPhoneId().equalsIgnoreCase("")) {
			finish();
		}
		super.onResume();
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
