//
//  SOSBEACONAppDelegate.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 08/06/2010.
//  Copyright CNC 2010. All rights reserved.
//

#import "SOSBEACONAppDelegate.h"
#import "SplashView.h"
#import "HomeView.h"
#import "TermsService.h"
#import "StatusView.h"
#import "Uploader.h"
#import <AVFoundation/AVFoundation.h>
#import "GroupsView.h"
#import "TableGroup.h"
#import "Tracking.h"
//#import "YOSUser.h"
//#import "YOSUserRequest.h"
//#import "ContactListViewController.h"
//#import "AddContactViewController.h"
#import "VideoViewController.h"
#import "NewLogin.h"
#import "JSONKit.h"
#import "OfflineViewController.h"
#import "Crittercism.h"
#import "TestFlight.h"
#define ST_ImageRecordFrequency @"imageRecordFrequency"
#define ST_VoiceRecordDuration @"voiceRecordDuration"
#define ST_LocationReporting @"locationReporting"
#define ST_EmergencySetting @"emergencySetting"
#define ST_SendToAlert @"sendToAlert"
#define ST_IncomingGovernment @"incomingGovernment"
#define ST_SamaritanStatus @"samaritanStatus"
#define ST_SamaritanRange @"samaritanRange"
#define ST_ReceiverSamaritan @"receiverSamaritan"
#define ST_ReciveRange @"receiveRangeSamaritan"
#define CK_CheckingIn @"checkingIn"
#define offline_None 0
#define offline_Loading 1
#define offline_Sms 2

@implementation SOSBEACONAppDelegate

@synthesize window;
@synthesize flagsentalert;
@synthesize viewHome;
@synthesize apiKey,userID,settingId;
@synthesize phoneID;
@synthesize settingArray;
@synthesize panicEmgergency;
@synthesize coordinate,alertLocation,informationArray,latitudeString,longitudeString;
@synthesize webView;
@synthesize tabBarController;
@synthesize statusView;
@synthesize uploader,logout,groupView,locationManager,saveContact,savePerson;
@synthesize flagSetting;
//@synthesize session;
@synthesize launchDefault;
@synthesize oauthResponse;
@synthesize guid;
@synthesize emailList;
//@synthesize addContactViewController;
@synthesize email,contactCount;
@synthesize groupName;
@synthesize flagforGroup;
@synthesize respondcode;
@synthesize canShowVideo;
@synthesize newlogin;
@synthesize number, userName, schoolId;
@synthesize defaultGroupId, recordDuration;
@synthesize broadcastIds, broadcastType;
@synthesize groups, alertId;

//@synthesize internetReach;
//Update location
- (void)showVideo
{
	VideoViewController *video =[[VideoViewController alloc] init];
	[self.tabBarController dismissModalViewControllerAnimated:YES];
	[video  autorelease];
}
- (void)updateLocation{
	[locationManager startUpdatingLocation];
}

//Function delay updated location
- (void)countUpdateLocation {
	[self performSelector:@selector(updateLocation) withObject:nil afterDelay:1];
}

#pragma mark LocationDelegate
- (void)locationManager:(CLLocationManager *)manager didUpdateToLocation:(CLLocation *)newLocation fromLocation:(CLLocation *)oldLocation
{
	[self countUpdateLocation];
	
	NSString *tempLat = [[NSString alloc] initWithFormat:@"%g",newLocation.coordinate.latitude];
	self.latitudeString = tempLat;
	[tempLat release];
	NSString *tempLong = [[NSString alloc] initWithFormat:@"%g",newLocation.coordinate.longitude];
	self.longitudeString = tempLong;
	[tempLong release];
	
}

- (void)locationManager:(CLLocationManager *)manager didFailWithError:(NSError *)error {
	
	[self countUpdateLocation];
}
void uncaughtExceptionHandler(NSException *exception) {
    [Tracking trackException:exception];
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
}

- (void)applicationWillEnterForeground:(UIApplication *)application 
{
	[newlogin DimisAlertView1];
	//	NSLog(@"ok ngon");
}


