package cnc.sosbeacon.constant;

import java.net.URLEncoder;

public class Values {
	
	public static final String Url = "http://www.sosbeacon.org";
	public static final int UPDATE_LOCATION_INTERVAL = 30 * 60 * 1000;
	public static final int RECORD_TIME = 30 * 1000;
	public static final String PHONE_TYPE_ID = "2";
	
	public static final String PREF_SETTINGID = "SettingId";
	public static final String PREF_PHONEID = "PhoneId";
	public static final String PREF_TOKEN = "Token";
	public static final String PREF_PHONENUMBER = "PhoneNumber";
	public static final String PREF_USERNAME = "UserName";
	public static final String PREF_PASSWORD = "Password"; 
	public static final String PREF_EMAIL = "Email";
	public static final String PREF_RECORDDURATION = "RecordDuration"; 
	public static final String PREF_ALERTSENDTOGROUP = "AlertSendToGroup";
	public static final String PREF_PANICNUMBER = "PanicNumber";
	public static final String PREF_PANICRANGE = "PanicRange";
	public static final String PREF_PANICSTATUS = "PanicStatus";
	public static final String PREF_GOODSAMARITANSTATUS = "GoodSamaritanStatus"; 
	public static final String PREF_GOODSAMARITANRANGE = "GoodSamaritanRange";
	public static final String PREF_INCOMINGGOVERMENTALERT = "IncomingGovermentAlert";
	public static final String PREF_CONTACT_GROUPS = "ContactGroups";
	
	public static final String FLURRY_API_KEY = "7EL6AKAU8746M1ZKT7LX";
	public static final String VIDEO_DEMO_URL = "http://sosbeacon.org/web/about/take-the-tour";
	public static final String USAGE_COUNT = "usage_count";
	
	public final static String CALLBACK_URL = "sosbeacon://oauth";
	public static final int OAUTH_GOOGLE = 1;
	public static final int OAUTH_YAHOO = 2;
	public static final int OAUTH_FACEBOOK = 3;
	
	public final static String GOOGLE_CONSUMER_KEY = "sosbeacon.org";
	public final static String GOOGLE_CONSUMER_SECRET = "8SLDym8v5FxtqLyFb1KKn2go";
	public final static String GOOGLE_REQUEST_TOKEN_URL = "https://www.google.com/accounts/OAuthGetRequestToken";
	public final static String GOOGLE_AUTHORIZATION_URL = "https://www.google.com/accounts/OAuthAuthorizeToken?hd=default";
	public final static String GOOGLE_ACCESS_TOKEN_URL = "https://www.google.com/accounts/OAuthGetAccessToken";
	public final static String GOOGLE_REQUEST_CONTACT_URL = "https://www.google.com/m8/feeds";
	public final static String GOOGLE_REQUEST_CONTACT_URL_PARAMS = "/contacts/default/full?max-results=200";

	public final static String YAHOO_CONSUMER_KEY = "dj0yJmk9OEJnWDZVRjBta1RZJmQ9WVdrOVVIWTNZakZLTXpBbWNHbzlNVFV5TXprd01UYzJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD04Yw--";
	public final static String YAHOO_CONSUMER_SECRET = "4f8cbed1ac24a2fe2121d6f16030226e0782fbd2";
	public final static String YAHOO_OAUTH_SIGNATURE = "49ec77bf1d83c28a6c7c6d5dedf19dd5df05de17%26";
	public final static String YAHOO_REQUEST_TOKEN_URL = "https://api.login.yahoo.com/oauth/v2/get_request_token";
	public final static String YAHOO_ACCESS_TOKEN_URL = "https://api.login.yahoo.com/oauth/v2/get_token";
	public final static String YAHOO_AUTHORIZATION_URL = "https://api.login.yahoo.com/oauth/v2/request_auth";
	public final static String YAHOO_REQUEST_CONTACT_URL = "http://query.yahooapis.com/v1/yql?format=json&q=" + URLEncoder.encode("SELECT * FROM social.contacts(200) WHERE ((guid=me)  AND ((fields.type=\"email\") OR (fields.type=\"phone\")))  limit 200");
	
	public final static String FACEBOOK_APP_ID = "137385389665389";
	public final static String FACEBOOK_API_KEY = "b3ac3716fe528532e10afeac7200983c";
	public final static String FACEBOOK_APP_SECRET = "ff6aa37b81108941a231d6f8e436cf7d";
	public final static String FACEBOOK_REQUEST_TOKEN_URL = "https://graph.facebook.com/oauth/access_token";
	public final static String FACEBOOK_AUTHORIZATION_URL = "https://graph.facebook.com/oauth/authorize";
	public final static String FACEBOOK_ACCESS_TOKEN_URL = "https://graph.facebook.com/me/friends";
	public final static String FACEBOOK_OAUTH_LOGIN_URL = "http://touch.facebook.com/login.php?redirect_uri=" + URLEncoder.encode(CALLBACK_URL) + "&app_id=" + FACEBOOK_APP_ID  + "&display=touch";//"http://touch.facebook.com/dialog/oauth?client_id=" + FACEBOOK_APP_ID  + "&redirect_uri=" + Url + "";
	
