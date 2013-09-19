//
//  HomeView.m
//  SOSBEACON
//
//  Created by cncsoft on 7/30/10.

//  Copyright 2010 CNC. All rights reserved.
//

#import <QuartzCore/QuartzCore.h>
#import "HomeView.h"
#import "SOSBEACONAppDelegate.h"
#import "RestConnection.h"
#import "ValidateData.h"
#import "CaptorView.h"
#import "SlideToCancelViewController.h"
#import "CheckingIn.h"
#import "Uploader.h"
#import "StatusView.h"
#import "VideoViewController.h"

@implementation HomeView
@synthesize _scrollView;
@synthesize shortMessageTextView;
@synthesize longMessageTextView;
@synthesize characterRemainLabel;
@synthesize broadcastTypeLabel;
@synthesize broadcastGroupLabel;
@synthesize _previewTable;
@synthesize preview;
@synthesize rest;
@synthesize contacts;

-(void)showvideo
{
	flag =4;
	NSString *pass = [appDelegate.informationArray objectForKey:@"password"];
	NSString *emergency = [appDelegate.settingArray objectForKey:ST_EmergencySetting];
	//[pass length]
//	NSLog(@" pass length ========= : %d",[pass length]);
//	NSLog(@" %@ ,%@",pass,emergency);
	
	if ([[NSFileManager defaultManager]  fileExistsAtPath:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] ]) 
	{
		
		NSMutableDictionary *accArray =[[NSMutableDictionary alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER]];
		NSString *strEmail = appDelegate.email;
		NSMutableArray *acc = [accArray  objectForKey:strEmail];
		if (acc == nil) 
		{
			
			
		}
		else 
		{
			NSInteger active = [[acc objectAtIndex:1] intValue];
			//NSLog(@" active : %d",active);
			if (active == 0 && ([pass length] == 0 ) && [emergency isEqualToString:@"0"]&&( appDelegate.contactCount == 0)&&appDelegate.canShowVideo)
			{
			//	NSLog(@" home view-------------> ");
				appDelegate.canShowVideo = NO;
				[acc replaceObjectAtIndex:1 withObject:@"1"];
				[accArray  setObject:acc forKey:strEmail];
				[accArray writeToFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] atomically:YES];
				
				flagforAlert =1;
				UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"Video",@"")
															  delegate:self cancelButtonTitle:@"Yes"
													 otherButtonTitles:@"Not Now",nil];
				[alert show];
				[alert release];
				
			}
			
			else
			//if ([emergency isEqualToString:@"0"] || [pass length] == 0)
			if ([emergency isEqualToString:@"0"] ||( [pass length] == 0 )||(appDelegate.contactCount == 0))
			{
				appDelegate.flagSetting = 3; 
				appDelegate.tabBarController.selectedIndex = 3;
			//	NSLog(@"sao the nhi lai loi roi a");
			}
			else 
			{
				appDelegate.tabBarController.selectedIndex = 0;

			}

			
		
		}
		
		[accArray release];
	}
}
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	if (flagforAlert ==1)
	{
		if(buttonIndex == 0)
		{
			VideoViewController *video = [[VideoViewController alloc] init];
			[self presentModalViewController:video animated:YES];
            [video release];
			flag = 2;
		}else 
		if(buttonIndex ==1)	
		{
			
			NSString *pass = [appDelegate.informationArray objectForKey:@"password"];
			NSString *emergency = [appDelegate.settingArray objectForKey:ST_EmergencySetting];
				if ([emergency isEqualToString:@"0"] || [pass length] == 0 ||appDelegate.contactCount == 0) 
				{
					appDelegate.flagSetting = 3; 
					appDelegate.tabBarController.selectedIndex = 3;
				}
				else 
			{
				appDelegate.tabBarController.selectedIndex = 0;

			}

			
		}

	}
	else
	if (flagforAlert == 100)
	{
	
		if (buttonIndex == 0)
		{
			/*
			 */
//			[self dismissUIView: self.vwINeedHelpPopUp];
			appDelegate.flagSetting = 100;
			appDelegate.tabBarController.selectedIndex = 3;
		}
		else 
		if(buttonIndex == 1)
		{
			
		}

	}
	

}
- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
	[super didReceiveMemoryWarning];
    
	[self viewDidUnload];
	[self viewDidLoad];
}

