package cnc.schoolbeacon.adapter;

import java.util.ArrayList;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import cnc.schoolbeacon.R;

public class ContactAdapter extends ArrayAdapter<ContactDeviceInfo>{
	private ArrayList<ContactDeviceInfo> items;
	private Context context;
	private TextView txtList;
	private TextView txtExtraInfo;
	
	public ContactAdapter(Context context, int textViewResourceId, ArrayList<ContactDeviceInfo> items) {
		super(context, textViewResourceId, items);
		this.items = items;
		this.context = context;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View v = convertView;
		if (v == null) {
			LayoutInflater vi = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			v = vi.inflate(R.layout.contact_list, null);
		}
		txtList		 = (TextView) v.findViewById(R.id.txttitle_bookmark);
		txtExtraInfo = (TextView) v.findViewById(R.id.txtextra_bookmark);
		ContactDeviceInfo bookmarkMap = items.get(position);
		if (bookmarkMap != null) {
			if (txtList != null) {
				txtList.setText(bookmarkMap.getName());
			}
			if (txtExtraInfo != null) {
				txtExtraInfo.setText("Number :"+bookmarkMap.getNumber());
			}
		}
		return v;
	}	
}