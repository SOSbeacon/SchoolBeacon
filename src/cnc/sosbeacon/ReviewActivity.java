package cnc.sosbeacon;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.TextView;

import com.flurry.android.FlurryAgent;

public class ReviewActivity extends GeneralActivity{
    private WebView  webView;
    private ProgressDialog mProgressDialog;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.review);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		final TextView title = (TextView) findViewById(R.id.title);
		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		title.setText(APP_DIR);
		leftBtn.setVisibility(View.INVISIBLE);
		rightBtn.setVisibility(View.INVISIBLE);
		webView  = (WebView) findViewById(R.id.webview);
		webView.getSettings().setJavaScriptEnabled(true);
		webView.getSettings().setPluginsEnabled(true);
		webView.setWebViewClient(new WebViewClient() {
			@Override
			public void onPageFinished(WebView view, String url) {
				if(mProgressDialog.isShowing()){
		    		mProgressDialog.dismiss();
		    	}   
			}
		});
		webView.loadUrl(String.format(getApiUrl(REVIEW_URL),getToken()));
		mProgressDialog = ProgressDialog.show(ReviewActivity.this, "", getString(R.string.loading));
		mProgressDialog.setCancelable(true);
	}
	
	public void onStart() {
	   super.onStart();
	   FlurryAgent.onStartSession(this, getPrefs(FLURRY_API_KEY));
	}
	public void onStop() {
	   super.onStop();
	   FlurryAgent.onEndSession(this);
	}
	
	public static void show(Context context) {
		final Intent intent = new Intent(context, ReviewActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		context.startActivity(intent);
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.home, menu);
		MenuItem item = menu.findItem(R.id.menu_item_review);
	    item.setChecked(true);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		finish();
		callActivity(item.getItemId(), this);
		return super.onMenuItemSelected(featureId, item);
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			finish();
			SosBeaconActivity.show(ReviewActivity.this, false);
			return true;
		}
		return super.onKeyDown(keyCode, event);
	}
}