#pragma mark -
-(void)timerTick
{
	//NSLog(@"time tick---------------");
	if (appDelegate.flagSetting == 10)
	{
		[countDownTimer invalidate];
		[self showvideo];
		//[self performSelector:@selector(showvideo) withObject:nil afterDelay:2.0];
	}
	//if (flag == 4)[ countDownTimer invalidate];
}

- (void)refreshGroups {
    appDelegate.broadcastType = [NSString stringWithString:@"1"];
    NSArray *ids = [NSArray arrayWithObject:appDelegate.defaultGroupId];
    appDelegate.broadcastIds = ids;
    for (NSDictionary *group in appDelegate.groups) {
        if ([[group objectForKey:@"id"] isEqual:appDelegate.defaultGroupId]) {
            broadcastGroupLabel.text = [group objectForKey:@"name"];
            break;
        }
    }
    if (broadcastGroupLabel.text.length == 0) {
        broadcastGroupLabel.text = @"All Groups";
    }
}

- (void)viewDidLoad {
//	countDownTimer=[NSTimer scheduledTimerWithTimeInterval:2.5 target:self selector:@selector(timerTick) userInfo:nil repeats:YES];
   [ _closeBtn setTitleTextAttributes:[NSDictionary dictionaryWithObjectsAndKeys:[UIColor whiteColor], UITextAttributeTextColor,nil] forState:UIControlStateNormal];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(refreshGroups) name:@"GetGroupsDidFinish" object:nil];
    _scrollView.contentSize = CGSizeMake(320, 800);
	flag =1;
	[super viewDidLoad];
	appDelegate = (SOSBEACONAppDelegate *)[[UIApplication sharedApplication] delegate];
    [appDelegate.window addSubview:preview];
    
    preview.frame = CGRectMake(0, 480, 320, preview.frame.size.height);
    //TODO:luan fix
    if (IS_IPHONE_5) {
       preview.frame = CGRectMake(0, 568, 320, preview.frame.size.height);
    }
	actAlert.hidden=YES;
	rest = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	rest.delegate = self;	
	isSendOK=NO;
}
	
- (void)viewDidUnload 
{

    [self set_scrollView:nil];
    [self setShortMessageTextView:nil];
    [self setLongMessageTextView:nil];
    [self setCharacterRemainLabel:nil];
    [self setBroadcastTypeLabel:nil];
    [self setBroadcastGroupLabel:nil];
    [self set_previewTable:nil];
    [self setPreview:nil];
    [self setCloseBtn:nil];
    [super viewDidUnload];
}

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
	slideToCancel.enabled = YES;
	slideToCancel2.enabled = YES;
	slideToCancel3.enabled=YES;
	appDelegate.uploader.delegate = self;
	if (flag == 1 ) 
		{
		//	[self performSelector:@selector(showvideo) withObject:nil afterDelay:2.5];
		}

	else 
	if (appDelegate.flagSetting == 1) 
	{
		[self showvideo];
	}
	else 
	{
		if (flag ==2) 
		{
			[self showvideo];
		}
	}


}

- (void)dealloc {	
	 [slideToCancel2 release];
	 [slideToCancel3 release];
	 [slideToCancel release];
	 slideToCancel = nil;
	 slideToCancel2 = nil;
	 slideToCancel3=nil;
	 
	[actAlert release];
	[actAlert release];
	[rest release];
    [_scrollView release];
    [shortMessageTextView release];
    [longMessageTextView release];
    [characterRemainLabel release];
    [broadcastTypeLabel release];
    [broadcastGroupLabel release];
    [_previewTable release];
    [preview release];
    self.contacts = nil;
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    [_closeBtn release];
    [super dealloc];
}

