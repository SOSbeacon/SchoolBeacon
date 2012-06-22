
package cnc.sosbeacon;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;
import android.os.Message;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;

import cnc.sosbeacon.util.TextUtil;

import com.flurry.android.FlurryAgent;

public class LoginActivity extends GeneralActivity {

    private EditText etNumber;

    //private EditText etPassword;
    //private RadioButton rbLogin;
    //private RadioButton rbRegister;
    private LinearLayout loginForm;

    private ProgressDialog mProgressDialog;

    private Handler mHandler;

    //private Integer loginFailCount = 0;
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
        //rbLogin = (RadioButton) findViewById(R.id.rbLogin);
        //rbRegister = (RadioButton)  findViewById(R.id.rbRegister);
        etNumber = (EditText) findViewById(R.id.etNumber);
        //etPassword  = (EditText) findViewById(R.id.etPassword);
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
                    mPhoneNumber = etNumber.getText().toString();
                    //mPassword =  etPassword.getText().toString();
                    //if (requestType.equals(LOGIN)) {
                    //	login();
                    //}
                    //if (requestType.equals(REGISTER)) {
                    register("");
                    //}
                }
            }
        });
        btCancel.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
                finish();
                System.exit(0);
            }
        });
        /*
        if (requestType.equals(LOGIN)) {
        	rbLogin.setChecked(true);
        	etPassword.setVisibility(View.VISIBLE);
        } else {
        	etPassword.setVisibility(View.GONE);
        	rbRegister.setChecked(true);
        }
        
        rbLogin.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
        	public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
        		if (isChecked) {
        			requestType = LOGIN;
        			etPassword.setVisibility(View.VISIBLE);
        		}
        	}
        });
        rbRegister.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
        	public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
        		if (isChecked) {
        			requestType = REGISTER;
        			etPassword.setVisibility(View.GONE);
        		}
        	}
        });
        */
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

    private void register(final String action) {
        mProgressDialog.setMessage(getString(R.string.loadingSubmit));
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        loginForm.setVisibility(View.INVISIBLE);
        mHandler = new Handler() {
            public void handleMessage(android.os.Message msg) {
                processLogin();
            };
        };
        new Thread(new Runnable() {
            public void run() {
                regsiterNewPhone(mPhoneNumber, action);
                mHandler.sendEmptyMessage(0);
            }
        }).start();
    }

    private void processLogin() {
        mProgressDialog.hide();
        if (mResponseCode == CODE_SUCCESS) { // OK
            finish();
            SosBeaconActivity.show(LoginActivity.this, true);
            Toast.makeText(LoginActivity.this, mMessage, Toast.LENGTH_LONG).show();
        }
        if (mResponseCode == CODE_ERROR) { // Unauthorized: login fail
            /*loginFailCount ++;
            if (loginFailCount >= 2) {
            	loginFailCount = 0;
            	forgotPasswordPrompt();
            }*/
            Toast.makeText(LoginActivity.this, mMessage, Toast.LENGTH_LONG).show();
            loginForm.setVisibility(View.VISIBLE);
        }
        if (mResponseCode == CODE_ACCOUNT_NOT_ACTIVATED) { // Forbidden: phone is not activated
            showWaitingActivate();
        }
        if (mResponseCode == CODE_NEW_ACCOUNT) { // Phone not found
            loginForm.setVisibility(View.VISIBLE);
            clearForm();
        }
        if (mResponseCode == CODE_ACCOUNT_NEW_IMEI || mResponseCode == CODE_ACCOUNT_NEW_NUMBER) {
            final AlertDialog adAction = new AlertDialog.Builder(this).setMessage(mMessage)
                    .create();
            LayoutInflater li = (LayoutInflater) getSystemService(LAYOUT_INFLATER_SERVICE);
            View v = li.inflate(R.layout.account_dialog, null);
            Button btCreateAccout = (Button) v.findViewById(R.id.btCreate);
            btCreateAccout.setOnClickListener(new OnClickListener() {
                public void onClick(View v) {
                    adAction.hide();
                    register(NEW);
                }
            });
            Button btUpateAccount = (Button) v.findViewById(R.id.btUpdate);
            btUpateAccount.setOnClickListener(new OnClickListener() {
                public void onClick(View v) {
                    adAction.hide();
                    register(UPDATE);
                }
            });
            Button btCancelAccount = (Button) v.findViewById(R.id.btCancel);
            btCancelAccount.setOnClickListener(new OnClickListener() {
                public void onClick(View v) {
                    adAction.hide();
                    loginForm.setVisibility(View.VISIBLE);
                }
            });
            adAction.setView(v);
            adAction.show();
        }
        if (mResponseCode == 0) { // App exception error
            Toast.makeText(this, mMessage, Toast.LENGTH_LONG).show();
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
                                etNumber.requestFocus();
                                etNumber.setText(mPhoneNumber);
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

    @SuppressWarnings("unused")
    private void forgotPasswordPrompt() {
        mHandler = new Handler() {
            @Override
            public void handleMessage(Message msg) {
                super.handleMessage(msg);
                mProgressDialog.hide();
                new AlertDialog.Builder(LoginActivity.this).setMessage(mMessage)
                        .setNeutralButton(R.string.btnOK, null).show();
            }
        };

        final Thread requestPassword = new Thread(new Runnable() {
            public void run() {
                updatePhone(REQUEST_PASSWORD, "", "", "");
                mHandler.sendEmptyMessage(0);
            }
        });

        AlertDialog.Builder adForgot = new AlertDialog.Builder(this);
        adForgot.setMessage(R.string.forgotPasswordPrompt);
        adForgot.setNeutralButton(R.string.btnYes, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
                clearForm();
                mProgressDialog.setMessage(getString(R.string.loadingRequestPassword));
                mProgressDialog.setCancelable(false);
                mProgressDialog.show();
                requestPassword.start();
            }
        });
        adForgot.setNegativeButton(R.string.btnNo, null);
        adForgot.show();
    }

    private void clearForm() {
        etNumber.setText("");
        //etPassword.setText("");
    }

    private boolean checkValidate() {
        String number = etNumber.getText().toString().trim();
        number = TextUtil.removePhoneCharacters(number);
        etNumber.setText(number);
        //String password = etPassword.getText().toString();
        if (number.length() == 0) {
            Toast.makeText(this, R.string.numberInvalid, Toast.LENGTH_LONG).show();
            etNumber.setText("");
            return false;
        }
        /*if(!password.equalsIgnoreCase("") && password.length() < 6) {
        	Toast.makeText(this, R.string.validate_password, Toast.LENGTH_LONG).show();
        	etPassword.setText("");
        	return false;
        }*/
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
