package cnc.sosbeacon;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpParams;
import org.json.JSONArray;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.ContextMenu;
import android.view.ContextMenu.ContextMenuInfo;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.MenuItem.OnMenuItemClickListener;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnCreateContextMenuListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.AdapterContextMenuInfo;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import cnc.sosbeacon.http.HttpRequest;
import cnc.sosbeacon.util.ContactInfo;
import cnc.sosbeacon.util.GroupInfo;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class ContactCategory extends GeneralActivity implements Runnable {
	
	private final Logger logger = LoggerFactory.getLogger(ContactCategory.class);
	protected ArrayList<GroupInfo> category;
	private ListView listCategory;
	private LinearLayout groupForm;
	private TextView etGroupName;
	private Button btAdd;
	private Button btSave;
	private Button btCancel;
	private Integer editType = 0; // 1 = new, 2 = edit, 3 = delete;
	private GroupInfo editGroup;
	ProgressDialog mProgressDialog;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.contact_category);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);

		TextView title = (TextView) findViewById(R.id.title);
		title.setText(R.string.contact_group);
		
		groupForm = (LinearLayout) findViewById(R.id.groupForm);
		etGroupName = (TextView) findViewById(R.id.etGroupName);
		btSave = (Button) findViewById(R.id.btSave);
		btCancel = (Button) findViewById(R.id.btCancel);
		
		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		leftBtn.setVisibility(View.INVISIBLE);
		btAdd = (Button) findViewById(R.id.title_right_btn);
		btAdd.setVisibility(View.VISIBLE);
		btAdd.setText(R.string.btnAdd);
		
		btAdd.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				setEditType(1, null);
			}
		});
		
		btSave.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				saveGroup();
			}
		});
		btCancel.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				setEditType(0, null);
			}
		});
		
		listCategory = (ListView) findViewById(R.id.list_category);
		mProgressDialog =  new ProgressDialog(this);
		mProgressDialog.setMessage(getString(R.string.loading));
		
		String cacheGroups = preferences.getString(CONTACT_GROUPS, "");
		if (cacheGroups.equalsIgnoreCase("")) {
			mProgressDialog.show();
			Thread tr = new Thread(this);
			tr.start();
			
		} else {
			try {
				JSONArray cacheGroupsJson = new JSONArray(cacheGroups);
				logger.log(Level.INFO, "cacheGroupsJson: " + cacheGroupsJson);
				category = ContactInfo.getGroupsFromJson(cacheGroupsJson);
				initList();
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
	}
	
	private void setEditType(Integer type, GroupInfo group) {
		editType = type;
		editGroup = group;
		if (type == 0) {
			groupForm.setVisibility(View.GONE);
			listCategory.setVisibility(View.VISIBLE);
			btAdd.setVisibility(View.VISIBLE);
			etGroupName.setText("");
			editGroup = null;
		}
		if (type == 1 || type == 2) {
			groupForm.setVisibility(View.VISIBLE);
			listCategory.setVisibility(View.GONE);
			btAdd.setVisibility(View.INVISIBLE);
			if (group != null) {
				etGroupName.setText(editGroup.getName());
			}
		}
	}
	
	private void saveGroup() {
		logger.log(Level.INFO, "saveGroup");
		final String groupName = etGroupName.getText().toString().trim();
		final Handler handler = new Handler() {
			@Override
			public void handleMessage(Message msg) {
				super.handleMessage(msg);
				if (mProgressDialog.isShowing()) {
					mProgressDialog.hide();
				}
				if (msg.what == 1) {
					setEditType(0, null);
					if (category.size() > 0) {
						initList();
					}
				}
				if (!mMessage.equalsIgnoreCase("")) {
					Toast.makeText(ContactCategory.this, mMessage, Toast.LENGTH_LONG).show();
				}
			}
		};
		if (editType == 1 || editType == 2 || editType == 3) {
			if (editType == 3 || (editType != 3 && !groupName.equalsIgnoreCase(""))) {
				mProgressDialog.setMessage(getString(R.string.saving));
				mProgressDialog.show();
				new Thread(new Runnable() {
					public void run() {
						HttpClient client = new DefaultHttpClient();
						String url = (editType == 2 || editType == 3) ? getApiUrl(GROUP_URL) + editGroup.getId() : getApiUrl(GROUP_URL);
						String method = editType == 1 ? METHOD_POST : (editType == 2 ? METHOD_PUT : METHOD_DELETE);  
						HttpPost httpPost = new HttpPost(url);
						List<NameValuePair> params = new ArrayList<NameValuePair>();
						params.add(new BasicNameValuePair(FORMAT, JSON));
						params.add(new BasicNameValuePair(PHONE_ID, mPhoneId));
						params.add(new BasicNameValuePair(TOKEN, mToken));
						params.add(new BasicNameValuePair(NAME, groupName));
						params.add(new BasicNameValuePair(METHOD, method));
						try {
							UrlEncodedFormEntity urlEncodedFormEntity = new UrlEncodedFormEntity(params);
							httpPost.setEntity(urlEncodedFormEntity);
						} catch (Exception e) {
								e.printStackTrace();
						}
						HttpResponse response;
						Boolean state = false;
						try {
							response = client.execute(httpPost);
							String responseContent = HttpRequest.GetText(response);
							JSONObject responseJson = new JSONObject(responseContent);
							responseJson = responseJson.getJSONObject(RESPONSE);
							state = responseJson.getBoolean(STATE);
							mMessage = responseJson.getString(MESSAGE);
							if (state) {
								JSONArray categoryJSON = responseJson.getJSONArray(DATA);
								category = ContactInfo.getGroupsFromJson(categoryJSON);
								preferences.edit().putString(CONTACT_GROUPS, categoryJSON.toString()).commit();
							}
						} catch (Exception e) {
							e.printStackTrace();
							mMessage = e.getMessage();
						}
						int what = state ? 1 : 0;
						handler.sendEmptyMessage(what);
					}
				}).start();
			} else {
				Toast.makeText(this, getString(R.string.groupNameInvalid), Toast.LENGTH_LONG).show();
			}
		}
	}
	
	private void initList() {
		listCategory.setAdapter(new ArrayAdapter<GroupInfo>(this, R.layout.simple_listview, category));
		listCategory.setOnItemClickListener(mListener);
		
		listCategory.setOnCreateContextMenuListener(new OnCreateContextMenuListener() {
			
			public void onCreateContextMenu(ContextMenu menu, View v,
					ContextMenuInfo menuInfo) {
				
				final AdapterContextMenuInfo contextMenuInfo = (AdapterContextMenuInfo) menuInfo;
				final GroupInfo group =  category.get(contextMenuInfo.position);
				final Integer groupType = Integer.valueOf(group.getType());
				menu.add(R.string.viewContacts).setOnMenuItemClickListener(new OnMenuItemClickListener() {
					public boolean onMenuItemClick(MenuItem item) {
						viewContacts(group.getId(), group.getName());
						return false;
					}
				});
				menu.add(R.string.editGroup).setOnMenuItemClickListener(new OnMenuItemClickListener() {
					public boolean onMenuItemClick(MenuItem item) {
						if (groupType == 0 || groupType == 1 || groupType == 2) {
							Toast.makeText(ContactCategory.this, getString(R.string.groupNotAllowEdit), Toast.LENGTH_SHORT).show();
						} else {
							setEditType(2, group);
						}
						return true;
					}
				});
				menu.add(R.string.deleteGroup).setOnMenuItemClickListener(new OnMenuItemClickListener() {
					public boolean onMenuItemClick(MenuItem item) {
						if (groupType == 0 || groupType == 1 || groupType == 2) {
							Toast.makeText(ContactCategory.this, getString(R.string.groupNotAllowEdit), Toast.LENGTH_SHORT).show();
						} else {
							new AlertDialog.Builder(ContactCategory.this).setMessage(R.string.deleteGroupConfirm)
							.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog, int which) {
									setEditType(3, group);
									saveGroup();
								}
							})
							.setNegativeButton(R.string.btnNo, null).show();
						}
						return true;
					}
				});
			}
		});
	}

	private OnItemClickListener mListener = new OnItemClickListener() {
		public void onItemClick(AdapterView<?> arg0, View view, int position, long id) {
			viewContacts(category.get(position).getId(), category.get(position).getName());
		}
	};

	private void viewContacts(String groupId, String groupName) {
		finish();
		ContactList.show(ContactCategory.this, groupId, groupName);
	}
	
	private void getGroup() throws Exception {
		HttpClient client = new DefaultHttpClient();
		String getURL = String.format(getApiUrl(GROUP_GET_URL), getPhoneId(), getToken());
		HttpGet httpGet = new HttpGet(getURL);
		HttpParams httpParams = new BasicHttpParams();

		httpParams.setParameter(FORMAT, JSON);
		httpParams.setParameter("http.protocol.content-charset", "UTF-8");

		httpGet.setParams(httpParams);

		HttpResponse response = client.execute(httpGet);
		String content = HttpRequest.GetText(response);
		JSONObject jo = new JSONObject(content);
		JSONArray categoryJSON = jo.getJSONObject(RESPONSE).getJSONArray(DATA);
		category = ContactInfo.getGroupsFromJson(categoryJSON);
		logger.log(Level.INFO, "getGroup, categoryJSON: " + categoryJSON);
		// save category
		if (category.size() > 0) {
			preferences.edit().putString(CONTACT_GROUPS, categoryJSON.toString()).commit();
		}
	}
	
	Handler handle = new Handler() {
		@Override
		public void handleMessage(android.os.Message msg) {
			try {
				initList();
				mProgressDialog.hide();
			} catch (Exception e) {
				e.printStackTrace();
			}
		};
	};
	
	public void run() {
		try {
			getGroup();
		} catch (Exception e) {
			e.printStackTrace();
		}
		handle.sendEmptyMessage(0);
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
		finish();
		callActivity(item.getItemId(), this);
		return super.onMenuItemSelected(featureId, item);
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			finish();
			SosBeaconActivity.show(ContactCategory.this, false);
			return true;
		}
		return super.onKeyDown(keyCode, event);
	}
	
	public static void show(Context context) {
		final Intent intent = new Intent(context, ContactCategory.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}
}