- (void) cancelled:(SlideToCancelViewController*)sender {
	if(sender==slideToCancel){
		//NSLog(@"%^$%^ emergency Phone is :%@",[appDelegate.settingArray objectForKey:@"emergencySetting"]);
		if ([[appDelegate.settingArray objectForKey:@"emergencySetting"] isEqualToString:@"0"]) {
			//UIAlertView *alertPhone = [[UIAlertView alloc] initWithTitle:@"Message" message:@"Emergency phone number not yet set for your location" delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
			//[alertPhone show];
			//[alertPhone release];
			[self doEmercgenyCall];
			[slideToCancel loadView];
			slideToCancel.enabled=YES;
			slideToCancel.view.alpha=1.0;
		}
		else {
			slideToCancel3.enabled=NO;
			slideToCancel2.enabled=NO;
			slideToCancel.view.alpha=0.0;
			[self doEmercgenyCall];
			
			/*
			actAlert.hidden=NO;
			[actAlert startAnimating];
			lblSendAlert.text = @"Sending alert to server...";
			loadIndex = 0;
			NSArray *key1 = [NSArray arrayWithObjects:@"phoneid",@"token",@"type",nil];
			NSArray *obj1 = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.phoneID],
							 appDelegate.apiKey,@"1",nil];
			NSDictionary *param1 =[NSDictionary dictionaryWithObjects:obj1 forKeys:key1];
			[rest postPath:[NSString stringWithFormat:@"/alert?latitude=%@&longtitude=%@&format=json",appDelegate.latitudeString,appDelegate.longitudeString] withOptions:param1];
			*/
			
		}
			}
	else if(sender==slideToCancel2){
		//Sending alert to server
		slideToCancel.enabled=NO;
		slideToCancel3.enabled=NO;
		[self doAlert];
	
		/*
		appDelegate.uploader.delegate = self;
		[appDelegate.uploader sendAlert];
		appDelegate.uploader.isAlert=TRUE;
		 */
	}
	
	else if(sender == slideToCancel3) {
				
		slideToCancel.enabled=NO;
		slideToCancel2.enabled=NO;
		[self doCheckIn];
	}
}

-(void)doEmercgenyCall{
	if ([[appDelegate.settingArray objectForKey:@"emergencySetting"] isEqualToString:@"0"]) 
	{
		flagforAlert = 100;
		UIAlertView *alertPhone = [[UIAlertView alloc] initWithTitle:@"Message" message:NSLocalizedString(@"SetEmergency",@"")
							delegate:self cancelButtonTitle:@"Yes"
								otherButtonTitles:@"Not Now",nil];
		[alertPhone show];
		[alertPhone release];
	}
	else {
		//NSLog(@"call");
		actAlert.hidden=NO;
		[actAlert startAnimating];
//		lblSendAlert.text = @"Sending alert to server...";
		loadIndex = 0;
		NSArray *key1 = [NSArray arrayWithObjects:@"phoneId",@"token",@"type",nil];
		NSArray *obj1 = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.phoneID],
						 appDelegate.apiKey,@"1",nil];
		NSDictionary *param1 =[NSDictionary dictionaryWithObjects:obj1 forKeys:key1];
		[rest postPath:[NSString stringWithFormat:@"/alert?latitude=%@&longtitude=%@&format=json",appDelegate.latitudeString,appDelegate.longitudeString] withOptions:param1];
		
		
	//	NSArray *key2 = [[NSArray alloc] initWithObjects:@"token",@"phoneId",@"latitude",@"longtitude",@"type",@"toGroup",]
	}
}

-(void)doAlert{
	isSendOK=NO;
	appDelegate.uploader.delegate = self;
	[appDelegate.uploader sendAlert];
	appDelegate.uploader.isAlert=TRUE;
}
-(void)doCheckIn{
	isSendOK=NO;
	appDelegate.uploader.isAlert=FALSE;
	CheckingIn *viewCheckIn=[[CheckingIn alloc] init];
	[self.navigationController pushViewController:viewCheckIn animated:YES];
	[viewCheckIn release];
}
-(void)doImOkCheckIn{
	isSendOK=YES;
	appDelegate.uploader.delegate = self;
	appDelegate.uploader.isAlert=FALSE;
	[appDelegate.uploader sendImOkAlert];
	
}
#pragma mark -
- (void)uploadFinish
{
	//NSLog(@"up load finish");
	//NSLog(@"up load finish ----------************........");
}

