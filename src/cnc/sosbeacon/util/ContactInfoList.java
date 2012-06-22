package cnc.sosbeacon.util;

import java.util.ArrayList;

import android.os.Parcel;
import android.os.Parcelable;

public class ContactInfoList  extends ArrayList<ContactInfo> implements Parcelable {
	private static final long serialVersionUID = 1L;
	public String id;
	public String name;
	public String email;
	public String voicephone;
	public String textphone;
	public String status = ""; // add, edit, delete: use for update contact list to server
	public String type = "";
	
	public  ContactInfoList() {
		
	}
	
	public  ContactInfoList(Parcel in) {
		clear();
		int size = in.readInt();
		for (int i = 0; i < size; i++) {
			ContactInfo ci = new ContactInfo();
			ci.setId(in.readString());
			ci.setName(in.readString());
			ci.setEmail(in.readString());
			ci.setVoicephone(in.readString());
			ci.setTextphone(in.readString());
			ci.setStatus(in.readString());
			ci.setType(in.readString());
			add(ci);
		}
	}
	
	public int describeContents() {
		return 0;
	}
	
	public void writeToParcel(Parcel dest, int flags) {
		int size = size();
		dest.writeInt(size);
		for (int i = 0; i < size; i++) {
			dest.writeString(get(i).getId());
			dest.writeString(get(i).getName());
			dest.writeString(get(i).getEmail());
			dest.writeString(get(i).getVoicephone());
			dest.writeString(get(i).getTextphone());			
			dest.writeString(get(i).getStatus());
			dest.writeString(get(i).getType());
		}
		
	}
	
	@SuppressWarnings({ "rawtypes" })
	public static final Parcelable.Creator CREATOR = new Parcelable.Creator() {
		public ContactInfoList createFromParcel(Parcel in) {
		    return new ContactInfoList(in);
		}
		
		public ContactInfoList[] newArray(int size) {
		    return new ContactInfoList[size];
		}
	};
		
}
