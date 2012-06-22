package cnc.sosbeacon.util;

import java.util.Map;
import android.content.Context;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;
import cnc.sosbeacon.R;

public class Preferences implements SharedPreferences {
	
	private  SharedPreferences preferences;
	
	public Preferences(Context context) {
		PreferenceManager.setDefaultValues(context, R.xml.preferences, true);
		preferences =  PreferenceManager.getDefaultSharedPreferences(context);
	}

	public String get(String key) {
		return getString(key, "");
	}
	
	public String getString(String key, String defValue) {
		if (!preferences.contains(key)) {
			return "";
		}
		return preferences.getString(key, defValue);
	}
	
	public boolean getBoolean(String key, boolean defValue) {
		return preferences.getBoolean(key, defValue);
	}

	public float getFloat(String key, float defValue) {
		return preferences.getFloat(key, defValue);
	}

	public int getInt(String key, int defValue) {
		return preferences.getInt(key, defValue);
	}

	public long getLong(String key, long defValue) {
		return preferences.getLong(key, defValue);
	}

	public void registerOnSharedPreferenceChangeListener(
			OnSharedPreferenceChangeListener listener) {
	}

	public void unregisterOnSharedPreferenceChangeListener(
			OnSharedPreferenceChangeListener listener) {
	}
	
	public boolean contains(String key) {
		return preferences.contains(key);
	}

	public Editor edit() {
		return preferences.edit();
	}

	public Map<String, ?> getAll() {
		return preferences.getAll();
	}
}
