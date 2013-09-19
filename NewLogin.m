//
//  NewLogin.m
//  SOSBEACON
//
//  Created by bon on 7/27/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#define CK_CheckingIn @"checkingIn"
#import "SettingsMain.h"
#import "NewLogin.h"
#import "SplashView.h"
#import "SettingsAlertView.h"
#import "VideoViewController.h"
#import "ValidateData.h"
#import "SOSBEACONAppDelegate.h"
@implementation NewLogin
@synthesize flagForAlert;
@synthesize strImei;
@synthesize strEmail;
@synthesize strPassword;
@synthesize strSchoolId;
@synthesize restConnection;
@synthesize flagForRequest,flag;
@synthesize token;
@synthesize video;
@synthesize emailTextField;
@synthesize passwordTextField;
@synthesize submit_button;
@synthesize cancel_button;
@synthesize schools = _schools;

-(void)showLoading
{
	submit_button.hidden = YES;
	cancel_button.hidden = YES;
	emailTextField.hidden= YES;
    passwordTextField.hidden = YES;
	actSignup.hidden = NO;
	[actSignup startAnimating];
}

-(IBAction)backgroundTap:(id)sender
{
	[emailTextField resignFirstResponder];
    [passwordTextField resignFirstResponder];
}

-(IBAction)cancelButtonPress:(id)sender
{
	exit(0);
}