	public final static String directoryApp   = "SOSbeacon";
	public final static String directoryVoice = directoryApp+"/Voice";
	public final static String directoryImage = directoryApp+"/Image";
	public final static String saveImage  = "/sdcard/" + directoryImage + "/%d.jpg";
	public final static String saveTempImage  = "/sdcard/" + directoryImage + "/temp.jpg";
	public final static String saveVoice  = "/"+directoryVoice+"/";
	
	public static final String PROVIDER_CHECKIN_NAME = "sosbeacon.providerCheckinMessages";
	public final static String DATABASE_TABLE_CHECKIN = "sos_checkin_messages";
	public final static String DATABASE_CREATE_SOSBEACON_CHECKIN = "create table " + DATABASE_TABLE_CHECKIN  + " (_id integer primary key autoincrement, message text);";
	
	public final static String[] arrRecordImage = {"1 session","2 sessions","3 sessions","4 sessions","5 sessions","6 sessions"};
	public final static String[] arrRecordImageValues = {"1","2","3","4","5","6"};
	public final static String[] arrRecordLocation = {"10 minutes","20 minutes","30 minutes"};
	public final static String[] arrFamily = {"Family","Friends", "Family &amp; Friends", "Neighborhood Watch", "Group A", "Group B"};
	public final static String[] arrDistance = {"0 km","1 km","3 km","5 km","10 km","20 km"};
	public final static String[] arrDistanceValues = {"0","1","3","5","10","20"};
	public static final String[] GENRES = new String[] {"View Contact", "Add Contact"};

	public static final String PHONE_TYPE = "phoneType";
	public static final String getMailAccountUrl = Url + "/mail?format=json";  // get email user and pass
	public static final String registerUrl = Url + "/users";  //Url use when first register
	public static final String loginUrl = Url + "/rest?format=json&email=%s&password=%s&phoneNumber=%s&imei=%s&phoneType=%s";//Url use when login auto  in SplashActivity
	public static final String phoneUrl = Url + "/phones/"; //get all info phone include: setting id	
	public static final String updatePhone = Url + "/phones"; //Update username, password for user
	public static final String updateSettingUrl = Url + "/setting/";
	public static final String getSettingUrl = Url + "/setting/%s?phoneid=%s&token=%s&format=json";
	
	// JPEG Compress
	public static final int cameraJPEGCompress = 100;
	public static final int cameraJPEGWidth = 700; //maximum pixel
	public static final int cameraJPEGHeight = 500; //maximum
	public static final boolean imageCompress = true;
	
	//Update location
	public static final String locationUrl = Url + "/location"; 
	public static final String uploadUrl = Url + "/data"; 
	public static final String extendFileImage = ".jpg";
	public static final String alertUrl = Url + "/alert"; //alert after upload all
	public static final String reviewUrl = Url + "/web/alert/latest?token=%s";
	public static final String groupUrl = Url + "/groups/?format=json&phoneid=%s&token=%s";
	public static final String listContactUrl = Url + "/contacts?format=json&groupid=%s&token=%s";
	public static final String addContactUrl = Url + "/contacts";
	public static final String editContactUrl = Url + "/contacts";
	public static final String emergencyTelephoneNumberUrl = "file:///android_asset/SOSb-v4-911-other-countries.html";
	
	//Post object
	public static final String FORM_METHOD 	= "_method";
	public static final String RESPONE 	= "response";
	public static final String FORMAT 	= "format";
	public static final String EMAIL 	= "email";
	public static final String NAME 	= "name";
	public static final String PASSWORD = "password";
	public static final String ADDRESS  = "address";
	public static final String PHONENUMBER_LOGIN ="phoneNumber";
	public static final String PHONEID_LOGIN  = "phone_id";
	public static final String TOKEN    = "token";
	public static final String RESPONSECODE    = "responseCode";
	public static final String STATE    = "success";
	public static final String ALERTID    = "id";	
	public static final String UPLOADALERTID    = "alertid";	//alert id in upload file
	public static final String IMEI     = "imei";
	public static final String SETTINGID = "settingId";
	public static final String MESSAGE	 = "message";
	public static final String PANICNUMBERLOGIN	 = "panic_number";
	public static final String SAMARITANCESTATUS ="good_samaritan_status";
	public static final String PANIC_ALERT_GOOD_SAMARITAN_STATUS = "panic_alert_good_samaritan_status";
	public static final String ALERT_SENDTO_GROUP = "alert_sendto_group";
	public static final String PANIC_ALERT_GOOD_SAMARITAN_RANGE = "panic_alert_good_samaritan_range";
	public static final String INCOMING_GOVERNMENT_ALERT = "incoming_government_alert";
	public static final String GOOD_SAMARITAN_RANGE = "good_samaritan_status";
	public static final String VOICE_DURATION = "voice_duration";
	public static final String PHONE_STATUS = "phone_status";
	
