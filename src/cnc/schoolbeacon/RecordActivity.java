
package cnc.schoolbeacon;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.lang.reflect.Method;
import java.util.ArrayList;

import android.app.AlertDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.graphics.Rect;
import android.graphics.YuvImage;
import android.hardware.Camera;
import android.hardware.Camera.ShutterCallback;
import android.hardware.Camera.Size;
import android.hardware.SensorManager;
import android.media.MediaPlayer;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.os.Message;
import android.preference.PreferenceManager;
import android.view.KeyEvent;
import android.view.OrientationEventListener;
import android.view.SurfaceHolder;
import android.view.SurfaceView;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import cnc.schoolbeacon.R;
import cnc.schoolbeacon.dialog.UploadDialog;
import cnc.schoolbeacon.recorder.AudioRecorder;
import cnc.schoolbeacon.util.FileUtil;

import com.flurry.android.FlurryAgent;
import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

@SuppressWarnings("serial")
public class RecordActivity extends GeneralActivity implements SurfaceHolder.Callback, Runnable,
        Camera.PreviewCallback {

    private final Logger logger = LoggerFactory.getLogger(RecordActivity.class);

    protected static final String TAG = "SosBeacon_RecordActivity";

    private Camera _camera = null;

    private SurfaceView _surfaceView;

    private SurfaceHolder _surfaceHolder;

    private boolean _cameraInUse = false;

    private Camera.PictureCallback _jpgCallback;

    private Camera.PictureCallback _rawCallback;

    private ShutterCallback _shutterCallback;

    private AudioRecorder audioRecord;

    private ArrayList<String> lstFileImage = new ArrayList<String>() {
    };

    private ArrayList<String> lstFileVoice = new ArrayList<String>() {
    };

    private RelativeLayout topControl;

    private RelativeLayout bottomControl;

    private TextView txtSeconds;

    private TextView txtNotice;

    private TextView txtNumberPhoto;

    private TextView txtCameraCountdown;

    private Button btnCapture;

    private Button btnDone;

    private String type;

    private String returnActivity;

    private String groupIds;

    private String checkInMessage = "";

    private String broadcastType = "";

    private String shortmessage = "";

    private String longmessage = "";

    private String checkInResultMessage = "";

    private int recordDuration;

    private int numberPhoto = 0;

    private boolean isManualCapture = false;

    private boolean isRecordStopped = false;

    private boolean isRecordReady = false;

    private boolean isRecordStarted = false;

    private boolean onPictureProcessing = false;

    private int count = 0;

    Thread runner;

    Runnable updater = new Runnable() {
        public void run() {
            recordProgress();
        };
    };

    final Handler mHandler = new Handler();

    private Handler callBackHandler;

    private AlertDialog countdownDialog;

    private int mOrientation = -1;

    private OrientationEventListener mOrientationEventListener;

    private static final int ORIENTATION_PORTRAIT_NORMAL = 1;

    private static final int ORIENTATION_PORTRAIT_INVERTED = 2;

    private static final int ORIENTATION_LANDSCAPE_NORMAL = 3;

    private static final int ORIENTATION_LANDSCAPE_INVERTED = 4;

    private SharedPreferences prefs;

    MediaPlayer player;

    private boolean capture = false;

    private ArrayList<Boolean> saveDone;

    private LinearLayout previewCover;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        logger.log(Level.INFO, ">>>>>>>>>> onCreate");
        super.onCreate(savedInstanceState);
        prefs = PreferenceManager.getDefaultSharedPreferences(this);
        player = MediaPlayer.create(RecordActivity.this, R.raw.bip);
        saveDone = new ArrayList<Boolean>();
        setContentView(R.layout.record);
        previewCover = (LinearLayout) findViewById(R.id.previewCover);
        previewCover.setVisibility(View.GONE);
        topControl = (RelativeLayout) findViewById(R.id.topControl);
        bottomControl = (RelativeLayout) findViewById(R.id.bottomControl);
        btnCapture = (Button) findViewById(R.id.btn_capture);
        btnDone = (Button) findViewById(R.id.btn_done);
        txtNotice = (TextView) findViewById(R.id.txt_notice);
        txtSeconds = (TextView) findViewById(R.id.txt_seconds);
        txtNumberPhoto = (TextView) findViewById(R.id.txt_number_photo);
        txtCameraCountdown = (TextView) findViewById(R.id.txt_camera_countdown);
        try {
            recordDuration = Integer.parseInt(mRecordDuration);
            recordDuration = (recordDuration >= 1 ? recordDuration : 1);
        } catch (Exception e) {
            recordDuration = 1;
        }

        _surfaceView = (SurfaceView) findViewById(R.id.preview);
        _surfaceHolder = _surfaceView.getHolder();

        topControl.setVisibility(View.GONE);
        txtCameraCountdown.setVisibility(View.INVISIBLE);
        countdownDialog = new AlertDialog.Builder(this).create();
        countdownDialog.setMessage(getString(R.string.check_in_start_audio_now));
        txtNotice.setText(R.string.check_in_start_audio_now);
        countdownDialog.setCancelable(true);

        returnActivity = getIntent().getStringExtra(ACTIVITY) != null ? getIntent().getStringExtra(
                ACTIVITY) : "";

        groupIds = getIntent().getStringExtra(GROUP_ID);
        broadcastType = getIntent().getStringExtra(BROADCASTTYPE);
        shortmessage = getIntent().getStringExtra(SHORT_MESSAGE);
        longmessage = getIntent().getStringExtra(LONG_MESSAGE);

        // alertLogType = check in: 2, alert: 0, call: 1
        if (returnActivity.equalsIgnoreCase("SosBeaconActivity")) {
            type = "2";
        }
        if (returnActivity.equalsIgnoreCase("NeedHelpActivity")) {
            type = "0";
        }
        try {
            recordDuration = Integer.parseInt(mRecordDuration);
            recordDuration = (recordDuration >= 1 ? recordDuration : 1);
        } catch (Exception e) {
            recordDuration = 1;
        }
        recordDuration = recordDuration * 30;
        callBackHandler = new Handler() {
            @Override
            public void handleMessage(Message msg) {
                super.handleMessage(msg);
                finish();
                logger.log(Level.INFO, "callBackHandler, checkInResultMessage"
                        + checkInResultMessage);
                if (returnActivity.equalsIgnoreCase("NeedHelpActivity")) {
                    NeedHelpActivity.show(RecordActivity.this, true);
                }
                if (returnActivity.equalsIgnoreCase("SosBeaconActivity")) {
                    SosBeaconActivity.show(RecordActivity.this, false);
                }
            }
        };

        isRecordReady = checkSdCard();
    }

    @Override
    protected void onResume() {
        logger.log(Level.INFO, "=== onResume");
        super.onResume();
        if (isRecordReady && !isRecordStarted && !isRecordStopped) {
            logger.log(Level.INFO, "onResume process");
            isRecordStarted = true;
            checkDirectory();
            btnDone.setOnClickListener(new OnClickListener() {
                public void onClick(View v) {
                    stopAndUploadFile();
                    while (runner != null) {
                        runner = null;
                    }
                    if (player != null) {
                        try {
                            player.stop();
                            player.release();
                        } catch (Exception e) {
                        }
                    }
                }
            });
            btnCapture.setOnClickListener(new OnClickListener() {
                public void onClick(View v) {
                    isManualCapture = true; // if user press capture then turn off auto capture
                    //takePicture();
                    btnCapture.setBackgroundResource(R.drawable.camera_press);
                    capture = true;
                }
            });
            _surfaceHolder.addCallback(this);
            _surfaceHolder.setType(SurfaceHolder.SURFACE_TYPE_PUSH_BUFFERS);
            _surfaceHolder.setKeepScreenOn(true);
            addCameraCallbacks();
            detectDisplayOrientation();
        }
    }

    @Override
    protected void onPause() {
        logger.log(Level.INFO, "=== onPause");
        while (runner != null) {
            runner = null;
        }
        finish();
        //System.exit(0);		
        super.onPause();
    }

    private void detectDisplayOrientation() {
        //logger.log(Level.INFO, "===== START detectDisplayOrientation");
        try {
            if (mOrientationEventListener == null) {
                mOrientationEventListener = new OrientationEventListener(this,
                        SensorManager.SENSOR_DELAY_NORMAL) {
                    @Override
                    public void onOrientationChanged(int orientation) {
                        // determine our orientation based on sensor response
                        int lastOrientation = mOrientation;
                        if (orientation > 315 || orientation < 45) {
                            if (mOrientation != ORIENTATION_PORTRAIT_NORMAL) {
                                mOrientation = ORIENTATION_PORTRAIT_NORMAL;
                            }
                        } else if (orientation < 315 && orientation > 225) {
                            if (mOrientation != ORIENTATION_LANDSCAPE_NORMAL) {
                                mOrientation = ORIENTATION_LANDSCAPE_NORMAL;
                            }
                        } else if (orientation < 225 && orientation > 135) {
                            if (mOrientation != ORIENTATION_PORTRAIT_INVERTED) {
                                mOrientation = ORIENTATION_PORTRAIT_INVERTED;
                            }
                        } else { // orientation <135 && orientation > 45
                            if (mOrientation != ORIENTATION_LANDSCAPE_INVERTED) {
                                mOrientation = ORIENTATION_LANDSCAPE_INVERTED;
                            }
                        }
                        if (lastOrientation != mOrientation) {
                            changeRotation(mOrientation);
                        }
                    }
                };
            }
            if (mOrientationEventListener.canDetectOrientation()) {
                mOrientationEventListener.enable();
            }
        } catch (Exception e) {
            logger.log(Level.ERROR, "detectDisplayOrientation: " + e.getMessage());
        }
        //logger.log(Level.INFO, "===== END detectDisplayOrientation");
    }

    public void onStart() {
        super.onStart();
        FlurryAgent.onStartSession(this, getPrefs(FLURRY_API_KEY));
    }

    public void onStop() {
        super.onStop();
        FlurryAgent.onEndSession(this);
    }

    public static void show(Context context, String alertid) {
        final Intent intent = new Intent(context, RecordActivity.class);
        intent.putExtra(ALERT_ID, alertid);
        context.startActivity(intent);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        logger.log(Level.INFO, "=== onKeyDown, keyCode=" + keyCode);
        if ((keyCode == KeyEvent.KEYCODE_BACK)) {
            stopAndUploadFile();
            while (runner != null) {
                runner = null;
            }
            if (player != null) {
                try {
                    player.stop();
                    player.release();
                } catch (Exception e) {
                }
            }
        }
        return true;
    }

    private void addCameraCallbacks() {
        logger.log(Level.INFO, "===== START addCameraCallbacks");
        try {
            _jpgCallback = new Camera.PictureCallback() {
                public void onPictureTaken(byte[] data, Camera camera) {
                    final byte[] _tempData = data;
                    RecordActivity.this.runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            lstFileImage = FileUtil.SaveImage(_tempData, lstFileImage);
                        }
                    });
                    try {
                        _camera.startPreview();
                    } catch (Exception e) {
                        logger.log(Level.ERROR,
                                "addCameraCallback: _camera.startPreview: " + e.getMessage());
                    }

                    onPictureProcessing = false;
                }

            };
            _rawCallback = new Camera.PictureCallback() {
                public void onPictureTaken(byte[] data, Camera camera) {

                }
            };
            // shutter callback
            _shutterCallback = new ShutterCallback() {
                public void onShutter() {

                }
            };
        } catch (Exception e) {
            logger.log(Level.ERROR, "addCameraCallbacks: " + e.getMessage());
        }
        logger.log(Level.INFO, "===== END addCameraCallbacks");
    }

    public void surfaceChanged(SurfaceHolder holder, int format, int width, int height) {
        if (!isRecordStopped) {
            if (_camera != null) {
                try {
                    // check to see if camera is already in preview mode
                    if (_cameraInUse) {
                        _camera.stopPreview();
                    }
                    setCameraParams();
                    _camera.getParameters().setPreviewSize(width, height);
                    _camera.setPreviewDisplay(holder);
                    _camera.startPreview();
                    _cameraInUse = true;
                } catch (Exception e) {
                    logger.log(Level.ERROR, "surfaceChanged: " + e.getMessage());
                    try {
                        if (_camera != null)
                            _camera.release();
                    } catch (Exception ex) {
                        logger.log(Level.ERROR, "_camera.release(): " + ex.getMessage());
                    }
                }
            }
        }
    }

    public void surfaceCreated(SurfaceHolder holder) {
        logger.log(Level.INFO, "===== start surfaceCreated");
        try {
            _camera = Camera.open();
            _camera.setPreviewCallback(this);
        } catch (Exception e) {
            Toast.makeText(this, "Surface created : " + e.getMessage(), Toast.LENGTH_LONG).show();
        }
        recordVoice();
        if (_camera != null) {
            try {
                if (runner == null) {
                    runner = new Thread(this);
                    runner.start();
                }
                _camera.setPreviewDisplay(holder);
            } catch (IOException e) {
                logger.log(Level.ERROR, "surfaceCreated: " + e.getMessage());
            }
        }
        try {
            countdownDialog.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void surfaceDestroyed(SurfaceHolder holder) {

    }

    private void playSound() {
        if (prefs.getBoolean("shutter", false)) {
            if (player != null) {
                player.stop();
                player = MediaPlayer.create(RecordActivity.this, R.raw.shutter);
                player.start();
            }
        }
    }

    public static void show(Context context, String returnActivity, String broadcastType,
            String groupIds, String shortmessage, String longmessage) {
        final Intent intent = new Intent(context, RecordActivity.class);
        intent.putExtra(ACTIVITY, returnActivity);
        intent.putExtra(BROADCASTTYPE, broadcastType);
        intent.putExtra(GROUP_ID, groupIds);
        intent.putExtra(SHORT_MESSAGE, shortmessage);
        intent.putExtra(LONG_MESSAGE, longmessage);

        intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
        context.startActivity(intent);
    }

    protected void setDisplayOrientation(Camera camera, int angle) {
        try {
            Method downPolymorphic;
            downPolymorphic = camera.getClass().getMethod("setDisplayOrientation", new Class[] {
                int.class
            });
            if (downPolymorphic != null)
                downPolymorphic.invoke(camera, new Object[] {
                    angle
                });
        } catch (Exception e) {
            logger.log(Level.ERROR, "== setDisplayOrientation: " + e.toString());
        }
    }

    private void setCameraParams() {
        //logger.log(Level.INFO, "===== START setCameraParams() =====");
        if (!isRecordStopped) {
            try {
                Camera.Parameters p = _camera.getParameters();
                p.set("orientation", "portrait");
                if (sdkVersion >= 8) {
                    setDisplayOrientation(_camera, 90);
                } else {
                    if (getResources().getConfiguration().orientation == Configuration.ORIENTATION_PORTRAIT) {
                        p.set("orientation", "portrait");
                        p.set("rotation", 90);
                    }
                    if (getResources().getConfiguration().orientation == Configuration.ORIENTATION_LANDSCAPE) {
                        p.set("orientation", "landscape");
                        p.set("rotation", 90);
                    }
                }
                _camera.setParameters(p);
            } catch (Exception e) {
                logger.log(Level.ERROR, "setCameraParams: " + e.getMessage());
            }
            detectDisplayOrientation();
        }
    }

    private void recordProgress() {
        Integer remainderCount = recordDuration - count;
        Integer captureTimeDown = remainderCount % 10;
        if (!isRecordStopped) {
            count++;
            if (count == 3) {
                countdownDialog.dismiss();
            }
            if (!isManualCapture) {
                // auto capture 3 seconds count
                if ((remainderCount > 10)
                        && (remainderCount < recordDuration)
                        && ((captureTimeDown == 1) || (captureTimeDown == 2) || (captureTimeDown == 3))) {
                    String captureCountdown = getString(R.string.check_in_captureinseconds);
                    captureCountdown = String.format(captureCountdown, captureTimeDown);
                    txtNotice.setText(captureCountdown);
                    topControl.setVisibility(View.VISIBLE);
                    btnCapture.setEnabled(false);
                    btnCapture.setVisibility(View.INVISIBLE);
                    txtCameraCountdown.setVisibility(View.VISIBLE);
                    txtCameraCountdown.setText(captureTimeDown.toString());
                    btnCapture.setBackgroundResource(R.drawable.camera_prepare);
                } else {
                    topControl.setVisibility(View.GONE);
                }
                if ((remainderCount >= 10) && (remainderCount < recordDuration)
                        && (captureTimeDown == 0)) {
                    txtCameraCountdown.setVisibility(View.INVISIBLE);
                    capture = true;
                    //takePicture();
                }
            } else {
                topControl.setVisibility(View.GONE);
                txtCameraCountdown.setVisibility(View.INVISIBLE);
            }
            if (remainderCount < 3) {
                btnCapture.setEnabled(false);
                btnCapture.setVisibility(View.INVISIBLE);
            }
            if (count <= recordDuration) {
                txtSeconds.setText(Integer.toString(remainderCount));
                txtNumberPhoto.setText(Integer.toString(numberPhoto));
            } else {
                stopAndUploadFile();
            }
        }
    }

    public void run() {
        while (runner != null) {
            try {
                Thread.sleep(1000);
            } catch (Exception e) {
                e.printStackTrace();
            }
            mHandler.post(updater);
        }
    }

    public void stopAndUploadFile() {
        logger.log(Level.INFO, "===== START stopAndUploadFile, isRecordStopped=" + isRecordStopped);
        if (!isRecordStopped && checkSaveDone()) {
            //_camera.setPreviewCallback(null);
            isRecordStopped = true;
            if (mOrientationEventListener != null)
                mOrientationEventListener.disable();
            while (runner != null) {
                runner = null;
            }

            btnCapture.setEnabled(false);
            btnCapture.setVisibility(View.INVISIBLE);
            topControl.setVisibility(View.GONE);
            bottomControl.setVisibility(View.GONE);

            stopRecord();
            if (type.equalsIgnoreCase("0")) {
                if (lstFileImage.size() > 0 || lstFileVoice.size() > 0) {
                    sendServer();
                } else {
                    callBackHandler.sendEmptyMessage(0);
                }
            }
            if (type.equalsIgnoreCase("2")) {
                Handler checkinHandler = new Handler() {
                    @Override
                    public void handleMessage(Message msg) {
                        saveCheckinMessage(checkInMessage);
                        super.handleMessage(msg);
                        try {
                            if (mProgressDialog.isShowing())
                                mProgressDialog.hide();
                        } catch (Exception e) {
                            logger.log(Level.ERROR, "Error dalog: " + e.getMessage());
                        }

                        checkInResultMessage = msg.obj.toString();
                        if (msg.arg1 > 0) {
                            broadcastType = String.valueOf(msg.arg1);
                            if (lstFileImage.size() > 0 || lstFileVoice.size() > 0) {
                                sendServer();
                            } else {
                                callBackHandler.sendEmptyMessage(0);
                            }
                        }
                    }
                };

                mProgressDialog.setMessage(getString(R.string.sendingCheckIn));
                try {
                    mProgressDialog.show();
                } catch (Exception e) {
                    logger.log(Level.ERROR, "stopAndUploadFile: " + e.getMessage());
                }

                String[] groupids = groupIds.split(",");
                sendCheckIn(checkinHandler, broadcastType, shortmessage, longmessage, groupids);
            }
        }
    }

    private void sendServer() {
        logger.log(Level.INFO, "===== START sendServer()");
        try {
            UploadDialog uploadDialog = new UploadDialog(RecordActivity.this,
                    getApiUrl(UPLOAD_URL), callBackHandler, lstFileImage, lstFileVoice,
                    broadcastType, type, mUserId, mSchoolId, getToken());

            uploadDialog.setCancelable(false);
            try {
                uploadDialog.show();
            } catch (Exception e) {
                e.printStackTrace();
                logger.log(Level.ERROR, "sendServer: " + e.getMessage());

            }
        } catch (Exception e) {
            logger.log(Level.ERROR, "send server: " + e.getMessage());
            Toast.makeText(this, "Send server : " + e.getMessage(), Toast.LENGTH_SHORT).show();
        }
        lstFileImage = new ArrayList<String>();
        lstFileVoice = new ArrayList<String>();
        logger.log(Level.INFO, "===== END sendServer()");
    }

    private void stopRecord() {
        logger.log(Level.INFO, "===== stopRecord");
        try {
            _cameraInUse = false;
            _camera.setPreviewCallback(null);
            _camera.stopPreview();
            _camera.release();
        } catch (Exception e) {
            logger.log(Level.ERROR, "stop record: " + e.getMessage());
            Toast.makeText(this, "Stop record : " + e.getMessage(), Toast.LENGTH_SHORT).show();
        }
        try {
            lstFileVoice.addAll(audioRecord.stop());
        } catch (Exception e) {
            logger.log(Level.ERROR, "stop record 2: " + e.getMessage());
        }
    }

    private void checkDirectory() {
        createDirectory(APP_DIR);
        createDirectory(IMAGE_DIR);
        createDirectory(VOICE_DIR);
    }

    private boolean checkSdCard() {
        String state = android.os.Environment.getExternalStorageState();
        if (!state.equals(android.os.Environment.MEDIA_MOUNTED)) {
            Toast.makeText(this, "SD Card is not mounted.  It is " + state + ".",
                    Toast.LENGTH_SHORT).show();
            return false;
        }
        return true;
    }

    private void createDirectory(String nDirectory) {
        try {
            String path = Environment.getExternalStorageDirectory().getAbsolutePath() + "/"
                    + nDirectory;
            File directory = new File(path);
            directory.mkdirs();
        } catch (Exception e) {
            logger.log(Level.ERROR, "create directory: " + e.getMessage());
        }
    }

    /*
     * Addition for testing purpose
     */
    private void changeRotation(int orientation) {
        //logger.log(Level.INFO, "===== START changeRotation. orientation = " + orientation);
        if (_camera != null && _cameraInUse && !isRecordStopped) {
            try {
                Camera.Parameters parameters = _camera.getParameters();
                switch (orientation) {
                    case ORIENTATION_PORTRAIT_NORMAL:
                        parameters.set("rotation", 90);
                        //logger.log(Level.INFO, "ORIENTATION_PORTRAIT_NORMAL : Rotation = 90");
                        break;
                    case ORIENTATION_LANDSCAPE_NORMAL:
                        parameters.set("rotation", 0);
                        //logger.log(Level.INFO, "ORIENTATION_LANDSCAPE_NORMAL : Rotation = 0");
                        break;
                    case ORIENTATION_PORTRAIT_INVERTED:
                        parameters.set("rotation", 270);
                        //logger.log(Level.INFO, "ORIENTATION_PORTRAIT_INVERTED : Rotation = 270");
                        break;
                    case ORIENTATION_LANDSCAPE_INVERTED:
                        parameters.set("rotation", 180);
                        //logger.log(Level.INFO, "ORIENTATION_LANDSCAPE_INVERTED : Rotation = 180");
                        break;
                }
                _camera.setParameters(parameters);
            } catch (Exception e) {
                logger.log(Level.ERROR, "changeRotation: " + e.getMessage());
            }
        }
        //logger.log(Level.INFO, "END changeRotation =====");
    }

    public void recordVoice() {
        try {
            audioRecord = new AudioRecorder(AUDIO_FILE);
            //MediaPlayer player = MediaPlayer.create(RecordActivity.this, R.raw.bip);
            player.start();
            audioRecord.start();
        } catch (IOException e) {
            logger.log(Level.ERROR, "Record voice: " + e.getMessage());
        }
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        try {
            if (mProgressDialog != null)
                mProgressDialog.dismiss();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onPreviewFrame(final byte[] data, final Camera camera) {
        if (capture) {
            playSound();
            final Boolean check = false;
            saveDone.add(check);
            if (mOrientationEventListener == null) {
                mOrientationEventListener = new OrientationEventListener(this,
                        SensorManager.SENSOR_DELAY_NORMAL) {
                    @Override
                    public void onOrientationChanged(int orientation) {
                        if (orientation > 315 || orientation < 45) {
                            if (mOrientation != ORIENTATION_PORTRAIT_NORMAL) {
                                mOrientation = ORIENTATION_PORTRAIT_NORMAL;
                            }
                        } else if (orientation < 315 && orientation > 225) {
                            if (mOrientation != ORIENTATION_LANDSCAPE_NORMAL) {
                                mOrientation = ORIENTATION_LANDSCAPE_NORMAL;
                            }
                        } else if (orientation < 225 && orientation > 135) {
                            if (mOrientation != ORIENTATION_PORTRAIT_INVERTED) {
                                mOrientation = ORIENTATION_PORTRAIT_INVERTED;
                            }
                        } else { // orientation <135 && orientation > 45
                            if (mOrientation != ORIENTATION_LANDSCAPE_INVERTED) {
                                mOrientation = ORIENTATION_LANDSCAPE_INVERTED;
                            }
                        }
                    }
                };
            }
            if (mOrientationEventListener.canDetectOrientation()) {
                mOrientationEventListener.enable();
            }
            if (!prefs.getBoolean("shutter", false)) {
                previewCover.setVisibility(View.VISIBLE);
                new AsyncTask<Void, Void, Void>() {
                    @Override
                    protected Void doInBackground(Void... params) {
                        try {
                            Thread.sleep(500);
                        } catch (InterruptedException e) {
                            e.printStackTrace();
                        }
                        return null;
                    }

                    @Override
                    protected void onPostExecute(Void result) {
                        previewCover.setVisibility(View.GONE);
                        super.onPostExecute(result);
                    }

                }.execute();
            }
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    onPictureProcessing = true;
                    try {
                        Camera.Parameters parameters = camera.getParameters();
                        Size size = parameters.getPreviewSize();
                        YuvImage image = new YuvImage(data, parameters.getPreviewFormat(),
                                size.width, size.height, null);
                        Long time = System.currentTimeMillis();
                        File file = new File(Environment.getExternalStorageDirectory().getPath()
                                + "/SchoolBeacon/" + String.format("%d", time) + ".jpg");
                        FileOutputStream filecon = new FileOutputStream(file);
                        image.compressToJpeg(new Rect(0, 0, image.getWidth(), image.getHeight()),
                                90, filecon);
                        lstFileImage = FileUtil.savePicture(file, lstFileImage, mOrientation);
                    } catch (FileNotFoundException e) {
                        Toast toast = Toast.makeText(getBaseContext(), e.getMessage(), 1000);
                        toast.show();
                    }
                    onPictureProcessing = false;
                    capture = false;
                    numberPhoto++;
                    int index = saveDone.indexOf(check);
                    saveDone.set(index, new Boolean(true));
                    btnCapture.setVisibility(View.VISIBLE);
                    btnCapture.setEnabled(true);
                    btnCapture.setBackgroundResource(R.drawable.camera);
                }
            });
        }
    }

    private boolean checkSaveDone() {
        if (saveDone.size() > 0) {
            for (Boolean b : saveDone) {
                if (!b) {
                    return false;
                }
            }
        }
        return true;
    }
}