- (void)applicationWillTerminate:(UIApplication *)application
{
	//[self showLoading];
}

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {  
	//	flagSetting = 1;
	[Crittercism enableWithAppID:@"52a97d0d46b7c22593000006"];
    //testflight
     [TestFlight takeOff:@"5690b79c-ea23-4266-83ce-dd2e578c1d73"];
	UIImageView *imageview = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"splash.png"]];
	imageview.frame = CGRectMake(0, 0, 320, 480);
    if (IS_IPHONE_5) {
        imageview.frame = CGRectMake(0, 0, 320, 568);
        self.window.frame=CGRectMake(0, 0, 320, 568);
    }
	[self.window addSubview:imageview];
	// check netWork Connection
	flagOffline = offline_Loading;
	[self newCheckInternetConnection];
    
    //Init rest for check internet connection
    restForCheckInternet = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
    [restForCheckInternet getPath:@"/images/logo.ico" withOptions:nil];
	
    
	////
    
//    [TestFlight takeOff:@"41231978913cad37d08f8029345791b1_MzkyMzMyMDExLTExLTA3IDAzOjM2OjU1LjE4MDIzMA"];
	
	if (![[NSFileManager defaultManager]  fileExistsAtPath:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] ])
	{
		
		[[NSFileManager defaultManager] createFileAtPath:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] contents:nil attributes:nil];
	}
	
	emailList = [[NSMutableArray alloc] init];
	launchDefault = YES;
	
	NSSetUncaughtExceptionHandler(&uncaughtExceptionHandler);
	[Tracking startTracking];
	locationManager = [[CLLocationManager alloc] init];
	locationManager.delegate =self;
	locationManager.desiredAccuracy = kCLLocationAccuracyBest;
	
	[self countUpdateLocation];

/*	
	informationArray = [[NSMutableDictionary alloc] init]; 
	settingArray = [[NSMutableDictionary alloc] init];
	[settingArray setObject:@"15" forKey:@"imageRecordFrequency"];
	[settingArray setObject:@"1" forKey:@"voiceRecordDuration"];
	[settingArray setObject:@"30" forKey:@"locationReporting"];
	[settingArray setObject:@"0" forKey:@"emergencySetting"];
	[settingArray setObject:@"0" forKey:@"samaritanRange"];
	[settingArray setObject:@"0" forKey:@"incomingGovernment"];
	[settingArray setObject:@"0" forKey:@"samaritanStatus"];
	[settingArray setObject:@"0" forKey:@"receiverSamaritan"];
	[settingArray setObject:@"0" forKey:@"receiveRangeSamaritan"];
	[settingArray setObject:@"0" forKey:@"sendToAlert"];
	[settingArray setObject:@"0" forKey:@"checkingIn"];
*/
//    [settingArray setObject:@"15" forKey:ST_ImageRecordFrequency];
//	[settingArray setObject:@"1" forKey:@"voiceRecordDuration"];
//	[settingArray setObject:@"0" forKey:@"sendToAlert"];

