package cnc.schoolbeacon;

import java.util.ArrayList;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.view.ContextMenu;
import android.view.ContextMenu.ContextMenuInfo;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnCreateContextMenuListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.AdapterContextMenuInfo;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import cnc.schoolbeacon.R;
import cnc.schoolbeacon.adapter.ContactListAdapter;
import cnc.schoolbeacon.http.HttpRequest;
import cnc.schoolbeacon.util.ContactInfo;
import cnc.schoolbeacon.util.ContactInfoList;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class ContactList extends GeneralActivity implements View.OnClickListener, Runnable {
	
	private final Logger logger = LoggerFactory.getLogger(ContactList.class); 
	ListView listView;
	private ContactInfoList listContact;
	protected static final int CONTEXTMENU_VIEWITEM = 0;
	protected static final int CONTEXTMENU_ADDITEM = 1;
	protected static final int CONTEXTMENU_REMOVEITEM = 2;
	ProgressDialog mProgressDialog;
	private String groupId, groupName;
	String TAG = "contactlist";
	int activityId = 0;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);		
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.contact_wrap);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		
		TextView title = (TextView) findViewById(R.id.title);
		final Button rightBtn  = (Button) findViewById(R.id.title_right_btn);	
		rightBtn.setText(R.string.btnAdd);
		rightBtn.setOnClickListener(this);
		rightBtn.setOnCreateContextMenuListener(new OnCreateContextMenuListener() {
			public void onCreateContextMenu(ContextMenu menu, View v,
					ContextMenuInfo menuInfo) {
				MenuInflater inflater = getMenuInflater();
				inflater.inflate(R.menu.add_contacts, menu);
				
			}
		});
		
		final Button leftBtn  = (Button) findViewById(R.id.title_left_btn);	
		leftBtn.setText(R.string.btnBack);
		leftBtn.setOnClickListener(this);
		// Create contact list
		listView=(ListView)findViewById(R.id.list);	
		listView.setOnItemClickListener(mListener);
		registerForContextMenu(listView);
		listView.setOnCreateContextMenuListener(new OnCreateContextMenuListener() {
			public void onCreateContextMenu(ContextMenu menu, View v, ContextMenuInfo menuInfo) {
                menu.add(0, CONTEXTMENU_VIEWITEM,0, R.string.contact_edit); 
                menu.add(0, CONTEXTMENU_REMOVEITEM,0, R.string.contact_remove);
			} 
		});	 
		
		// Get Intent Extra
		groupId = getIntent().getStringExtra(GROUP_ID) != null ? getIntent().getStringExtra(GROUP_ID) : "";
		groupName  = getIntent().getStringExtra(GROUP_NAME) != null ? getIntent().getStringExtra(GROUP_NAME) : "";
		String whatDo = getIntent().getStringExtra(DO) != null ? getIntent().getStringExtra(DO) : "";
		title.setText(groupName);
		
		mProgressDialog =  new ProgressDialog(this);
		mProgressDialog.setMessage(getString(R.string.loading));
		if (whatDo.equalsIgnoreCase("")) {
			//Get list contact from server if not update, edit
			mProgressDialog.show();
			Thread tr = new Thread(this);
			tr.start();
		 } else {
			 listContact = getIntent().getParcelableExtra(CONTACT_LIST);
			 innitAdapter();
		 }
	}
	
	Handler handle = new Handler() {
		@Override
		public void handleMessage(android.os.Message msg) {
			if (msg.what == MESSAGE_SAVED) {
				callActivity();
			}
			if (msg.what == MESSAGE_FINISH) {
				mProgressDialog.hide();
				innitAdapter();
			}
		};
	};
	
	private void innitAdapter() {
		// use tempContactList to remove contact was marked delete
		ContactInfoList tempContactList = (ContactInfoList) listContact.clone();
		for (int i = 0; i < listContact.size(); i ++) {
			ContactInfo ci = listContact.get(i);
			if (ci != null) {
				try {
					if (ci.getStatus().equalsIgnoreCase(DELETE)) { 
						tempContactList.remove(ci);
					}
				} catch (Exception e) {
					e.printStackTrace();
				}
			}
		}
		ContactListAdapter contactAdapter = new ContactListAdapter(this, android.R.layout.simple_list_item_1, tempContactList );
		listView.setAdapter(contactAdapter);
	}
	
	private OnItemClickListener mListener = new OnItemClickListener() {
		public void onItemClick(AdapterView<?> arg0, View view, int position,
				long id) {
			view.showContextMenu();
		}
	};

	private ContactInfoList getListContact(String groupId) {
		ContactInfoList listContactTemp = new ContactInfoList();
		try {
			HttpClient client = new DefaultHttpClient();
			String getURL = String.format(getApiUrl(CONTACT_GET_URL), groupId, getToken());
			HttpGet httpGet = new HttpGet(getURL);
			HttpResponse response = client.execute(httpGet);
			String content = HttpRequest.GetText(response);					
			JSONObject jo = new JSONObject(content);
			JSONObject contactsJSON = jo.getJSONObject("response").getJSONObject("data");
			logger.log(Level.INFO, "getListContact, contactsJSON: " + contactsJSON);
			JSONArray jnames = contactsJSON.names();
			JSONArray jvalues = contactsJSON.toJSONArray(jnames);
		
			for (int i = 0; i < jvalues.length(); i ++) {
				ContactInfo ci = new ContactInfo();
				JSONObject  item = jvalues.getJSONObject(i);
				if (item != null) {
					ci.setId(item.getString(ID));
					ci.setName(item.getString(NAME));
					ci.setEmail(item.getString(EMAIL));
					ci.setTextphone(item.getString(TEXT_PHONE).equalsIgnoreCase("null") ? "" : android.telephony.PhoneNumberUtils.formatNumber(item.getString(TEXT_PHONE)));
					ci.setVoicephone(item.getString(VOICE_PHONE).equalsIgnoreCase("null") ? "" : android.telephony.PhoneNumberUtils.formatNumber(item.getString(VOICE_PHONE)));
					ci.setType(item.getString(TYPE));
					listContactTemp.add(ci);
				}
			}
			return listContactTemp;
		} catch (Exception e) {
			logger.log(Level.ERROR, "getListContact, Exception: " + e.getMessage());
		}
		return listContactTemp;
	}
	
	private Boolean checkContactsChange() {
		Boolean changed = false;
		for (int i = 0; i < listContact.size(); i ++) {
			ContactInfo ci = listContact.get(i);
			if (ci != null) {
				if (!ci.getStatus().equalsIgnoreCase("")) {
					changed = true;
					break; // only one item changed, set changed
				}
			}
		}
		return changed;
	}
	
	boolean saveContactConfirm(final int callActivity) {
		activityId = callActivity;
		// Check if changed
		if (checkContactsChange()) {
			AlertDialog.Builder confirm = new AlertDialog.Builder(ContactList.this);
			confirm.setTitle(R.string.contact_save_confirm);
			confirm.setMessage(R.string.contact_save_confirm_message);
			confirm.setNegativeButton(R.string.btnNo, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					callActivity();
				}
			});
			confirm.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					ContactInfo.saveContacts(ContactList.this, listContact, groupId, getToken(), handle);
				}
			});
			confirm.show();
			
		} else {
			callActivity();
		}
		return true;
	}
	
	private void callActivity() {
		finish();
		if (activityId > 0) {
			callActivity(activityId, ContactList.this);
			activityId = 0;
		} else { 
			ContactCategory.show(ContactList.this);
		}
	}
	
	@Override
	public boolean onKeyDown(int keyCode, android.view.KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			return saveContactConfirm(0);
		} else {
			return super.onKeyDown(keyCode, event);
		}
	};
	
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.title_right_btn:
			v.showContextMenu();
			break;			
		case R.id.title_left_btn:
			saveContactConfirm(0);
			break;
		}	
	}
	
	@Override
	public boolean onContextItemSelected(MenuItem aItem) {
		AdapterContextMenuInfo menuInfo = (AdapterContextMenuInfo) aItem.getMenuInfo();
		ContactInfo contactInfo;
		switch (aItem.getItemId()) {
			case CONTEXTMENU_VIEWITEM:
				contactInfo = (ContactInfo) listView.getAdapter().getItem(menuInfo.position);
				ContactAdd.showCallBack(ContactList.this, UPDATE, contactInfo, groupId, groupName, listContact);
				finish();
				return true;
			case CONTEXTMENU_ADDITEM:
				return true;
			case CONTEXTMENU_REMOVEITEM:
				contactInfo = (ContactInfo) listView.getAdapter().getItem(menuInfo.position);
				removeTempContact(contactInfo);			
				innitAdapter();
				return true; 
			case R.id.menu_enter_contact:
				TextView title =  (TextView) findViewById(R.id.title);
				setResult(RESULT_OK, getIntent());
				ContactAdd.show(this, groupId, listContact, title.getText().toString());
				finish();
				break;
			case R.id.menu_import_google_contact:
				ImportContactsActivity.show(this, OAUTH_GOOGLE, getString(R.string.google), Integer.valueOf(groupId), groupName);
				finish();
				break;	
			case R.id.menu_import_yahoo_contact:
				ImportContactsActivity.show(this, OAUTH_YAHOO, getString(R.string.yahoo), Integer.valueOf(groupId), groupName);
				finish();
				break;
		}
		return false;
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.home, menu);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		Integer menuId = item.getItemId();
		// check to anti conflict with onContextItemSelected
		switch (menuId) {
		case R.id.menu_item_home:
		case R.id.menu_item_review:
		/*case R.id.menu_item_groups:*/
		case R.id.menu_item_more:	
			saveContactConfirm(menuId);
			break;
		default:
			break;
		}
		
		return super.onMenuItemSelected(featureId, item);
	}
	
	private void removeTempContact(ContactInfo contactInfo) {
		// not remove contact default
		if (!contactInfo.getType().equalsIgnoreCase("1")) {
			for (int i = 0; i < listContact.size(); i ++) {
				ContactInfo ci =  listContact.get(i);
				if (ci == contactInfo) {
					listContact.get(i).setStatus(DELETE);
				}
			}
		} else {
			Toast.makeText(this, R.string.contact_not_allow_delete, Toast.LENGTH_SHORT).show();
		}
	}
	
	public void run() {
		listContact = getListContact(groupId);
		handle.sendEmptyMessage(MESSAGE_FINISH);
	}
	
	public void onStart() {
	   super.onStart();
	   FlurryAgent.onStartSession(this, getPrefs(FLURRY_API_KEY));
	}
		
	public void onStop() {
	   super.onStop();
	   FlurryAgent.onEndSession(this);
	}
	
	public static void show(Context context, String position) {
		show(context, position, "");
	}
	
	public static void show(Context context, String position, String viewTitle) {
		final Intent intent = new Intent(context, ContactList.class);
		intent.putExtra(GROUP_ID, position);
		intent.putExtra(GROUP_NAME, viewTitle);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}
	
	public static void showCallBack(Context context, String whatDo, String position, String viewTitle, ArrayList<ContactInfo> listContact) {
		final Intent intent = new Intent(context, ContactList.class);
		intent.putExtra(GROUP_ID, position);
		intent.putExtra(GROUP_NAME, viewTitle);
		intent.putExtra(CONTACT_LIST, listContact);
		intent.putExtra(DO, whatDo);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}
}