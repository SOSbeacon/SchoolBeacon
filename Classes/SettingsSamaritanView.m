//
//  SettingsSamaritanView.m
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/18/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import "SettingsSamaritanView.h"
#import "ValidateData.h"
@implementation SettingsSamaritanView
@synthesize btnRangeSamaritan,receiveRangeStatus,receiverSamaritan,samaritanStatus,lblSamaritanRange,receiveRange,btnSave,rest,actSetting;
@synthesize scoll;
// The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
		btnSave=[[UIBarButtonItem alloc] initWithTitle:@"Save" style:UIBarButtonItemStyleBordered target:self action:@selector(SaveSetting)];
		self.navigationItem.rightBarButtonItem = btnSave;
    }
    return self;
}



// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	self.scoll.contentSize = CGSizeMake(320, 800);
	self.title=@"Good Samaritan";
	rest = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	rest.delegate =self;	
	//set default value for recevieRange
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	if ([[appDelegate.settingArray objectForKey:ST_ReciveRange] intValue] == 1) {
		selectReciveRange = @"1";
	}
	else if([[appDelegate.settingArray objectForKey:ST_ReciveRange] intValue] == 3) {
		selectReciveRange = @"3";
	}
	else if([[appDelegate.settingArray objectForKey:ST_ReciveRange] intValue] == 5) {
		selectReciveRange = @"5";
	}
	else if([[appDelegate.settingArray objectForKey:ST_ReciveRange] intValue] == 10) {
		selectReciveRange = @"10";
	}
	else if([[appDelegate.settingArray objectForKey:ST_ReciveRange] intValue] == 20) {
		selectReciveRange = @"20";
	}
	else {
		selectReciveRange = @"0";
	}
	
	if ([[appDelegate.settingArray objectForKey:ST_SamaritanRange] intValue] == 1) {
		selectSamaritanRange = @"1";
	}
	else if([[appDelegate.settingArray objectForKey:ST_SamaritanRange] intValue] == 3) {
		selectSamaritanRange = @"3";
	}
	else if([[appDelegate.settingArray objectForKey:ST_SamaritanRange] intValue] == 5) {
		selectSamaritanRange = @"5";
	}
	else if([[appDelegate.settingArray objectForKey:ST_SamaritanRange] intValue] == 10) {
		selectSamaritanRange = @"10";
	}
	else if([[appDelegate.settingArray objectForKey:ST_SamaritanRange] intValue] == 20) {
		selectSamaritanRange = @"20";
	}
	else {
		selectSamaritanRange = @"0";
	}
	
	lblSamaritanRange.text =[NSString stringWithFormat:@"%@ Km",[appDelegate.settingArray objectForKey:ST_SamaritanRange]];
	receiveRange.text =[NSString stringWithFormat:@"%@ Km",[appDelegate.settingArray objectForKey:ST_ReciveRange]];
	
	
	//getreceiversamaritan
	if (![[appDelegate.settingArray objectForKey:ST_ReceiverSamaritan] isEqual:[NSNull null]]) {
		if ([[appDelegate.settingArray objectForKey:ST_ReceiverSamaritan] intValue] == 0) {
			receiveRangeStatus.enabled = FALSE;
			[receiverSamaritan setOn:NO];
		}
		else {
			receiveRangeStatus.enabled = TRUE;
			[receiverSamaritan setOn:YES];
			
		}
	}
	
	//get samaritanstatus
	if (![[appDelegate.settingArray objectForKey:ST_SamaritanStatus] isEqual:[NSNull null]]){
		if([[appDelegate.settingArray objectForKey:ST_SamaritanStatus] intValue] == 0)
		{
			btnRangeSamaritan.enabled = FALSE;
			[samaritanStatus setOn:NO];
			[NSObject cancelPreviousPerformRequestsWithTarget:self selector:@selector(sendSamaristanAuto) object:nil];
		}
		else {
			btnRangeSamaritan.enabled = TRUE;
			[samaritanStatus setOn:YES];
			[self delayAlert];
		}
	}
	
    [super viewDidLoad];
}


