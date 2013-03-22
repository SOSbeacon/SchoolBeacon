package cnc.schoolbeacon;

import android.content.Context;
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
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.ToggleButton;
import cnc.schoolbeacon.R;

import com.flurry.android.FlurryAgent;

public class GoodSamaritanSettingActivity  extends SettingActivity {
	private Spinner spinPanicMax;
	private Spinner spinSamaMax;
	private ToggleButton btnToggNear;
	private ToggleButton btnToggReceive;
	private Handler handler;
	private int[] distanceArray;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.samaritan_settings);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		TextView title = (TextView) findViewById(R.id.title);
		title.setText(R.string.menu_item_good_samaritan_settings);
		
		Button leftBtn   = (Button) findViewById(R.id.title_left_btn);
		Button rightBtn  = (Button) findViewById(R.id.title_right_btn);
		leftBtn.setText(R.string.btnBack);
		rightBtn.setText(R.string.btnSave);
	
		distanceArray = getResources().getIntArray(R.array.distanceValues);
		
		btnToggNear= (ToggleButton) findViewById(R.id.btntogg_near);
		btnToggReceive  = (ToggleButton) findViewById(R.id.btntogg_receive);	
		spinPanicMax =  (Spinner)findViewById(R.id.spinner_panic_maximum);
		spinSamaMax  =  (Spinner)findViewById(R.id.spinner_samaritant_maximum);
	    ArrayAdapter<CharSequence> adapterDistance = ArrayAdapter.createFromResource(this, R.array.distance, android.R.layout.simple_spinner_item);
	    adapterDistance.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
	    spinPanicMax.setAdapter(adapterDistance);
	    spinSamaMax.setAdapter(adapterDistance);
	    spinPanicMax.setClickable(false);
	    spinSamaMax.setClickable(false);
	    
	    btnToggNear.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				if (btnToggNear.isChecked()){
					spinPanicMax.setClickable(true);
				} else {
					spinPanicMax.setClickable(false);
				}
			}
		});
	    
	    btnToggReceive.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				if (btnToggReceive.isChecked()) {
					spinSamaMax.setClickable(true);
				} else {
					spinSamaMax.setClickable(false);
				}
			}
		});
	        
	    leftBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				submitChange(true, true);
			}
		});
		
		rightBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				submitChange(true, false);
			}
		});
		
		handler = new Handler() {
			@Override
			public void handleMessage(Message msg) {
				if (msg.what == MESSAGE_FINISH) {
					finish();
					ManagerActivity.show(GoodSamaritanSettingActivity.this);
				}
				if (msg.what == MESSAGE_FINISH_ACTIVITY) {
					finish();
					if (callMenuId > 0) {
						callActivity(callMenuId, GoodSamaritanSettingActivity.this);
						callMenuId = 0;
					} else {
						ManagerActivity.show(GoodSamaritanSettingActivity.this);
					}
				}
				super.handleMessage(msg);
			}
		};
		bindDataToForm();
	}
	
	private void submitChange(boolean finishActivity, Boolean confirm) {
		if (checkChange()) {
			mPanicStatus = btnToggNear.isChecked() ? "1" : "0";
			mPanicRange = String.valueOf(distanceArray[spinPanicMax.getSelectedItemPosition()]);
			mGoodSamaritanStatus = btnToggReceive.isChecked() ? "1" : "0";
			mGoodSamaritanRange = String.valueOf(distanceArray[spinSamaMax.getSelectedItemPosition()]);
			saveSettingsConfirm(handler, finishActivity, false, confirm);
		} else {
			if (finishActivity && confirm) {
				handler.sendEmptyMessage(MESSAGE_FINISH_ACTIVITY);
			} else {
				Toast.makeText(this, R.string.setting_not_change, Toast.LENGTH_SHORT).show();
			}
		}
	}
	
	private void bindDataToForm(){
		if (mPanicStatus.equals("1")) {
			btnToggNear.setChecked(true);
			spinPanicMax.setClickable(true);
		}
		if (mGoodSamaritanStatus.equals("1")) {
			btnToggReceive.setChecked(true);
			spinSamaMax.setClickable(true);
		}
		for (int i = 0; i < distanceArray.length; i++) {
			if (String.valueOf(distanceArray[i]).equals(mPanicRange)) {
				spinPanicMax.setSelection(i);
			}
			if (String.valueOf(distanceArray[i]).equals(mGoodSamaritanRange)) {
				spinSamaMax.setSelection(i);
			}
		}
	}
	
	
	private boolean checkChange() {
		String panicCheck = btnToggNear.isChecked()? "1" : "0";
		if(!mPanicStatus.equals(panicCheck)){
			return true;
		}
		if (panicCheck.equals("1")) {
			if (!mPanicRange.equals(String.valueOf(distanceArray[spinPanicMax.getSelectedItemPosition()]))) {
				return true;
			}
		}
		String samaritanCheck = btnToggReceive.isChecked() ? "1" : "0";
		if(!mGoodSamaritanStatus.equals(samaritanCheck)){
			return true;
		}
		if (samaritanCheck.equals("1")) {
			if(!mGoodSamaritanRange.equals(String.valueOf(distanceArray[spinSamaMax.getSelectedItemPosition()]))) {
				return true;
			}
		}
		return false;
	}

	public static void show(Context context) {
		final Intent intent = new Intent(context, GoodSamaritanSettingActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		context.startActivity(intent);
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.home, menu);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		int menuId = item.getItemId();
		callMenuId = menuId;
		submitChange(true, true);
		return true;
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			submitChange(true, true);
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
