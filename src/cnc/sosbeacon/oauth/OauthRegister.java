package cnc.sosbeacon.oauth;

import android.content.Context;
import cnc.sosbeacon.constants.Constants;
import cnc.sosbeacon.util.Preferences;

public class OauthRegister implements Constants {
	
	private String consumerId;
	private String consumerKey;
	private String consumerScret;
	private String requestTokenUrl;
	private String accessTokenUrl;
	private String authorizationUrl;
	private String requestContactUrl;
	private String requestContactParams;
	
	public OauthRegister(Context context, String oauth) {
		final Preferences prefs =  new Preferences(context);
		String prefix = OAUTH + "_" + oauth + "_";
		consumerKey = prefs.get(prefix + CONSUMER_KEY);
		consumerScret = prefs.get(prefix + CONSUMER_SECRET);
		requestTokenUrl = prefs.get(prefix + REQUEST_TOKEN_URL);
		accessTokenUrl = prefs.get(prefix + ACCESS_TOKEN_URL);
		authorizationUrl = prefs.get(prefix + AUTHORIZATION_URL);
		requestContactUrl = prefs.get(prefix + REQUEST_CONTACT_URL);
		requestContactParams = prefs.get(prefix + REQUEST_CONTACT_PARAMS);
	}
	
	public String getConsumerId() {
		return consumerId;
	}
	
	public String getConsumerKey() {
		return consumerKey;
	}
	
	public String getConsumerScret() {
		return consumerScret;
	}
	
	public String getRequestTokenUrl() {
		return requestTokenUrl;
	}
	
	public String getAccessTokenUrl() {
		return accessTokenUrl;
	}
	
	public String getAuthorizationUrl() {
		return authorizationUrl;
	}
	
	public String getRequestContactUrl() {
		return requestContactUrl;
	}
	
	public OauthRegister setRequestContactUrl(String url) {
		this.requestContactUrl = url;
		return this;
	}

	public String getRequestContactParams() {
		return requestContactParams;
	}
}
