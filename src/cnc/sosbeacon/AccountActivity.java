package cnc.sosbeacon;

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
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import cnc.sosbeacon.util.EmailValidator;

import com.flurry.android.FlurryAgent;

public class AccountActivity  extends SettingActivity {
	private EditText txtUserName;
	private EditText txtEmail;
	private EditText txtPhoneNumber; 
	private EditText txtPassword;
	private Handler handler;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.account);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		TextView title = (TextView) findViewById(R.id.title);
		title.setText(R.string.lblAccount);
		
		Button leftBtn   = (Button) findViewById(R.id.title_left_btn);
		Button rightBtn  = (Button) findViewById(R.id.title_right_btn);
		leftBtn.setText(R.string.btnBack);
		rightBtn.setText(R.string.btnSave);
		
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
		
		txtUserName   = (EditText) findViewById(R.id.txtUserName);
		txtEmail  = (EditText)findViewById(R.id.txtEmail);		
		txtPassword   = (EditText)findViewById(R.id.txtPassword);		
		txtPhoneNumber = (EditText)findViewById(R.id.txtPhonenumber);
		
		handler = new Handler() {
			@Override
			public void handleMessage(Message msg) {
				if (msg.what == MESSAGE_FINISH) {
				}
				if (msg.what == MESSAGE_FINISH_ACTIVITY) {
					finish();
					if (callMenuId > 0) {
						callActivity(callMenuId, AccountActivity.this);
						callMenuId = 0;
					} else {
						ManagerActivity.show(AccountActivity.this);
					}
				}
				super.handleMessage(msg);
			}
		};
		bindDataToForm();
	}
 
	private void submitChange(boolean finishActivity, Boolean confirm) {
		if (checkValidate() && checkChange()) {
			mUserName = txtUserName.getText().toString();
			mPassword = txtPassword.getText().toString();
			mEmail = txtEmail.getText().toString();
			saveSettingsConfirm(handler, finishActivity, true, confirm);
		} else {
			if (finishActivity && confirm) {
				handler.sendEmptyMessage(MESSAGE_FINISH_ACTIVITY);
			} else {
				Toast.makeText(this, R.string.setting_not_change, Toast.LENGTH_SHORT).show();
			}
		}
	}
	
	private void bindDataToForm() {
		//txtPassword.setText(mPassword);	
		txtUserName.setText(mUserName);
		txtEmail.setText(mEmail);
		txtPhoneNumber.setText(android.telephony.PhoneNumberUtils.formatNumber(mPhoneNumber));
	}
	
	private boolean checkChange() {
		if(!mUserName.equals(txtUserName.getText().toString())){
			return true;
		}
		if(!txtPassword.getText().toString().equalsIgnoreCase("")){
			return true;
		}
		if(!mEmail.equals(txtEmail.getText().toString())){
			return true;
		}
		return false;
	}
	
	private boolean checkValidate() {
		/*if(txtUserName.getText().toString().length() == 0){
			Toast.makeText(this, R.string.validate_username, Toast.LENGTH_LONG).show();
			return false;
		}*/
		// if password is set
		String password = txtPassword.getText().toString();
		if(!password.equalsIgnoreCase("") &&  password.length() < 6){
			Toast.makeText(this, R.string.validate_password, Toast.LENGTH_LONG).show();
			return false;
		}
		
		String email = txtEmail.getText().toString();
		EmailValidator emailValidator = new EmailValidator(); 
		if(!email.equalsIgnoreCase("") && !emailValidator.validate(email)){
			Toast.makeText(this, R.string.validate_email, Toast.LENGTH_LONG).show();
			return false;
		}
		return true;
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
	
	public static void show(Context context) {
		final Intent intent = new Intent(context, AccountActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
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
}
