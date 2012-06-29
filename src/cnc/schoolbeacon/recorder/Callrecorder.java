package cnc.schoolbeacon.recorder;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import cnc.schoolbeacon.constants.Constants;

import android.media.MediaRecorder;
import android.os.Environment;


public class Callrecorder {

  private MediaRecorder recorder = new MediaRecorder();
  final String path;
  private String fileName;
  private List<String> lstVoiceImage = new ArrayList<String>();
  /**
   * Creates a new audio recording at the given path (relative to root of SD card).
   */
  public Callrecorder(String path) {
    this.path = sanitizePath(path);
  }

  private String sanitizePath(String path) {
    if (!path.startsWith("/")) {
      path = "/" + path;
    }
    if (!path.contains(".")) {
      fileName= System.currentTimeMillis()+"_call"+Constants.AUDIO_EXTEND;
      path +=fileName;     
      lstVoiceImage.add(fileName);
    }
    return Environment.getExternalStorageDirectory().getAbsolutePath() + path;
  }

  /**
   * Starts a new recording.
   */
  public void start() throws IOException {
    String state = android.os.Environment.getExternalStorageState();
    if(!state.equals(android.os.Environment.MEDIA_MOUNTED))  {
        throw new IOException("SD Card is not mounted.  It is " + state + ".");
    }
    // make sure the directory we plan to store the recording in exists
    File directory = new File(path).getParentFile();
    if (!directory.exists() && !directory.mkdirs()) {
      throw new IOException("Path to file could not be created.");
    }

    recorder.setAudioSource(MediaRecorder.AudioSource.VOICE_DOWNLINK);
    recorder.setOutputFormat(MediaRecorder.OutputFormat.THREE_GPP);
    recorder.setAudioEncoder(MediaRecorder.AudioEncoder.AMR_NB);
    recorder.setOutputFile(path);
    recorder.prepare();
    recorder.start();
  }

  /**
   * Stops a recording that has been previously started.
   */
  public List<String> stop() throws IOException {
	recorder.stop();
    recorder.release();
    return lstVoiceImage;
  }

}
