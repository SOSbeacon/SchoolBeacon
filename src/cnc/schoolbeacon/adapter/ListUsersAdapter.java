
package cnc.schoolbeacon.adapter;

import java.util.List;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;
import cnc.schoolbeacon.R;
import cnc.schoolbeacon.model.User;

public class ListUsersAdapter extends BaseAdapter {
    private List<User> users;

    private Context mContext;

    public ListUsersAdapter(Context context, List<User> users) {
        this.users = users;
        this.mContext = context;
    }

    @Override
    public int getCount() {
        return users.size();
    }

    @Override
    public Object getItem(int position) {
        return users.get(position);
    }

    @Override
    public long getItemId(int position) {
        return Long.valueOf(users.get(position).userId);
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(R.layout.item_list_user, null);
        }

        User item = users.get(position);

        TextView name = (TextView) convertView.findViewById(R.id.name);
        TextView email = (TextView) convertView.findViewById(R.id.email);
        TextView textphone = (TextView) convertView.findViewById(R.id.textphone);

        name.setText(item.name);
        email.setText(item.email);
        textphone.setText(item.textphone);

        convertView.setTag(item);

        return convertView;
    }

}