/*	
	if ([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER] ]) 
	{
		
	}
	else
	{
		
		NSString *pass = [[NSString alloc] initWithString:@"1"];
		[[NSFileManager defaultManager] createFileAtPath:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER] contents:nil attributes:nil];
		//NSLog(@" write to file");
		[pass writeToFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER] atomically:YES];
		//[pass writeToFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER] atomically:YES encoding:NSUnicodeStringEncoding error:nil];
		[pass release];
	}
*/	
	[window addSubview:tabBarController.view];
	
	splashView = [[SplashView alloc] init];
	newlogin = [[NewLogin alloc] init];
    if (IS_IPHONE_5) {
        splashView.view.frame=CGRectMake(0, 0, 320, 568);
    }
    //NSLog(@"add subview splash view");
	[window addSubview:splashView.view];
	//[self.tabBarController presentModalViewController:splashView animated:YES];
	//[splashView release];
    
	if ([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER]])
	{
		NSDictionary *newarray = [NSDictionary dictionaryWithContentsOfFile:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER]];
		newlogin.strEmail = [newarray objectForKey:@"email"];
		newlogin.strPassword = [newarray objectForKey:@"password"];
        newlogin.strSchoolId = [newarray objectForKey:@"schoolId"];
        self.schoolId = [NSString stringWithFormat:@"%@", [newarray objectForKey:@"schoolId"]];
		if ([newlogin.strEmail isEqualToString:@""]||[newlogin.strPassword isEqualToString:@""]) 
		{
			newlogin.flag = 1 ;
			newlogin.strEmail = nil;
            newlogin.strPassword = nil;
			[window addSubview:newlogin.view];
			
		}
		else 
		{
			newlogin.flag = 3;
			newlogin.submit_button.hidden = YES;
			newlogin.cancel_button.hidden = YES;
			newlogin.emailTextField.hidden = YES;
            newlogin.passwordTextField.hidden = YES;
			[window addSubview:newlogin.view];
			email = [[NSString  alloc]initWithString:newlogin.strEmail];
			[newlogin getdata];
		}
		
	}
	else
	{
		[[NSFileManager defaultManager] createFileAtPath:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER] contents:nil attributes:nil];
		NSMutableDictionary *newinfo = [[NSMutableDictionary alloc] init];
		[newinfo setObject:@"" forKey:@"email"];
		[newinfo setObject:@"" forKey:@"password"];
        [newinfo setObject:@"" forKey:@"schoolId"];
		[newinfo writeToFile:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER] atomically:YES];
		newlogin.flag = 1;
		termsService = [[TermsService alloc] init] ;
        //TODO:luan fix
		termsService.view.frame = CGRectMake(0, 0, 320, 480);
        if (IS_IPHONE_5) {
            termsService.view.frame = CGRectMake(0, 0, 320, 568);
        }
        termsService.view.frame = self.window.frame;
		[window addSubview:newlogin.view];
		[window addSubview:termsService.view];
		[newinfo release];
        
        //New
        [window bringSubviewToFront:splashView.view];
        [self hiddenSplash];
		
	}

	statusView.frame = CGRectMake(0, -60, 320, 60);
	[window addSubview:statusView];
	uploader = [[Uploader alloc] init];
	[uploader removeAllFileCache]; 
	NSURL *bipURL = [NSURL fileURLWithPath:[[NSBundle mainBundle] pathForResource:@"BIP" ofType:@"mp3"]];
	NSError *error;
	soundBip = [[AVAudioPlayer alloc] initWithContentsOfURL:bipURL error:&error];
	if(error)
	{
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"ERROR" message:@"Can't play sound" delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show];
		[alert release];
	}
	logout = NO;
	saveContact = NO;
	savePerson=NO;
    [window makeKeyAndVisible];
    
	return YES;
}

#pragma mark -
#pragma mark tabBarControllerDelegate

-(BOOL)tabBarController:(UITabBarController *)tabBarControl shouldSelectViewController:(UIViewController *)viewController{
	if (tabBarControl.selectedIndex == 3) {
		if ([tabBarControl.selectedViewController isMemberOfClass:[GroupsView class]]) {
			GroupsView *tbl = (GroupsView *)tabBarControl.selectedViewController;
			if (saveContact) {
				[[[tbl tableGroups] tbGroup] checkEditContact];
				
				saveContact = NO;
			}
			else if (savePerson && saveContact) {
				
				[[[tbl tableGroups] tbGroup] checkEditContact];
				savePerson = NO;
			}
		}
		
	}
	return YES;
}
- (void)tabBarController:(UITabBarController *)tabBarController1 didSelectViewController:(UIViewController *)viewController {
	
	[homNavigationController popToRootViewControllerAnimated:NO];
	/*
	 NewLogin *new = [[NewLogin alloc] init];
	 [self.tabBarController.view addSubview:new.view];
	 */	
}



- (void)dealloc {
	[offlineview release];
	[emailList release];
	[email release];
//	[addContactViewController release];
	[latitudeString release];
	[longitudeString release];
	[soundBip release];
	[uploader release];
	[statusView release];
	[tabBarController release];
	[locationManager release];
	[alertLocation release];
	[webView release];
	[informationArray release];
	[viewHome release];
    [window release];
	[groupView release];
	[homNavigationController release];
    [number release];
    [userName release];
    [restForCheckInternet release];
    self.defaultGroupId = nil;
    self.recordDuration = nil;
    self.schoolId       = nil;
    self.broadcastIds   = nil;
    self.broadcastType  = nil;
    self.groups         = nil;
    self.alertId        = nil;
	if (termsService)
	{
		[termsService release];
	}
    [super dealloc];
}

- (void)playSound {
	soundBip.volume = 1.0;
	[soundBip play];
}

- (void)playSound3 {
	[soundBip play];
	[soundBip performSelector:@selector(play) withObject:nil afterDelay:1];
	[soundBip performSelector:@selector(play) withObject:nil afterDelay:2];
}

