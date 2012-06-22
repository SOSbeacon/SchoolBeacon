package cnc.sosbeacon.util;

import java.io.BufferedInputStream;
import java.io.Closeable;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import cnc.sosbeacon.constants.Constants;

import android.app.Activity;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.util.Log;

public class CompressImage {
	
	@SuppressWarnings("unused")
	private static  Bitmap getBitmap(File file){
		 if (file.exists()) {
	            InputStream stream = null;
	            try {
	                stream = new FileInputStream(file);
	                BufferedInputStream buf = new BufferedInputStream(stream);
	                BitmapFactory.Options options=new BitmapFactory.Options();
		            options.inSampleSize = 2;//
	                closeStream(stream);
	                return BitmapFactory.decodeStream(buf, null, options);
	            } catch (FileNotFoundException e) {
	            	android.util.Log.e("Compressing image", "Could not close stream", e);
	            } 
	        }
	        return null;
	}
	
	 public static void closeStream(Closeable stream) {
	        if (stream != null) {
	            try {
	                stream.close();
	            } catch (IOException e) {
	                android.util.Log.e("Compressing image", "Could not close stream", e);
	            }
	        }
	 }
	 
	 public static String run(Activity contextActivity,String filePath){
		try {
			 OutputStream fOut = null;
		     File file = new File(filePath);
		     String fileTemp = filePath.substring(0,filePath.indexOf(Constants.IMAGE_EXTEND));
		     fileTemp = fileTemp+"_tmp"+Constants.IMAGE_EXTEND;
	         Bitmap sourceBitmap  = decodeFile(file);
	         fOut = new FileOutputStream(fileTemp);
	         try {
	            sourceBitmap.compress(Bitmap.CompressFormat.JPEG, 50,fOut);
	            fOut.close();
	         } catch (Exception e) {
	        	 android.util.Log.d("Compressing image","exception while writing image: ");
	         } 
	         return fileTemp;
			} catch (Exception e) {
				e.printStackTrace();
		}
		return "";
	 }
	 
	 //decodes image and scales it to reduce memory consumption
	 private static Bitmap decodeFile(File f){
	     try {
	         //Decode image size
	         BitmapFactory.Options o = new BitmapFactory.Options();
	         o.inJustDecodeBounds = true;
	         BitmapFactory.decodeStream(new FileInputStream(f),null,o);
	         //The new size we want to scale to
	         final int REQUIRED_SIZE=70;
	         //Find the correct scale value. It should be the power of 2.
	         int width_tmp=o.outWidth, height_tmp=o.outHeight;
	         int scale=1;
	         while(true){
	             if(width_tmp/2<REQUIRED_SIZE || height_tmp/2<REQUIRED_SIZE)
	                 break;
	             width_tmp/=2;
	             height_tmp/=2;
	             scale++;
	         }
	         //Decode with inSampleSize
	         BitmapFactory.Options o2 = new BitmapFactory.Options();
	         o2.inSampleSize=scale;
	         return BitmapFactory.decodeStream(new FileInputStream(f), null, o2);
	     } catch (FileNotFoundException e) {}
	     return null;
	 }
	 
	 
	 public static Bitmap decodeFile(String filepath, int width, int height){
			File  f = new File(filepath);
		    Bitmap b = null;
		    try {
		        //Decode image size
		        BitmapFactory.Options o = new BitmapFactory.Options();
		        o.inJustDecodeBounds = true;
		        BitmapFactory.decodeStream(new FileInputStream(f), null, o);
		        int scale = 1;
		        if (o.outWidth > width || o.outHeight > height) {
		        	int maxsize;
		        	maxsize = (o.outHeight > o.outWidth) ? Constants.JPEG_WIDTH: Constants.JPEG_HEIGHT;
		            scale = (int) Math.pow(2, (int) Math.round(Math.log(maxsize / (double)Math.max(o.outHeight, o.outWidth)) / Math.log(0.5)));
		        }
		        //Decode with inSampleSize
		        BitmapFactory.Options o2 = new BitmapFactory.Options();
		        o2.inSampleSize = scale;
		        FileInputStream fileInput = new FileInputStream(f);
		        b = BitmapFactory.decodeStream(fileInput, null, o2);
		        fileInput.close();
		        
		        
		    } catch (Exception e) {
		    	Log.d("BitmapAdance",e.toString());
		    }
		    return b;
		}
}