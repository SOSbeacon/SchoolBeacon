package cnc.sosbeacon;

import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

import oauth.signpost.OAuthConsumer;
import oauth.signpost.OAuthProvider;
import oauth.signpost.commonshttp.CommonsHttpOAuthConsumer;
import oauth.signpost.commonshttp.CommonsHttpOAuthProvider;
import oauth.signpost.signature.HmacSha1MessageSigner;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.webkit.WebView;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import cnc.sosbeacon.adapter.ImportContactsAdapter;
import cnc.sosbeacon.oauth.OauthRegister;
import cnc.sosbeacon.util.ContactInfo;
import cnc.sosbeacon.util.ContactInfoList;
import cnc.sosbeacon.util.TextUtil;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class ImportContactsActivity extends GeneralActivity  {
	
	private final Logger logger = LoggerFactory.getLogger(ImportContactsActivity.class);
	private OAuthConsumer consumer;
	private OAuthProvider provider;
	private OauthRegister oauthRegister;
	private Intent intent;
	private List<Map<String, String>> contactList;
	TextView mMessage;
	WebView webView;
	AlertDialog.Builder dialog;
	String accessToken;
	String consumerId;
	String consumerName;
	Integer groupId;
	String groupName;
	String oauthToken; 
	String tokenSecret;
	static String oauthVerifier = "";
	ImportContactsAdapter contactAdapter;
	Handler saveContactHandler;
	String authUrl;
	Intent requestOauthIntent;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.import_contacts);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		
		mMessage = (TextView) findViewById(R.id.message);
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		rightBtn.setText(R.string.btnImport);
		TextView title = (TextView) findViewById(R.id.title);
		
		oauthVerifier = "";
		//get and set intent infor to use later
		intent = getIntent();
		String consumerIdFromIntent = intent.getStringExtra(CONSUMER_ID);
		consumerId = (consumerIdFromIntent != null) ? consumerIdFromIntent : preferences.getString(CONSUMER_ID, "");
		consumerName = intent.getStringExtra(CONSUMER_NAME);
		consumerName = (consumerName != null) ? consumerName : preferences.getString(CONSUMER_NAME, "");
		groupId = intent.getIntExtra(GROUP_ID, 0);
		groupId = (groupId != 0) ? groupId : preferences.getInt(GROUP_ID, 0);
		groupName = intent.getStringExtra(GROUP_NAME);
		groupName = (groupName != null) ? groupName : preferences.getString(GROUP_NAME, "");
		
		preferences.edit().putString(CONSUMER_ID, consumerId)
						  .putString(CONSUMER_NAME, consumerName)
						  .putInt(GROUP_ID, groupId)
						  .putString(GROUP_NAME, groupName)
						  .commit();
		
		title.setText(consumerName);
		
		saveContactHandler = new Handler() {
			@Override
			public void handleMessage(android.os.Message msg) {
				try {
					returnContactList();
				}	
				catch (Exception e) {
					Log.d("Contacts_Save", e.toString());
				}
			};
		};	
		rightBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				AlertDialog.Builder confirm = new AlertDialog.Builder(ImportContactsActivity.this);
				confirm.setTitle(R.string.contact_save_confirm);
				if (contactAdapter != null) {
				final ContactInfoList selectedContacts = contactAdapter.getSelectedContacts();
				if (selectedContacts.size() > 0 ) {
					String importConfirm = getString(R.string.contact_import_confirm_message);
					importConfirm = String.format(importConfirm, groupName);
					confirm.setMessage(importConfirm);
					confirm.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							dialog.dismiss();
							ContactInfo.saveContacts(ImportContactsActivity.this, selectedContacts, groupId.toString(), getToken(), saveContactHandler);
						}
					});
					confirm.setNegativeButton(R.string.btnNo, null);
				} else {
					confirm.setMessage(R.string.please_select_contact);
					confirm.setNeutralButton(R.string.btnOK, null);
				}
				} else {
					confirm.setMessage(R.string.please_select_contact);
					confirm.setNeutralButton(R.string.btnOK, null);
				}
				confirm.show();
				
			}
		});
		leftBtn.setText(R.string.btnBack);
		leftBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				returnContactList();
			}
		});
		
		if (consumerId != null) {
			oauthRegister = new OauthRegister(this, consumerId);
			String getRequestTokenUrl = oauthRegister.getRequestTokenUrl();
			if (consumerId.equalsIgnoreCase(OAUTH_GOOGLE)) {
				getRequestTokenUrl = oauthRegister.getRequestTokenUrl() + "?scope=" + URLEncoder.encode(oauthRegister.getRequestContactUrl());
			}
			if (consumerId.equalsIgnoreCase(OAUTH_YAHOO)) {
				getRequestTokenUrl = oauthRegister.getRequestTokenUrl();
				oauthRegister.setRequestContactUrl(oauthRegister.getRequestContactUrl() + URLEncoder.encode(oauthRegister.getRequestContactParams()));
			}
			consumer = new CommonsHttpOAuthConsumer(oauthRegister.getConsumerKey(), oauthRegister.getConsumerScret());
			consumer.setMessageSigner(new HmacSha1MessageSigner()); 
			HttpClient defaultClient = new DefaultHttpClient();
			provider = new CommonsHttpOAuthProvider(getRequestTokenUrl, oauthRegister.getAccessTokenUrl(), oauthRegister.getAuthorizationUrl(), defaultClient);
			provider.setOAuth10a(true);
		}
		//request token in first call (from ContactList Activity)
		if (consumerIdFromIntent != null) {
			requestToken();
		}
	}
	
	
	public static void setOauthVerifier(String returnOauthVerifier) {
		oauthVerifier = returnOauthVerifier;
	}
	
	@Override
	protected void onResume() {
		super.onResume();
		//Uri returnUri = intent.getData();
		//if (returnUri != null && consumerId != 0) {
		if (!oauthVerifier.equalsIgnoreCase("") && consumerId != null) {
			oauthToken = preferences.getString("oauthToken", ""); 
			tokenSecret = preferences.getString("tokenSecret", "");
			//oauthVerifier = returnUri.getQueryParameter("oauth_verifier");
			//finishActivity(REQUEST_ACTIVITY);
			mProgressDialog.setMessage(getString(R.string.wait_loading));
			mProgressDialog.show();
			final Handler getContactHandler = new Handler() {
				@Override
				public void handleMessage(Message msg) {
					setListView();
					if (mProgressDialog.isShowing()) {
						mProgressDialog.hide();
					}
					super.handleMessage(msg);
				}
			};
			new Thread(new Runnable() {
				public void run() {
					getContactList();
					getContactHandler.sendEmptyMessage(0);
				}
			}).start();
		}
	}
	
	private void requestToken() {
		try {
			authUrl = provider.retrieveRequestToken(consumer, CALLBACK_URL);
			logger.log(Level.INFO, "requestToken: authUrl=" + authUrl);
			if (intent.getData() == null) {
				requestOauthIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(authUrl));
				requestOauthIntent.putExtra("oauthToken", consumer.getToken());
				requestOauthIntent.putExtra("tokenSecret", consumer.getTokenSecret());
				requestOauthIntent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
				SharedPreferences.Editor  preferencesEditor = preferences.edit();
				preferencesEditor.putString("oauthToken", consumer.getToken());
				preferencesEditor.putString("tokenSecret", consumer.getTokenSecret());
				preferencesEditor.putBoolean("isOAuth10a", provider.isOAuth10a());
				preferencesEditor.commit();
				//startActivityForResult(requestOauthIntent, REQUEST_ACTIVITY);
				//finish();
				OauthRequestActivity.show(this, authUrl, consumerId, consumerName);
			}
		} catch (Exception e) {
			logger.log(Level.ERROR, "requestToken: " + e.getMessage());
			mMessage.setText(e.getMessage());
		}
	}

	private void getContactList() {
		contactList = new ArrayList<Map<String, String>>();
		consumer.setTokenWithSecret(oauthToken, tokenSecret);
		provider.setOAuth10a(true);
		HttpResponse response = null;
		String getRequestContactUrl = "";

		if (consumerId.equalsIgnoreCase(OAUTH_GOOGLE)) {
			getRequestContactUrl = oauthRegister.getRequestContactUrl() + oauthRegister.getRequestContactParams();
		}
		if (consumerId.equalsIgnoreCase(OAUTH_YAHOO)) {
			getRequestContactUrl = oauthRegister.getRequestContactUrl();
		}
		try {
			provider.retrieveAccessToken(consumer, oauthVerifier);
			HttpGet request = new HttpGet(getRequestContactUrl);
			consumer.sign(request);
			HttpClient client = new DefaultHttpClient();
			response = client.execute(request);
		} catch (Exception e) {
			logger.log(Level.ERROR, "getContactList: " + e.getMessage());
		}
		if (response != null) {
			if (consumerId.equalsIgnoreCase(OAUTH_GOOGLE)) {
				getGoogleContactList(response);
			}
			if (consumerId.equalsIgnoreCase(OAUTH_YAHOO)) {
				getYahooContactList(response);
			}
		}
	}
	
	private void getGoogleContactList(HttpResponse response) {
		DocumentBuilderFactory documentBuilderFactory = DocumentBuilderFactory.newInstance();
		try {
			DocumentBuilder documentBuilder = documentBuilderFactory.newDocumentBuilder();
			Document document =  documentBuilder.parse(response.getEntity().getContent());
			NodeList nodeListParent =  document.getChildNodes();
			if (nodeListParent.getLength() > 0) {
				NodeList nodeList = nodeListParent.item(0).getChildNodes();
				for(int i = 0; i < nodeList.getLength(); i ++) {
					String contactName = "";
					String contactPhone = "";
					String contactEmail = "";
					Node node =  nodeList.item(i);
					if (node.getNodeName().equalsIgnoreCase("entry")) {
						NodeList entry = node.getChildNodes();
						for(int j = 0; j < entry.getLength(); j ++) {
							Node n = entry.item(j);
							Element nodeElement = (Element) n;
							String nodeName = n.getNodeName();
							String nodeValue =  "";
							if (n.getFirstChild() !=null) {
								try { 
									nodeValue = (n.getFirstChild().getNodeValue() != null) ? n.getFirstChild().getNodeValue() : "" ;
								} catch (Exception e) {}
							}
							if (nodeName.equalsIgnoreCase("title")) {
								contactName = nodeValue;
							}
							if (nodeName.equalsIgnoreCase("gd:phoneNumber")) {
								contactPhone  = nodeValue;
							}
							if (nodeName.equalsIgnoreCase("gd:email")) {
								contactEmail = nodeElement.getAttribute("address");
							}
						}
					}
					if (!contactName.equalsIgnoreCase("") && (!contactPhone.equalsIgnoreCase("") || !contactEmail.equalsIgnoreCase(""))) {
						Map<String, String> values = new HashMap<String, String>();
						values.put("name", contactName);
						values.put("phone", contactPhone);
						values.put("email", contactEmail);
						contactList.add(values);
					}
				}
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	private void getYahooContactList(HttpResponse response) {
		JSONObject json;
		JSONArray contacts = new JSONArray();
		String data = cnc.sosbeacon.http.HttpRequest.GetText(response);
		try {
			json = new JSONObject(data);
			contacts = json.getJSONObject("query").getJSONObject("results").getJSONArray("contact");
		} catch (JSONException e) {
			e.printStackTrace();
		}
		if (contacts.length() > 0) {
			contactList = new ArrayList<Map<String, String>>();
			for (int i = 0; i < contacts.length(); i ++) {
				try {
					JSONObject contactObject =  contacts.getJSONObject(i);
					JSONArray fields;
					fields = contactObject.getJSONArray("fields");
					String contactName = "";
					String contactPhone = "";
					String contactEmail = "";
					Boolean firstEmail = true;
					Boolean firstPhone = true;
					for (int j = 0; j < fields.length(); j ++) {
						JSONObject field = fields.getJSONObject(j);
						String type = field.getString("type");
						if (type.equalsIgnoreCase("email") && firstEmail) {
							contactEmail = field.getString("value"); 
							firstEmail = false;
						}
						if (type.equalsIgnoreCase("phone") && firstPhone) {
							contactPhone = field.getString("value");
							if (field.getString("flags").equalsIgnoreCase("MOBILE")) {
								firstPhone = false;
							}
						}
						if (type.equalsIgnoreCase("name")) {
							JSONObject nameObject = field.getJSONObject("value");
							if (!nameObject.isNull("givenName")) {
								contactName = nameObject.optString("givenName");
							}
							if (!nameObject.isNull("middleName")) {
								contactName += " " + nameObject.optString("middleName");
							}
							if (!nameObject.isNull("familyName")) {
								contactName += " " + nameObject.optString("familyName");
							}
							contactName = contactName.trim();
						}
					}
					if (!contactName.equalsIgnoreCase("") && (!contactPhone.equalsIgnoreCase("") || !contactEmail.equalsIgnoreCase(""))) {
						Map<String, String> values = new HashMap<String, String>();
						values.put("name", contactName);
						values.put("phone", contactPhone);
						values.put("email", contactEmail);
						contactList.add(values);
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}
		}
	}
	
	void returnContactList() {
		finish();
		ContactList.show(ImportContactsActivity.this, groupId.toString(), groupName);
	}
	
	private void setListView() {
		String from[] = {"name", "phone", "email"};
		int[] to = {R.id.contactName, R.id.contactPhone, R.id.contactEmail};
		if (contactList.size() >0 ) {
			contactAdapter = new ImportContactsAdapter(this.getApplicationContext(), (List<? extends Map<String, String>>) contactList, R.layout.contact_item, from, to);
			ListView contactListView = (ListView) findViewById(R.id.contactList);
			contactListView.setAdapter(contactAdapter);
			contactListView.setOnItemSelectedListener(new OnItemSelectedListener() {
				public void onItemSelected(AdapterView<?> adapter, View view,
						int position, long itemId) {
				}
				public void onNothingSelected(AdapterView<?> arg0) {
				}
			});
			contactListView.setOnItemClickListener(new OnItemClickListener() {
				public void onItemClick(AdapterView<?> adapter, View view, int position,
						long itemId) {
					adapter.setSelection(position);
				}
			});
		} else {
			mMessage.setText(R.string.contact_empty);
		}
	}

	class JSInterface {
	    public void showHTML(String html)
	    {
	    	try {
	    		String text = TextUtil.removeHTML(html);
	    		String values[] = text.split("access_token=");
	    		if (values.length > 1) {
	    			accessToken = values[1];
	    		}
			} catch (Exception e) {
				mMessage.setText(e.getMessage());
				e.printStackTrace();
			}
	    }
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			returnContactList();
		}
		return true;
	}
	
	public static void show(Context context, String consumerId, String consumerName, Integer groupId, String groupName) {
		final Intent intent = new Intent(context, ImportContactsActivity.class);
		intent.putExtra(CONSUMER_ID, consumerId);
		intent.putExtra(CONSUMER_NAME, consumerName);
		intent.putExtra(GROUP_ID, groupId);
		intent.putExtra(GROUP_NAME, groupName);
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
}
