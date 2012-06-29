
package cnc.schoolbeacon.dialog;

import java.util.ArrayList;
import java.util.List;

import android.app.Dialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.Window;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;
import cnc.schoolbeacon.R;
import cnc.schoolbeacon.constants.Constants;
import cnc.schoolbeacon.http.HttpUploadFile;

public class UploadDialog extends Dialog implements Constants {
    private Context context;

    private Handler uHandler = new Handler();

    private Handler callBackHandler = new Handler();

    private List<String> lstFileImage = new ArrayList<String>();

    private List<String> lstFileVoice = new ArrayList<String>();

    private LinearLayout layoutType;

    private LinearLayout layoutFile;

    private LinearLayout layoutDetails;

    private TextView txtType;

    private TextView txtFile;

    private TextView txtNotification;

    private TextView txtDetails;

    private ProgressBar progressBar;

    private String type;

    private String alertId;

    private String phoneId;
    
    private String userId;

    private String token;

    private String uploadUrl;

    private int countImage = 0;

    private int imageSuccess = 0;

    private int imageFail = 0;

    private int audioSuccess = 0;

    private int audioFail = 0;

    // alertLogType = check in: 2, alert: 0, call: 1
    public UploadDialog(Context context, String uploadUrl, Handler callBackHandler,
            List<String> lstFileImage, List<String> lstFileVoice, String alertId, String type,
            String userId, String token) {
        super(context);
        this.context = context;
        this.lstFileImage = lstFileImage;
        this.lstFileVoice = lstFileVoice;
        this.alertId = alertId;
        this.userId = userId;
        this.token = token;
        this.type = type;
        this.callBackHandler = callBackHandler;
        this.uploadUrl = uploadUrl;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.upload_dialog);
        //get widget
        txtType = (TextView) findViewById(R.id.txt_type);
        txtFile = (TextView) findViewById(R.id.txt_file);
        layoutType = (LinearLayout) findViewById(R.id.layout_type);
        layoutFile = (LinearLayout) findViewById(R.id.layout_file);
        layoutDetails = (LinearLayout) findViewById(R.id.layoutDetails);
        txtNotification = (TextView) findViewById(R.id.txt_upload_notification);
        txtDetails = (TextView) findViewById(R.id.uploadDetails);
        progressBar = (ProgressBar) findViewById(R.id.progressbar);
        layoutType.setVisibility(View.GONE);
        layoutFile.setVisibility(View.GONE);
        layoutDetails.setVisibility(View.GONE);
        upload();
    }

    private void setUploadDetail(Message msg, int type, boolean isFinish) {
        String uploadImageDetail = "Uploading photo %d \nSuccessful: %d";
        String uploadImageDetailFails = ", failed: %d";

        String uploadImagesSuccess = "Upload photos done!";
        String uploadAudioSuccess = "\nUpload audio done!";
        String uploadAudioFails = "\nUpload audio failed";

        String details = "";
        if (isFinish) {
            if (countImage > 0)
                details = uploadImagesSuccess;
            details += uploadAudioSuccess;
        } else {
            if (type == 0) {
                if (countImage > 0)
                    details = String.format(uploadImageDetail, countImage, imageSuccess);
                if (imageFail > 0)
                    details = String.format(uploadImageDetail + uploadImageDetailFails, countImage,
                            imageSuccess, imageFail);
            } else {
                if (countImage > 0)
                    details = uploadImagesSuccess;
                if (audioFail > 0) {
                    details += uploadAudioFails;
                } else {
                    details += "\nUploading audio";
                }
            }

        }

        if (!details.equalsIgnoreCase("")) {
            txtDetails.setText(details);
            layoutDetails.setVisibility(View.VISIBLE);
        }

        if (msg != null)
            if (msg.obj != null)
                Toast.makeText(context, msg.obj.toString(), Toast.LENGTH_SHORT).show();
    }

    private void upload() {
        uHandler = new Handler() {
            @Override
            public void handleMessage(Message msg) {
                switch (msg.what) {
                    case 1:
                    case 2:
                        if (msg.obj.toString().contains("audio")) {
                            type = "Audio";
                            setUploadDetail(null, 1, false);
                            try {
                                Thread.sleep(1000);
                            } catch (Exception ex) {
                                ex.printStackTrace();
                            }
                        } else {
                            countImage++;
                            type = "Image";
                            setUploadDetail(null, 0, false);
                        }
                        layoutType.setVisibility(View.VISIBLE);
                        layoutFile.setVisibility(View.VISIBLE);
                        txtType.setText(type);
                        txtFile.setText(msg.obj.toString());
                        break;

                    case UPLOAD_IMAGE_SUCCESS:
                        imageSuccess++;
                        setUploadDetail(null, 0, false);
                        break;

                    case UPLOAD_IMAGE_FAIL:
                        imageFail++;
                        setUploadDetail(msg, 0, false);
                        break;

                    case UPLOAD_AUDIO_SUCCESS:
                        audioSuccess++;
                        setUploadDetail(null, 1, false);
                        break;

                    case UPLOAD_AUDIO_FAIL:
                        audioFail++;
                        setUploadDetail(msg, 1, false);
                        break;

                    case UPLOAD_SUCCESS:
                        try {
                            Thread.sleep(1000);
                        } catch (Exception ex) {
                            ex.printStackTrace();
                        }
                        txtNotification.setText(R.string.uploadSuccess);
                        setUploadDetail(null, 1, true);
                        layoutType.setVisibility(View.GONE);
                        layoutFile.setVisibility(View.GONE);
                        progressBar.setVisibility(View.GONE);
                        uHandler.sendEmptyMessage(10);
                        break;

                    case EXCEPTION_ERROR:
                        setUploadDetail(msg, 1, false);
                        break;

                    case 10:
                        try {
                            Thread.sleep(2000); // wait for dialog show
                        } catch (Exception ex) {
                            ex.printStackTrace();
                        }
                        try {
                            if (UploadDialog.this.isShowing())
                                UploadDialog.this.dismiss();
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                        callBackHandler.sendEmptyMessage(0);
                        break;
                }
                super.handleMessage(msg);
            }
        };

        // alertLogType = check in: 2, alert: 0, call: 1
        Thread workthreadImage = new Thread(new HttpUploadFile(uploadUrl, uHandler, lstFileImage,
                lstFileVoice, alertId, type, userId, token));
        workthreadImage.start();

        //reset list	 
        lstFileImage = new ArrayList<String>();
        lstFileVoice = new ArrayList<String>();
    }
}