/*
- (BOOL)application:(UIApplication *)application handleOpenURL:(NSURL *)url 
{
	launchDefault = NO;
	
	if (!url) { 
		return NO; 
	}
	
	NSArray *pairs = [[url query] componentsSeparatedByString:@"&"];
	NSMutableDictionary *response = [NSMutableDictionary dictionary];
	
	for (NSString *item in pairs) {
		NSArray *fields = [item componentsSeparatedByString:@"="];
		NSString *name = [fields objectAtIndex:0];
		NSString *value = [[fields objectAtIndex:1] stringByReplacingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
		
		[response setObject:value forKey:name];
	}
	
	self.oauthResponse = response;
	
	[self createYahooSession];
	
	return YES;
}


- (void)handlePostLaunch
{
	[self createYahooSession];
}

- (void)createYahooSession
{
	// create session with consumer key, secret and application id
	// set up a new app here: https://developer.yahoo.com/dashboard/createKey.html
	// because the default values here won't work
	
	self.session = [YOSSession sessionWithConsumerKey:@"dj0yJmk9VlY0ZmE2TWFETWJlJmQ9WVdrOWFYQXlWVzUxTjJrbWNHbzlOelF3T0RRNE5qWXkmcz1jb25zdW1lcnNlY3JldCZ4PWM0" 
									andConsumerSecret:@"6bff9fe33e498f037b246cc8d7049ae5a91755f6" 
									 andApplicationId:@"ip2Unu7i"];
	
	
//	
//	 self.session = [YOSSession sessionWithConsumerKey:@"dj0yJmk9WWs1Y05hbVlDT3hNJmQ9WVdrOVpVSnZUVTR3TkRJbWNHbzlNVEUwTmpNMU5ERTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD01Yg" 
//	 andConsumerSecret:@"f07151ee4e9fd53f7f7d1756beb51ca8e32b0790" 
//	 andApplicationId:@"ip2Unu7i"];
//	 
	
	if(self.oauthResponse) {
		NSString *verifier = [self.oauthResponse valueForKey:@"oauth_verifier"];
		[self.session setVerifier:verifier];
	}
	
	BOOL hasSession = [self.session resumeSession];
	
	if(!hasSession) {
		[self.session sendUserToAuthorizationWithCallbackUrl:@"http://sosbeacon.org:8085/web/contacts/oauth?a=1111&b=222"];
	} else {
		[self getUserProfile];
	}
}

- (void)getUserProfile
{
	index = 1;
	// initialize the profile request with our user.
	YOSUserRequest *userRequest = [YOSUserRequest requestWithSession:self.session];
	
	// get the users profile
	[userRequest fetchProfileWithDelegate:self];
}
 
- (void)requestDidFinishLoading:(YOSResponseData *)data
{
	if (index == 1) {
		NSDictionary *userProfile = [[data.responseText objectFromJSONString] objectForKey:@"profile"];
		guid = [[NSString alloc] initWithString:[userProfile objectForKey:@"guid"]];
		//if(userProfile) {
		//		[viewController setUserProfile:userProfile];
		//	}
		YOSUserRequest *userRequest = [YOSUserRequest requestWithSession:self.session];
		
		// get the users profile
		[userRequest fetchContactsWithStart:0 andCount:500 withDelegate:self];
		index = 2;
	}
	else {
		NSMutableArray *contactList = [[[data.responseText objectFromJSONString] objectForKey:@"contacts"] objectForKey:@"contact"];
		for (int i = 0; i < [contactList count]; i++) {
			NSArray *fiels = [[contactList objectAtIndex:i] objectForKey:@"fields"];
			NSMutableDictionary *dic = [[NSMutableDictionary alloc] init];
			BOOL hasEmail = NO;
			for (int j = 0; j< [fiels count]; j ++) {
				NSString *type = [[fiels objectAtIndex:j] objectForKey:@"type"];
				if ([type isEqualToString:@"email"]) {
					[dic setObject:[[fiels objectAtIndex:j] objectForKey:@"value"] forKey:@"email"];
					hasEmail = YES;
				}
				if ([type isEqualToString:@"name"]) {
					NSString *str = [[NSString alloc] initWithFormat:@"%@ %@ %@",[[[fiels objectAtIndex:j] objectForKey:@"value"] objectForKey:@"familyName"],[[[fiels objectAtIndex:j] objectForKey:@"value"] objectForKey:@"middleName"],[[[fiels objectAtIndex:j] objectForKey:@"value"] objectForKey:@"givenName"]];
					[dic setObject:[str stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] forKey:@"name"];
					[str release];
				}
				if ([type isEqualToString:@"phone"]) {
					[dic setObject:[[fiels objectAtIndex:j] objectForKey:@"value"] forKey:@"phone"];
				}
			}
			if ([dic objectForKey:@"phone"] == nil) {
				[dic setObject:@"" forKey:@"phone"];
			}
			if ([dic objectForKey:@"name"] == nil) {
				[dic setObject:@"" forKey:@"name"];
			}
			if (hasEmail) {
				[emailList addObject:dic];
			}
			[dic release];
		}
		ContactListViewController *ymailContactList = [[ContactListViewController alloc] init];
        
        NSMutableArray *contactArray =[[NSMutableArray alloc] initWithArray:emailList];
		//ymailContactList.contactList = [[NSMutableArray alloc] initWithArray:emailList];
        ymailContactList.contactList = contactArray;
		ymailContactList.title = @"Ymail Contacts";
		self.addContactViewController.gettingContact = NO;
		self.addContactViewController.indicator.hidden = YES;
		[self.addContactViewController.navigationController pushViewController:ymailContactList animated:YES];
        //NSLog(@"fix leak yahoo 15.10.2011 ");
        //[ymailContactList.contactList release];
        [contactArray release];
		[ymailContactList release];
		[self.session clearSession];
	}
}

- (void)abc:(NSString *)str {
	NSMutableDictionary *response = [NSMutableDictionary dictionary];
	[response setObject:str forKey:@"oauth_verifier"];
	
	self.oauthResponse = response;
	
	[self createYahooSession];
}
//- (void)logout {
//	[self.session clearSession];
//}
*/ 

