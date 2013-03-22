package cnc.schoolbeacon;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.TextView;
import cnc.schoolbeacon.R;
import cnc.schoolbeacon.provider.SosBeaconCheckinMessageProvider;

import com.flurry.android.FlurryAgent;

public class StorageSettingActivity  extends SettingActivity {
	private Button btnReset;
	private Button btnClear;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.storage_settings);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		TextView title = (TextView) findViewById(R.id.title);
		title.setText(R.string.lblStorage);
		
		Button leftBtn   = (Button) findViewById(R.id.title_left_btn);
		Button rightBtn  = (Button) findViewById(R.id.title_right_btn);
		leftBtn.setText(R.string.btnBack);
		rightBtn.setVisibility(View.INVISIBLE);
		
		leftBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
				ManagerActivity.show(StorageSettingActivity.this);
			}
		});
		
		btnReset = (Button) findViewById(R.id.btn_reset);
		btnReset.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				new AlertDialog.Builder(StorageSettingActivity.this).setMessage(R.string.setting_reset_confirm)
				.setNegativeButton(R.string.btnNo, null)
				.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						resetAll();
					}
				}).show();
			}
		});
		
		btnClear = (Button) findViewById(R.id.btn_clear);
		btnClear.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
				ManagerActivity.show(StorageSettingActivity.this);
			}
		});
		
		mHandler.sendEmptyMessage(MESSAGE_FINISH);
	}
 
	private void resetAll() {
		preferences.edit().clear().commit();
		try {
			this.getContentResolver().delete(SosBeaconCheckinMessageProvider.CONTENT_URI, "_id > 0", null);
		} catch (Exception e) {
			e.printStackTrace();
		}
		finish();
		LoginActivity.show(this);
	}
	
	public static void show(Context context) {
		final Intent intent = new Intent(context, StorageSettingActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			finish();
			ManagerActivity.show(StorageSettingActivity.this);
			return true;
		}
		return super.onKeyDown(keyCode, event);
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
		return true;
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
