
package cnc.schoolbeacon;

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
import org.apache.http.protocol.HTTP;
import org.json.JSONArray;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import cnc.schoolbeacon.adapter.ListGroupsAdapter;
import cnc.schoolbeacon.adapter.ListUsersAdapter;
import cnc.schoolbeacon.http.HttpRequest;
import cnc.schoolbeacon.model.Group;
import cnc.schoolbeacon.model.User;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class SosBeaconActivity extends GeneralActivity implements OnClickListener {
    private static final String TAG = "SosBeaconActivity";

    private static final int GET_ALL_GROUPS = 1;

    private static final int PREVIEW_BROADCAST = 2;

    private static final int SEND_BROADCAST = 3;

    private static final int MAX_CHARACTERS = 75;

    private Spinner spBroadcastType;

    private Spinner spBroadcastGroup;

    private EditText edtShortMessage;

    private EditText edtLongMessage;

    private Button btnPreviewBroadcast;

    private Button btnSendBroadcast;

    private Button btnReset;

    private Handler mHandler;

    private String groupNames;

    private View mContainer;

    private String groupIds = "";

    private ArrayList<User> users = new ArrayList<User>();

    private final Logger logger = LoggerFactory.getLogger(SosBeaconActivity.class);

    private TextView tvRemainCharacters;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        logger.log(Level.INFO, ">>>>>>>>>> onCreate");
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
        setContentView(R.layout.home);
        getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);

        final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
        leftBtn.setVisibility(View.INVISIBLE);
        final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
        rightBtn.setVisibility(View.INVISIBLE);

        mContainer = findViewById(R.id.container);
        mContainer.setOnClickListener(this);

        btnPreviewBroadcast = (Button) findViewById(R.id.btnPreviewBroadCast);
        btnPreviewBroadcast.setOnClickListener(this);

        btnSendBroadcast = (Button) findViewById(R.id.btnSendBroadCast);
        btnSendBroadcast.setOnClickListener(this);

        btnReset = (Button) findViewById(R.id.btnReset);
        btnReset.setOnClickListener(this);

        spBroadcastGroup = (Spinner) findViewById(R.id.spBroadcastGroup);
        spBroadcastType = (Spinner) findViewById(R.id.spBroadcastType);

        tvRemainCharacters = (TextView) findViewById(R.id.remain_characters);
        tvRemainCharacters.setText(getString(R.string.remain_characters, MAX_CHARACTERS));

        edtShortMessage = (EditText) findViewById(R.id.edtShortMessage);
        edtShortMessage.addTextChangedListener(new TextWatcher() {

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {

            }

            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void afterTextChanged(Editable s) {
                int remain_characters = MAX_CHARACTERS - s.length();
                tvRemainCharacters
                        .setText(getString(R.string.remain_characters, remain_characters));

            }
        });

        edtLongMessage = (EditText) findViewById(R.id.edtLongMessage);

        int usageCount = preferences.getInt(USAGE_COUNT, 0);

        sendCurrentLocation();
        String configProblem = "";
        Boolean promptToConfig = false;
        Boolean appFirstLoad = getIntent().getBooleanExtra(APP_FIRST_LOAD, false);

        // Check location services
        try {
            String locationMessage = getLocation();
            if (mLocation == null) {
                configProblem += locationMessage;
                promptToConfig = true;
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        /* if (mEmergencyNumber.toString().trim().equalsIgnoreCase("0")
                 || mEmergencyNumber.toString().trim().equalsIgnoreCase("")) {
             configProblem += "\n" + getString(R.string.set_emergency_number);
             promptToConfig = true;
         }*/

        /*// if normal contact is empty 
        if (appFirstLoad && mCountContact <= 0) {
            AlertDialog.Builder adContact = new AlertDialog.Builder(this);
            adContact.setMessage(R.string.updateContactPrompt);
            adContact.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
                public void onClick(DialogInterface dialog, int which) {
                    finish();
                    ContactCategory.show(SosBeaconActivity.this);
                }
            });
            adContact.setNegativeButton(R.string.btnNotNow, null);
            adContact.show();
        }*/

        /*if (appFirstLoad && promptToConfig) {
            AlertDialog.Builder adProblem = new AlertDialog.Builder(this);
            adProblem.setMessage(configProblem);
            adProblem.setNeutralButton(R.string.btnOK, null);
            adProblem.show();
        }*/

        /*// show video demo if user first active phone
        if (appFirstLoad) {
            AlertDialog.Builder askViewVideo = new AlertDialog.Builder(this);
            askViewVideo.setMessage(R.string.ask_view_video);
            askViewVideo.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();
                    Intent videoIntent = new Intent(Intent.ACTION_VIEW, Uri
                            .parse(getApiUrl(DEMO_URL)));
                    videoIntent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
                    startActivity(videoIntent);
                }
            });
            askViewVideo.setNegativeButton(R.string.btnNotNow, null);
            askViewVideo.show();
        }*/

        usageCount++;
        preferences.edit().putInt(USAGE_COUNT, usageCount).commit();

        mHandler = new Handler() {
            public void handleMessage(android.os.Message msg) {
                if (msg.what == GET_ALL_GROUPS) {
                    doneRequestGetAllGroup();
                }

                if (msg.what == PREVIEW_BROADCAST) {
                    doneGetAllUsers();
                }

                if (msg.what == SEND_BROADCAST) {
                    doneSendBroadcast();
                }
            };
        };
    }

    public static void show(Context context, Boolean isFirstLoad) {
        final Intent intent = new Intent(context, SosBeaconActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        intent.putExtra(APP_FIRST_LOAD, isFirstLoad); // true if first call when app load, false if call from menu
        context.startActivity(intent);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.home, menu);
        MenuItem item = menu.findItem(R.id.menu_item_home);
        item.setChecked(true);
        return super.onCreateOptionsMenu(menu);
    }

    @Override
    public boolean onMenuItemSelected(int featureId, MenuItem item) {
        finish();
        callActivity(item.getItemId(), this);
        return super.onMenuItemSelected(featureId, item);
    }

    @Override
    protected void onResume() {
        if (getPhoneId().equalsIgnoreCase("")) {
            //			finish();
        }
        super.onResume();
        bindData();
    }

    public void onStart() {
        super.onStart();
        FlurryAgent.onStartSession(this, getPrefs(FLURRY_API_KEY));
    }

    public void onStop() {
        super.onStop();
        FlurryAgent.onEndSession(this);
    }

    private void bindData() {
        //Get all Groups and bind
        performGetAllGroups();

        // bind broadcast type
        bindBroadcastType();
    }

    private void performGetAllGroups() {
        mProgressDialog.setMessage("Loading...");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();

        new Thread(new Runnable() {
            public void run() {
                requestGetAllGroup();
                mHandler.sendEmptyMessage(GET_ALL_GROUPS);
            }
        }).start();
    }

    private void bindGroups() {
        groups.add(0, new Group("allgroups", "All Group", ""));
        Log.w(TAG, "groups.size():"+groups.size());
        ListGroupsAdapter adapter = new ListGroupsAdapter(this, groups);
        spBroadcastGroup.setAdapter(adapter);
        makeDefaultGroup();
    }

    private void makeDefaultGroup() {
        for (int i = 1; i < groups.size(); i++) {
            if (mDefaultGroupId.equals(groups.get(i).id)) {
                spBroadcastGroup.setSelection(i);
                break;
            }
        }
    }

    private void doneRequestGetAllGroup() {
        mProgressDialog.hide();
        bindGroups();
    }

    private void bindBroadcastType() {
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, R.layout.item_text,
                R.id.item_name, mBroadcastTypes);
        spBroadcastType.setAdapter(adapter);
    }

    private void performGetListUsers() {
        mProgressDialog.setMessage("Loading...");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();

        new Thread(new Runnable() {
            public void run() {
                getListUsers();
                mHandler.sendEmptyMessage(PREVIEW_BROADCAST);
            }
        }).start();
    }

    private void getListUsers() {
        users.clear();
        HttpClient client = new DefaultHttpClient();
        String getUrl = getAllBroadcastListUrl();
        HttpGet httpGet = new HttpGet(getUrl);
        HttpResponse response;
        logger.log(Level.INFO, "get groups" + getUrl);
        try {
            response = client.execute(httpGet);
            String responseContent = HttpRequest.GetText(response);
            Log.e("get groups response", responseContent);
            JSONObject responseJson = new JSONObject(responseContent);
            responseJson = responseJson.getJSONObject(RESPONSE);
            mSuccess = responseJson.getString(SUCCESS);
            if (mSuccess.equals(TRUE)) {
                JSONArray jArray = responseJson.getJSONArray("contacts");
                for (int i = 0; i < jArray.length(); i++) {
                    JSONObject jUser = jArray.getJSONObject(i);
                    String id = jUser.getString("contactId");
                    String name = jUser.getString("name");
                    String email = jUser.getString("email");
                    String textphone = jUser.getString("textphone");

                    User user = new User(id, name, email, textphone);
                    users.add(user);

                }
            }
        } catch (Exception e) {
            e.printStackTrace();
            mSuccess = "";
            logger.log(Level.ERROR, e.getMessage());
        }
    }

    public void doneGetAllUsers() {
        mProgressDialog.hide();
        Dialog dialog = new Dialog(this);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.broadcast_preview);
        dialog.setCanceledOnTouchOutside(true);

        ListView listUsers = (ListView) dialog.findViewById(R.id.list);
        ListUsersAdapter adapter = new ListUsersAdapter(this, users);
        listUsers.setAdapter(adapter);

        TextView groups = (TextView) dialog.findViewById(R.id.tvGroups);
        groups.setText(groupNames);

        dialog.show();
    }

    public String getAllBroadcastListUrl() {
    	Log.w(TAG, "get all");
        Group group = (Group) spBroadcastGroup.getSelectedItem();

        ArrayList<String> groupIds = new ArrayList<String>();
        groupNames = "";
        if (group.id.equals("allgroups")) {

            for (int i = 1; i < groups.size(); i++) {
                groupIds.add(groups.get(i).id);
                groupNames = groupNames + groups.get(i).name + ", ";
            }

        } else {
            groupIds.add(group.id);
            groupNames = group.name;
        }

        String parameter = "";

        for (int i = 0; i < groupIds.size(); i++) {
            parameter = parameter + "&toGroupIds[]=" + groupIds.get(i).toString();
        }

        return getApiUrl(ALERT_URL) + "?_method=get&format=json&userId=" + mUserId + "&schoolId="
                + mSchoolId + "&token=" + mToken + parameter;
    }

    private void performSendBroadcast() {
        mProgressDialog.setMessage("Broadcast sending...");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();

        new Thread(new Runnable() {
            public void run() {
                sendBroadcast();
                mHandler.sendEmptyMessage(SEND_BROADCAST);
            }
        }).start();
    }

    private void sendBroadcast() {

        HttpClient client = new DefaultHttpClient();
        HttpPost httpPut = new HttpPost(getApiUrl(ALERT_URL));

        int broadcastTypeId = spBroadcastType.getSelectedItemPosition();
        Group group = (Group) spBroadcastGroup.getSelectedItem();

        String shortMessage = edtShortMessage.getText().toString();
        String longMessage = edtLongMessage.getText().toString();

        List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
        nameValuePairs.add(new BasicNameValuePair("_method", "post"));
        nameValuePairs.add(new BasicNameValuePair("format", "json"));
        nameValuePairs.add(new BasicNameValuePair("userId", mUserId));
        nameValuePairs.add(new BasicNameValuePair("token", mToken));
        nameValuePairs.add(new BasicNameValuePair(SCHOOLID, mSchoolId));
        nameValuePairs.add(new BasicNameValuePair("type", broadcastTypeId + 1 + ""));
        nameValuePairs.add(new BasicNameValuePair("shortMessage", shortMessage));
        nameValuePairs.add(new BasicNameValuePair("longMessage", longMessage));

        if (group.id.equals("allgroups")) {
            for (int i = 1; i < groups.size(); i++) {
                String groupId = groups.get(i).id;
                nameValuePairs.add(new BasicNameValuePair("toGroupIds[]", groupId));
            }
        } else {
            String groupId = group.id;
            nameValuePairs.add(new BasicNameValuePair("toGroupIds[]", groupId));
        }
        nameValuePairs.add(new BasicNameValuePair("latitude", mLatitude));
        nameValuePairs.add(new BasicNameValuePair("longitude", mLongtitude));

        try {
            UrlEncodedFormEntity urlEncodedFormEntity = new UrlEncodedFormEntity(nameValuePairs,
                    HTTP.UTF_8);
            httpPut.setEntity(urlEncodedFormEntity);
        } catch (Exception e) {
            e.printStackTrace();
        }

        HttpResponse response;
        try {
            response = client.execute(httpPut);
            String responseContent = HttpRequest.GetText(response);
            JSONObject responseJson = new JSONObject(responseContent);
            responseJson = responseJson.getJSONObject(RESPONSE);
            mSuccess = responseJson.getString(SUCCESS);
            mMessage = responseJson.getString(MESSAGE);
        } catch (Exception e) {
            e.printStackTrace();
            mSuccess = "";
        }
    }

    private void doneSendBroadcast() {
        mProgressDialog.hide();
        Toast.makeText(getBaseContext(), mMessage, Toast.LENGTH_LONG).show();
        reset();
    }

    private void reset() {
        edtLongMessage.setText("");
        edtShortMessage.setText("");
        makeDefaultGroup();
        spBroadcastType.setSelection(0);
    }

    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.btnPreviewBroadCast:
                performGetListUsers();
                break;
            case R.id.btnSendBroadCast:
                confirmCheckin();
                //                performSendBroadcast();
                break;
            case R.id.btnReset:
                reset();
                break;
            case R.id.container:
                hideSoftKeyboard();
                break;
            default:
                break;
        }
    }

    private void hideSoftKeyboard() {
        InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
        imm.hideSoftInputFromWindow(getCurrentFocus().getWindowToken(), 0);
    }

    private void confirmCheckin() {
        final String shortmessage = edtShortMessage.getText().toString();
        if (shortmessage.equals("")) {
            Toast.makeText(getBaseContext(), "Please input short message", Toast.LENGTH_LONG)
                    .show();
            return;
        }

        final String longmessage = edtLongMessage.getText().toString();

        final int broadcastType = spBroadcastType.getSelectedItemPosition() + 1;

        Group group = (Group) spBroadcastGroup.getSelectedItem();

        if (group.id.equals("allgroups")) {
            for (int i = 1; i < groups.size(); i++) {
                groupIds = groupIds + groups.get(i).id + ",";
            }
        } else {
            groupIds = group.id;
        }

        if (groupIds.contains(",")) {
            groupIds = groupIds.substring(0, groupIds.lastIndexOf(","));
        }

        AlertDialog.Builder attachConfirm = new AlertDialog.Builder(this);
        attachConfirm.setMessage(R.string.attachFilesConfirm);
        attachConfirm.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                RecordActivity.show(SosBeaconActivity.this, TAG, broadcastType + "", groupIds,
                        shortmessage, longmessage);
            }
        });

        attachConfirm.setNegativeButton(R.string.btnNo, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                performSendBroadcast();
            }
        });

        attachConfirm.show();

    }
}
