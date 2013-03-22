
package cnc.schoolbeacon;

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
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.widget.Toast;
import cnc.schoolbeacon.R;
import cnc.schoolbeacon.http.HttpSolution;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class SettingActivity extends GeneralActivity {

    private final Logger logger = LoggerFactory.getLogger(SettingActivity.class);

    protected Handler mHandler;

    private ProgressDialog mProgressDialog;

    protected AlertDialog.Builder mAlertDialog;

    protected int callMenuId = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        logger.log(Level.INFO, ">>>>>>>>>> onCreate");
        super.onCreate(savedInstanceState);
        mProgressDialog = new ProgressDialog(this);
        mProgressDialog.setMessage(getString(R.string.loading));
        mProgressDialog.setCancelable(false);
        mHandler = new Handler() {
            @Override
            public void handleMessage(Message msg) {
                if (mProgressDialog.isShowing()) {
                    mProgressDialog.hide();
                }
                if (msg.what == MESSAGE_FINISH) {
                }
                if (msg.what == MESSAGE_SAVED) {
                }
                if (!mMessage.equalsIgnoreCase("")) {
                    Toast.makeText(SettingActivity.this, mMessage, Toast.LENGTH_LONG).show();
                }
            }
        };
    }

    protected void saveSettingsConfirm(final Handler handler, Boolean finishActivity,
            final Boolean updateAccount, Boolean confirm) {
        if (finishActivity) {
            if (confirm) {
                mAlertDialog = new AlertDialog.Builder(this);
                mAlertDialog.setMessage(R.string.setting_save_confirm);
                mAlertDialog.setNeutralButton(R.string.btnYes,
                        new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int which) {
                                updateSetting(handler, updateAccount);
                            }
                        });
                mAlertDialog.setNegativeButton(R.string.btnNo,
                        new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int which) {
                                handler.sendEmptyMessage(MESSAGE_FINISH_ACTIVITY);
                            }
                        });
                mAlertDialog.show();
            } else {
                updateSetting(handler, updateAccount);
            }
        } else {
            updateSetting(null, updateAccount);
        }
    }

    public void checkDialog(int type) {
        finish();
    }

    public static void show(Context context) {
        final Intent intent = new Intent(context, SettingActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
        context.startActivity(intent);
    }

    protected void updateSetting(final Handler newHandler, final Boolean updateAccount) {
        mProgressDialog = ProgressDialog.show(SettingActivity.this, "", getString(R.string.saving),
                true);
        new Thread(new Runnable() {
            public void run() {
                if (updateAccount) {
                    updatePhone(mUserName, mEmail, mPhoneNumber, mPassword);
                } else {
                    sendSetting();
                }
                mHandler.sendEmptyMessage(MESSAGE_SAVED);
                if (newHandler != null) {
                    newHandler.sendEmptyMessage(MESSAGE_FINISH_ACTIVITY);
                }
            }
        }).start();
    }

    private void sendSetting() {
        try {
            HttpClient client = new DefaultHttpClient();
            String postURL = getApiUrl(SETTING_URL) + mSettingId;
            HttpPost post = new HttpPost(postURL);
            HttpSolution httpObject = new HttpSolution();
            httpObject.put(METHOD, "PUT");
            httpObject.put(FORMAT, "json");
            httpObject.put(USERID, mUserId);
            httpObject.put(SCHOOLID, mSchoolId);
            httpObject.put(TOKEN, mToken);

            httpObject.put(RECORD_DURATION, mRecordDuration);
            httpObject.put(DEFAULTGROUPID, mAlertSendToGroup);

            //            httpObject.put(EMERGENCY_NUMBER, mEmergencyNumber);
            //            httpObject.put(PANIC_RANGE, mPanicRange);
            //            httpObject.put(PANIC_STATUS, mPanicStatus);
            //            httpObject.put(GOOD_SAMARITAN_STATUS, mGoodSamaritanStatus);
            //            httpObject.put(GOOD_SAMARITAN_RANGE, mGoodSamaritanRange);
            //            httpObject.put(INCOMING_GOVERNMENT_ALERT, mIncomingGovernmentAlert);

            List<NameValuePair> params = httpObject.getParams();
            UrlEncodedFormEntity ent = new UrlEncodedFormEntity(params, HTTP.UTF_8);
            post.setEntity(ent);
            HttpResponse responsePOST;
            HttpEntity resEntity = null;
            try {
                responsePOST = client.execute(post);
                resEntity = responsePOST.getEntity();
                String rest = EntityUtils.toString(resEntity);
                logger.log(Level.INFO, "sendSetting, responseJson: " + rest);
                JSONObject responseJson = new JSONObject(rest);
                responseJson = responseJson.getJSONObject(RESPONSE);
                Boolean state = responseJson.getBoolean(SUCCESS);
                if (state) {
                    initPhoneSettings(responseJson.getJSONObject("settings"));
                    savePhoneInfor();
                }
                mMessage = responseJson.getString(MESSAGE);
            } catch (Exception e) {
                mMessage = e.getMessage();
                logger.log(Level.INFO, "sendSetting, Exception: " + e.getMessage());
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