#pragma mark -
#pragma mark  Offlinemode

- (void)showOfflineMode
{
    if(flagOffline == offline_Sms) return;
    //NSLog(@"SHOW OFFLINE MODE");    
	flagOffline = offline_Sms;
	if (offlineview == nil) 
	{
		offlineview = [[OfflineViewController alloc] init];
	}
	offlineview.view.frame =CGRectMake(0, 20, 320, 460);
//    TODO: luan fix
    if (IS_IPHONE_5) {
        offlineview.view.frame =CGRectMake(0, 20, 320, 548);
    }
    [viewHome.navigationController popToRootViewControllerAnimated:NO];
//    [viewHome hideAllUIView];
    tabBarController.selectedIndex = 0;
    
	[self.window addSubview:offlineview.view];
    
    //Hide statusView
    [statusView performSelector:@selector(hideStatus) withObject:nil afterDelay:0.5];
}

- (void)showLoading
{
    if(flagOffline == offline_Loading) return;

	flagOffline = offline_Loading;

    //[offlineview.view performSelector:@selector(removeFromSuperview) withObject:nil afterDelay:0.1];

	[self.window addSubview:splashView.view];
    
    //Active timer
	/*[NSTimer scheduledTimerWithTimeInterval:10.0 
                                     target:self 
                                   selector:@selector(recieveConnectionResult) 
                                   userInfo:nil 
                                    repeats:NO];
    */
    
    [self performSelector:@selector(recieveConnectionResult) withObject:nil afterDelay:10.0];
    
    //[[NSRunLoop currentRunLoop] addTimer:countDownTimer forMode:NSRunLoopCommonModes];
}

- (void)newCheckInternetConnection
{
	
	//internetReach = [[Reachability reachabilityForInternetConnection] retain];
    internetReach = [[Reachability reachabilityForInternetConnection] retain];
	[internetReach startNotifier];
}

-(void)dissmissAlert:(UIAlertView *)newAlert
{
	[newAlert dismissWithClickedButtonIndex:10 animated:NO];
}

- (void)recieveConnectionResult
{
	NSLog(@"START RecieveConnectionResult ===");
	NetworkStatus netStatus = [internetReach currentReachabilityStatus];
	if (netStatus == NotReachable)
	{
        NSLog(@"No internet connection!");
        if ([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER]]) {
            NSDictionary *newarray = [NSDictionary dictionaryWithContentsOfFile:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER]];
            NSLog(@"new array: %@", newarray);
            if (![[newarray objectForKey:@"email"] isEqual:@""] && ![[newarray objectForKey:@""] isEqual:@"password"]) {
                isOfflinemode = NO;
                [self performSelector:@selector(beginOfflineMode)];                
            }
        }
