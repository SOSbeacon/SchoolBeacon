package cnc.sosbeacon.adapter;

import java.util.List;
import java.util.Map;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.SimpleAdapter;
import android.widget.TextView;
import cnc.sosbeacon.R;
import cnc.sosbeacon.constants.Constants;
import cnc.sosbeacon.util.ContactInfo;
import cnc.sosbeacon.util.ContactInfoList;

public class ImportContactsAdapter extends SimpleAdapter {
	Context context;
	List<? extends Map<String, String>> data;
	LayoutInflater inflater;
	Boolean[] itemCheckeds = new Boolean[250];
	
	public ImportContactsAdapter(Context context,
			List<? extends Map<String, String>> data, int resource, String[] from,
			int[] to) {
		super(context, data, resource, from, to);
		this.data = data;
		this.context = context;
		this.inflater = LayoutInflater.from(context);
	}
	
	@Override
	public View getView(final int position, View convertView, ViewGroup parent) {
		if (convertView ==  null) {
			convertView = inflater.inflate(R.layout.contact_item, null);
		}
		
		Map<String, String> item = (Map<String, String>) data.get(position);
		final CheckBox cbContact = (CheckBox) convertView.findViewById(R.id.contactSelect);
		cbContact.setTag(String.valueOf(position));
		cbContact.setOnClickListener( new OnClickListener() {
			public void onClick(View v) {
				Integer itemPosition = Integer.valueOf((String) cbContact.getTag());
				itemCheckeds[itemPosition] = cbContact.isChecked(); 
				
			}
		});
		if (itemCheckeds[position] == null) {
			itemCheckeds[position] = false;
		}
		cbContact.setChecked(itemCheckeds[position]);
		cbContact.setOnCheckedChangeListener(new OnCheckedChangeListener() {
			public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
				
			}
		});
		TextView tvCotactName = (TextView) convertView.findViewById(R.id.contactName);
		tvCotactName.setText(item.get("name"));
		TextView tvCotactPhone = (TextView) convertView.findViewById(R.id.contactPhone);
		tvCotactPhone.setText(item.get("phone"));
		TextView tvCotactEmail = (TextView) convertView.findViewById(R.id.contactEmail);
		tvCotactEmail.setText(item.get("email"));
		return convertView;
	}
	
	
	@Override
	public Object getItem(int position) {
		return data.get(position);
	}
	
	@Override
	public long getItemId(int position) {
		return position;
	}
	
	@Override
	public int getCount() {
		return data.size();
	}
		
	@Override
	public boolean isEmpty() {
		return super.isEmpty();
	}
	
	public ContactInfoList getSelectedContacts() {
		ContactInfoList selectedContacts =  new ContactInfoList();
		if (data != null) {
			for (int i = 0; i < data.size(); i++) {
				if (itemCheckeds[i] != null) {
					if (itemCheckeds[i]) {
						Map<String, String> item = (Map<String, String>) data.get(i);
						ContactInfo contactInfo = new ContactInfo();
						contactInfo.setStatus(Constants.NEW);
						contactInfo.setName(item.get("name"));
						contactInfo.setTextphone(item.get("phone"));
						contactInfo.setEmail(item.get("email"));
						selectedContacts.add(contactInfo);
					}
				}
			}
		}
		return selectedContacts;
	}
}
