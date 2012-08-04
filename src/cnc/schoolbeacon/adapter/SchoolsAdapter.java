
package cnc.schoolbeacon.adapter;

import java.util.ArrayList;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;
import cnc.schoolbeacon.R;
import cnc.schoolbeacon.model.School;

public class SchoolsAdapter extends BaseAdapter {

    private ArrayList<School> schools = new ArrayList<School>();

    private Context context;

    public SchoolsAdapter(ArrayList<School> schools, Context context) {
        this.schools = schools;
        this.context = context;
    }

    @Override
    public int getCount() {
        return schools.size();
    }

    @Override
    public Object getItem(int position) {
        return schools.get(position);
    }

    @Override
    public long getItemId(int position) {
        return 0;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(R.layout.list_item, null);
        }

        TextView name = (TextView) convertView.findViewById(R.id.list_item_text);
        name.setText(schools.get(position).name);
        convertView.setTag(schools.get(position));

        return convertView;
    }
}
