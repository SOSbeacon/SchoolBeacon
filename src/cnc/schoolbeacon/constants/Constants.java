
package cnc.schoolbeacon.constants;

public interface Constants {

    public static final String ID = "id";

    public static final String PHONE_ID = "phoneId";

    public static final String IMEI = "imei";

    public static final String PHONE_NUMBER = "number";

    public static final String PHONE_TYPE = "phoneType";

    public static final String PHONE_INFO = "phoneInfo";

    public static final String EMAIL = "email";

    public static final String EMAILS = "emails";

    public static final String NAME = "name";

    public static final String PASSWORD = "password";

    public static final String TOKEN = "token";

    public static final String RESPONSE_CODE = "responseCode";

    public static final String STATE = "success";

    public static final String ERROR = "error";

    public static final String SUBJECT = "subject";

    public static final String MESSAGE = "message";

    public static final String ALERT_ID = "alertId";

    public static final String SETTING_ID = "settingId";

    public static final String RECORD_DURATION = "recordDuration";

    public static final String ALERT_SEND_TO_GROUP = "alertSendToGroup";

    public static final String EMERGENCY_NUMBER = "emergencyNumber";

    public static final String SINGLE_CONTACT = "singleContact";

    public static final String GOOD_SAMARITAN_STATUS = "goodSamaritanStatus";

    public static final String GOOD_SAMARITAN_RANGE = "goodSamaritanRange";

    public static final String PANIC_RANGE = "panicRange";

    public static final String PANIC_STATUS = "panicStatus";

    public static final String INCOMING_GOVERNMENT_ALERT = "incomingGovernmentAlert";

    public static final String CONTACT_GROUPS = "contactGroups";

    public static final String TO_GROUP = "toGroup";

    public static final String LATITUDE = "latitude";

    public static final String LONGITUDE = "longitude";

    public static final String METHOD = "_method";

    public static final String METHOD_GET = "GET";

    public static final String METHOD_POST = "POST";

    public static final String METHOD_PUT = "PUT";

    public static final String METHOD_DELETE = "DELETE";

    public static final String RESPONSE = "response";

    public static final String FORMAT = "format";

    public static final String JSON = "json";

    public static final String TYPE = "type";

    public static final String ALERT_LOG_TYPE = "alertlogType";

    public static final String PHONE_STATUS = "phoneStatus";

    public static final String COUNT_CONTACT = "countContact";

    public static final String DO = "do";

    public static final String REQUEST_PASSWORD = "REQUEST_PASSWORD";

    public static final String NEW = "NEW";

    public static final String UPDATE = "UPDATE";

    public static final String DELETE = "DELETE";

    public static final String LOGIN = "login";

    public static final String REGISTER = "register";

    public static final String ACTIVATE = "activate";

    public static final String DATA = "data";

    public static final String TEXT_PHONE = "textPhone";

    public static final String VOICE_PHONE = "voicePhone";

    public static final String CONTACT_LIST = "contactList";

    public static final String CONTACT_INFO = "contactInfo";

    public static final String BACK = "back";

    public static final String CONSUMER_ID = "consumerId";

    public static final String CONSUMER_NAME = "consumerName";

    public static final String CALL_BACK = "callBack";

    public static final String REGISTER_TYPE = "registerType";

    public static final String TITLE = "title";

    public static final String ACTIVITY = "activity";

    public static final String TO = "to";

    public static final String GROUP_ID = "groupId";

    public static final String GROUP_NAME = "groupName";

    public static final String SELECT = "select";

    public static final String CHECK_IN = "checkIn";

    public static final int MESSAGE_CONNECT_FAIL = 1;

    public static final int MESSAGE_CONNECT_EXCEPTION = 2;

    public static final int MESSAGE_FINISH = 3;

    public static final int MESSAGE_SAVED = 4;

    public static final int MESSAGE_FINISH_ACTIVITY = 5;

    public static final int TYPE_IMAGE = 0;

    public static final int TYPE_VOICE = 1;

    public static final int UPLOAD_IMAGE_SUCCESS = 3;

    public static final int UPLOAD_IMAGE_FAIL = 4;

    public static final int UPLOAD_AUDIO_FAIL = 8;

    public static final int UPLOAD_SUCCESS = 5;

    public static final int UPLOAD_AUDIO_SUCCESS = 6;

    public static final int EXCEPTION_ERROR = 7;

    public static final int CODE_SUCCESS = 1;

    public static final int CODE_ERROR = 2;

    public static final int CODE_NEW_ACCOUNT = 3;