- (void)requestUploadIdFinish:(NSInteger)uploadId 
{
	//NSLog(@"newflag1: %d",newflag1);
	if (newflag1 == 1) 
	{
		//newflag1 =2;
		isSendOK=NO;

		return;
	}
	else
	if (uploadId > 0 && !isSendOK) 
	{
		///
		slideToCancel2.enabled = YES;
		appDelegate.uploader.autoUpload = YES;
		appDelegate.uploader.isSendAlert = YES;
		CaptorView *captor = [[CaptorView alloc] init];
		captor.modalTransitionStyle = UIModalTransitionStyleFlipHorizontal;
		[self presentModalViewController:captor animated:YES];	
		appDelegate.flagsentalert = 1;
		[captor release];
		isSendOK=NO;

	}
}

-(void)dismissUIView:(UIView*)theView{
	[UIView beginAnimations:nil context:NULL];
	theView.frame=CGRectMake(0, 480, theView.frame.size.width, theView.frame.size.height);
	if (IS_IPHONE_5) {
        theView.frame=CGRectMake(0, 568, theView.frame.size.width, theView.frame.size.height);
    }
	
	[UIView commitAnimations];
	[theView performSelector:@selector(removeFromSuperview) withObject:nil afterDelay:0.5];
	
}
-(void)showUIView:(UIView*)theView {
	
	theView.frame=CGRectMake(0, 480, theView.frame.size.width, theView.frame.size.height);
    if (IS_IPHONE_5) {
        theView.frame=CGRectMake(0, 568, theView.frame.size.width, theView.frame.size.height);
    }
	[self.tabBarController.view addSubview:theView];
	[UIView beginAnimations:nil context:NULL];
	theView.frame=CGRectMake(0, 480-theView.frame.size.height, theView.frame.size.width, theView.frame.size.height);
    if (IS_IPHONE_5) {
        theView.frame=CGRectMake(0, 568-theView.frame.size.height, theView.frame.size.width, theView.frame.size.height);
    }
	[UIView commitAnimations];
    
    //check internet connection
    [[[UIApplication sharedApplication] delegate] performSelector:@selector(doCheckInternetViaRest)];
}

#pragma mark ActionSheet
- (void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex{
    if (actionSheet.tag == 1000) {
        if (actionSheet.cancelButtonIndex != buttonIndex) {
            broadcastTypeLabel.text = [actionSheet buttonTitleAtIndex:buttonIndex];
            appDelegate.broadcastType = [NSString stringWithFormat:@"%d", buttonIndex + 1];
        }
        return;
    }
    
    if (actionSheet.tag == 2000) {
        if (actionSheet.cancelButtonIndex != buttonIndex) {
            broadcastGroupLabel.text = [actionSheet buttonTitleAtIndex:buttonIndex];
            if (buttonIndex == 0) {
                NSMutableArray *array = [[NSMutableArray alloc] initWithCapacity:appDelegate.groups.count];
                for (NSDictionary *group in appDelegate.groups) {
                    [array addObject:[group objectForKey:@"id"]];
                }
                appDelegate.broadcastIds = array;
                [array release];
            }
            else {
                NSDictionary *group = [appDelegate.groups objectAtIndex:buttonIndex - 1];
                appDelegate.broadcastIds = [NSArray arrayWithObject:[group objectForKey:@"id"]];
            }
        }
        return;
    }
}

- (void)actionSheetCancel:(UIActionSheet *)actionSheet{
	currentAction=ActionType_None;
}
#pragma mark finishRequest

-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector{
	actAlert.hidden = YES;
	[actAlert stopAnimating];
//	lblSendAlert.text = @"";
	slideToCancel.enabled = YES;
	slideToCancel.view.alpha = 1.0;
	slideToCancel2.enabled=YES;	
	slideToCancel3.enabled=YES;
	//NSLog(@" array data: %@",arrayData);
	if (loadIndex == 0) {
		if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"])
		{	
			//NSLog(@" call roi ma sao no ko call nhi");
			[self callPanic];
			//NSLog(@"ngon");
			
		}else {
			UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
																message:@"Error with alert data"
															   delegate:nil
													  cancelButtonTitle:@"Ok"
													  otherButtonTitles:nil];
			[alertView show];
			[alertView release];
		}
	}

}

