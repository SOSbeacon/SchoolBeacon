//
//  SettingsAlertView.m
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/18/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import "SettingsAlertView.h"
#import "EmergencyView.h"
@implementation SettingsAlertView
@synthesize incomingGovernment,txtPanicPhone,lblSendToAlert,voiceRecord,rest,btnSave,actSetting;
@synthesize flag;
@synthesize actionSheet3;
@synthesize groupArray;
@synthesize typeArray;
@synthesize scoll;
@synthesize defaultGroupId, recordDuration;
// The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
		btnSave=[[UIBarButtonItem alloc] initWithTitle:@"Save" style:UIBarButtonItemStyleBordered target:self action:@selector(SaveSetting)];
		self.navigationItem.rightBarButtonItem = btnSave;
    }
    return self;
}

- (IBAction)back:(id)sender {
    if (isEdit) {
        isPop = YES;
        flagalert =1;
        UIAlertView *alert =[ [UIAlertView alloc] initWithTitle:nil 
                                                        message:NSLocalizedString(@"SaveChange",@"")
                                                       delegate:self
                                              cancelButtonTitle:@"Yes" otherButtonTitles:@"No",nil];
        [alert show];
        [alert release];        
    }
    else {
        [self.navigationController popViewControllerAnimated:YES];
    }
}

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	UIImage *buttonImage = [[UIImage imageNamed:@"backImage.png"] stretchableImageWithLeftCapWidth:16.0 topCapHeight:2.0];
    UIButton *backButton = [UIButton buttonWithType:UIButtonTypeCustom];
    [backButton setBackgroundImage:buttonImage forState:UIControlStateNormal];
    [backButton setBackgroundImage:[UIImage imageNamed:@"backImage2.png"] forState:UIControlStateHighlighted];
    [backButton addTarget:self action:@selector(back:) forControlEvents:UIControlEventTouchUpInside];
    backButton.frame = CGRectMake(0.0, 0.0, 49, 30);
	
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc] initWithCustomView:backButton];
	self.navigationItem.leftBarButtonItem = btnBack;
	[btnBack release];    
    isPop = NO;
	self.scoll.contentSize =  CGSizeMake(320, 800);
	self.title=@"Alert Settings";
	rest = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	rest.delegate =self;	
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
    [super viewDidLoad];
}

- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    [self loadData];
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
	[selectAlert  release];
	[typeArray release];
	[groupArray release];
	[actionSheet3 release];
    self.defaultGroupId = nil;
    self.recordDuration = nil;
    [super dealloc];
}

-(void)loadData{
    self.defaultGroupId = appDelegate.defaultGroupId;
    for (NSDictionary *group in appDelegate.groups) {
        if ([[group objectForKey:@"id"] isEqual:appDelegate.defaultGroupId]) {
            lblSendToAlert.text = [group objectForKey:@"name"];
            break;
        }
    }
    if (lblSendToAlert.text.length == 0) {
        lblSendToAlert.text = @"All Groups";
    }
    
	if([appDelegate.recordDuration integerValue] == 2)  {
		voiceRecord.text = @"60 seconds";
        self.recordDuration = [NSString stringWithString:@"2"];        
	}
	else if([appDelegate.recordDuration intValue] == 3) {
		voiceRecord.text = @"1 minute";
        self.recordDuration = [NSString stringWithString:@"3"];        
	}
	else if([appDelegate.recordDuration intValue] == 4) {
		voiceRecord.text = @"1.5 minutes";
        self.recordDuration = [NSString stringWithString:@"4"];        
    }	
	else if([appDelegate.recordDuration intValue] == 5) {
		voiceRecord.text = @"2 minutes";
        self.recordDuration = [NSString stringWithString:@"5"];        
    }
	else if([appDelegate.recordDuration intValue] == 6) {
		voiceRecord.text = @"3 minutes";
        self.recordDuration = [NSString stringWithString:@"6"];        
	}else {
		voiceRecord.text = @"30 seconds";
        self.recordDuration = [NSString stringWithString:@"1"];        
	}
}

- (IBAction)sendToAlert {
	editIndex = 4;
    UIActionSheet *actionSheet = [[UIActionSheet alloc] init];
    for (int i = 0; i < appDelegate.groups.count; i++) {
        NSDictionary *group = [appDelegate.groups objectAtIndex:i];
        [actionSheet addButtonWithTitle:[group objectForKey:@"name"]];
    }
    actionSheet.cancelButtonIndex = [actionSheet addButtonWithTitle:@"Cancel"];
    actionSheet.tag = 2000;
    actionSheet.delegate = self;
    [actionSheet showFromTabBar:self.tabBarController.tabBar];
    [actionSheet release];
}

-(IBAction)choicesRecordingDuration{
	editIndex = 2;
	UIActionSheet *actionSheet1 = [[UIActionSheet alloc] initWithTitle:@""
															  delegate:self 
													 cancelButtonTitle:@"Cancel"
												destructiveButtonTitle:nil 
													 otherButtonTitles:@"30 seconds",@"60 seconds",@"1 minute",@"1.5 minutes",@"2 minutes",@"3 minutes",nil];
	actionSheet1.actionSheetStyle = UIActionSheetStyleBlackOpaque;
	[actionSheet1 showInView:appDelegate.window];
	[actionSheet1 release];
}

#pragma mark UIActionSheetDelegate

