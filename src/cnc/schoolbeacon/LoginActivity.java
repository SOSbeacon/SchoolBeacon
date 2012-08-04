
package cnc.schoolbeacon;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;
import cnc.schoolbeacon.R;

import com.flurry.android.FlurryAgent;

public class LoginActivity extends GeneralActivity {

    private EditText edtEmail;

    private EditText etPassword;

    private LinearLayout loginForm;

    private ProgressDialog mProgressDialog;

    private String requestType;

    private Integer waitingCount = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
        setContentView(R.layout.login);
        getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
        findViewById(R.id.title_left_btn).setVisibility(View.INVISIBLE);
        findViewById(R.id.title_right_btn).setVisibility(View.INVISIBLE);

        Button btLogin = (Button) findViewById(R.id.btnLogIn);
        Button btCancel = (Button) findViewById(R.id.btnCancel);

        edtEmail = (EditText) findViewById(R.id.edtEmail);
        etPassword = (EditText) findViewById(R.id.edtPassword);

        loginForm = (LinearLayout) findViewById(R.id.loginForm);
        mProgressDialog = new ProgressDialog(this);
        loginForm.setVisibility(View.VISIBLE);

        waitingCount = 0;

        requestType = getIntent().getStringExtra(DO);
        mMessage = getIntent().getStringExtra(MESSAGE);
        requestType = (requestType != null) ? requestType : "";
        if (requestType.equals(ACTIVATE)) {
            showWaitingActivate();
        }

        btLogin.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
                if (checkValidate()) {
                    mEmail = edtEmail.getText().toString();
                    mPassword = etPassword.getText().toString();
                    login();
                }
            }
        });
        btCancel.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
                finish();
                System.exit(0);
            }
        });
    }

    private void login() {
        loginForm.setVisibility(View.INVISIBLE);
        mProgressDialog.setMessage(getString(R.string.loadingLogin));
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread(new Runnable() {
            public void run() {
                requestPhoneData();
                mHandler.sendEmptyMessage(0);
            }
        }).start();
        mHandler = new Handler() {
            public void handleMessage(android.os.Message msg) {
                processLogin();
            };
        };
    }

    private void processLogin() {
        mProgressDialog.hide();
        if (mSuccess.equals(TRUE)) { // OK
            mRequestChooseSchool = false;
            SosBeaconActivity.show(LoginActivity.this, true);
            Toast.makeText(LoginActivity.this, mMessage, Toast.LENGTH_LONG).show();
            finish();
        }
        if (mSuccess.equals(FALSE)) {
            if (mRequestChooseSchool) {
                showSelectSchoolDialog(mSchools);
                return;
            }

            Toast.makeText(LoginActivity.this, mMessage, Toast.LENGTH_LONG).show();
            loginForm.setVisibility(View.VISIBLE);
        }
    }

    private void showWaitingActivate() {
        loginForm.setVisibility(View.INVISIBLE);
        preferences.edit().putBoolean(PHONE_FIRST_ACTIVATED, true).commit();
        mMessage = mMessage.equalsIgnoreCase("") ? getString(R.string.checkActivateSms) : mMessage;
        AlertDialog adActivatePhone = new AlertDialog.Builder(this).setMessage(mMessage).create();
        adActivatePhone.setCancelable(true);
        adActivatePhone.setOnCancelListener(new DialogInterface.OnCancelListener() {
            public void onCancel(DialogInterface dialog) {
                finish();
            }
        });
        adActivatePhone.setButton(getString(R.string.btnOK), new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                mProgressDialog.setMessage(getString(R.string.waitingActivation));
                mProgressDialog.setCancelable(true);
                mProgressDialog.setOnCancelListener(new DialogInterface.OnCancelListener() {
                    public void onCancel(DialogInterface dialog) {
                        LoginActivity.this.finish();
                    }
                });
                mProgressDialog.show();
                waitingCountDown.start();
            }
        });
        adActivatePhone.show();
    }

    private CountDownTimer waitingCountDown = new CountDownTimer(WAITING_ACTIVATE_TIME, 1000) {
        @Override
        public void onTick(long millisUntilFinished) {
        }

        @Override
        public void onFinish() {
            mProgressDialog.hide();
            waitingCount++;
            if (waitingCount == 1) { // Login again in first waiting
                login();
            } else {
                // Phone still not activated then ask receive SMS  	
                AlertDialog.Builder ackReceived = new AlertDialog.Builder(LoginActivity.this);
                ackReceived.setMessage(R.string.askReceiveSms);
                ackReceived.setCancelable(false);
                ackReceived.setNeutralButton(getString(R.string.btnOK),
                        new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int which) {
                                login();
                            }
                        });
                ackReceived.setNegativeButton(getString(R.string.btnNo),
                        new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int which) {
                                edtEmail.requestFocus();
                                edtEmail.setText(mPhoneNumber);
                                new AlertDialog.Builder(LoginActivity.this)
                                        .setMessage(R.string.verifyPhonePrompt)
                                        .setNegativeButton(R.string.btnOK, null).show();
                                loginForm.setVisibility(View.VISIBLE);
                            }
                        });
                ackReceived.show();
            }
        }
    };

    private boolean checkValidate() {
        String number = edtEmail.getText().toString().trim();
        String password = etPassword.getText().toString().trim();

        if (number.length() == 0 || password.length() == 0) {
            Toast.makeText(this, R.string.input_data_invalid, Toast.LENGTH_LONG).show();
            return false;
        }
        return true;
    }

    public static void show(Context context) {
        show(context, "", "");
    }

    public static void show(Context context, String requestType, String message) {
        final Intent intent = new Intent(context, LoginActivity.class);
        intent.putExtra(DO, requestType);
        intent.putExtra(MESSAGE, message);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        context.startActivity(intent);
    }

    @Override
    protected void onDestroy() {
        if (waitingCountDown != null) {
            try {
                waitingCountDown.cancel();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        super.onDestroy();
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
