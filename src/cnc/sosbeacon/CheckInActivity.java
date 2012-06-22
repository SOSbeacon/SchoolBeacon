/**
 * 
 */
package cnc.sosbeacon;

import java.util.ArrayList;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.ContentResolver;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.ActivityInfo;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.preference.PreferenceManager;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ListAdapter;
import android.widget.TextView;
import android.widget.Toast;
import cnc.sosbeacon.adapter.SampleMessageAdapter;
import cnc.sosbeacon.provider.SosBeaconCheckinMessageProvider;
import cnc.sosbeacon.util.ContactAccessor;
import cnc.sosbeacon.util.GroupInfo;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class CheckInActivity extends GeneralActivity {
	
	private final Logger logger = LoggerFactory.getLogger(CheckInActivity.class);
	EditText mCheckInTo;
	EditText mMessage;
	Button mCheckIn, mCancel;
	EditText mSelectMessage;
	TextView tvSmsLimit;
	LinearLayout messageLayout;
	View viewCategory;
	AlertDialog.Builder alCheckIn, alSampleMessage;
	Dialog adSampleMessage;
	String mSingleContact;
	String mMessageBody;
	String defaultMessage;
	String alertId = "";
	private int sendToGroupId;
	private String sendToGroupName = "";
	private ProgressDialog mProgressDialog;
	private AlertDialog.Builder mCheckInFinish;
	private ArrayList<GroupInfo> category = new ArrayList<GroupInfo>();
	private static final int PICK_CONTACT = 3;
	private String[] samples;
	private String TAG = "CheckInActivity";
	private String[] checkInNormal;
	private String[] checkInAlert;
	private Uri contentUri = SosBeaconCheckinMessageProvider.CONTENT_URI;
	private String noticeMessage = "";
	private Boolean isLoadCheckin = false;
	int maxLength = 75;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);
		
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.check_in);
		mSelectMessage = (EditText) findViewById(R.id.etSelectMessage);
		messageLayout = (LinearLayout) findViewById(R.id.messageLayout);
		mCheckInTo = (EditText) findViewById(R.id.check_in_to);
		mMessage = (EditText) findViewById(R.id.check_in_message);
		mCheckIn = (Button) findViewById(R.id.check_in_submit);
		mCancel = (Button) findViewById(R.id.check_in_cancel);
		tvSmsLimit = (TextView) findViewById(R.id.tvSmsLimit);
		
		checkInNormal = getResources().getStringArray(R.array.checkInNormal);
		checkInAlert = getResources().getStringArray(R.array.checkInAlert);
		defaultMessage = getString(R.string.checkInOk);
		SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);
		sendToGroupName = prefs.getString("LastCheckIn", getString(R.string.check_in_to_default));
		//sendToGroupName = getString(R.string.check_in_to_default);
		tvSmsLimit.setText(String.format(getString(R.string.offline_sms_char_remain), maxLength));
		mMessage.addTextChangedListener(new TextWatcher() {
			public void onTextChanged(CharSequence s, int start, int before, int count) {}
			public void beforeTextChanged(CharSequence s, int start, int count, int after) {}
			public void afterTextChanged(Editable s) {
				maxLength = 75 - s.length();
				tvSmsLimit.setText(String.format(getString(R.string.offline_sms_char_remain), maxLength));
//				if (mMessage.getText().toString().length() >= 95) {
//					tvSmsLimit.setVisibility(View.VISIBLE);
//				} else {
//					tvSmsLimit.setVisibility(View.GONE);
//				}
			}
		});
		mMessage.setText(defaultMessage);
		messageLayout.setVisibility(View.GONE);
		
		mCheckIn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				confirmCheckin();		
			}
		});
		mSelectMessage.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				sampleMessagesSelect();
			}
		});
		mCheckInTo.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				try {
					checkInSelect();
				} catch (Exception e) {
					e.printStackTrace();
				}
			}
		});
		mCancel.setOnClickListener( new OnClickListener() {
			public void onClick(View v) {
				finish();
				CheckInOkActivity.show(CheckInActivity.this);
			}
		});
		mCheckInTo.setText(sendToGroupName);
		// Get Intent Extra and set to form
		sendToGroupId = Integer.valueOf(mAlertSendToGroup);
		noticeMessage = getIntent().getStringExtra(MESSAGE);
		if (noticeMessage == null) noticeMessage = "";
		// Progress Dialog
		mProgressDialog =  new ProgressDialog(CheckInActivity.this);
		mProgressDialog.setCancelable(false);
		// Create successful Dialog
		mCheckInFinish = new AlertDialog.Builder(this);
		mCheckInFinish.setPositiveButton(R.string.btnOK, new DialogInterface.OnClickListener() {
			public void onClick(DialogInterface dialog, int which) {
				dialog.dismiss();
			}
		});
		mCheckInFinish.setMessage(R.string.check_in_success);
		setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
	}

	@Override
	protected void onResume() {
		super.onResume();
		if (!isLoadCheckin) {
			isLoadCheckin = true;
			getSampleMessages();
			mProgressDialog.setMessage(getString(R.string.loading));
			try {
				mProgressDialog.show();
			} catch (Exception e) {
				e.printStackTrace();
			}
			Thread trLoad = new Thread(loadCheckIn);
			trLoad.start();
		}
	}
	
	void getSampleMessages() {
		ArrayList<String> recentMessage = new ArrayList<String>();
		String checkInOk = getString(R.string.checkInOk); 
		recentMessage.add(checkInOk);
		try {
			int count = 0;
			ContentResolver cr = this.getContentResolver(); 
			Cursor c = cr.query(contentUri, null, null, null, "count DESC, _id ASC");
			if (c != null) {	
				if (c.moveToFirst()) {
					count = c.getCount();
				}
				c.close();
			}
			if (count < 9) { // Create default check-in messages
				cr.delete(contentUri, null, null);
				for(int i = 0; i < checkInNormal.length; i++) {
					ContentValues values = new ContentValues();
					values.put("message", checkInNormal[i]);
					values.put("count", 0);
					cr.insert(contentUri, values);
				}
				for(int i = 0; i < checkInAlert.length; i++) {
					ContentValues values = new ContentValues();
					values.put("message", checkInAlert[i]);
					values.put("count", 0);
					cr.insert(contentUri, values);
				}
			}
			c = cr.query(contentUri, null, null, null, "_id ASC"); // Get messages
			if (c != null) {			
				if (c.getCount() > 0) {
					int limit = 9;
					if(c.moveToFirst()) {
						count = 0;
			    		do {
			    			count ++;
			    			String message = c.getString(c.getColumnIndex(MESSAGE)).trim();
			    			if (!message.equalsIgnoreCase("")) {
			    				recentMessage.add(message);
			    			}
			    	    } while(count < limit && c.moveToNext());
					}
				}
				c.close();
			}
		} catch (Exception e) {
			logger.log(Level.FATAL, "getSampleMessages, Exception: " + e.getMessage());
		}
		recentMessage.add(getString(R.string.enter_message));
		samples = recentMessage.toArray(new String[]  {});
	}
	
	// sample message select
	private void sampleMessagesSelect() {
		final ListAdapter adapter =  new SampleMessageAdapter(this, android.R.layout.select_dialog_item, samples, checkInNormal, checkInAlert);
		//final ListAdapter adapter =  new SampleMessageAdapter(this, android.R.layout.select_dialog_item, samples);			
		alSampleMessage = new AlertDialog.Builder(this);
		alSampleMessage.setAdapter(adapter, new DialogInterface.OnClickListener() {
			public void onClick(DialogInterface dialog, int which) {
				CharSequence msg = (CharSequence) adapter.getItem(which);
				if (msg.toString().equalsIgnoreCase(getString(R.string.enter_message))) {
					mMessage.setText("");
					messageLayout.setVisibility(View.VISIBLE);
				} else {
					mMessage.setText(msg);
					messageLayout.setVisibility(View.GONE);
				}
				mSelectMessage.setText(msg);
			}
		});
		alSampleMessage.setTitle(getString(R.string.select_message));
		alSampleMessage.show();
	}
	
	// Check in to select
	private void checkInSelect() throws Exception {
		//final ListAdapter adapter = ArrayAdapter.createFromResource(this, R.array.check_in_to, android.R.layout.select_dialog_item);
		final ListAdapter adapter =  new ArrayAdapter<GroupInfo>(this, android.R.layout.select_dialog_item, category);
		alCheckIn = new AlertDialog.Builder(this);
		alCheckIn.setAdapter(adapter, new DialogInterface.OnClickListener() {
			public void onClick(DialogInterface dialog, int which) {
				GroupInfo selectTo = (GroupInfo) adapter.getItem(which);
				if (selectTo.getName().equalsIgnoreCase(getString(R.string.singleContact))) { // If select from phone contact
					dialog.dismiss();
					phoneContactsSelect();
				} else {
					mCheckInTo.setText(selectTo.getName());	
				}
				sendToGroupId = Integer.valueOf(selectTo.getId());
			}
		});
		alCheckIn.setTitle(getString(R.string.lblCheckingIn));
		alCheckIn.show();
	}
	
	private void confirmCheckin() {
		String to = mCheckInTo.getText().toString();
		mMessageBody = mMessage.getText().toString();
		mSingleContact = "";
		if (!to.trim().equalsIgnoreCase("") && !mMessageBody.trim().equalsIgnoreCase("")) {
			Boolean fromGroup = false; // Check check in from group or phone number
			for (GroupInfo item : category) {				
				if (item.getName().equalsIgnoreCase(to)) { // if select a group
					fromGroup = true;
				} 
			}
			if (fromGroup) {
				mSingleContact = "";
			} else {
				mSingleContact = to;
			}
			AlertDialog.Builder attachConfirm = new AlertDialog.Builder(this);
			attachConfirm.setMessage(R.string.attachFilesConfirm);
			attachConfirm.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					finish();
					RecordActivity.show(CheckInActivity.this, TAG, "", sendToGroupId, mSingleContact, mMessageBody);
				}
			});
			attachConfirm.setNegativeButton(R.string.btnNo, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					mProgressDialog.setMessage(getString(R.string.sendingCheckIn));
					try {
						mProgressDialog.show();
					} catch (Exception e) {
						e.printStackTrace();
					}
					sendCheckIn(checkinHandler, sendToGroupId, mSingleContact, mMessageBody);
				}
			});
			attachConfirm.show();
			/*
			 * Last chosen contact
			 */
			SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
			prefs.edit().putString("LastCheckIn", mCheckInTo.getText().toString()).commit();
		} else { 			
			Toast.makeText(this, getString(R.string.check_in_require), Toast.LENGTH_SHORT).show();
			if (mMessageBody.trim().equalsIgnoreCase("")) {
				mMessage.setText("");
			}
		}
	}
	
	private Handler checkinHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			super.handleMessage(msg);
			saveCheckinMessage(mMessageBody);
			try {
				if (mProgressDialog.isShowing()) mProgressDialog.hide();
			} catch (Exception e) {
				e.printStackTrace();
			}
			String checkInMessage = msg.obj.toString();
			showCheckinResult(checkInMessage);
			getSampleMessages();
		}
	};
	
	private void showCheckinResult(String checkInMessage) {
		AlertDialog.Builder mCheckInFinish = new AlertDialog.Builder(CheckInActivity.this);
		mCheckInFinish.setPositiveButton(R.string.btnOK, null);
		mCheckInFinish.setMessage(checkInMessage);
		mCheckInFinish.show();
		resetForm();
	}
	
	private void resetForm() { 
		try {
			GroupInfo firstGroup = category.get(0); // set default to first group (family)
			sendToGroupId = Integer.valueOf(firstGroup.getId());
			//sendToGroupName = getString(R.string.check_in_to_default);
			SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);
			sendToGroupName = prefs.getString("LastCheckIn", getString(R.string.check_in_to_default));
			mCheckInTo.setText(sendToGroupName);
			mMessage.setText(defaultMessage);
			mSelectMessage.setText(defaultMessage);
			messageLayout.setVisibility(View.GONE);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	Runnable loadCheckIn = new Runnable() {
		Handler hCheckIn = new Handler() {
			@Override
			public void handleMessage(Message msg) {
				super.handleMessage(msg);
				try {
					if (mProgressDialog.isShowing()) mProgressDialog.hide(); 
				}  
		        catch (Exception e) {
		        	e.printStackTrace();
		        }
				if (category.size() > 0) {
					/*/ Check select to default group
					for(int i = 0; i < category.size(); i++) {
						GroupInfo checkGroup = category.get(i);
						if (checkGroup.getId().equalsIgnoreCase(String.valueOf(sendToGroupId))) {
							sendToGroupName = checkGroup.getName();
							mCheckInTo.setText(sendToGroupName);
						}
					} */
				}
				if (!noticeMessage.equalsIgnoreCase("")) {
					showCheckinResult(noticeMessage);
				}
				resetForm();
			}
		};
		
		public void run() {
			Looper.prepare();
			try {
				category = getContactGroups();
				// add "All group"
				GroupInfo groupInfo = new GroupInfo();
				groupInfo.setId("0"); // groupId = 0 : Send to all groups
				groupInfo.setName(getString(R.string.allGroups));
				category.add(groupInfo);
				// add "Single contact"
				groupInfo = new GroupInfo();
				groupInfo.setId("-1");
				groupInfo.setName(getString(R.string.singleContact));
				category.add(groupInfo);
			} catch (Exception e) {
				e.printStackTrace();
			}
			hCheckIn.sendEmptyMessage(0);
		}
	};

	public void getPhoneNumbers(Cursor c) {
		ContactAccessor.getPhoneInfo(this, c);
		String number = ContactAccessor.Number;
        if (!number.equalsIgnoreCase("")) {
        	mCheckInTo.setText(number);
        } else {
        	Toast.makeText(this, getString(R.string.check_in_contact_has_not_phone), Toast.LENGTH_LONG).show();
        }
    } 
	
	private void phoneContactsSelect() {
		Intent intent = ContactAccessor.getContactPickerIntent();
		startActivityForResult(intent, PICK_CONTACT);
	}
	
	@Override
	public void onActivityResult(int reqCode, int resultCode, Intent data) {
	    super.onActivityResult(reqCode, resultCode, data);
	    switch(reqCode){
	       case (PICK_CONTACT):
	         if (resultCode == Activity.RESULT_OK) {
	             Uri contactData = data.getData();
	             Cursor c = managedQuery(contactData, null, null, null, null);
	             if (c.moveToFirst()) {
	                 getPhoneNumbers(c);       
	             }
	        }
	    }
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			finish();
			CheckInOkActivity.show(this);
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
		return super.onMenuItemSelected(featureId, item);
	}

	
	public static void show(Context context) {
		show(context, "");
	}
	
	public static void show(Context context, String noticeMessage) {
		final Intent intent = new Intent(context, CheckInActivity.class);
		intent.putExtra(MESSAGE, noticeMessage);
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
