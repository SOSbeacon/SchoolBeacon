//
//  SOSBEACONAppDelegate.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 08/06/2010.
//  Copyright CNC 2010. All rights reserved.
//  Entitlements.plist
//  define connect string 

//#define SERVER_URL @"http://www.sosbeacon.com"
//#define SERVER_URL @"http://sosbeacon.org:8085"
#define SERVER_URL @"http://sosbeacon.org/school"
#define DOCUMENTS_FOLDER [NSHomeDirectory() stringByAppendingPathComponent:@"Documents"] //Define folder save information user

#define CONF_DIALOG_DELAY_TIME 1 //define delay UIAlertView
#define   X_AppDelegate (SOSBEACONAppDelegate *) [[UIApplication sharedApplication] delegate]
#import <UIKit/UIKit.h>
#import <CoreLocation/CoreLocation.h>
#import "SplashView.h"
//#import "YOSSession.h"
#import "Reachability.h"
#import "TNHRequestHelper.h"
//#import "NewLogin.h"
//#import "TermsService.h"
@class HomeView;
@class SOWebView;
@class NewLogin;
@class StatusView;
@class Uploader;
@class AVAudioPlayer;
@class GroupsView;
@class TermsService;
//@class AddContactViewController;
@class OfflineViewController;

@class RestConnection;

@interface SOSBEACONAppDelegate : NSObject<UIApplicationDelegate, CLLocationManagerDelegate, ASIHTTPRequestDelegate> {
	
	UIWindow *window;
	SplashView *splashView;
	HomeView *viewHome;
	SOWebView *webView;
	GroupsView *groupView;
	IBOutlet UINavigationController *homNavigationController;
	
	StatusView *statusView;
	
	NSString *panicEmgergency;
	NSString *apiKey;
	NSString *latitudeString;
	NSString *longitudeString;
	
	NSInteger userID;
	NSInteger phoneID;
	NSInteger settingId;
		
	CLLocationCoordinate2D coordinate;
	CLLocationManager *locationManager;
	UIAlertView *alertLocation;
	
	NSMutableDictionary *settingArray;
	NSMutableDictionary *informationArray;	
	UITabBarController *tabBarController;
	
	Uploader *uploader;
	AVAudioPlayer *soundBip;
	BOOL logout;
	BOOL saveContact;
	BOOL savePerson;

//	YOSSession					*session;
	NSMutableDictionary			*oauthResponse;
	NSString	*guid;
	BOOL						launchDefault;
	NSInteger index;
	NSMutableArray *emailList;
	
//	AddContactViewController *addContactViewController;
	NSInteger flagSetting;
	NSString *email;
    NSString *userName;
    NSString *number;
	NSInteger contactCount;
	NSString *groupName;
	NSInteger flagsentalert;
	NSInteger flagforGroup;
	NSInteger respondcode;
	BOOL canShowVideo;
	NewLogin *newlogin;
	TermsService *termsService; 
	
	//Reachability* hostReach;
    Reachability* internetReach;
	NSTimer *countDownTimer;
	BOOL isOfflinemode;
	NSInteger flagOffline;
	OfflineViewController *offlineview;
	NSInteger countOffline;
	BOOL isShowAlert;
    
    RestConnection *restForCheckInternet;
}
//@property (nonatomic, retain)  Reachability* internetReach;
@property(nonatomic,retain) NewLogin *newlogin;
@property(nonatomic) BOOL canShowVideo;
@property(nonatomic) NSInteger respondcode;
@property(nonatomic) NSInteger flagforGroup;
@property(nonatomic)NSInteger flagsentalert;
@property(nonatomic,retain) NSString *groupName;
@property(nonatomic) NSInteger contactCount;
@property(nonatomic,retain) NSString *email;
@property(nonatomic) NSInteger flagSetting;
@property (nonatomic, retain) CLLocationManager *locationManager;

@property (nonatomic, retain) IBOutlet UITabBarController *tabBarController;
@property (nonatomic, retain) IBOutlet StatusView *statusView;

@property (nonatomic, retain) IBOutlet HomeView *viewHome;
@property (nonatomic, retain) IBOutlet UIWindow *window;
@property (nonatomic, retain) SOWebView *webView;
@property (nonatomic, retain) IBOutlet GroupsView *groupView;

@property (nonatomic) NSInteger userID;
@property (nonatomic) NSInteger phoneID;
@property (nonatomic) NSInteger settingId;

@property (nonatomic, retain) NSMutableDictionary *informationArray;
@property (nonatomic, retain) NSMutableDictionary *settingArray;

@property (nonatomic, retain) NSString *latitudeString;
@property (nonatomic, retain) NSString *longitudeString;
@property (nonatomic, retain) NSString *panicEmgergency;
@property (nonatomic,retain) NSString *apiKey;

@property (nonatomic, readonly) CLLocationCoordinate2D coordinate;
@property (nonatomic, retain) UIAlertView *alertLocation;

@property (nonatomic, retain) Uploader *uploader;
@property (nonatomic) BOOL logout;
@property (nonatomic) BOOL saveContact;
@property (nonatomic) BOOL savePerson;

@property BOOL launchDefault;
//@property (nonatomic, readwrite, retain) YOSSession *session;
@property (nonatomic, readwrite, retain) NSMutableDictionary *oauthResponse;
@property (nonatomic, retain) NSString	*guid;
@property (nonatomic, retain) NSMutableArray *emailList;
//@property (nonatomic, retain) AddContactViewController *addContactViewController;

@property(nonatomic,retain) NSString *userName;
@property(nonatomic,retain) NSString *number;
@property(nonatomic,retain) NSString *defaultGroupId;
@property(nonatomic,retain) NSString *recordDuration;
@property(nonatomic,retain) NSString *schoolId;
@property(nonatomic,retain) NSString *alertId;
@property(nonatomic,retain) NSString *broadcastType;
@property(nonatomic,retain) NSArray  *broadcastIds;
@property(nonatomic,retain) NSArray  *groups;

- (void)showSplash;
- (void)playSound;
- (void)playSound3;
- (void)hiddenSplash;
- (void)showVideo;
- (void)getUserProfile;
- (void)createYahooSession;
- (void)handlePostLaunch;
- (void)abc:(NSString *)str;
- (void)handlePostLaunch;
- (void)showOfflineMode;

- (void)newCheckInternetConnection;
- (void)recieveConnectionResult;
- (void)showLoading;
- (void)getAllGroups;
-(NSString *)GetUUID;
@end