// function error connection
#pragma mark connectionFail

-(void)cantConnection:(NSError *)error andRestConnection:(id)connector{
	alertView();
	[actAlert stopAnimating];
//	lblSendAlert.text = @"";
	actAlert.hidden=YES;	
	slideToCancel.view.alpha = 1.0;
	slideToCancel.enabled = YES;
	slideToCancel2.enabled = YES;
	slideToCancel3.enabled=YES;
}

#pragma mark -
#pragma mark - Action methods
- (IBAction)backgroundTapped:(id)sender {
    [longMessageTextView resignFirstResponder];
    [shortMessageTextView resignFirstResponder];
}

- (IBAction)chooseBroadcastType:(id)sender {
    [self backgroundTapped:nil];
    UIActionSheet *actionSheet = [[UIActionSheet alloc] initWithTitle:@"" delegate:self cancelButtonTitle:@"Cancel" destructiveButtonTitle:nil otherButtonTitles:@"School Notice", @"Emergency Alert", nil];
    actionSheet.tag = 1000;
    [actionSheet showFromTabBar:self.tabBarController.tabBar];
    [actionSheet release];
}

- (IBAction)chooseBroadCastGroup:(id)sender {
    [self backgroundTapped:nil];
    UIActionSheet *actionSheet = [[UIActionSheet alloc] init];
    for (int i = 0; i < appDelegate.groups.count; i++) {
        NSDictionary *group = [appDelegate.groups objectAtIndex:i];
        [actionSheet addButtonWithTitle:[group objectForKey:@"name"]];
    }
    [actionSheet addButtonWithTitle:@"All Groups"];    
    actionSheet.cancelButtonIndex = [actionSheet addButtonWithTitle:@"Cancel"];
    actionSheet.tag = 2000;
    actionSheet.delegate = self;
    [actionSheet showFromTabBar:self.tabBarController.tabBar];
    [actionSheet release];
}

- (IBAction)previewBroadcast:(id)sender {
    [self backgroundTapped:nil];
    [actAlert startAnimating];
    actAlert.hidden = NO;
    if ([broadcastGroupLabel.text isEqualToString:@"All Groups"]) {
        NSMutableArray *array = [[NSMutableArray alloc] initWithCapacity:appDelegate.groups.count];
        for (NSDictionary *group in appDelegate.groups) {
            [array addObject:[group objectForKey:@"id"]];
        }
        appDelegate.broadcastIds = array;
        [array release];
    }
    NSMutableString *request = [NSMutableString stringWithFormat:@"%@/alert?_method=get&format=json&userId=%@&token=%@&schoolId=%@", SERVER_URL, [NSString stringWithFormat:@"%d", appDelegate.userID], appDelegate.apiKey, appDelegate.schoolId];
    for(int i = 0; i < [appDelegate.broadcastIds count]; i++){
        [request appendString:[NSString stringWithFormat:@"&toGroupIds[%d]=%@", i, [appDelegate.broadcastIds objectAtIndex:i]]];
    }
    NSLog(@"request: %@", request);
    [TNHRequestHelper sendGetRequest:request withParams:nil receiver:self];
}

- (IBAction)sendBroadcast:(id)sender {
    [self backgroundTapped:nil];
    if (shortMessageTextView.text.length == 0) {
        UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Invalid" message:@"Short message required." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alertView show];
        [alertView release];        
    }
    else {
        UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:nil message:@"Do you want to send audio and images with this broadcast?" delegate:self cancelButtonTitle:@"Yes" otherButtonTitles:@"No", nil];
        alertView.tag = 111;
        [alertView show];
        [alertView release];        
    }
}