- (void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex
{
	if (buttonIndex != [actionSheet cancelButtonIndex]) {
        isEdit = YES;        
		NSString *value;
		if (editIndex == 2){
		
			switch (buttonIndex) {
				case 0:
					voiceRecord.text = @"30 seconds";
                    self.recordDuration = [NSString stringWithString:@"1"];
					break;
				case 1:
					voiceRecord.text = @"60 seconds";
                    self.recordDuration = [NSString stringWithString:@"2"];                    
					break;
				case 2:
					voiceRecord.text = @"1 minute";
                    self.recordDuration = [NSString stringWithString:@"3"];                    
					break;
				case 3:
					voiceRecord.text = @"1.5 minutes";
                    self.recordDuration = [NSString stringWithString:@"4"];                    
					break;
				case 4:
					voiceRecord.text = @"2 minutes";
                    self.recordDuration = [NSString stringWithString:@"5"];                    
					break;
				case 5:
					voiceRecord.text = @"3 minutes";
                    self.recordDuration = [NSString stringWithString:@"6"];                    
					break;
				default:
					break;
			}
            NSLog(@"duration: %@", recordDuration);
		}
		
		else if(editIndex==4) 
		{
            
			NSInteger i = buttonIndex;
            NSDictionary *group = [appDelegate.groups objectAtIndex:i];
            [actionSheet addButtonWithTitle:[group objectForKey:@"name"]];
			lblSendToAlert.text = [group objectForKey:@"name"];
            self.defaultGroupId = [group objectForKey:@"id"];
            NSLog(@"defaul groups: %@", defaultGroupId);
		} 
    }
}

-(void)save {
   	save = TRUE;
	btnSave.enabled = FALSE;
	actSetting.hidden=NO;
	[actSetting startAnimating];
 
    NSArray *key1 = [NSArray arrayWithObjects:@"format", @"_method", @"userId",@"token",@"recordDuration",@"defaultGroupId", @"schoolId", nil];
    NSArray *obj1 = [NSArray arrayWithObjects:@"json", @"put", [NSString stringWithFormat:@"%d",appDelegate.userID],
                     appDelegate.apiKey, self.recordDuration, self.defaultGroupId, appDelegate.schoolId, nil];
    NSDictionary *param1 =[NSDictionary dictionaryWithObjects:obj1 forKeys:key1];
    NSString *request = [NSString stringWithString:@"http://sosbeacon.org/school/setting"];
    flag =1;
    [rest putPath:request withOptions:param1];    
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
		[self.navigationController popViewControllerAnimated:YES];
	}
	
}

-(IBAction)SaveSetting
{
    isPop = YES;
    [self save];
/*    
	flagalert =1;
	UIAlertView *alert =[ [UIAlertView alloc] initWithTitle:nil 
													message:NSLocalizedString(@"SaveChange",@"")
												   delegate:self
										  cancelButtonTitle:@"Yes" otherButtonTitles:@"No",nil];
	[alert show];
	[alert release];
*/ 
}

#pragma mark -
#pragma mark delayUIalert
- (void) DimisAlertView:(UIAlertView*)alertView {
	[alertView dismissWithClickedButtonIndex:0 animated:TRUE];
    if (alertView.tag == 112 && isPop) {
        [self.navigationController popViewControllerAnimated:YES];
    }
}

#pragma mark -
#pragma mark finish request

-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector{
	if (flag == 1) {
	[actSetting stopAnimating];
	actSetting.hidden = YES;
	btnSave.enabled = TRUE;
	isEdit = NO;
        if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"]){
            if (save) {
                appDelegate.defaultGroupId = self.defaultGroupId;
                appDelegate.recordDuration = self.recordDuration;
                NSString *defaultFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"defaultGroup.plist"];
                NSMutableArray *defaultGroupArr = [[NSMutableArray alloc] initWithObjects:appDelegate.defaultGroupId, nil];
                [defaultGroupArr writeToFile:defaultFile atomically:YES];
                [defaultGroupArr release];
                [[NSNotificationCenter defaultCenter] postNotificationName:@"GetGroupsDidFinish" object:nil];                
                btnSave.enabled = TRUE;
                UIAlertView *alertView= [[UIAlertView alloc] initWithTitle:@""
                                                                   message:@"Settings saved successfully" 
                                                                  delegate:self 
                                                         cancelButtonTitle:@"Ok" 
                                                         otherButtonTitles:nil] ;
                alertView.tag = 112;
                [alertView show];
                [self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
                [alertView release];
                save = NO;
            }
        } else {
        //	NSLog(@" error roi");
            
        }

	}
/*    
	else
	if (flag == 2) 
	{
		
		actionSheet3 = [[UIActionSheet alloc] initWithTitle:@""
												   delegate:self 
										  cancelButtonTitle:nil
									 destructiveButtonTitle:nil 
										  otherButtonTitles:nil];
		 groupArray = [[NSMutableArray alloc] init];
		 typeArray = [[NSMutableArray alloc] init];

	//	NSLog(@" ST_sendtoAlert in finish request: %@",[appDelegate.settingArray objectForKey:ST_SendToAlert]);
		if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"]) {
			NSDictionary *data = [[arrayData objectForKey:@"response"] objectForKey:@"data"];
			
			for(NSDictionary *dict in data)
			{
				[actionSheet3 addButtonWithTitle:[dict objectForKey:@"name"]];
				[groupArray  addObject:[dict objectForKey:@"name"]];
				[typeArray addObject:[dict objectForKey:@"id"]];
				
				
				
				NSInteger type = [[dict objectForKey:@"id"] intValue];
				if( type == [[appDelegate.settingArray objectForKey:ST_SendToAlert] intValue])
				{
				lblSendToAlert.text = [dict objectForKey:@"name"];
				selectAlert =[[NSString stringWithFormat:@"%d",type] retain];
				}
			}
			//NSLog(@"group array : %@",groupArray);
			
		}else 
		{
			//NSLog(@"getcontact error");
		}
		
	}	
*/	
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
