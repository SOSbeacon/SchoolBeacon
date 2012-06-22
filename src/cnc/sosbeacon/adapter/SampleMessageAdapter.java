package cnc.sosbeacon.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

public class SampleMessageAdapter extends ArrayAdapter<String> {
	private Context context;
	private String[] messages;
	private String[] checkInNormal;
	private String[] checkInAlert;
	
	public SampleMessageAdapter(Context context, int textViewResourceId, String[] objects, String[] checkInNormal, String[] checkInAlert) {
		super(context, textViewResourceId, objects);
		this.context = context;
		this.messages = objects;
		this.checkInNormal = checkInNormal;
		this.checkInAlert = checkInAlert;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		TextView tv = (TextView) View.inflate(context, android.R.layout.select_dialog_item, null);
		String message = messages[position];
		tv.setText(message);
		// Set color
		tv.setTextColor(Color.BLUE);
		for (String msg:checkInNormal) {
			if (msg.equalsIgnoreCase(message)) {
				tv.setTextColor(Color.BLACK);
			}
		}
		for (String msg:checkInAlert) {
			if (msg.equalsIgnoreCase(message)) {
				tv.setTextColor(Color.RED);
			}
		}
		if (position == 0 ||position >= 10) {
			tv.setTextColor(Color.GREEN);
		}
		return tv;
	}
}