	public static final String VOICEDURATION     = "voiceDuration";
	public static final String IMAGEDURATION     = "imageDuration";
	public static final String LOCATIONDURATION  = "locationDuration";
	public static final String TYPE     = "type";
	public static final String ALERT_LOG_TYPE     = "alertlogType";
	public static final String SETTINGNAME     = "username";
	public static final String SETTINGPASSWORD     = "password";
	public static final String SETTINGNUMBER     = "number";
	public static final String PHONEID_SETTING  = "id";
	public static final String PANICNUMBER     = "panicNumber";
	public static final String ALERTSENDTOGROUP     = "alertSendtoGroup";
	public static final String GOODSAMARITANSTATUS     = "goodSamaritanStatus";
	public static final String GOODSAMARITANRANGE     = "goodSamaritanRange";
	public static final String INCOMINGGOVERNMENTALERT     = "incomingGovernmentAlert";
	public static final String PANICSTATUS     = "panicStatus";
	public static final String PANICRANGE     = "panicRange";
	public static final String METHOD  ="_method";
	public static final String USERNAME  = "username";
	public static final String PHONEID  = "phoneid";
	public static final String PHONENUMBER  = "phonenumber";
	public static final String PHONEPANIC  = "ispanic";
	public static final String LATITUDE  = "latitude";
	public static final String LONGTITUDE  = "longtitude";
	//category
	public static final String FRIEND  = "Friend";
	public static final String FAMILY  = "Family";
	//parse group contact
	public static final String TAG_DATA  = "data";
	public static final String TAG_ID  = "id";
	public static final String TAG_NAME  = "name";
	public static final String TAG_TYPE = "type";
	public static final String TAG_EMAIL  = "email";
	public static final String TAG_VOICEPHONE = "voicephone";
	public static final String TAG_TEXTPHONE  = "textphone";
	//remove not full tag
	public static final String TAG_TYPE_NOTFULL = "<type/>";
	//Intent extra
	public static final String ITENT_POSITION  = "position";
	public static final String ITENT_GROUPID  = "groupid";
	public static final String ITENT_GROUPNAME  = "groupName";
	public static final String ITENT_VIEW_TITLE  = "viewTitle";
	public static final String ITENT_WHAT  = "what";
	public static final String ITENT_ID  = "id";
	public static final String ITENT_NAME  = "name";
	public static final String ITENT_EMAIL  = "email";
	public static final String ITENT_TEXTPHONE  = "textPhone";
	public static final String ITENT_VOICEPHONE  = "voicePhone";
	public static final String ITENT_CONTACTLIST  = "contactList";
	public static final String ITENT_CONTACTINFO  = "contactInfo";
	public static final String ITENT_ADD  = "add";
	public static final String ITENT_DELETE  = "delete";
	public static final String ITENT_UPDATE  = "update";
	public static final String ITENT_BACK  = "back";
	public static final String ITENT_ISLOGINEXIST  = "isLoginExist";
	public static final String INTENT_REGISTERPHASE  = "registerPhase";
	public static final String INTENT_FIRSTACTIVEPHONE  = "firstActivePhone";
	public static final String ITENT_TYPE="type";
	public static final String INTENT_ALERTID = "alertid";
	public static final String ITENT_MESSAGE="message";
	public static final String ITENT_USERNAME="username";
	public static final String ITENT_PASSWORD="password";
	public static final String ITENT_PHONENUMBER="phonenumber";
	public static final String INTENT_CONSUMER_ID = "consumerId";
	public static final String INTENT_CONSUMER_NAME = "consumerName";
	public static final String INTENT_ACTIVITY = "activity";
	public static final String INTENT_TO = "to";
	public static final String INTENT_GROUP = "group";
	public static final String INTENT_SELECT = "select";
	public static final String INTENT_MESSAGE = "message";
	public static final String INTENT_CALLBACK = "CALL_BACK";
	public static final String INTENT_NOTICE_MESSAGE = "noticeMessage";
	public static final String AUTH_URL = "authUrl";

	//Default panic phone
	public static final String panicPhone = "0";
	
	//format audio
	public static final String fAudio	 = ".3gp";
	public static final String nameAudio = "audio.amr";
	//get setting from server
	public static final String voiceduration	 = "voice_duration";
	
	public static final String sendMailSubject = "[From %s - %s] %s";
	public static final String sendMailMessage = "Sender information: %s, %s.\n\n Message: %s";
	// waiting active account
	public static final int waitingForActiveCountDown = 30 * 1000;
	public static final int callCountDown = 11 * 1000;

}