-(IBAction)submitButtonPress:(id)sender
{
	//flag = 1;
	strEmail =[[NSString alloc] init];	
    strPassword = [[NSString alloc] init];
    
    if (checkMail(emailTextField.text) && passwordTextField.text.length > 0) {
        submit_button.hidden = YES;
        cancel_button.hidden = YES;
        emailTextField.hidden= YES;
        passwordTextField.hidden = YES;
        [emailTextField  resignFirstResponder];
        [passwordTextField resignFirstResponder];
        
        UIDevice *device = [UIDevice currentDevice];
//        TODO:luan fix
        strImei = [[NSString alloc] initWithString:[appDelegate GetUUID]];
//        strImei = [[NSString alloc] initWithString:[device uniqueIdentifier]];
        strEmail = [[NSString alloc] initWithString:emailTextField.text];
        strPassword = [[NSString alloc] initWithString:passwordTextField.text];
        NSString *Version = [NSString stringWithFormat:@"Version:%@",[[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleVersion"]];
        NSLog(@"Version: %@",Version);
        NSString *model= [[UIDevice currentDevice] model];            
        NSString *systemName= [[UIDevice currentDevice] systemName];     
        NSString *systemVersion =[[UIDevice currentDevice] systemVersion]; 
        NSString *phoneInfor = [NSString stringWithFormat:@"%@;Model:%@;SystemName:%@;Systemversion:%@",Version,model,systemName,systemVersion];
        phoneInfor =[phoneInfor stringByReplacingOccurrencesOfString:@" "withString:@""];
        NSString *request = [NSString stringWithFormat:@"%@/users?_method=get&format=json&email=%@&password=%@&phoneType=%@&phoneInfo=%@&imei=%@&schoolId=%@", SERVER_URL, strEmail, strPassword, @"1", phoneInfor, strImei, @""];
        [TNHRequestHelper sendGetRequest:request tag:666 params:nil receiver:self];
        actSignup.hidden = NO;
        [actSignup startAnimating];
    }
    else {
        NSString *message = nil;
        if (!checkMail(emailTextField.text) && emailTextField.text.length > 0) {
            message = @"Email invalid";
        }
        else {
            message = @"Please enter email and password";
        }
        UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Invalid" message:message delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alertView show];
        [alertView release];
    }
}

-(void)process
{
	if (flag == 1) 	
	{ 	
		//SplashView *splashView = [[SplashView alloc] init];
		//[appDelegate.window addSubview:splashView.view];
			NSLog(@"removefromsuperview flag ==1");
		[self.view removeFromSuperview];
	}
	if (flag == 2) 
	{
		//SplashView *splashView = [[SplashView alloc] init];
		//[appDelegate.window addSubview:splashView.view];
		NSLog(@"dismissModalViewControllerAnimated");
		[self dismissModalViewControllerAnimated:YES];
		appDelegate.tabBarController.selectedIndex= 0;
	}
	if (flag == 3) 	
	{
		//SplashView *splashView = [[SplashView alloc] init];
		//[appDelegate.window addSubview:splashView.view];
		
	//	NSLog(@"removefromsuperview flag == 3");
	//	[self.view removeFromSuperview];
	}
	appDelegate.tabBarController.selectedIndex = 0;
	
	if (contactCount == 0 ) 
	{  
		
		NSInteger aloflag= flagForAlert;
		flagForAlert = 20;
		//NSLog(@"show alert");
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"doYouWantAddcontact",@"")
													  delegate:self cancelButtonTitle:@"Yes"
											 otherButtonTitles:@"Not Now",nil];
		//[alert show];
		if (aloflag == 30) {
			[alert show];
		}else 
		{
			[alert performSelector:@selector(show) withObject:nil afterDelay:1.5];
		}

		
		
		[alert release];
		
	}
	else 
	{
//		NSString *phoneString =[[appDelegate.settingArray objectForKey:ST_EmergencySetting] retain];
//		NSString *password =[[NSString alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER]];
//		if ([phoneString isEqualToString:@"0"] || [password isEqualToString:@"1"]);
//		{
//			
//			appDelegate.tabBarController.selectedIndex =3;
//			
//			
//		}
//		[phoneString release];
//		[password release];
		
	}
	
}


-(void)getdata
{
/*	
	NSString *Version = [NSString stringWithFormat:@"Version:%@",[[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleVersion"]];
	NSLog(@"version: %@",Version);
	NSString *model= [[UIDevice currentDevice] model];            
	NSString *systemName= [[UIDevice currentDevice] systemName];     
	NSString *systemVersion =[[UIDevice currentDevice] systemVersion]; 
	NSString *phoneInfor = [NSString stringWithFormat:@"%@;Model:%@;SystemName:%@;Systemversion:%@",Version,model,systemName,systemVersion];
	phoneInfor =[phoneInfor stringByReplacingOccurrencesOfString:@" "withString:@""];
	NSLog(@"phone infor:%@",phoneInfor);
	[restConnection getPath:[NSString stringWithFormat:@"/phones/%@?format=json&imei=%@&number=%@&phoneInfo=%@",strImei,strImei,strEmail,phoneInfor] withOptions:nil];
	
	emailTextField.hidden = YES;
    passwordTextField.hidden = YES;
	submit_button.hidden = YES;
	cancel_button.hidden = YES;
	actSignup.hidden = NO;
	[actSignup startAnimating];
*/
    
    UIDevice *device = [UIDevice currentDevice];
    //        TODO:luan fix
//    strImei = [[NSString alloc] initWithString:[device uniqueIdentifier]];    
    strImei = [[NSString alloc] initWithString:[appDelegate GetUUID]];

	NSString *Version = [NSString stringWithFormat:@"Version:%@",[[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleVersion"]];
	NSString *model= [[UIDevice currentDevice] model];            
	NSString *systemName= [[UIDevice currentDevice] systemName];     
	NSString *systemVersion =[[UIDevice currentDevice] systemVersion]; 
	NSString *phoneInfor = [NSString stringWithFormat:@"%@;Model:%@;SystemName:%@;Systemversion:%@",Version,model,systemName,systemVersion];
	phoneInfor =[phoneInfor stringByReplacingOccurrencesOfString:@" "withString:@""];
    
    NSString *request = [NSString stringWithFormat:@"%@/users?_method=get&format=json&email=%@&password=%@&phoneType=%@&phoneInfo=%@&imei=%@&schoolId=%@", SERVER_URL, strEmail, strPassword, @"1", phoneInfor, strImei, strSchoolId];
    [TNHRequestHelper sendGetRequest:request withParams:nil receiver:self];    
	emailTextField.hidden = YES;
    passwordTextField.hidden = YES;
	submit_button.hidden = YES;
	cancel_button.hidden = YES;
	actSignup.hidden = NO;
	[actSignup startAnimating];    
}

-(void)timerTick
{
	countTime--;
	if (video.flag == 2) 
	{ 
		//[self.video dismissModalViewControllerAnimated:YES];
		[self.video.view removeFromSuperview];
		[self performSelector:@selector(process) withObject:nil afterDelay:1.0];
		[countDownTimer invalidate];

		
		
	}else 
	if (countTime == 0)
		{
		   [self.video dismissModalViewControllerAnimated:YES];
			[self performSelector:@selector(process) withObject:nil afterDelay:1.0];
			[countDownTimer invalidate];

		}

	
}

- (void)viewDidLoad {
	countActive=0;
    actSignup.hidden=YES;
	restConnection = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	restConnection.delegate = self;
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	[super viewDidLoad];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
}

- (void)viewDidUnload {
	actSignup= nil;
	emailTextField = nil;
    passwordTextField = nil;
	submit_button= nil;
	cancel_button = nil;
    [super viewDidUnload];
}


- (void)dealloc {
	[video release];
	[countDownTimer release];
	[strImei release];
	[strEmail release];
    [strPassword release];
    [strSchoolId release];
	[restConnection release];
    self.schools = nil;
    [super dealloc];
}

- (void)cantConnection:(NSError *)error andRestConnection:(id)connector{
	emailTextField.text = strEmail;
    passwordTextField.text = strPassword;
	emailTextField.hidden = NO;    
    passwordTextField.hidden = NO;
	[actSignup stopAnimating];
	actSignup.hidden = YES;
	cancel_button.hidden = NO;
	submit_button.hidden = NO;
    
	/*
	//NSLog(@"cant connection");
	submit_button.enabled = TRUE;
	cancel_button.enabled = TRUE;
	[appDelegate hiddenSplash];
	[actSignup stopAnimating];
	actSignup.hidden=YES;
	flagForAlert = 10;
	UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Cannot open application"
													    message:@"Can not get data from sever. Please check your Internet connection."
													   delegate:self
											  cancelButtonTitle:@"OK"
											  otherButtonTitles:nil];
	[alertView show];
	[alertView release];	
	 */
}

-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector
{

    NSLog(@"%@",arrayData);
    
    [appDelegate hiddenSplash];

	[actSignup stopAnimating];
	actSignup.hidden = YES;
	NSDictionary *respondArray =[arrayData objectForKey:@"response"];
	//NSLog(@" bon bon :%@", respondArray);
	
	NSInteger responsecode =[[respondArray objectForKey:@"responseCode"] intValue];
	NSString *message = [respondArray objectForKey:@"message"];
	if(responsecode == 1 )
	{ /// 1: thanh cong -> main form
		countActive =0;
		if ([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER]])
		{
			NSMutableDictionary *repondse = [[NSMutableDictionary alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER]];
			if ([[repondse objectForKey:strEmail] isEqualToString:@"6"]) 
			{
				appDelegate.canShowVideo = YES;
			}
			[repondse release];
			[[NSFileManager defaultManager]  removeItemAtPath:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER] error:nil];
		}
		/////
		
		NSString *file=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"newlogin.plist"];
		NSArray *key = [NSArray arrayWithObjects:@"imei",@"number",nil];
		NSArray *obj = [NSArray arrayWithObjects:strImei,strEmail,nil];			
		NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
		[param writeToFile:file atomically:YES];
		[param release];
		appDelegate.email = [strEmail retain];
		[appDelegate.informationArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"number"] forKey:@"number"];	
		[appDelegate.informationArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"name"] forKey:@"username"];
		[appDelegate.informationArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"email"] forKey:@"email"];	
		[appDelegate.informationArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"password"] forKey:@"password"];	
		appDelegate.apiKey = [[arrayData objectForKey:@"response"] objectForKey:@"token"];
		appDelegate.userID = [[[arrayData objectForKey:@"response"] objectForKey:@"user_id"] intValue];
		appDelegate.phoneID =[[[arrayData objectForKey:@"response"] objectForKey:@"id"] intValue];
		
		// save user id
		NSMutableDictionary *dic_userID = [[NSMutableDictionary alloc] init];
		NSString *fileUserId = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"UserID.plist"];
		[dic_userID setObject:[[arrayData objectForKey:@"response"] objectForKey:@"id"] forKey:@"userId"];
		[dic_userID writeToFile:fileUserId atomically:YES];
		[dic_userID release];
		//
		
		// save emergence phone for offline mode
		NSString *emergenceFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"emergencyNumber.plist"];
		NSMutableDictionary *emergencePhone = [[NSMutableDictionary alloc] init];
		[emergencePhone setObject:[[arrayData objectForKey:@"response"] objectForKey:@"emergencyNumber"] forKey:@"emerPhone"];
		[emergencePhone writeToFile:emergenceFile atomically:YES];
		NSLog(@" emergence phone : %@",emergencePhone);
		[emergencePhone release];
		//
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"recordDuration"] forKey:ST_VoiceRecordDuration];

		appDelegate.settingId = [[[arrayData objectForKey:@"response"] objectForKey:@"settingId"] intValue];

		
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"locationId"] forKey:ST_LocationReporting];
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"emergencyNumber"] forKey:ST_EmergencySetting];
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"goodSamaritanRange"] forKey:ST_ReciveRange];
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"goodSamaritanStatus"] forKey:ST_ReceiverSamaritan];
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"incomingGovernmentAlert"] forKey:ST_IncomingGovernment];
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"panicStatus"] forKey:ST_SamaritanStatus];
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"panicRange"] forKey:ST_SamaritanRange];	
		
		
		[appDelegate.settingArray setObject:[[arrayData objectForKey:@"response"] objectForKey:@"alertSendToGroup"] forKey:ST_SendToAlert];
		///
		NSString *defaultFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"defaultGroup.plist"];

		if ([[NSFileManager defaultManager ] fileExistsAtPath:defaultFile])
		{
			
		}
		else
		{
			//NSLog(@"file not exist");
			NSMutableArray *defaultGroupArr = [[NSMutableArray alloc]initWithObjects:@"Family",[appDelegate.settingArray objectForKey:ST_SendToAlert],nil];
			//[defaultGroup addObject:[appDelegate.settingArray objectForKey:ST_SendToAlert]]
			//NSLog(@"array default group : %@",defaultGroupArr);
			[defaultGroupArr writeToFile:defaultFile atomically:YES];
			[defaultGroupArr release];
		}
		
		
		appDelegate.contactCount = [[[arrayData objectForKey:@"response"] objectForKey:@"countContact"] intValue];
		NSString *file1=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"newlogin.plist"];
		NSArray *key1 = [NSArray arrayWithObjects:@"imei",@"number",nil];
		NSArray *obj1 = [NSArray arrayWithObjects:strImei,strEmail,nil];			
		NSDictionary *param1 = [[NSDictionary alloc] initWithObjects:obj1 forKeys:key1];
		[param1 writeToFile:file1 atomically:YES];
		[param1 release];
		NSMutableDictionary *accArray =[[NSMutableDictionary alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER]];
		if (accArray == NULL) {
			accArray =[[NSMutableDictionary alloc] init];
		}
			NSMutableArray *acc = [accArray  objectForKey:strEmail];
			if (acc == nil) 
			{
				acc =[[NSMutableArray alloc] initWithObjects:@"1",@"0",nil];
				[accArray  setObject:acc forKey:strEmail];
				[accArray writeToFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] atomically:YES];
				[accArray  release];
				[acc release];
			}
			else 
			{
				[accArray  release];

			}

		if (flag == 1) 
		{
			[self.view removeFromSuperview];
			appDelegate.flagSetting =10;
		}
		else
		if(flag == 2)
		{
			[self dismissModalViewControllerAnimated:YES];
		}
		else if(flag == 3)
		{
			[self.view removeFromSuperview];
		 	appDelegate.flagSetting =10;

		}
			
	}else
	if (responsecode == 2) 
	{  // 2: error
		countActive =0;
		submit_button.hidden = NO;
		cancel_button.hidden = NO;
		emailTextField.hidden = NO;
        passwordTextField.hidden = NO;
		flagForAlert = 2;
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:@" Error " message:message
													  delegate:self cancelButtonTitle:@"OK"
											 otherButtonTitles:nil];
		[alert show];
		[alert release];
	}
	if (responsecode == 3) 
	{
		countActive =0;
		submit_button.hidden = NO;
		cancel_button.hidden = NO;
		emailTextField.hidden = NO;
        passwordTextField.hidden = NO;
		//// 3: imei va phone moi -> dk moi
		if (flag == 3) 
		{
			NSMutableDictionary *newinfo = [[NSMutableDictionary alloc] init];
		    [newinfo setObject:@"" forKey:@"imei"];
			[newinfo setObject:@"" forKey:@"number"];
			[newinfo writeToFile:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER] atomically:YES];		
			[newinfo autorelease];
			
		}
		else
		{
		flagForAlert = 3;
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:message
													  delegate:self cancelButtonTitle:@"OK"
											 otherButtonTitles:@"Cancel",nil];
		[alert show];
		[alert release];
		}		
		
	}	
	if (responsecode == 4) 
	{ 
		countActive =0;
		submit_button.hidden = NO;
		cancel_button.hidden = NO;
		emailTextField.hidden = NO;
        passwordTextField.hidden = NO;
		// 4: new number + emei exist
		
		//NSLog(@"responsecode =4");		
		flagForAlert =  4;
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:message
													  delegate:self cancelButtonTitle:nil
											 otherButtonTitles:@"Keep Current Account",@"Set Up New Account",@"Cancel",nil];
		[alert  show];
		[alert release];
		
		
	}	
	if (responsecode == 5) 
	{ 
		countActive =0;
		submit_button.hidden = NO;
		cancel_button.hidden = NO;
		emailTextField.hidden = NO;
        passwordTextField.hidden = NO;
		// 5: new imei + exist phone number
		//NSLog(@"responsecode =5");
		if (flag == 3) 
		{
			NSMutableDictionary *newinfo = [[NSMutableDictionary alloc] init];
		    [newinfo setObject:@"" forKey:@"imei"];
			[newinfo setObject:@"" forKey:@"number"];
			[newinfo writeToFile:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER] atomically:YES];		
			[newinfo autorelease];
			//flag = 1;
			
		}else 
		{
		flagForAlert =  5;
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:message
													  delegate:self cancelButtonTitle:nil
											 otherButtonTitles:@"Keep Current Account",@"Set Up New Account",@"Cancel",nil];
		
		[alert show];
		[alert release];
		}
	}	
	if (responsecode == 6) 
	{ 
		submit_button.hidden = YES;
		cancel_button.hidden = YES;
		emailTextField.hidden = YES;
        passwordTextField.hidden = YES;
		
		////
		if([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER]])
		{
			
		}
		else
		{
			[[NSFileManager defaultManager] createFileAtPath:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER] contents:nil attributes:nil];
			NSMutableDictionary *repondse = [[NSMutableDictionary alloc] init];
			[repondse setObject:@"6" forKey:strEmail];
			[repondse writeToFile:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER] atomically:YES];
			[repondse release];
		}
		// 6: account chua active
		/////////////////
		NSString *file=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"newlogin.plist"];
		NSArray *key = [NSArray arrayWithObjects:@"imei",@"number",nil];
		NSArray *obj = [NSArray arrayWithObjects:strImei,strEmail,nil];			
		NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
		[param writeToFile:file atomically:YES];
		[param release];
		/////////////////////
		
		countActive++;
		if (countActive <= 1)
		{
			flagForAlert = 6;
			isDissmiss = NO;
			alert1 =[[UIAlertView alloc] initWithTitle:nil message:message
														  delegate:self cancelButtonTitle:@"Ok"
												 otherButtonTitles:nil];
			[alert1 show];
			
		//	[alert1 dismissWithClickedButtonIndex:0 animated:YES];
			
			[alert1 release]; 
		}
		else
		{
			activMessage =[message  retain];
			flagForAlert = 7;
			UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"TextActive",@"")
														  delegate:self cancelButtonTitle:@"Yes"
												 otherButtonTitles:@"No",nil];
			[alert show];
			[alert release];
		}
		/*
		NSString *fileactive=[NSString stringWithFormat:@"%@/countactive.plist",DOCUMENTS_FOLDER];
		if ([[NSFileManager defaultManager]fileExistsAtPath:fileactive ]) 
		{
			NSMutableDictionary *active =[[NSMutableDictionary alloc] initWithContentsOfFile:fileactive];
			NSString *numberactive = [active objectForKey:strEmail];

			NSLog(@" numberactive : %@",numberactive);
			if ([numberactive length] == 0)
			{
				NSLog(@"alo");
				if (active != nil) {
					[active  release];
					active = nil;
				}
				active =[[NSMutableDictionary alloc] init];
				[active setValue:@"0" forKey:strEmail];
				NSLog(@"write to file");
				NSLog(@" %@",active);
				[active writeToFile:fileactive atomically:NO];
				[active  autorelease];
				flagForAlert = 6;
				NSLog(@" ?");
				UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:message
															  delegate:self cancelButtonTitle:@"Ok"
													 otherButtonTitles:nil];
				[alert show];
				[alert release]; 
				
			}else
			{
				NSLog(@"co ton tai file");
				if (active != nil) 
				{
					[active  release];
					active = nil;
				}
				NSArray *ob =[NSArray arrayWithObjects:@"1",nil];
				NSArray *ke =[NSArray arrayWithObjects:strEmail,nil];
				active =[[NSMutableDictionary alloc] initWithObjects:ob forKeys:ke];		
				[active writeToFile:fileactive atomically:YES];
				[active  release];
				activMessage =[message  retain];
				flagForAlert = 7;
				UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"TextActive",@"")
															  delegate:self cancelButtonTitle:@"Yes"
													 otherButtonTitles:@"No",nil];
				[alert show];
				//[alert performSelector:@selector(show) withObject:nil afterDelay:3.0];
				[alert release];
			}

			
			
		}
		else 
		{
			[[NSFileManager defaultManager] createFileAtPath:fileactive contents:nil attributes:nil];
			NSArray *ob =[NSArray arrayWithObjects:@"0",nil];
			NSArray *ke =[NSArray arrayWithObjects:strEmail,nil];
			NSMutableDictionary *active =[[NSMutableDictionary alloc] initWithObjects:ob forKeys:ke];
			[active writeToFile:fileactive atomically:YES];
			[active  release];
			flagForAlert = 6;
			NSLog(@"khong ton tai file");
			UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:message
														  delegate:self cancelButtonTitle:@"Ok"
												 otherButtonTitles:nil];
			[alert show];
			[alert release];
		}
		 */
		//////
		//NSLog(@"responsecode = 6");
		
		
	}	

}