//        isOfflinemode = NO;
//        [self performSelector:@selector(beginOfflineMode)];
	}
	else 
	{
            NSLog(@"show loading screen from offline mode");
            
            [offlineview.view removeFromSuperview];
            [splashView performSelector:@selector(removeView) withObject:nil afterDelay:1];
            flagOffline = offline_None;
            isOfflinemode = NO;
    }
	
}

- (void)showSplash
{
	//[tabBarController.view removeFromSuperview];
	
	//[self.window addSubview:splashView.view ];
	[tabBarController.view addSubview:splashView.view];
}

- (void)hiddenSplash {
    [splashView performSelector:@selector(fadeScreen)];
    flagOffline = offline_None;
}

#pragma mark -
#pragma mark alert delegate

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	isShowAlert = NO;

	switch (buttonIndex) {

		case 0:
        {
			[self showOfflineMode];
			break;
        }
		case 1:
        {
			[self showLoading];
            
            /*
            [NSTimer scheduledTimerWithTimeInterval:10.0 
                                             target:self 
                                           selector:@selector(recieveConnectionResult) 
                                           userInfo:nil 
                                            repeats:NO];
            */
            
            [self performSelector:@selector(recieveConnectionResult) withObject:nil afterDelay:10.0];
			break;	
        }
		default:
			
			break;
	}
}

#pragma mark - For Offline Mode
- (void)beginOfflineMode {
    if (!isOfflinemode) {
        
        UIAlertView *alert  = [[UIAlertView alloc] 
                               initWithTitle:@"" 
                               message:@"Cannot get data from server. Switching to offline mode now"
                               delegate:self
                               cancelButtonTitle:@"Ok"
                               otherButtonTitles:@"Cancel",nil];
        [alert show];
        [alert release];
        isOfflinemode = YES;
        isShowAlert = YES;
    }
}

#pragma mark - Other
- (void)doCheckInternetViaRest {
    [restForCheckInternet getPath:@"/images/logo.ico" withOptions:nil];
}

- (void)getAllGroups {    
    NSString *request = [NSString stringWithFormat:@"%@/groups?_method=get&format=json&userId=%d&token=%@&schoolId=%@", SERVER_URL, self.userID, self.apiKey, self.schoolId];
    NSLog(@"request: %@", request);
    [TNHRequestHelper sendGetRequest:request withParams:nil receiver:self];
}

- (void)requestFinished:(ASIHTTPRequest *)request {
    NSString *theJSON = [request responseString];
    SBJsonParser *parer = [[SBJsonParser alloc] init];
    NSDictionary *dict = [[parer objectWithString:theJSON] objectForKey:@"response"];
    [parer release];
    NSString *success = [dict objectForKey:@"success"];
    if ([success isEqual:@"true"]) {
        // get groups done
        NSArray *array = [dict objectForKey:@"groups"];
        NSSortDescriptor *sortByName = [NSSortDescriptor sortDescriptorWithKey:@"name" ascending:YES];
        NSArray *sortDescriptors = [NSArray arrayWithObject:sortByName];
        self.groups = [NSMutableArray arrayWithArray:[array sortedArrayUsingDescriptors:sortDescriptors]];
        NSString *groupsPath = [DOCUMENTS_FOLDER stringByAppendingPathComponent:[NSString stringWithFormat:@"allGroups.plist"]];        
        [self.groups writeToFile:groupsPath atomically:YES];
    }
    else {
        self.groups = nil;
    }
    [[NSNotificationCenter defaultCenter] postNotificationName:@"GetGroupsDidFinish" object:nil];
}

- (void)requestFailed:(ASIHTTPRequest *)request {
    self.groups = nil;
    [self performSelector:@selector(beginOfflineMode)];                    
}
-(NSString *)GetUUID
{

    if ([[UIDevice currentDevice] respondsToSelector:@selector(identifierForVendor)]) {
        // This is will run if it is iOS6
        return [[[UIDevice currentDevice] identifierForVendor] UUIDString];
    } else {
        // This is will run before iOS6 and you can use openUDID or other
        // method to generate an identifier
        CFUUIDRef theUUID = CFUUIDCreate(NULL);
        CFStringRef string = CFUUIDCreateString(NULL, theUUID);
        CFRelease(theUUID);
        return [(NSString *)string autorelease];
    }
}
@end