/*
// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations.
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}
*/

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc. that aren't in use.
}

- (void)viewDidUnload {
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


- (void)dealloc {
	rest.delegate = nil;
	[rest release];
	[scoll release];
	[actSetting release];
	[lblSamaritanRange release];
	[receiveRange release];
	[btnRangeSamaritan release];
	[receiveRangeStatus release];
	[receiverSamaritan  release];
	[samaritanStatus release];
    [super dealloc];
}

-(void)save
{
	//NSLog(@"SaveSetting",nil);
	//save array;
	[appDelegate.settingArray setObject:selectReciveRange forKey:ST_ReciveRange];
	[appDelegate.settingArray setObject:selectSamaritanRange forKey:ST_SamaritanRange];
	if (samaritanStatus.on) {
		[appDelegate.settingArray setObject:[NSString stringWithFormat:@"%d",1] forKey:ST_SamaritanStatus] ;
	}
	else {
		[appDelegate.settingArray setObject:[NSString stringWithFormat:@"%d",0] forKey:ST_SamaritanStatus];
		
	}
	
	if (receiverSamaritan.on) {
		[appDelegate.settingArray setObject:[NSString stringWithFormat:@"%d",1] forKey:ST_ReceiverSamaritan] ;
	}
	else {
		[appDelegate.settingArray setObject:[NSString stringWithFormat:@"%d",0] forKey:ST_ReceiverSamaritan];
		
	}
	
	/*
	 NSArray *key1 = [NSArray arrayWithObjects:@"phoneId",@"token",@"goodSamaritanRange",@"panicStatus",@"goodSamaritanStatus",@"panicRange",nil];
	 NSArray *obj1 = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.phoneID],
	 appDelegate.apiKey,
	 [appDelegate.settingArray objectForKey:ST_ReciveRange],
	 [appDelegate.settingArray objectForKey:ST_SamaritanStatus],
	 [appDelegate.settingArray objectForKey:ST_ReceiverSamaritan],
	 [appDelegate.settingArray objectForKey:ST_SamaritanRange],nil];
	 NSDictionary *param1 =[NSDictionary dictionaryWithObjects:obj1 forKeys:key1];
	 [rest putPath:[NSString stringWithFormat:@"/setting/%d?format=json",appDelegate.settingId] withOptions:param1];
	 */
	NSArray *key1 = [NSArray arrayWithObjects:@"id",@"phoneId",@"token",@"recordDuration",@"emergencyNumber",@"alertSendToGroup",@"goodSamaritanStatus",@"goodSamaritanRange",@"panicStatus",@"panicRange",@"incomingGovernmentAlert",nil];
	NSArray *obj1 = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.settingId],
					 [NSString stringWithFormat:@"%d",appDelegate.phoneID],
					 appDelegate.apiKey,
					 [appDelegate.settingArray objectForKey:ST_VoiceRecordDuration],
					 [appDelegate.settingArray objectForKey:ST_EmergencySetting],
					 [appDelegate.settingArray objectForKey:ST_SendToAlert],
					 [appDelegate.settingArray objectForKey:ST_ReceiverSamaritan],
					 [appDelegate.settingArray objectForKey:ST_ReciveRange],
					 [appDelegate.settingArray objectForKey:ST_SamaritanStatus],
					 [appDelegate.settingArray objectForKey:ST_SamaritanRange],
					 [appDelegate.settingArray  objectForKey:ST_IncomingGovernment],
					 nil];
	NSDictionary *param1 =[NSDictionary dictionaryWithObjects:obj1 forKeys:key1];
	[rest putPath:[NSString stringWithFormat:@"/setting/%d?format=json",appDelegate.settingId] withOptions:param1];
	
	
	
}
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	if (buttonIndex == 0) 
	{
		if (flagalert ==1)
		{
			flagalert = 2;
			[self save];
		}
	}
	else {
		
	}
	
}
-(IBAction)SaveSetting
{
	flagalert =1;
	UIAlertView *alert =[ [UIAlertView alloc] initWithTitle:nil 
													message:NSLocalizedString(@"SaveChange",@"")
												   delegate:self
										  cancelButtonTitle:@"Yes" otherButtonTitles:@"No",nil];
	[alert show];
	[alert release];
	/*
	NSLog(@"SaveSetting",nil);
	//save array;
	[appDelegate.settingArray setObject:selectReciveRange forKey:ST_ReciveRange];
	[appDelegate.settingArray setObject:selectSamaritanRange forKey:ST_SamaritanRange];
	if (samaritanStatus.on) {
		[appDelegate.settingArray setObject:[NSString stringWithFormat:@"%d",1] forKey:ST_SamaritanStatus] ;
	}
	else {
		[appDelegate.settingArray setObject:[NSString stringWithFormat:@"%d",0] forKey:ST_SamaritanStatus];
		
	}
	
	if (receiverSamaritan.on) {
		[appDelegate.settingArray setObject:[NSString stringWithFormat:@"%d",1] forKey:ST_ReceiverSamaritan] ;
	}
	else {
		[appDelegate.settingArray setObject:[NSString stringWithFormat:@"%d",0] forKey:ST_ReceiverSamaritan];
		
	}
	
	
	NSArray *key1 = [NSArray arrayWithObjects:@"id",@"phoneId",@"token",@"recordDuration",@"emergencyNumber",@"alertSendToGroup",@"goodSamaritanStatus",@"goodSamaritanRange",@"panicStatus",@"panicRange",@"incomingGovernmentAlert",nil];
	NSArray *obj1 = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.settingId],
					 [NSString stringWithFormat:@"%d",appDelegate.phoneID],
					 appDelegate.apiKey,
					 [appDelegate.settingArray objectForKey:ST_VoiceRecordDuration],
					 [appDelegate.settingArray objectForKey:ST_EmergencySetting],
					 [appDelegate.settingArray objectForKey:ST_SendToAlert],
					 [appDelegate.settingArray objectForKey:ST_ReceiverSamaritan],
					 [appDelegate.settingArray objectForKey:ST_ReciveRange],
					 [appDelegate.settingArray objectForKey:ST_SamaritanStatus],
					 [appDelegate.settingArray objectForKey:ST_SamaritanRange],
					 [appDelegate.settingArray  objectForKey:ST_IncomingGovernment],
					 nil];
	NSDictionary *param1 =[NSDictionary dictionaryWithObjects:obj1 forKeys:key1];
	[rest putPath:[NSString stringWithFormat:@"/setting/%d?format=json",appDelegate.settingId] withOptions:param1];
	*/
	
}


