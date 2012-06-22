package cnc.sosbeacon.util;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.util.ArrayList;

import cnc.sosbeacon.constants.Constants;

import android.graphics.Bitmap;
import android.os.Build;
import android.os.Environment;
import android.util.Log;

public class FileUtil {
	
	public static ArrayList<String> savePicture(File sourceFile,ArrayList<String> lstFileImage, int rotation){
		try {
			Long fileName = System.currentTimeMillis();		
			FileOutputStream outStream = null;
			FileInputStream fin = null;
			try {
				outStream = new FileOutputStream(String.format(Constants.IMAGE_FILE_TEMP, fileName));
				fin = new FileInputStream(sourceFile);
				byte[] buffer = new byte[1024];
				int read = 0;
				while ((read = fin.read(buffer)) != -1) {
					outStream.write(buffer, 0, read);
				}
				outStream.flush();
				outStream.close();
				File file = new File(Environment.getExternalStorageDirectory().getAbsolutePath() + "/" + Constants.IMAGE_DIR + "/" + "temp" + Constants.IMAGE_EXTEND);
				String filepath = Environment.getExternalStorageDirectory().getAbsolutePath() + "/" + Constants.IMAGE_DIR + "/" + "temp" + Constants.IMAGE_EXTEND;
				
				outStream = new FileOutputStream(String.format(Constants.IMAGE_FILE, fileName));
				Bitmap bm = CompressImage.decodeFile(file.getPath(), Constants.JPEG_WIDTH, Constants.JPEG_HEIGHT);
				//int sdkVersion = Integer.parseInt(Build.VERSION.SDK);
		        //if (sdkVersion >= 7) {
		        	cnc.sosbeacon.util.CompressImageApi7.Compress(filepath, outStream, bm, rotation);
		        //} else {
		        //	bm.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
		        //}
				
				lstFileImage.add(Long.toString(fileName)+Constants.IMAGE_EXTEND);
				bm.recycle();
				sourceFile.delete();
			} catch (Exception e) {
				e.printStackTrace();
				Log.d("",e.getMessage());
			}
			outStream.flush();
			outStream.close();
			fin.close();
		} catch (Exception e) {
			e.printStackTrace();
		} finally {}
		return lstFileImage;
	}
	
	public static ArrayList<String> SaveImage(byte[] data,  ArrayList<String> lstFileImage) {
		try {
			Long fileName = System.currentTimeMillis();		
			FileOutputStream outStream = null;
			try {
				if (Constants.IMAGE_COMPRESS) {
					// Compress Images
					outStream = new FileOutputStream(Constants.IMAGE_FILE_TEMP);
					outStream.write(data);
					outStream.flush();
					outStream.close();
					File file = new File(Environment.getExternalStorageDirectory().getAbsolutePath() + "/" + Constants.IMAGE_DIR + "/" + "temp" + Constants.IMAGE_EXTEND);
					String filepath = Environment.getExternalStorageDirectory().getAbsolutePath() + "/" + Constants.IMAGE_DIR + "/" + "temp" + Constants.IMAGE_EXTEND;
					
					outStream = new FileOutputStream(String.format(Constants.IMAGE_FILE, fileName));
					Bitmap bm = CompressImage.decodeFile(file.getPath(), Constants.JPEG_WIDTH, Constants.JPEG_HEIGHT);
					int sdkVersion = Integer.parseInt(Build.VERSION.SDK);
			        if (sdkVersion >= 7) {
			        	cnc.sosbeacon.util.CompressImageApi7.Compress(filepath, outStream, bm);
			        } else {
			        	bm.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			        }
					
					File fileUse = new File(Environment.getExternalStorageDirectory().getAbsolutePath() + "/" + Constants.IMAGE_DIR + "/" + Long.toString(fileName)+Constants.IMAGE_EXTEND);
					lstFileImage.add(Long.toString(fileName)+Constants.IMAGE_EXTEND);
					Long longsize = fileUse.length();
					bm.recycle();
					Log.d("Save File", "Saved image, wrote bytes: " + longsize);
				} else {
					outStream = new FileOutputStream(String.format(Constants.IMAGE_FILE, fileName));
					outStream.write(data);
					lstFileImage.add(Long.toString(fileName)+Constants.IMAGE_EXTEND);
				}
			} catch (Exception e) {
				e.printStackTrace();
			}
			outStream.flush();
			outStream.close();
		} catch (Exception e) {
			e.printStackTrace();
		} finally {}
		return lstFileImage;
	}
	
	public static void deleteSosBeaconFolder() {
		try {
			File folderImage = new File(Environment.getExternalStorageDirectory().getAbsolutePath()+"/"+Constants.IMAGE_DIR);
			File[] filesi = folderImage.listFiles();
			for (int i = 0; i < filesi.length; i ++) {
				try {
					File f = filesi[i];
					f.delete();
				} catch (Exception e) {}
			}
			File folderAudio = new File(Environment.getExternalStorageDirectory().getAbsolutePath()+"/"+Constants.VOICE_DIR);
			File[] filesa = folderAudio.listFiles();
			for (int i = 0; i < filesa.length; i ++) {
				try {
					File f = filesa[i];
					f.delete();
				} catch (Exception e) {}
			}
		}
		catch (Exception e) {
			e.printStackTrace();
		}
	}
}
