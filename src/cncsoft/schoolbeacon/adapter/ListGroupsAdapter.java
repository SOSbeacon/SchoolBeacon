
package cncsoft.schoolbeacon.adapter;

import java.util.List;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;
import cncsoft.schoolbeacon.R;
import cncsoft.schoolbeacon.model.Group;

public class ListGroupsAdapter extends BaseAdapter {
    private List<Group> groups;

    private Context context;

    public ListGroupsAdapter(Context context, List<Group> groups) {
        this.groups = groups;
        this.context = context;
    }

    @Override
    public int getCount() {
        return groups.size();
    }

    @Override
    public Object getItem(int position) {
        return groups.get(position);
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
            convertView = inflater.inflate(R.layout.item_text, null);
        }

        Group group = groups.get(position);
        TextView group_name = (TextView) convertView.findViewById(R.id.item_name);
        group_name.setText(group.name);

        return convertView;
    }

}