- (void)alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex
{
	
	if (buttonIndex ==0) 
	{
		if(flagForAlert == 2)// error
		{}
		else
			if(flagForAlert == 3)/// new account
			{
				NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
				NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"NEW",nil];
				NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
				
				[restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
				[key release];
				[obj release];
				[param release];
			}
			else
				if (flagForAlert == 4) //new account
				{
					/*
					 NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
					 NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"NEW",nil];
					 NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
					 
					 [restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
					 */
					
					NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
					NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"UPDATE",nil];
					NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
					[restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
					
					[key release];
					[obj release];
					[param release];
					
				}
				else
					if (flagForAlert == 5)//new account
					{
						/*
						 NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
						 NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"NEW",nil];
						 NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
						 
						 [restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
						 
						 */
						
						NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
						NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"UPDATE",nil];
						NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
						[restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
						
						[key release];
						[obj release];
						[param release];
					}
					else
						if (flagForAlert == 6) //thoat app de active
						{
							isDissmiss = YES;
							actSignup.hidden = NO;
							[actSignup startAnimating];
							[self performSelector:@selector(getdata) withObject:nil afterDelay:3.0];
						}
						else 
							if (flagForAlert == 7 )
							{
								isDissmiss = NO;
								flagForAlert = 6;
								alert1 =[[UIAlertView alloc] initWithTitle:nil message:activMessage
																  delegate:self cancelButtonTitle:@"Ok"
														 otherButtonTitles:nil];
								[alert1 show];
								[alert1 release]; 
								//	alert1 = nil;
								
								[activMessage  release];
								
								
							}
		
							else 
								if (flagForAlert == 10) 
								{
									exit(0);
								}
								else 
									if (flagForAlert == 20) 
									{
										//	appDelegate.flagSetting =2;
										//NSLog(@" alo mot hai ba bon alo");
										appDelegate.tabBarController.selectedIndex = 2;
										
									}else
										if(flagForAlert == 30)	/// xem video
										{
											/*
											video =[[VideoViewController alloc] init];
											video.flag == 1;
											[appDelegate.window addSubview:video.view];
											//[self.tabBarController presentModalViewController:video animated:YES];
											NSLog(@" xe video xong thi quit");
											countTime= 221;
											countDownTimer=[NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(timerTick) userInfo:nil repeats:YES];
											*/
										}
		
		
	}
	else
		if(buttonIndex == 1)
			
		{
			//NSLog(@"buttonindex = 1");
			if (flagForAlert == 4) // user old account (update number)
			{
				
				NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
				NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"NEW",nil];
				NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
				
				[restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
				[key release];
				[obj release];
				[param release];
				
				/*
				 NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
				 NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"UPDATE",nil];
				 NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
				 [restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
				 */
			}
			else
				if (flagForAlert == 5) //  user old account (update imei)
				{
					/*
					 NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
					 NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"UPDATE",nil];
					 NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
					 [restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
					 */
					
					NSArray *key = [[NSArray alloc] initWithObjects:@"imei",@"number",@"phoneType",@"do",nil];
					NSArray *obj = [[NSArray alloc] initWithObjects:strImei,strEmail,@"1",@"NEW",nil];
					NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
					
					[restConnection postPath:[NSString stringWithFormat:@"/phones?format=json"]withOptions:param];
					
					[key release];
					[obj release];
					[param release];
				}
				else 
					if(flagForAlert == 6)
					{
					}
					else 
						if (flagForAlert == 7)
						{
							submit_button.hidden = NO;
							cancel_button.hidden = NO;
							emailTextField.hidden = NO;
                            passwordTextField.hidden = NO;
							emailTextField.text = strEmail;
                            passwordTextField.text = strPassword;
							UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil 
																		   message:NSLocalizedString(@"VerifyPhone",@"")
																		  delegate:nil
																 cancelButtonTitle:@"Ok"
																 otherButtonTitles:nil];
							[alert show];
							[alert release];
						}
						else
							if(flagForAlert == 20)
							{
								
								appDelegate.tabBarController.selectedIndex = 3;
								
							}else
								if(flagForAlert == 30)
								{
									
									[self process];				
								}
			
			
		}
		else 
			if (buttonIndex == 2)
			{
				//NSLog(@"button index = 2");
				
				
			}
			else 
			{
				
			}	
}

