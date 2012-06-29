package cnc.schoolbeacon;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import cnc.schoolbeacon.R;

public class ManagerActivity extends GeneralActivity {
	
	private ListView listCategory;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.contact_category);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);

		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		leftBtn.setVisibility(View.VISIBLE);
		leftBtn.setText(R.string.btnBack);
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		rightBtn.setVisibility(View.INVISIBLE);
		TextView title = (TextView) findViewById(R.id.title);
		title.setText(R.string.menu_item_more);
		
		leftBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
				SosBeaconActivity.show(ManagerActivity.this, false);
			}
		});
		
		listCategory = (ListView) findViewById(R.id.list_category);
		String[] activities = new String[6];
		activities[0] = getString(R.string.menu_item_account);
		activities[1] = getString(R.string.menu_item_alert_settings);
//		activities[2] = getString(R.string.menu_item_good_samaritan_settings);
		activities[2] = getString(R.string.menu_item_storage_settings);
		activities[3] = getString(R.string.menu_item_contact);
		activities[4] = getString(R.string.menu_item_send_friend);
		activities[5] = getString(R.string.menu_item_about);
		
		listCategory.setAdapter(new ArrayAdapter<String>(this, R.layout.simple_listview, activities));
		listCategory.setOnItemClickListener(new AdapterView.OnItemClickListener() {
			public void onItemClick(AdapterView<?> arg0, View arg1, int position, long id) {
//				finish();
				switch (position) {
				case 0:
					AccountActivity.show(ManagerActivity.this);
					break;
				case 1:
					AlertSettingActivity.show(ManagerActivity.this);
					break;
//				case 2:
//					GoodSamaritanSettingActivity.show(ManagerActivity.this);
//					break;
				case 2:
					StorageSettingActivity.show(ManagerActivity.this);
					break;
				case 3:
					TellUsActivity.show(ManagerActivity.this, false);
					break;
				case 4:
					TellUsActivity.show(ManagerActivity.this, true);
					break;
				case 5:
					AboutActivity.show(ManagerActivity.this);
					break;
				default:
					break;
				}
			}
		});
	}
 
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.home, menu);
		MenuItem item = menu.findItem(R.id.menu_item_more);
	    item.setChecked(true);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		callActivity(item.getItemId(), this);
		return super.onMenuItemSelected(featureId, item);
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			finish();
			SosBeaconActivity.show(ManagerActivity.this, false);
			return true;
		}
		return super.onKeyDown(keyCode, event);
	}
	
	public static void show(Context context) {
		final Intent intent = new Intent(context, ManagerActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}
}