- (IBAction)cancelBroadcast:(id)sender {
    [self backgroundTapped:nil];
    broadcastTypeLabel.text = @"School Notice";
    appDelegate.broadcastType = [NSString stringWithString:@"1"];    
    for (NSDictionary *group in appDelegate.groups) {        
        if ([[group objectForKey:@"id"] isEqual:appDelegate.defaultGroupId]) {
            broadcastGroupLabel.text = [group objectForKey:@"name"];
            appDelegate.broadcastIds = [NSArray arrayWithObject:[group objectForKey:@"id"]];
            break;
        }
    }    
    longMessageTextView.text = @"";
    shortMessageTextView.text = @"";
    sleep(0.2);
   	[UIView beginAnimations:nil context:nil];
	[UIView setAnimationDuration:0.5];
    [_scrollView setContentOffset:CGPointMake(1, 1)];    
	[UIView commitAnimations];     
}

- (IBAction)closePreview:(id)sender {
    [UIView beginAnimations:nil context:NULL];
    [UIView setAnimationDuration:0.3];        
    preview.frame = CGRectMake(0, 480, 320, preview.frame.size.height);
//TODO: luan fix
    if (IS_IPHONE_5) {
         preview.frame = CGRectMake(0, 568, 320, preview.frame.size.height);
    }
    [UIView commitAnimations];
}

#pragma mark -
#pragma mark - UI Text View Delegate Methods
- (void)textViewDidBeginEditing:(UITextView *)textView {    
   	[UIView beginAnimations:nil context:nil];
	[UIView setAnimationDuration:0.5];
    float y = 255;
    if (textView.tag == 2)
        y = 380;
	[_scrollView setContentOffset:CGPointMake( 0, y)];
	[UIView commitAnimations]; 
}

- (BOOL)textView:(UITextView *)textView shouldChangeTextInRange:(NSRange)range replacementText:(NSString *)text {
    if (textView.tag == 2) {
        return YES;
    }
    else {
        NSString* proposedString = [textView.text stringByReplacingCharactersInRange:range withString:text];
        if ([proposedString length] <= 75) {
            int remain = 75 - proposedString.length;
            NSString *counter = nil;
            if (remain > 0) {
                counter = [NSString stringWithFormat:@"(remain %02d characters)", remain];
            }
            else {
                counter = [NSString stringWithFormat:@"(remain %d characters)", remain];
            }
            characterRemainLabel.text = counter;
            return YES;
        }
    }
    return NO;
}

