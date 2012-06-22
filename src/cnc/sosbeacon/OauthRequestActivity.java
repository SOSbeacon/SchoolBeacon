package cnc.sosbeacon;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.TextView;

public class OauthRequestActivity extends GeneralActivity {

	private final Logger logger = LoggerFactory.getLogger(OauthRequestActivity.class); 
	String consumerId; 
	String consumerName;
	String authUrl;
	TextView tvMessage;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		logger.log(Level.INFO, ">>>>>>>>>> onCreate");
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.oauth_request);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.i_title);
		
		final WebView webView = (WebView) findViewById(R.id.webView);
		tvMessage = (TextView) findViewById(R.id.message);
		
		final Button rightBtn = (Button) findViewById(R.id.title_right_btn);
		rightBtn.setVisibility(View.INVISIBLE);
		final Button leftBtn = (Button) findViewById(R.id.title_left_btn);
		leftBtn.setText(R.string.btnBack);
		leftBtn.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				finish();
			}
		});
		
		authUrl = getIntent().getStringExtra(AUTH_URL);
		consumerId = getIntent().getStringExtra(CONSUMER_ID);
		consumerName = getIntent().getStringExtra(CONSUMER_NAME);
		
		TextView title = (TextView) findViewById(R.id.title);
		title.setText(consumerName);
		if (consumerId.equalsIgnoreCase(OAUTH_YAHOO)) {
			tvMessage.setVisibility(View.VISIBLE);
			tvMessage.setText(R.string.yahooScreen);
		} else {
			tvMessage.setVisibility(View.GONE);
		}
		
		
		mProgressDialog.setMessage(getString(R.string.loading));
		mProgressDialog.show();
		webView.getSettings().setJavaScriptEnabled(true);
		webView.setHorizontalScrollBarEnabled(true);
		webView.setVerticalScrollBarEnabled(true);
		webView.setWebViewClient(new WebViewClient() {
			@Override
			public boolean shouldOverrideUrlLoading(WebView view, String url) {
				if(url.startsWith(CALLBACK_URL)) {
					OauthRequestActivity.this.finish();
					Uri uri = Uri.parse(url);
					String oauthVerifier = uri.getQueryParameter("oauth_verifier");
					oauthVerifier = oauthVerifier != null ? oauthVerifier : "";
					ImportContactsActivity.setOauthVerifier(oauthVerifier);
				} else {
					view.loadUrl(url);
				}
				return super.shouldOverrideUrlLoading(view, url);
			}
			@Override
			public void onPageFinished(WebView view, String url) {
				if (mProgressDialog.isShowing()) {
					mProgressDialog.hide();
				}
				super.onPageFinished(view, url);
			}
		});
		webView.loadUrl(authUrl);
	}
	

	public static void show(Context context, String authUrl, String consumerId, String consumerName) {
		final Intent intent = new Intent(context, OauthRequestActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		intent.putExtra(AUTH_URL, authUrl);
		intent.putExtra(CONSUMER_ID, consumerId);
		intent.putExtra(CONSUMER_NAME, consumerName);
		context.startActivity(intent);
	}
}