- (IBAction)SamaritanSatusSwitch:(id)sender{
	isEdit = YES;
	NSString *status;
	if (samaritanStatus.on) {
		//status = @"1";		
		btnRangeSamaritan.enabled = TRUE;
		
	}
	else {
		btnRangeSamaritan.enabled = FALSE;
		//status = @"0";
	}
}

- (IBAction)ReceiverSamaritanSwitch:(id)sender{
	isEdit = YES;
	NSString *receiver;
	if (receiverSamaritan.on) {
		//receiver = @"1";
		receiveRangeStatus.enabled =TRUE;	
		
	}
	else {
		//receiver = @"0";
		receiveRangeStatus.enabled = FALSE;
		
	}
}
#pragma mark -
#pragma mark delayAlert

- (void)delayAlert{
	
	[NSObject cancelPreviousPerformRequestsWithTarget:self selector:@selector(sendSamaristanAuto) object:nil];
	[self performSelector:@selector(sendSamaristanAuto) withObject:nil afterDelay:1800]; 
}

#pragma mark UIActionSheetDelegate

- (void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex
{
	//isEdit = YES;
	
	if(editIndex==5) {
			switch (buttonIndex) {
				case 0:
					selectSamaritanRange=@"1";
					lblSamaritanRange.text = @"1 Km";
					break;
				case 1:
					selectSamaritanRange=@"3";
					lblSamaritanRange.text = @"3 Km";
					break;
				case 2:
					selectSamaritanRange=@"5";
					lblSamaritanRange.text = @"5 Km";
					break;
				case 3:
					selectSamaritanRange=@"10";
					lblSamaritanRange.text = @"10 Km";
					break;
				case 4:
					selectSamaritanRange=@"20";
					lblSamaritanRange.text = @"20 Km";
					break;
				default:
					break;
			}
		}
		
		else if(editIndex==6) {
			switch (buttonIndex) {
				case 0:
					selectReciveRange=@"1";
					
					receiveRange.text = @"1 Km";
					break;
				case 1:
					selectReciveRange=@"3";
					receiveRange.text = @"3 Km";
					break;
				case 2:
					selectReciveRange=@"5";
					receiveRange.text = @"5 Km";
					break;
				case 3:
					selectReciveRange=@"10";
					receiveRange.text = @"10 Km";
					break;
				case 4:
					selectReciveRange=@"20";
					receiveRange.text = @"20 Km";
					break;
				default:
					break;
			}
		}
		
	
}
#pragma mark -
#pragma mark delayUIalert

- (void) DimisAlertView:(UIAlertView*)alertView {
	[alertView dismissWithClickedButtonIndex:0 animated:TRUE];
}
- (IBAction)rangeSatus:(id)sender {
	editIndex=5;
	UIActionSheet *actionSheet4 = [[UIActionSheet alloc] initWithTitle:@""
															  delegate:self 
													 cancelButtonTitle:@"Cancel"
												destructiveButtonTitle:nil 
													 otherButtonTitles:@"1 Km",@"3 Km",@"5 Km",@"10 Km",@"20 Km",nil];
	actionSheet4.actionSheetStyle = UIActionSheetStyleBlackOpaque;
	[actionSheet4 showInView:appDelegate.window];
	[actionSheet4 release];
}	

- (IBAction)ReceiveRangeSamaritan:(id)sender {
	editIndex=6;
	UIActionSheet *actionSheet5 = [[UIActionSheet alloc] initWithTitle:@""
															  delegate:self 
													 cancelButtonTitle:@"Cancel"
												destructiveButtonTitle:nil 
													 otherButtonTitles:@"1 Km",@"3 Km",@"5 Km",@"10 Km",@"20 Km",nil];
	actionSheet5.actionSheetStyle = UIActionSheetStyleBlackOpaque;
	[actionSheet5 showInView:appDelegate.window];
	[actionSheet5 release];
}
-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector{
	[actSetting stopAnimating];
	actSetting.hidden = YES;
	btnSave.enabled = TRUE;
	isEdit = NO;

	if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"]){
		

			btnSave.enabled = TRUE;
			UIAlertView *alertView= [[UIAlertView alloc] initWithTitle:@""
															   message:@"Settings saved successfully" 
															  delegate:nil 
													 cancelButtonTitle:@"Ok" 
													 otherButtonTitles:nil] ;
			[alertView show];
			[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
			[alertView release];
			save = NO;
			if(samaritanStatus.on){
				[self delayAlert];
			}else {
				[NSObject cancelPreviousPerformRequestsWithTarget:self selector:@selector(sendSamaristanAuto) object:nil];
			}
			
		
	}
	
	

	
	
}

#pragma mark -
#pragma mark -network fail

-(void)cantConnection:(NSError *)error andRestConnection:(id)connector{
	btnSave.enabled = TRUE;
	[actSetting stopAnimating];
	actSetting.hidden=YES;
	if (save) {
		alertView();
		save = NO;
	}
}


@end