#pragma mark -
#pragma mark - UI Alert View delegate methods
- (void)alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex {    
    if (buttonIndex == 0) {
        attachMedia = YES;
    }
    [actAlert startAnimating];
    actAlert.hidden = NO;
    if ([broadcastGroupLabel.text isEqualToString:@"All Groups"]) {
        NSMutableArray *array = [[NSMutableArray alloc] initWithCapacity:appDelegate.groups.count];
        for (NSDictionary *group in appDelegate.groups) {
            [array addObject:[group objectForKey:@"id"]];
        }
        appDelegate.broadcastIds = array;
        [array release];
    }    
    NSString *request = [NSString stringWithFormat:@"%@/alert?_method=post&format=json", SERVER_URL];
    NSArray *keys = [NSArray arrayWithObjects:@"userId", @"schoolId", @"token", @"type", @"shortMessage", @"longMessage", @"latitude", @"longitude", nil];
    NSArray *objs = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d", appDelegate.userID], appDelegate.schoolId, appDelegate.apiKey, appDelegate.broadcastType, shortMessageTextView.text, longMessageTextView.text, appDelegate.latitudeString, appDelegate.longitudeString, nil];
    NSDictionary *params = [NSDictionary dictionaryWithObjects:objs forKeys:keys];
    NSLog(@"params: %@", params);
    NSString *URLString = [request stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIFormDataRequest *formDataRequest = [ASIFormDataRequest requestWithURL:URL];
    formDataRequest.tag = 1;
    [formDataRequest setRequestMethod:@"POST"];
    for (NSString *key in [params allKeys]) {
        [formDataRequest setPostValue:[params objectForKey:key] forKey:key];
    }
    for(int i = 0; i < [appDelegate.broadcastIds count]; i++){
        [formDataRequest setPostValue:[appDelegate.broadcastIds objectAtIndex:i]  forKey:[NSString stringWithFormat:@"toGroupIds[%d]", i]];
    }
    formDataRequest.delegate = self;
    [formDataRequest startAsynchronous];    
}

#pragma mark -
#pragma mark - ASI HTTP Request Delegate Methods
- (void)requestFinished:(ASIHTTPRequest *)request {
    [actAlert stopAnimating];
    actAlert.hidden = YES;    
    NSString *theJSON = [request responseString];
    NSLog(@"the Json: %@", theJSON);
    SBJsonParser *parer = [[SBJsonParser alloc] init];
    NSDictionary *dict = [[parer objectWithString:theJSON] objectForKey:@"response"];
    [parer release];
    NSString *success = [dict objectForKey:@"success"];
    if (request.tag == 1) {
        appDelegate.alertId = nil;
        if (attachMedia) {
            attachMedia = NO;            
            if ([success isEqual:@"true"]) {
                appDelegate.alertId = [dict objectForKey:@"alertId"];
                appDelegate.uploader.isAlert = YES;        
                appDelegate.uploader.delegate = self;
                appDelegate.uploader.autoUpload = YES;
                CaptorView *captor = [[CaptorView alloc] init] ;
                captor.modalTransitionStyle = UIModalTransitionStyleFlipHorizontal;
                [self presentModalViewController:captor animated:YES];                    
            }
            else {
                NSString *message = [dict objectForKey:@"message"];
                UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error" message:message delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
                [alertView show];
                [alertView release];                    
            }           
        }
        else {
            NSString *message = [dict objectForKey:@"message"];
            UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Send Broadcast" message:message delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
            [alertView show];
            [alertView release];
        }
    }
    else {
        if ([success isEqual:@"true"]) {
            NSArray *array = [dict objectForKey:@"contacts"];
            NSSortDescriptor *sortByName = [NSSortDescriptor sortDescriptorWithKey:@"name" ascending:YES];
            NSArray *sortDescriptors = [NSArray arrayWithObject:sortByName];
            self.contacts = [NSMutableArray arrayWithArray:[array sortedArrayUsingDescriptors:sortDescriptors]];
            [_previewTable reloadData];
            [UIView beginAnimations:nil context:NULL];
            [UIView setAnimationDuration:0.3];    
            preview.frame = CGRectMake(0, 20, 320, preview.frame.size.height);
            [UIView commitAnimations];
        }            
        else {
            NSString *message = [dict objectForKey:@"message"];
            UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error" message:message delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
            [alertView show];
            [alertView release];                
        }        
    }
}

- (void)requestFailed:(ASIHTTPRequest *)request {
    [actAlert stopAnimating];
    actAlert.hidden = YES;
    [appDelegate performSelector:@selector(beginOfflineMode)];                    
}

#pragma mark -
#pragma mark - UI Scroll View Delegate Methods
- (void)scrollViewDidScroll:(UIScrollView *)scrollView {
    [self backgroundTapped:nil];
}

#pragma mark -
#pragma mark - Table view data source methods
- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return [contacts count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
	static NSString *CellIdentifier = @"Cell";
	UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
	
	if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:CellIdentifier] autorelease];
        cell.textLabel.textColor = [UIColor whiteColor];
        cell.detailTextLabel.textColor = [UIColor whiteColor];
        cell.detailTextLabel.numberOfLines = 2;
	}
    NSDictionary *contact = [contacts objectAtIndex:indexPath.row];
    cell.textLabel.text = [contact objectForKey:@"name"];
    NSMutableString *detail = [NSMutableString stringWithFormat:@"Email: %@\n", [contact objectForKey:@"email"]];
    NSLog(@"%@", [contact objectForKey:@"textphone"]);
    if ([[contact objectForKey:@"textphone"] isEqual:[NSNull null]]) {
        [detail appendString:@"Phone number: no number"];
    }
    else {
        [detail appendFormat:@"Phone number: %@", [contact objectForKey:@"textphone"]];
    }
    cell.detailTextLabel.text = detail;
    cell.backgroundColor=[UIColor clearColor];
    return cell;
}

@end
