package cnc.schoolbeacon;

import java.util.ArrayList;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import cnc.schoolbeacon.R;
import cnc.schoolbeacon.util.ContactAccessor;
import cnc.schoolbeacon.util.ContactInfo;
import cnc.schoolbeacon.util.ContactInfoList;
import cnc.schoolbeacon.util.EmailValidator;
import cnc.schoolbeacon.util.TextUtil;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class ContactAdd extends GeneralActivity implements View.OnClickListener {

	private final Logger logger = LoggerFactory.getLogger(ContactAdd.class);
	private TextView txtName;
	private TextView txtEmail;
	private TextView txtVoicePhone;
	private TextView txtTextPhone;
	private TextView tvNotice;
	private String groupId;
	private String groupName;
	private String whatDo = NEW;
	private ContactInfoList listContact;
	private ContactInfo contactInfo;
	private static final int PICK_CONTACT = 3;
	String name = "";
	String email= "";
	String textPhone = "";
	String voicePhone = "";
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.contact_add);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		// get group id
		groupId = getIntent().getStringExtra(GROUP_ID) != null ? getIntent().getStringExtra(GROUP_ID) : "";
		groupName = getIntent().getStringExtra(GROUP_NAME) != null ? getIntent().getStringExtra(GROUP_NAME) : "";
		listContact = getIntent().getParcelableExtra(CONTACT_LIST);
		
		contactInfo = (ContactInfo) getIntent().getSerializableExtra(CONTACT_INFO);
		
		Toast.makeText(this, groupId, Toast.LENGTH_SHORT);

		// get id from form
		txtName = (TextView) findViewById(R.id.txtUserName);
		txtEmail = (TextView) findViewById(R.id.txtEmail);
		txtTextPhone = (TextView) findViewById(R.id.txtTextPhone);
		txtVoicePhone = (TextView) findViewById(R.id.txtVoicePhone);
		tvNotice = (TextView) findViewById(R.id.tvNotice);
		
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		rightBtn.setText(R.string.btnSave);

		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		leftBtn.setText(R.string.btnBack);
		leftBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				confirmSave(false, 0);
			}
		});

		TextView title = (TextView) findViewById(R.id.title);
		title.setText(R.string.contact_add);

		// set event listener
		rightBtn.setOnClickListener(this);
		Button btnAddFromContact = (Button) findViewById(R.id.btn_add_exist_contacts);
		btnAddFromContact.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				showContactActivity();
			}
		});
		
		if (contactInfo != null) {
			title.setText(R.string.contact_edit);
			if (contactInfo.getType().equalsIgnoreCase("1")) { // default contact
				//rightBtn.setVisibility(View.INVISIBLE);
				btnAddFromContact.setVisibility(View.GONE);
				tvNotice.setVisibility(View.VISIBLE);
			}
			if (!contactInfo.getName().equalsIgnoreCase("null")) {
				name = contactInfo.getName();
			} else {
				contactInfo.setName("");
			}
			if (!contactInfo.getEmail().equalsIgnoreCase("null")) {
				email = contactInfo.getEmail();
			} else {
				contactInfo.setEmail("");
			}
			if (!contactInfo.getTextphone().equalsIgnoreCase("null")) {
				textPhone = contactInfo.getTextphone();
			} else {
				contactInfo.setTextphone("");
			}
			if (!contactInfo.getVoicephone().equalsIgnoreCase("null")) {
				voicePhone = contactInfo.getVoicephone();
			} else {
				contactInfo.setVoicephone("");
			}
			fillForm();
		} else {
			contactInfo = new ContactInfo();
		}
		
		if (getIntent().getStringExtra(DO) != null) {
			whatDo =  getIntent().getStringExtra(DO);
		}
	}
	
	void updateTempContactList (ContactInfo receiveContactInfo) {
		if (whatDo.equalsIgnoreCase(NEW)) {
			 receiveContactInfo.setId("0");
			 receiveContactInfo.setStatus(NEW);
			 listContact.add(receiveContactInfo);
		 }
		 
		 if (whatDo.equalsIgnoreCase(UPDATE)) {
			 receiveContactInfo.setStatus(UPDATE);
			 // update temporary contact
			 for (int i = 0; i < listContact.size(); i ++) {
				ContactInfo ci = listContact.get(i);
				if (ci.getId().equalsIgnoreCase(receiveContactInfo.getId())) {
					listContact.set(i, receiveContactInfo);
				}
			 }
		 }
	}
	
	void fillForm() {
		txtName.setText(name);
		txtEmail.setText(email);
		txtTextPhone.setText(textPhone);
		txtVoicePhone.setText(voicePhone);
		if (contactInfo.getType().equalsIgnoreCase("1")) {
			txtName.setEnabled(false);
			txtEmail.setEnabled(false);
			txtTextPhone.setEnabled(false);
			txtVoicePhone.setEnabled(false);
		}
		formartPhoneNumber();
	}
	
	void getFormValues() {
		name = txtName.getText().toString().trim();
		email= txtEmail.getText().toString().trim();
		textPhone = txtTextPhone.getText().toString().trim();
		voicePhone = txtVoicePhone.getText().toString().trim();
	}
	
	void formartPhoneNumber() {
		textPhone = txtTextPhone.getText().toString().trim();
		textPhone = TextUtil.removePhoneCharacters(textPhone);
		voicePhone = txtVoicePhone.getText().toString().trim();
		voicePhone = TextUtil.removePhoneCharacters(voicePhone);
		txtTextPhone.setText(android.telephony.PhoneNumberUtils.formatNumber(textPhone));
		txtVoicePhone.setText(android.telephony.PhoneNumberUtils.formatNumber(voicePhone));
	}
	
	private void showContactActivity() {
		Intent intent = ContactAccessor.getContactPickerIntent();
		try {
			startActivityForResult(intent, PICK_CONTACT);
		}
		catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	@Override
	public void onActivityResult(int reqCode, int resultCode, Intent data) {
	    super.onActivityResult(reqCode, resultCode, data);
	    switch(reqCode) {
	       case (PICK_CONTACT):
	           if (resultCode == Activity.RESULT_OK) {
				 Uri contactData = data.getData();
				 Cursor c = managedQuery(contactData, null, null, null, null);
				 if (c.moveToFirst()) {
					 if (contactInfo == null) {
						 contactInfo = new ContactInfo();
					 }
					 //whatDo = NEW;
					 txtName.setEnabled(true);
					 txtEmail.setEnabled(true);
					 txtTextPhone.setEnabled(true);
					 txtVoicePhone.setEnabled(true);
				     getPhoneInfo(c);
				 }
	        }
	    }
	}
	
	private void confirmSave(final boolean saveServer, final int callActivity) {
		getFormValues();
		boolean checkChanged = false;
		if (whatDo.equalsIgnoreCase(NEW)) {
			if (!name.equalsIgnoreCase("") || !email.equalsIgnoreCase("") || !textPhone.equalsIgnoreCase("") || !voicePhone.equalsIgnoreCase("")) {
				checkChanged = true;
			}
		}
		if (whatDo.equalsIgnoreCase(UPDATE)) {
			if (!name.equals(contactInfo.getName().trim()) || !email.equals(contactInfo.getEmail()) || !textPhone.equals(contactInfo.getTextphone()) || !voicePhone.equals(contactInfo.getVoicephone())) { 
				checkChanged = true;
			}
		}
		if (checkChanged) {
			AlertDialog.Builder alChange = new AlertDialog.Builder(this);
			alChange.setMessage(R.string.contact_item_save_confirm_message);
			alChange.setNegativeButton(" No ", new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					dialog.dismiss();
					finish();
					if (callActivity == 0) {
					   ContactList.showCallBack(ContactAdd.this, BACK, groupId, groupName, listContact);
					} else {
						callActivity(callActivity, ContactAdd.this);
					}
				}
			});
			alChange.setNeutralButton(" Yes ", new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					saveContact(saveServer, callActivity);
				}
			});
			alChange.show();
		} else {
			finish();
			if (callActivity == 0) {
				ContactList.showCallBack(ContactAdd.this, BACK, groupId, groupName, listContact);
			} else {
				callActivity(callActivity, ContactAdd.this);
			}
		}
	}
	
	public void getPhoneInfo(Cursor c) { 
        ContactAccessor.getPhoneInfo(this, c); 
        txtTextPhone.setText(ContactAccessor.Number);
        txtName.setText(ContactAccessor.Name);
        txtEmail.setText(ContactAccessor.Email);
    } 
	
	public static void show(Context context, String groupid, ArrayList<ContactInfo> listContact, String groupName) {
		final Intent intent = new Intent(context, ContactAdd.class);
		intent.putExtra(GROUP_ID, groupid);
		intent.putExtra(CONTACT_LIST, listContact);
		intent.putExtra(GROUP_NAME, groupName);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}

	
	public static void showCallBack(Context context, String whatDo, ContactInfo contactInfo,String groupid, String groupName,ArrayList<ContactInfo> listContact) {
		final Intent intent = new Intent(context, ContactAdd.class);
		intent.putExtra(CONTACT_INFO, contactInfo);
		intent.putExtra(GROUP_ID, groupid);
		intent.putExtra(CONTACT_LIST, listContact);
		intent.putExtra(GROUP_NAME, groupName);
		intent.putExtra(DO, whatDo);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}
	
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.title_right_btn:
		    saveContact(false, 0);		
			break;
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.home, menu);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		confirmSave(true, item.getItemId());
		return super.onMenuItemSelected(featureId, item);
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			confirmSave(false, 0);
			return true;
		} else {
			return super.onKeyDown(keyCode, event);
		}	
	}
	
	void saveContactInfo() {
		contactInfo.setName(name);
		contactInfo.setEmail(email);
		contactInfo.setTextphone(textPhone);
		contactInfo.setVoicephone(voicePhone);
	}

	private boolean saveContact(boolean saveServer, int callActivityId) {
		boolean saved = false;
		// name and text phone or nane and email require
		if (!contactInfo.getType().equalsIgnoreCase("1")) {
			formartPhoneNumber();
			getFormValues();
			
			if ((!name.equalsIgnoreCase("")	&& !textPhone.equalsIgnoreCase("")) || 
				(!name.equalsIgnoreCase("")	&& !email.equalsIgnoreCase(""))) {
				if (checkvalidate()) {
					saveContactInfo();
					updateTempContactList(contactInfo);
					setResult(RESULT_OK, getIntent());
					if (saveServer) {  // send to server and save
						ContactInfo.saveContacts(this, listContact, groupId, getToken());
						if (callActivityId == 0) {
						   ContactList.showCallBack(ContactAdd.this, BACK, groupId, groupName, listContact);
						} else {
							callActivity(callActivityId, ContactAdd.this);
					    }
					} else {
				       ContactList.showCallBack(this, whatDo, groupId, groupName, listContact); // back to list and save lateer
					}
					finish();
					saved = true;
				}
			} else {
				Toast.makeText(this, R.string.contact_add_require, 100).show();
				saved = false;
			}
		} else {
			Toast.makeText(this, R.string.contact_not_allow_delete, Toast.LENGTH_SHORT).show();
			saved = false;
		}
		return saved;
	}
	
	
	private boolean checkvalidate(){
		if (!textPhone.equalsIgnoreCase("")) {
			if (!TextUtil.allowPhoneCharacters(textPhone)) {
				Toast.makeText(this, getString(R.string.validate_textphone) + getString(R.string.numberInvalid), Toast.LENGTH_LONG).show();
				return false;
			}
		}	
		if (!voicePhone.equalsIgnoreCase("")) {		
			if (!TextUtil.allowPhoneCharacters(voicePhone)) {
				Toast.makeText(this, getString(R.string.validate_voicephone) + getString(R.string.numberInvalid), Toast.LENGTH_LONG).show();
				return false;
			}
		}
		EmailValidator emailValidator  = new EmailValidator();
		if (!email.trim().equalsIgnoreCase("")) {
			if(!emailValidator.validate(email)){
				Toast.makeText(this, R.string.validate_email, Toast.LENGTH_LONG).show();
				txtEmail.setText("");
				return false;
			}
		}
		return true;
	}
}
