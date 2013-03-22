package cnc.schoolbeacon;

import java.util.ArrayList;

import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.provider.BaseColumns;
import android.provider.Contacts.People;
import android.provider.Contacts.PeopleColumns;
import android.provider.Contacts.PhonesColumns;
import android.view.ContextMenu;
import android.view.ContextMenu.ContextMenuInfo;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnCreateContextMenuListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.AdapterContextMenuInfo;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.Toast;
import cnc.schoolbeacon.adapter.ContactAdapter;
import cnc.schoolbeacon.adapter.ContactDeviceInfo;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class ContactActivity extends GeneralActivity implements View.OnClickListener {
	
	private final Logger logger = LoggerFactory.getLogger(ContactActivity.class); 
	ListView listView;
	private ArrayList<ContactDeviceInfo> listContact;
	protected static final int CONTEXTMENU_VIEWITEM = 0;
	protected static final int CONTEXTMENU_ADDITEM = 1;
	protected static final int CONTEXTMENU_REMOVEITEM = 2;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);		
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.contact_wrap);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		
		final Button rightBtn  = (Button) findViewById(R.id.title_right_btn);	
		rightBtn.setText(R.string.btnSave);
		listContact= getListContact();
		listView=(ListView)findViewById(R.id.list);		
		innitAdapter();
		listView.setOnItemClickListener(mListener);
	
		registerForContextMenu(listView);
		listView.setOnCreateContextMenuListener(new OnCreateContextMenuListener() {

			public void onCreateContextMenu(ContextMenu menu, View v,
					ContextMenuInfo menuInfo) {
				menu.setHeaderTitle("ContextMenu");
                menu.add(0, CONTEXTMENU_VIEWITEM,0, "View Contact"); 
                menu.add(0, CONTEXTMENU_ADDITEM,0, "Add Contact");
                menu.add(0, CONTEXTMENU_REMOVEITEM,0, "Remove Contact");	
			}      
         }); 
		
		 rightBtn.setOnClickListener(this);
		 rightBtn.setVisibility(View.INVISIBLE);
		 
		 final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		 leftBtn.setText(R.string.btnBack);
		 leftBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
			}
		});
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
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.home, menu);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		callActivity(item.getItemId(), this);		
		return super.onMenuItemSelected(featureId, item);
	}
	
	private void innitAdapter(){
		ContactAdapter contactAdapter = new  ContactAdapter(this, android.R.layout.simple_list_item_1,listContact );
		listView.setAdapter(contactAdapter);
	}
	
	private OnItemClickListener mListener = new OnItemClickListener(){
		public void onItemClick(AdapterView<?> arg0, View view, int position, long id) {
			ContactDeviceInfo contactInfo = (ContactDeviceInfo) listView.getAdapter().getItem(position);
			addContact(contactInfo.getId(),contactInfo.getName(),contactInfo.getNumber());
		}
	};
	
	private ArrayList<ContactDeviceInfo> getListContact(){
		ArrayList<ContactDeviceInfo> listContactTemp = new ArrayList<ContactDeviceInfo>();
		String[] projection = new String[] {
                BaseColumns._ID, 
                PeopleColumns.NAME,
                PhonesColumns.NUMBER
             };

		//Get the base URI for the People table in the Contacts content provider.
		Uri contacts =  People.CONTENT_URI;
		//Make the query. 
		Cursor managedCursor = managedQuery(contacts,
		            projection, // Which columns to return 
		            null,       // Which rows to return (all rows)
		            null,       // Selection arguments (none)
		            // Put the results in ascending order by name
		            PeopleColumns.NAME + " ASC");
		if (managedCursor != null) {
			if (managedCursor.moveToFirst()) {
				do {
					ContactDeviceInfo itemContact = new ContactDeviceInfo();
					if (itemContact != null) {
						itemContact.setId(managedCursor.getString(managedCursor.getColumnIndex(BaseColumns._ID)).toString());
						String username = managedCursor.getString(managedCursor.getColumnIndex(PeopleColumns.NAME))!=null?managedCursor.getString(managedCursor.getColumnIndex(PeopleColumns.NAME)).toString() : "No Name";
						itemContact.setName(username);
						String nummber = managedCursor.getString(managedCursor.getColumnIndex(PhonesColumns.NUMBER)) != null ? managedCursor.getString(managedCursor.getColumnIndex(PhonesColumns.NUMBER)).toString() : "";
						itemContact.setNumber(nummber);
						listContactTemp.add(itemContact);
					}
					
				} while (managedCursor.moveToNext());
			}
		}
		return listContactTemp;
		}
	
	public static void show(Context context) {
		final Intent intent = new Intent(context, ContactActivity.class);
		context.startActivity(intent);
		
	}
	
	@Override
	public boolean onContextItemSelected(MenuItem aItem) {
		AdapterContextMenuInfo menuInfo = (AdapterContextMenuInfo) aItem.getMenuInfo();
		ContactDeviceInfo contactInfo = (ContactDeviceInfo) listView.getAdapter()
		.getItem(menuInfo.position);
		switch (aItem.getItemId()) {
		case CONTEXTMENU_VIEWITEM:
			
			Toast.makeText(this, contactInfo.getName(), Toast.LENGTH_SHORT).show();
			return true;
		
		case CONTEXTMENU_ADDITEM:
			Toast.makeText(this,  contactInfo.getId(), Toast.LENGTH_SHORT).show();
			addContact(contactInfo.getId(),contactInfo.getName(),contactInfo.getNumber());
			
			return true;
		case CONTEXTMENU_REMOVEITEM:
			
			listContact.remove(contactInfo);
			innitAdapter();
			return true; 
		}
		return false;
	}

	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.title_right_btn:
			responeInfo();
			break;
		}
	}
	
	private void responeInfo(){
		Intent returnResult = new Intent();
		String listName =  listContact.get(0).getName();
		String listID   =  listContact.get(0).getId();
		for(int i=1; i< listContact.size();i++){
			listName += "," + listContact.get(i).getName();
			listID   += "," + listContact.get(i).getId();
		}
		returnResult.putExtra("listName", listName);
		returnResult.putExtra("listId", listID);
		setResult(RESULT_OK,returnResult);
		finish();
	}
	
	
	private void addContact(String id, String name, String number) {
		finish();	
	}
}
