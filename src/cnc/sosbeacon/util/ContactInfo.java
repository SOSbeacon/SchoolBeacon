package cnc.sosbeacon.util;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.Handler;
import android.util.Log;
import cnc.sosbeacon.GeneralActivity;
import cnc.sosbeacon.R;
import cnc.sosbeacon.constants.Constants;
import cnc.sosbeacon.http.HttpSolution;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class ContactInfo implements Serializable, Constants {
	
	private final static Logger logger = LoggerFactory.getLogger(ContactInfo.class);
	private static final long serialVersionUID = 1L;
	public String id = "";
	public String name = "";
	public String email = "";
	public String voicephone = "";
	public String textphone = "";
	public String status = ""; // add, edit, delete: use for update contact list to server
	public String type = "0";
	
	public  ContactInfo() {
		
	}
	
	public String getId() {
		return id;
	}
	public void setId(String id) {
		this.id = id;
	}
	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getEmail() {
		return email;
	}
	public void setEmail(String email) {
		this.email = email;
	}
	public String getVoicephone() {
		return voicephone;
	}
	public void setVoicephone(String voicephone) {
		this.voicephone = voicephone;
	}
	public String getTextphone() {
		return textphone;
	}
	
	public void setTextphone(String textphone) {
		this.textphone = textphone;
	}
	
	public void setStatus(String status) {
		this.status = status;
	}
	
	public String getStatus() {
		return status;
	}
	
	public void setType(String type) {
		this.type = type;
	}
	
	public String getType() {
		return type;
	}
	
	public static void saveContacts(Context context, final ContactInfoList listContact, final String groupId, final String token) {
		saveContacts(context, listContact, groupId, token, null);
	}
	
	
	public static ArrayList<GroupInfo> getGroupsFromJson(JSONArray categoryJSON) {
		ArrayList<GroupInfo> category = new ArrayList<GroupInfo>();
		for (int i = 0; i < categoryJSON.length(); i++) {
			try {
				GroupInfo gi = new GroupInfo();
				JSONObject groupItem;
				groupItem = categoryJSON.getJSONObject(i);
				gi.setId(groupItem.getString(ID));
				gi.setName(groupItem.getString(NAME));
				gi.setType(groupItem.getString(TYPE));
				category.add(gi);
			}
			catch (JSONException e) {
				e.printStackTrace();
			}
		}
		return category;
	}
	
	public static void saveContacts(Context context, final ContactInfoList listContact, final String groupId, final String token, final Handler newHandler) {
		final ProgressDialog mProgressDialog = new  ProgressDialog(context);
		mProgressDialog.setMessage(context.getString(R.string.saving));
		final Preferences prefs =  new Preferences(context);
		final String contactUrl = prefs.get(API_URL) + prefs.get(CONTACT_URL);
		
		Thread contactSaveThread = new Thread(
			new Runnable() {
				Handler handle = new Handler() {
					@Override
					public void handleMessage(android.os.Message msg) {
						try {
							if (mProgressDialog.isShowing()) mProgressDialog.hide();
						}	
						catch (Exception e) {
							e.printStackTrace();
						}
					};
				};
				public void run() {
					for (ContactInfo ci:listContact) {
						String method = "";
						String postURL = "";
						if (ci != null) {
							try {
								if (ci.getStatus().equalsIgnoreCase(UPDATE)) {
									method = METHOD_PUT;
									postURL = contactUrl + ci.getId();
								}
								if (ci.getId().equalsIgnoreCase("0") || ci.getStatus().equalsIgnoreCase(NEW)) {
									method = METHOD_POST;
									postURL = contactUrl;
								}
								if (ci.getStatus().equalsIgnoreCase(DELETE)) {
									method = METHOD_DELETE;
									postURL = contactUrl + ci.getId();
								}
								if (!ci.getStatus().equalsIgnoreCase("")) {
									try {
										HttpClient client = new DefaultHttpClient();
										HttpPost post = new HttpPost(postURL);
										HttpSolution httpObject = new HttpSolution();
										if (method == METHOD_PUT || method == METHOD_DELETE) {
											httpObject.put(METHOD, method);
										}
										httpObject.put(FORMAT, JSON);
										httpObject.put(GROUP_ID, groupId);
										httpObject.put(TOKEN, token);
										httpObject.put(NAME, ci.getName());
										httpObject.put(EMAIL, ci.getEmail());
										httpObject.put(VOICE_PHONE, ci.getVoicephone());
										httpObject.put(TEXT_PHONE, ci.getTextphone());
										List<NameValuePair> params = httpObject.getParams();
										UrlEncodedFormEntity ent = new UrlEncodedFormEntity(params, HTTP.UTF_8);
										post.setEntity(ent);
										HttpResponse responsePOST = client.execute(post);
										HttpEntity resEntity = responsePOST.getEntity();
										String rest = EntityUtils.toString(resEntity);
										logger.log(Level.INFO, "saveContacts: jsonResponse=" + rest);
										JSONObject jsonResponse = new JSONObject(rest);
										httpObject.setJsonObject(jsonResponse);
										httpObject.getRAddContact();
										Boolean state = httpObject.getState();
										Log.d("ContactSave", state.toString());
									} catch (Exception e) {							
										logger.log(Level.ERROR, "saveContacts: " + e.getMessage());
									}
								}
							} catch (Exception e) {
								logger.log(Level.ERROR, "saveContacts: " + e.getMessage());
							}
						}
					}
					handle.sendEmptyMessage(0);
					if (newHandler != null) {
						newHandler.sendEmptyMessage(GeneralActivity.MESSAGE_SAVED);
					}
				}
			}
		);
		try {
			mProgressDialog.show();
		} catch (Exception e) {
			e.printStackTrace();
		}
		contactSaveThread.start();
	}
}