- (void) DimisAlertView1
{
	//NSLog(@"dismisalert view");
	if (alert1) 
	{
		if (flagForAlert == 6 && !isDissmiss) 
		{
			//NSLog(@"1");
			[alert1 dismissWithClickedButtonIndex:0 animated:YES];
			//NSLog(@"2");
			[self alertView:alert1 didDismissWithButtonIndex:0];
			//NSLog(@"3");
			alert1 = nil;
		}
	}
	else
	{
		//NSLog(@"alert1== NULL");
	}	 
}

#pragma mark -
#pragma mark - TNH Request Helper Delegate Methods
- (void)requestFinished:(ASIHTTPRequest *)request {
    [appDelegate hiddenSplash];
	[actSignup stopAnimating];
	actSignup.hidden = YES;
    NSString *theJson = [request responseString];
    NSLog(@"the json: %@", theJson);
    SBJsonParser *parser = [[SBJsonParser alloc] init];
    NSDictionary *dict = [[parser objectWithString:theJson] objectForKey:@"response"];
    [parser release];
    NSString *success = [dict objectForKey:@"success"];
	if([success isEqual:@"true"])
	{ /// 1: thanh cong -> main form
		countActive =0;
/*        
		if ([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER]])
		{
			NSMutableDictionary *repondse = [[NSMutableDictionary alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER]];
			if ([[repondse objectForKey:strEmail] isEqualToString:@"6"]) 
			{
				appDelegate.canShowVideo = YES;
			}
			[repondse release];
			[[NSFileManager defaultManager]  removeItemAtPath:[NSString stringWithFormat:@"%@/respond.plist",DOCUMENTS_FOLDER] error:nil];
		}
*/ 
		/////
/*		
		NSString *file=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"newlogin.plist"];
		NSArray *key = [NSArray arrayWithObjects:@"imei",@"number",nil];
		NSArray *obj = [NSArray arrayWithObjects:strImei,strEmail,nil];			
		NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
		[param writeToFile:file atomically:YES];
		[param release];
*/ 
        NSDictionary *user = [dict objectForKey:@"user"];
		appDelegate.number = [user objectForKey:@"number"];
        appDelegate.userName = [user objectForKey:@"name"];
        appDelegate.email = [user objectForKey:@"email"];
        appDelegate.apiKey = [user objectForKey:@"token"];
		appDelegate.userID = [[user objectForKey:@"id"] intValue];
        NSDictionary *school = [dict objectForKey:@"school"];
        self.strSchoolId = [school objectForKey:@"id"];
        appDelegate.schoolId = [NSString stringWithFormat:@"%@", [school objectForKey:@"id"]];
		
		// save user id
		NSMutableDictionary *dic_userID = [[NSMutableDictionary alloc] init];
		NSString *fileUserId = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"UserID.plist"];
		[dic_userID setObject:[[dict objectForKey:@"user"] objectForKey:@"id"] forKey:@"userId"];
		[dic_userID writeToFile:fileUserId atomically:YES];
		[dic_userID release];
		//
/*		
		// save emergence phone for offline mode
		NSString *emergenceFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"emergencyNumber.plist"];
		NSMutableDictionary *emergencePhone = [[NSMutableDictionary alloc] init];
		[emergencePhone setObject:[[arrayData objectForKey:@"response"] objectForKey:@"emergencyNumber"] forKey:@"emerPhone"];
		[emergencePhone writeToFile:emergenceFile atomically:YES];
		NSLog(@" emergence phone : %@",emergencePhone);
		[emergencePhone release];
		//
*/ 
        NSDictionary *settings = [[dict objectForKey:@"user"] objectForKey:@"setting"];
        if ([[settings objectForKey:@"recordDuration"] isEqual:[NSNull null]]) {
            appDelegate.recordDuration = [NSString stringWithString:@"1"];
        }
        else {
            appDelegate.recordDuration = [settings objectForKey:@"recordDuration"];
        }
        if ([[settings objectForKey:@"defaultGroupId"] isEqual:[NSNull null]]) {
            appDelegate.defaultGroupId = [NSString stringWithString:@"0"];
        }
        else {
            appDelegate.defaultGroupId = [settings objectForKey:@"defaultGroupId"];
        }
		///
		NSString *defaultFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"defaultGroup.plist"];
        //NSLog(@"file not exist");
        NSMutableArray *defaultGroupArr = [[NSMutableArray alloc] initWithObjects:appDelegate.defaultGroupId, nil];
        [defaultGroupArr writeToFile:defaultFile atomically:YES];
        [defaultGroupArr release];
		
		NSString *file1=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"newlogin.plist"];
		NSArray *key1 = [NSArray arrayWithObjects:@"schoolId",@"email", @"password",nil];
		NSArray *obj1 = [NSArray arrayWithObjects:strSchoolId, strEmail, strPassword, nil];	
		NSDictionary *param1 = [[NSDictionary alloc] initWithObjects:obj1 forKeys:key1];
		[param1 writeToFile:file1 atomically:YES];
		[param1 release];
		NSMutableDictionary *accArray =[[NSMutableDictionary alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER]];
		if (accArray == NULL) {
			accArray =[[NSMutableDictionary alloc] init];
		}
        NSMutableArray *acc = [accArray  objectForKey:strEmail];
        if (acc == nil) 
        {
            acc =[[NSMutableArray alloc] initWithObjects:@"1",@"0",nil];
            [accArray  setObject:acc forKey:strEmail];
            [accArray writeToFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] atomically:YES];
            [accArray  release];
            [acc release];
        }
        else 
        {
            [accArray  release];
            
        }
        
		if (flag == 1) 
		{
			[self.view removeFromSuperview];
			appDelegate.flagSetting =10;
		}
		else
            if(flag == 2)
            {
                [self dismissModalViewControllerAnimated:YES];
            }
            else if(flag == 3)
            {
                [self.view removeFromSuperview];
                appDelegate.flagSetting =10;
                
            }
        [appDelegate getAllGroups];
	}
    else
    {  // 2: error
        // select school to login
        if (request.tag == 666) {
            self.schools = [dict objectForKey:@"selectSchool"];
            if (_schools.count > 0) {
                UIActionSheet *actionSheet = [[UIActionSheet alloc] init];
                for (int i = 0; i < _schools.count; i++) {
                    NSDictionary *school = [_schools objectAtIndex:i];
                    [actionSheet addButtonWithTitle:[school objectForKey:@"name"]];
                }
                actionSheet.cancelButtonIndex = [actionSheet addButtonWithTitle:@"Cancel"];
                NSLog(@"cancel button index: %d", actionSheet.cancelButtonIndex);
                actionSheet.tag = 200;
                actionSheet.delegate = self;
                [actionSheet showInView:appDelegate.window];
                [actionSheet release];                
                return;                
            }
        }        
        // login failed
        countActive =0;
        submit_button.hidden = NO;
        cancel_button.hidden = NO;
        emailTextField.hidden = NO;
        passwordTextField.hidden = NO;
        flagForAlert = 2;
        NSString *message = [dict objectForKey:@"message"];
        UIAlertView *alert =[[UIAlertView alloc] initWithTitle:@"Error" message:message
                                                      delegate:self cancelButtonTitle:@"OK"
                                             otherButtonTitles:nil];
        [alert show];
        [alert release];
    }    
}

