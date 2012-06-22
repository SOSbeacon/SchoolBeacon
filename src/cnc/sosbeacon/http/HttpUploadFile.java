package cnc.sosbeacon.http;

import java.io.File;
import java.io.FileOutputStream;
import java.io.OutputStream;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.apache.http.client.HttpClient;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONObject;

import android.os.Environment;
import android.os.Handler;
import android.os.Message;
import cnc.sosbeacon.constants.Constants;
import cnc.sosbeacon.util.FileUtil;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;
import com.sosbeacon.convert.ThreegpReader;

public class HttpUploadFile implements Runnable, Constants {
	
	private Handler mHandler = new Handler();
	private String  pFile = "";
	private String phoneId;
	private String token;
	private String alertid;
	private String alertLogType = "";
	private List<String> listFileImage;
	private List<String> listFileAudio;
	private String uploadUrl;
	
	private final Logger logger = LoggerFactory.getLogger(HttpUploadFile.class); 
	
	public HttpUploadFile(String uploadUrl, Handler mHandler, List<String> lstFileImages, List<String> lstFileAudio, String alertid, String alertLogType, String phoneId, String token) {
		super();
		this.mHandler = mHandler;
		this.pFile = Environment.getExternalStorageDirectory().getAbsolutePath()+"/"+pFile;
		this.listFileImage = lstFileImages;
		this.listFileAudio = lstFileAudio;
		this.alertid = alertid;
		this.alertLogType = alertLogType;
		this.phoneId = phoneId;
		this.token = token;
		this.uploadUrl = uploadUrl;
	}
	
	public void uploadFile(List<String> listFile,int type) {
		if (listFile.size() > 0) {
			String filePath = "";
			String filePathinput = "";
			
			HttpClient client = new DefaultHttpClient();
			Message msg;
			logger.log(Level.INFO, "start upload file: " + listFile.toString());
			try {
				for (int i = 0; i < listFile.size(); i++) {
					if (type == TYPE_IMAGE) {
						filePath = Environment.getExternalStorageDirectory().getAbsolutePath()+"/"+IMAGE_DIR+"/"+listFile.get(i).toString();
					}
					if (type == TYPE_VOICE) {
						filePathinput =Environment.getExternalStorageDirectory().getAbsolutePath()+"/"+VOICE_DIR+"/"+listFile.get(i).toString();
						//convert to raw ARM NB
						File fileaudio = new File(filePathinput);
						ThreegpReader threegpReader = new ThreegpReader(fileaudio);
						filePath  = Environment.getExternalStorageDirectory().getAbsolutePath()+"/"+VOICE_DIR+"/"+ AUDIO_FILE_NAME;
						OutputStream outputFile = new FileOutputStream(filePath);
						threegpReader.extractAmr(outputFile);	
					}
					File recodFile = new File(filePath);
					if (recodFile.exists()) {
						Map<String, String> postData = new HashMap<String, String>();
						postData.put(FORMAT, JSON);
						if (type == TYPE_IMAGE) {
							postData.put(TYPE, "0");
							msg = new Message();
							msg.obj = listFile.get(i).toString();
							msg.what = 1;
							mHandler.sendMessage(msg);
						}
						if (type == TYPE_VOICE) {
							postData.put(TYPE, "1");
							msg = new Message();
							msg.obj= AUDIO_FILE_NAME;
							msg.what = 2;
							mHandler.sendMessage(msg);
						}
						postData.put(PHONE_ID, phoneId);
						postData.put(TOKEN, token);
						if (!alertLogType.equalsIgnoreCase("")) {
						  postData.put(ALERT_LOG_TYPE, alertLogType);
						}
						if (!alertid.equalsIgnoreCase("")) {
						  postData.put(ALERT_ID, alertid);
						}
						Map<String, File> postDataFiles = new HashMap<String, File>();
						postDataFiles.put("uploadedfile", recodFile);
						
						try {
							HttpData httpData = HttpRequest.postRequest(client, uploadUrl, postData, postDataFiles);
							String jsonUploadResultString = httpData.data;
							JSONObject uploadJson = new JSONObject(jsonUploadResultString);
							uploadJson = uploadJson.getJSONObject(RESPONSE);
							Boolean success = uploadJson.getBoolean(STATE);
							if (type == TYPE_IMAGE) {
								if (success) {
									msg = new Message();
									msg.what = UPLOAD_IMAGE_SUCCESS;
									mHandler.sendMessage(msg);
								} else {
									msg = new Message();
									msg.what = UPLOAD_IMAGE_FAIL;
									mHandler.sendMessage(msg);
								}
							}
							if (type == TYPE_VOICE) {
								if (success) {
									msg = new Message();				
									msg.what = UPLOAD_AUDIO_SUCCESS;
							        mHandler.sendMessage(msg);
								} else {
									msg = new Message();				
									msg.what = UPLOAD_AUDIO_FAIL;
							        mHandler.sendMessage(msg);
								}
							}
							logger.log(Level.INFO, "upload file: " + filePath + " - server response: " + jsonUploadResultString);
						} catch (Exception e) {
							logger.log(Level.ERROR, "Upload error, type: " + TYPE_IMAGE + ", "  + e.getMessage()); 
							if (type == TYPE_IMAGE) {
								msg = new Message();
								msg.what = UPLOAD_IMAGE_FAIL;
								msg.obj = "Upload image \"" + listFile.get(i).toString() + "\" failed, error: " + e.getMessage();
								mHandler.sendMessage(msg);
							} else {
								msg = new Message();
								msg.what = UPLOAD_AUDIO_FAIL;
								msg.obj = "Upload audio failed, error: " + e.getMessage();
								mHandler.sendMessage(msg);
							}
						}
					} else {
						logger.log(Level.ERROR, "file not found " + filePath); 
						if (type == TYPE_IMAGE) {
							msg = new Message();
							msg.what = UPLOAD_IMAGE_FAIL;
							mHandler.sendMessage(msg);
						} else {
							msg = new Message();
							msg.what = UPLOAD_AUDIO_FAIL;
							mHandler.sendMessage(msg);
						}
					}
				}
			} catch (Exception e) {
				logger.log(Level.FATAL, "Exception: " + e.getMessage());
				msg = new Message();
				msg.what = EXCEPTION_ERROR;
				msg.obj = "\n\n Error: " + e.getMessage();
				mHandler.sendMessage(msg);
			}
		}
	}

	public void run() {
		uploadFile(listFileImage, TYPE_IMAGE);
		uploadFile(listFileAudio, TYPE_VOICE);
		FileUtil.deleteSosBeaconFolder();
		Message msgSuccess = new Message();
		msgSuccess.what = UPLOAD_SUCCESS;
		mHandler.sendMessage(msgSuccess);
	}
}
