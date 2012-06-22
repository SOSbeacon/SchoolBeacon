package cnc.sosbeacon.util;

import android.content.ContentResolver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.os.Build;
import android.provider.Contacts;
import android.provider.Contacts.People;
import android.util.Log;

//Support android 1.5
@SuppressWarnings("deprecation")
public class ContactAccessor  {
	
	public static String _ID = "";
	public static String Name = "";
	public static String Number = "";
	public static String Email = "";
	
	public static boolean checkNewApi() {
		boolean newApi = false;
		try {
			Class.forName("cnc.sosbeacon.util.ContactAccessorNewApi");
			newApi = true;
		} catch (Exception e) {
			Log.d("API Level", "Old API Level");
		}
		return newApi;
	}
	
	public static Intent getContactPickerIntent() {
		Intent intent;
		int sdkVersion = Integer.parseInt(Build.VERSION.SDK);
        if (sdkVersion < 7) {
	     	intent = new Intent(Intent.ACTION_PICK, People.CONTENT_URI);
	     	intent.setType(People.CONTENT_TYPE);
        } else {
        	intent = cnc.sosbeacon.util.ContactAccessorNewApi.getContactPickerIntent();
        }
        return intent;
     }
	
	public static void getPhoneInfo(Context context, Cursor c) {
		int sdkVersion = Integer.parseInt(Build.VERSION.SDK);
        if (sdkVersion < 7) {
        	getPhoneInfoOldApi(context, c);
        } else { 
        	cnc.sosbeacon.util.ContactAccessorNewApi.getPhoneInfo(context, c);
        }
	}
	
	public static void getPhoneInfoOldApi(Context context, Cursor c) {
		String id = "";
		String number = ""; 
		String name = "";
		String email = "";
		ContentResolver cr = context.getContentResolver();
    	id = c.getString(c.getColumnIndexOrThrow(People._ID));
    	// Get name
    	name = c.getString(c.getColumnIndexOrThrow(People.DISPLAY_NAME));
    	// Get phone number
    	Cursor cPhone = cr.query( 
                Contacts.Phones.CONTENT_URI, null, 
                Contacts.Phones.PERSON_ID + " = ? ", 
                new String[] { id }, null); 
        if (cPhone.moveToFirst()) { 
           number  = cPhone.getString(cPhone.getColumnIndex(Contacts.Phones.NUMBER));
        }
        // Get email address
        Cursor cEmail = cr.query( 
                Contacts.ContactMethods.CONTENT_URI, null, 
                Contacts.ContactMethods.PERSON_ID + " = ? AND " +  Contacts.ContactMethods.KIND + "=  ? ", 
                new String[] { id, Integer.toString(Contacts.KIND_EMAIL) }, null); 
        if(cEmail.getCount()>0) {
	        if (cEmail.moveToFirst()) { 
	             email = cEmail.getString(cEmail.getColumnIndex(Contacts.ContactMethods.DATA));
	             if((email == null) || (email.equals(""))) {
	            	 email = "";   
	             }
	        }
        }    
        _ID = id;
        Number = number.trim();
        Name = name.trim();
        Email = email.trim();
        try {
        	c.close();
        	cPhone.close();
        	cEmail.close();
        } catch (Exception e) {
        	e.printStackTrace();
		}
	}
}