- (void)requestFailed:(ASIHTTPRequest *)request {
	emailTextField.text = strEmail;
    passwordTextField.text = strPassword;
	emailTextField.hidden = NO;    
    passwordTextField.hidden = NO;
	[actSignup stopAnimating];
	actSignup.hidden = YES;
	cancel_button.hidden = NO;
	submit_button.hidden = NO;
    if ([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER]]) {
        NSDictionary *newarray = [NSDictionary dictionaryWithContentsOfFile:[NSString stringWithFormat:@"%@/newlogin.plist",DOCUMENTS_FOLDER]];
        NSLog(@"new array: %@", newarray);
        if (![[newarray objectForKey:@"email"] isEqual:@""] && ![[newarray objectForKey:@""] isEqual:@"password"]) {
            [appDelegate performSelector:@selector(beginOfflineMode)];                
        }
    }
}

#pragma mark -
#pragma mark - Action sheet delegate methods
- (void)actionSheet:(UIActionSheet *)actionSheet didDismissWithButtonIndex:(NSInteger)buttonIndex {
    NSLog(@"Button index: %d", buttonIndex);
    if (buttonIndex == actionSheet.cancelButtonIndex) {
        emailTextField.hidden = NO;
        passwordTextField.hidden = NO;
        cancel_button.hidden = NO;
        submit_button.hidden = NO;   
    }
    else {
        NSDictionary *selectedSchool = [_schools objectAtIndex:buttonIndex];
        NSString *schoolId = [selectedSchool objectForKey:@"id"];
        NSString *Version = [NSString stringWithFormat:@"Version:%@",[[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleVersion"]];
        NSLog(@"Version: %@",Version);
        NSString *model= [[UIDevice currentDevice] model];            
        NSString *systemName= [[UIDevice currentDevice] systemName];     
        NSString *systemVersion =[[UIDevice currentDevice] systemVersion]; 
        NSString *phoneInfor = [NSString stringWithFormat:@"%@;Model:%@;SystemName:%@;Systemversion:%@",Version,model,systemName,systemVersion];
        phoneInfor =[phoneInfor stringByReplacingOccurrencesOfString:@" "withString:@""];
        NSString *request = [NSString stringWithFormat:@"%@/users?_method=get&format=json&email=%@&password=%@&phoneType=%@&phoneInfo=%@&imei=%@&schoolId=%@", SERVER_URL, emailTextField.text, passwordTextField.text, @"1", phoneInfor, strImei, schoolId];
        actSignup.hidden = NO;
        [actSignup startAnimating];
        [TNHRequestHelper sendGetRequest:request tag:667 params:nil receiver:self];        
    }
}

@end
