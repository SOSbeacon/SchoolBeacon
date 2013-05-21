package cncsoft.schoolbeacon.adapter;

import java.util.ArrayList;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import cncsoft.schoolbeacon.R;
import cncsoft.schoolbeacon.util.ContactInfo;

public class ContactListAdapter extends ArrayAdapter<ContactInfo>{
	private ArrayList<ContactInfo> items;
	private Context context;
	private TextView tvName;
	private TextView tvPhone;
	private TextView tvEmail;
	
	public ContactListAdapter(Context context, int textViewResourceId, ArrayList<ContactInfo> items) {
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
		tvName		 = (TextView) v.findViewById(R.id.contactName);
		tvPhone		 = (TextView) v.findViewById(R.id.contactPhone);
		tvEmail		 = (TextView) v.findViewById(R.id.contactEmail);
		ContactInfo contactItem = items.get(position);
		if (contactItem != null) {
			tvName.setText(contactItem.getName());
			if (!contactItem.getTextphone().equalsIgnoreCase("") && !contactItem.getTextphone().equalsIgnoreCase("null")) {
				tvPhone.setText(contactItem.getTextphone());
			}
			if (!contactItem.getEmail().equalsIgnoreCase("") && !contactItem.getEmail().equalsIgnoreCase("null")) {
				tvEmail.setText(contactItem.getEmail());
			}
			if (Integer.valueOf(contactItem.getType()) == 1) { // set color for default contact
				String defaultContactColor = "#999999";
				tvName.setTextColor(Color.parseColor(defaultContactColor));
				tvPhone.setTextColor(Color.parseColor(defaultContactColor));
				tvEmail.setTextColor(Color.parseColor(defaultContactColor));
			}
		}
		return v;
	}
}