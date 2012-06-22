package cnc.sosbeacon;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.widget.Button;
import android.widget.TextView;

public class TermActivity extends Activity implements OnClickListener  {
	
	private final Logger logger = LoggerFactory.getLogger(TermActivity.class);
	private WebView mwebView;
	private Button btnAccept;
	private Button btnCancel;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.term_layout);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		final TextView title = (TextView) findViewById(R.id.title);
		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		title.setText("Terms of Service");
		leftBtn.setVisibility(View.INVISIBLE);
		rightBtn.setVisibility(View.INVISIBLE);
		btnAccept  = (Button) findViewById(R.id.btn_acept);
		btnCancel  = (Button) findViewById(R.id.btn_cancel);
		mwebView = (WebView) findViewById(R.id.webview_term);
		WebSettings webSettings = mwebView.getSettings();		
		webSettings.setJavaScriptEnabled(true);  
		webSettings.setDefaultTextEncodingName("utf-8");
		mwebView.loadUrl("file:///android_asset/term.html");
		btnAccept.setOnClickListener(this);
		btnCancel.setOnClickListener(this);
		super.onCreate(savedInstanceState);
		// Clear all storage
		PreferenceManager.getDefaultSharedPreferences(this).edit().clear().commit();
	}

	public void onClick(View v) {
		switch (v.getId()) {
			case R.id.btn_acept:
				logger.log(Level.INFO, "onClick: btn_acept");
				finish();
				LoginActivity.show(this);
				break;
			case R.id.btn_cancel:
				logger.log(Level.INFO, "onClick: btn_cancel");
				finish();
				System.exit(0);
				break;
			default:
				break;
			}
	}
	
	@Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
    	if (keyCode == KeyEvent.KEYCODE_BACK) {
    		logger.log(Level.INFO, "onKeyDown: KEYCODE_BACK");
    		finish();
    		System.exit(0);
    	}
    	return super.onKeyDown(keyCode, event);
    }
	
	public static void show(Context context) {
		final Intent intent = new Intent(context, TermActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		context.startActivity(intent);
	}
}
