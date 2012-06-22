package cnc.sosbeacon.util;

import android.content.ContentResolver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.provider.ContactsContract;

public class ContactAccessorNewApi  {
	
	public static Intent getContactPickerIntent() {
		Intent intent = new Intent(Intent.ACTION_PICK, ContactsContract.Contacts.CONTENT_URI);
    	intent.setType(ContactsContract.Contacts.CONTENT_TYPE);
        return intent;
     }
	
	public static void getPhoneInfo(Context context, Cursor c) {
		ContentResolver cr = context.getContentResolver();
		String id = "";
		String number = ""; 
		String name = "";
		String email = "";
    	id = c.getString(c.getColumnIndexOrThrow(android.provider.ContactsContract.Contacts._ID));
    	// Get name
    	name = c.getString(c.getColumnIndexOrThrow(android.provider.ContactsContract.Contacts.DISPLAY_NAME));
    	// Get phone number
    	Cursor cPhone = cr.query( 
    			ContactsContract.CommonDataKinds.Phone.CONTENT_URI, null,
    			ContactsContract.CommonDataKinds.Phone.CONTACT_ID + " = ? ", 
                new String[] { id }, null); 
        if (cPhone.moveToFirst()) {
           number = cPhone.getString(cPhone.getColumnIndex(ContactsContract.CommonDataKinds.Phone.NUMBER));
        }
        // Get email address
        Cursor cEmail = cr.query( 
        		ContactsContract.CommonDataKinds.Email.CONTENT_URI, null, // 
        		ContactsContract.CommonDataKinds.Email.CONTACT_ID + " = ?",  // 
                new String[] { id }, null); 
        if(cEmail.getCount()>0) {
	        if (cEmail.moveToFirst()) { 
	             email = cEmail.getString(cEmail.getColumnIndex(ContactsContract.CommonDataKinds.Email.DATA));
	             if((email == null) || (email.equals(""))) {
	            	 email = "";   
	             }
	        }
        }    
        ContactAccessor. _ID = id;
        ContactAccessor.Number = number.trim();
        ContactAccessor.Name = name.trim();
        ContactAccessor.Email = email.trim();
        
        try {
        	c.close();
        	cPhone.close();
        	cEmail.close();
        } catch (Exception e) {
        	e.printStackTrace();
		}
	}
}


