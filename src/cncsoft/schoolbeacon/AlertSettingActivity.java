
package cncsoft.schoolbeacon;

import java.util.ArrayList;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.preference.PreferenceManager;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import android.widget.TextView;
import cncsoft.schoolbeacon.R;
import cncsoft.schoolbeacon.util.GroupInfo;

import com.flurry.android.FlurryAgent;

public class AlertSettingActivity extends SettingActivity {
    private static final int GET_ALL_GROUPS = 1;

    private Spinner spinVoice;

    private Spinner spinIncase;

    //    private EditText txtPanicPhone;

    //    private ToggleButton btnToggIncoming;

    //    private ToggleButton btnToggShutter;

    private ProgressDialog mProgressDialog;

    private ArrayList<GroupInfo> category = new ArrayList<GroupInfo>();

    private int[] recordTimeArray;

    private Handler mHandler;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
        setContentView(R.layout.alert_settings);
        getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
        TextView title = (TextView) findViewById(R.id.title);
        title.setText(R.string.lblAlertSetting);

        Button leftBtn = (Button) findViewById(R.id.title_left_btn);
        Button rightBtn = (Button) findViewById(R.id.title_right_btn);
        leftBtn.setText(R.string.btnBack);
        rightBtn.setText(R.string.btnSave);

        recordTimeArray = getResources().getIntArray(R.array.recordTimeValues);

        spinVoice = (Spinner) findViewById(R.id.spinner_voice);
        spinIncase = (Spinner) findViewById(R.id.spinner_incase);

        final SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);

        // Bỏ trên bản School
        /* txtPanicPhone = (EditText) findViewById(R.id.txtpanicphone);
         btnToggIncoming = (ToggleButton) findViewById(R.id.btntogg_incom);
         btnToggShutter = (ToggleButton) findViewById(R.id.btntogg_setting_sound);*/

        /*btnToggShutter.setChecked(prefs.getBoolean("shutter", false));
        btnToggShutter.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                prefs.edit().putBoolean("shutter", btnToggShutter.isChecked()).commit();
            }
        });*/

        ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(this,
                R.array.recordTimes, android.R.layout.simple_spinner_item);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinVoice.setAdapter(adapter);

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

        mHandler = new Handler() {
            @Override
            public void handleMessage(Message msg) {
                if (msg.what == MESSAGE_FINISH) {
                    finish();
                    ManagerActivity.show(AlertSettingActivity.this);
                }
                if (msg.what == MESSAGE_FINISH_ACTIVITY) {
                    finish();
                    if (callMenuId > 0) {
                        callActivity(callMenuId, AlertSettingActivity.this);
                        callMenuId = 0;
                    } else {
                        ManagerActivity.show(AlertSettingActivity.this);
                    }
                }

                if (msg.what == GET_ALL_GROUPS) {
                }

                super.handleMessage(msg);
            }
        };

        bindDataToForm();

        /*//web view panic phone
        TextView telPhonenumber = (TextView) findViewById(R.id.linkPanicTelPhonenumber);
        final AlertDialog.Builder webDialog = new AlertDialog.Builder(this);
        webDialog.setTitle(getString(R.string.lblPanicTelPhonenumber));
        telPhonenumber.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                final ProgressDialog loadWebView = ProgressDialog.show(AlertSettingActivity.this,
                        "", getString(R.string.loading));
                WebView webviewPhone = new WebView(getApplicationContext());
                webDialog.create();
                webDialog.setView(webviewPhone);
                webDialog.setNeutralButton(getString(R.string.btnCancel),
                        new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int which) {
                                dialog.cancel();
                            }
                        });
                webviewPhone.setWebViewClient(new WebViewClient() {
                    public void onPageFinished(WebView view, String url) {
                        super.onPageFinished(view, url);
                        loadWebView.dismiss();
                        webDialog.show();
                    }
                });
                webviewPhone.loadUrl(getPrefs(EMERGENCY_LIST_URL));
            }
        });*/

        mProgressDialog = new ProgressDialog(this);
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread(new Runnable() {
            Handler handler = new Handler() {
                @Override
                public void handleMessage(Message msg) {
                    super.handleMessage(msg);
                    mProgressDialog.hide();
                    ArrayAdapter<GroupInfo> adapter = new ArrayAdapter<GroupInfo>(
                            AlertSettingActivity.this, android.R.layout.simple_spinner_item,
                            category);
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                    spinIncase.setAdapter(adapter);
                    for (int i = 0; i < category.size(); i++) {
                        if (category.get(i).getId().equalsIgnoreCase(mAlertSendToGroup)) { // if select a group
                            spinIncase.setSelection(i);
                        }
                    }
                }
            };

            public void run() {
                Looper.prepare();
                try {
                    category = getContactGroups();
                } catch (Exception e) {
                    e.printStackTrace();
                }
                handler.sendEmptyMessage(0);
            }
        }).start();

    }

    private void submitChange(boolean finishActivity, Boolean confirm) {
        if (checkChange()) {
            mRecordDuration = String.valueOf(recordTimeArray[spinVoice.getSelectedItemPosition()]);
            mAlertSendToGroup = category.get(spinIncase.getSelectedItemPosition()).getId();

            saveSettingsConfirm(mHandler, finishActivity, false, confirm);

        } else {
            if (finishActivity && confirm) {
                mHandler.sendEmptyMessage(MESSAGE_FINISH_ACTIVITY);
            } else {
                mHandler.sendEmptyMessage(MESSAGE_FINISH_ACTIVITY);
                //Toast.makeText(this, R.string.setting_not_change, Toast.LENGTH_SHORT).show();
            }
        }
    }

    private void bindDataToForm() {
        for (int i = 0; i < recordTimeArray.length; i++) {
            if (String.valueOf(recordTimeArray[i]).equals(mRecordDuration)) {
                spinVoice.setSelection(i);
            }
        }
        /* if (!mEmergencyNumber.equals("")) {
             txtPanicPhone.setText(mEmergencyNumber);
         } else {
             txtPanicPhone.setText(EMERGENCY_NUMBER_DEFAULT);
         }
         if (mIncomingGovernmentAlert.equals("1")) {
             btnToggIncoming.setChecked(true);
         }*/
    }

    private boolean checkChange() {
        if (category.size() == 0) {
            return false;
        }

        if (!mRecordDuration.equals(String.valueOf(recordTimeArray[spinVoice
                .getSelectedItemPosition()]))) {
            return true;
        }
        if (!mAlertSendToGroup.equals(category.get(spinIncase.getSelectedItemPosition()).getId())) {
            return true;
        }
        /*String emergencyNumber = txtPanicPhone.getText().toString().trim();
        emergencyNumber = TextUtil.removePhoneCharacters(emergencyNumber);
        txtPanicPhone.setText(emergencyNumber);
        if (!mEmergencyNumber.equals(emergencyNumber)) {
            return true;
        }
        String incoming = btnToggIncoming.isChecked() ? "1" : "0";
        if (!mIncomingGovernmentAlert.equals(incoming)) {
            return true;
        }*/
        return false;
    }

    public static void show(Context context) {
        final Intent intent = new Intent(context, AlertSettingActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
        context.startActivity(intent);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.home, menu);
        return super.onCreateOptionsMenu(menu);
    }

    @Override
    protected void onResume() {
        super.onResume();
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

    public void onStart() {
        super.onStart();
        FlurryAgent.onStartSession(this, getPrefs(FLURRY_API_KEY));
    }

    public void onStop() {
        super.onStop();
        FlurryAgent.onEndSession(this);
    }

}
