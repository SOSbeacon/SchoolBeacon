package cnc.sosbeacon.util;

import java.io.FileOutputStream;

import android.graphics.Bitmap;
import android.graphics.Matrix;
import android.util.Log;
import cnc.sosbeacon.constants.Constants;

public class CompressImageApi7 {
	public static void Compress(String filepath, FileOutputStream outStream, Bitmap bm) {
		try {
			Bitmap bmsave = null;
			android.media.ExifInterface exifInterface = new android.media.ExifInterface(filepath);
			if( Integer.parseInt(exifInterface.getAttribute(android.media.ExifInterface.TAG_ORIENTATION)) == 3){
				Matrix mtx = new Matrix();
				mtx.postRotate(180);
				bmsave = Bitmap.createBitmap(bm,0,0,bm.getWidth(),bm.getHeight(),mtx,true);
				bmsave.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			}else if(( Integer.parseInt(exifInterface.getAttribute(android.media.ExifInterface.TAG_ORIENTATION)) == 6)){
				Matrix mtx = new Matrix();
				mtx.postRotate(90);
				bmsave = Bitmap.createBitmap(bm,0,0,bm.getWidth(),bm.getHeight(),mtx,true);
				bmsave.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			}else if(( Integer.parseInt(exifInterface.getAttribute(android.media.ExifInterface.TAG_ORIENTATION)) == 8)){
				Matrix mtx = new Matrix();
				mtx.postRotate(-90);
				bmsave = Bitmap.createBitmap(bm,0,0,bm.getWidth(),bm.getHeight(),mtx,true);
				bmsave.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			}else {
				bm.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			}
			bmsave.recycle();
		} catch (Exception e) {
		}
	}
	
	public static void Compress(String filepath, FileOutputStream outStream, Bitmap bm, int orientation) {
		try {
			Bitmap bmsave = null;
			if( orientation == 1){
				Matrix mtx = new Matrix();
				mtx.postRotate(90);
				bmsave = Bitmap.createBitmap(bm,0,0,bm.getWidth(),bm.getHeight(),mtx,true);
				bmsave.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			}else if( orientation == 2){
				Matrix mtx = new Matrix();
				mtx.postRotate(270);
				bmsave = Bitmap.createBitmap(bm,0,0,bm.getWidth(),bm.getHeight(),mtx,true);
				bmsave.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			}else if( orientation == 4){
				Matrix mtx = new Matrix();
				mtx.postRotate(180);
				bmsave = Bitmap.createBitmap(bm,0,0,bm.getWidth(),bm.getHeight(),mtx,true);
				bmsave.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			}else {
				bm.compress(Bitmap.CompressFormat.JPEG, Constants.JPEG_COMPRESS, outStream);
			}
			bmsave.recycle();
		} catch (Exception e) {
		}
	}
}