    public static final int CODE_ACCOUNT_NEW_NUMBER = 4;

    public static final int CODE_ACCOUNT_NEW_IMEI = 5;

    public static final int CODE_ACCOUNT_NOT_ACTIVATED = 6;

    public static final String APP_FIRST_LOAD = "appFirstLoad";

    public static final String PHONE_FIRST_ACTIVATED = "phoneFirstActivated";

    public static final String FIRST_LOAD = "firstLoad";

    public static final String API_URL = "apiUrl";

    public static final String PHONE_URL = "phoneUrl";

    public static final String PHONE_GET_URL = "phoneGetUrl";

    public static final String SETTING_URL = "settingUrl";

    public static final String LOCATION_URL = "locationUrl";

    public static final String ALERT_URL = "alertUrl";

    public static final String REVIEW_URL = "reviewUrl";

    public static final String GROUP_URL = "groupUrl";

    public static final String GROUP_GET_URL = "groupGetUrl";

    public static final String CONTACT_URL = "contactUrl";

    public static final String CONTACT_GET_URL = "contactGetUrl";

    public static final String UPLOAD_URL = "uploadUrl";

    public static final String MAIL_ACCOUNT_URL = "mailAccountUrl";

    public static final String DEMO_URL = "demoUrl";

    public static final String AUTH_URL = "authUrl";

    public static final String EMERGENCY_LIST_URL = "emergencyListUrl";

    public final static String CONSUMER_KEY = "consumerKey";

    public final static String CONSUMER_SECRET = "consumerSecret";

    public final static String REQUEST_TOKEN_URL = "requestTokenUrl";

    public final static String AUTHORIZATION_URL = "authorizationUrl";

    public final static String ACCESS_TOKEN_URL = "accessTokenUrl";

    public final static String REQUEST_CONTACT_URL = "requestContactUrl";

    public final static String REQUEST_CONTACT_PARAMS = "requestContactParams";

    public static final String FLURRY_API_KEY = "flurryApiKey";

    public static final String USAGE_COUNT = "usageCount";

    public static final String OAUTH = "oauth";

    public static final String OAUTH_GOOGLE = "google";

    public static final String OAUTH_YAHOO = "yahoo";

    public static final String CALLBACK_URL = "sosbeacon://oauth";

    public final static String APP_DIR = "SchoolBeacon";

    public final static String IMAGE_DIR = APP_DIR + "/Image";

    public final static String VOICE_DIR = APP_DIR + "/Voice";

    public final static String IMAGE_FILE = "/sdcard/" + IMAGE_DIR + "/%d.jpg";

    public final static String IMAGE_FILE_TEMP = "/sdcard/" + IMAGE_DIR + "/temp.jpg";

    public final static String AUDIO_FILE = "/" + VOICE_DIR + "/";

    public final static String DATABASE = "schoolbeacon";

    public static final String PROVIDER_NAME = "cnc.schoolbeacon.provider";

    public final static String DATABASE_CREATE = "create table " + DATABASE
            + " (_id integer primary key autoincrement, message text, count integer);";

    public static final int UPDATE_LOCATION_INTERVAL = 30 * 60 * 1000;

    public static final int RECORD_TIME = 30 * 1000;

    public static final int WAITING_ACTIVATE_TIME = 30 * 1000;

    public static final int CALL_COUNTDOWN_TIME = 11 * 1000;

    public static final int JPEG_COMPRESS = 100;

    public static final int JPEG_WIDTH = 700;

    public static final int JPEG_HEIGHT = 500;

    public static final boolean IMAGE_COMPRESS = true;

    public static final String IMAGE_EXTEND = ".jpg";

    public static final String AUDIO_EXTEND = ".3gp";

    public static final String AUDIO_FILE_NAME = "audio.amr";

    public static final String PHONE_TYPE_ID = "2";

    public static final String EMERGENCY_NUMBER_DEFAULT = "0";

    public static final String SUCCESS = "success";

    public static final String TRUE = "true";

    public static final String FALSE = "false";

    public static final String USER = "user";

    public static final String USERID = "userId";

    public static final String DEFAULTGROUPID = "defaultGroupId";

    public static final String BROADCASTTYPE = "broadcastType";

    public static final String SHORT_MESSAGE = "shortMessage";

    public static final String LONG_MESSAGE = "longMessage";

    public static final String TOGROUPIDS = "toGroupIds[]";

    public static final String UPDATE_ACCOUNT = "updateAccount";

    public static final String SCHOOLID = "schoolId";

}
