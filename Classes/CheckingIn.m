//
//  CheckingIn.m
//  SOSBEACON
//
//  Created by cncsoft on 9/10/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <QuartzCore/QuartzCore.h>
#import "CheckingIn.h"
#import "RestConnection.h"
#import "SOSBEACONAppDelegate.h"
#import "ValidateData.h"
#import "CaptorView.h"
#import "Uploader.h"
#import "StatusView.h"
#import "SlideToCancelViewController.h"

@implementation CheckingIn

@synthesize restConnection,lblContact,tvTextMessage,scrollView;
@synthesize arrayCheckin;
@synthesize countArray;
@synthesize groupArray;
@synthesize typeArray;
@synthesize actionSheet1;

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
	appDelegate.uploader.delegate = self;
	
	//lblContact.text = @"Family";
	tvTextMessage.hidden = YES;
	//tvTextMessage.text = @"I'm ok";
	labelCountMessage.hidden = YES;

}

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
    [super viewDidLoad];
	tvTextMessage.text = @"I'm ok";
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
//	[appDelegate showOfflineMode];
	
	
	
	
	[btnCheck setTitle:@"Check in Now" forState:UIControlStateNormal];
	//[[btnCheck titleLabel] setTextColor:[UIColor blackColor]];
	[[btnCheck titleLabel] setTextAlignment:UITextAlignmentCenter];
	[[btnCheck titleLabel] setLineBreakMode:UILineBreakModeWordWrap];
	[[btnCheck titleLabel] setNumberOfLines:0];
	[[btnCheck titleLabel] setFont:[UIFont boldSystemFontOfSize:20.0f]];
	
	restConnection = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	restConnection.delegate = self;
	scrollView.contentSize = CGSizeMake(320, 460);
	tvTextMessage.delegate = self;
	/*
	if (!slideCheckin) {
		slideCheckin = [[SlideToCancelViewController alloc] init];
		slideCheckin.delegate = self;
		slideCheckin.labelText = @"  Send audio/image recording";
		CGRect sliderFrame = slideCheckin.view.frame;
		sliderFrame.origin.y = self.view.frame.size.height;
		slideCheckin.view.frame = sliderFrame;
		[self.view addSubview:slideCheckin.view];
	}
	slideCheckin.enabled=YES;
	CGPoint sliderCenter = slideCheckin.view.center;
	sliderCenter.y -= slideCheckin.view.bounds.size.height+125;
	slideCheckin.view.center = sliderCenter;
	[UIView commitAnimations];
	 */
	
	NSString *fileCount=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"count.plist"];
	NSString *file=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"message.plist"];
	if ([[NSFileManager defaultManager] fileExistsAtPath:file]) 
	{
	
		arrayCheckin = [[NSMutableArray alloc] initWithContentsOfFile:file];
		countArray = [[NSMutableArray alloc] initWithContentsOfFile:fileCount];
	}
	else 
	{
		[[NSFileManager defaultManager] createFileAtPath:file contents:nil attributes:nil];
		arrayCheckin = [[NSMutableArray alloc] initWithObjects:
						@"I'm ok",
						@"I will call later",
						@"Please call me asap",
						@"I will be late",
						@"Problems Resolved",
						@"Medical Alert",
						@"Fire Alert",
						@"Family Emergency",
						@"Weather Alert ",
						@"Security Problem",
						@"Enter your message",nil];		
		[arrayCheckin writeToFile:file atomically:YES];
		[[NSFileManager defaultManager] createFileAtPath:fileCount contents:nil attributes:nil];
		countArray = [[NSMutableArray alloc] initWithObjects:@"0",@"0",@"0",@"0",@"0",@"0",@"0",@"0",@"0",@"0",@"0",nil];
		[countArray writeToFile:fileCount atomically:YES];
		
	}
	tableViewCheckIn = [[UITableView alloc] initWithFrame:CGRectMake(10, 15, 280, 320) style:UITableViewStyleGrouped];
	tableViewCheckIn.delegate = self;
	tableViewCheckIn.dataSource = self;	
	flag = 2;
	[restConnection getPath:[NSString stringWithFormat:@"/groups?phoneId=%d&token=%@&format=json",appDelegate.phoneID,appDelegate.apiKey] withOptions:nil];
	
	[self saveLastGroup];
}
//function catch slide event
- (void) cancelled:(SlideToCancelViewController*)sender {
    //NSLog(@"show captorview Check_in");
	if(sender == slideCheckin){
		appDelegate.uploader.autoUpload = YES;
		CaptorView *captor = [[CaptorView alloc] init] ;
		captor.modalTransitionStyle = UIModalTransitionStyleFlipHorizontal;
		captor.isCheckIn=TRUE;
		[self presentModalViewController:captor animated:YES];
		[captor autorelease];
		slideCheckin.enabled = YES;
		/*
		if (tvTextMessage.hidden) 
		{
		}
		else 
		{
			newFlag = 1;
		}
		 */

	}	
}
-(void)sendAudioImage
{
    //NSLog(@"show captorview Check_in");
	appDelegate.uploader.autoUpload = YES;
	CaptorView *captor = [[CaptorView alloc] init] ;
	captor.modalTransitionStyle = UIModalTransitionStyleFlipHorizontal;
	captor.isCheckIn=TRUE;
	[self presentModalViewController:captor animated:YES];
    
    [captor autorelease];
	slideCheckin.enabled = YES;
	/*
	if (tvTextMessage.hidden) 
	{
	}
	else 
	{
		newFlag = 1;
	}
	*/
}

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    //NSLog(@"memory warning");
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)dealloc {
	[labelCountMessage release];
    if (familyGroupId)
    {
        [familyGroupId release];
    }
	[countArray release];
	[groupArray release];
	[typeArray release];
	[actionSheet1 release];
	[tableViewCheckIn release];
	[messageBackground release];
	[arrayCheckin release];
	[restConnection release];
	[lblStatusUpload release];
	[actChecking release];
	[lblGetMessage release];
	[scrollView release];
	[tvTextMessage release];
	[lblContact release];
    [super dealloc];
}
-(void)checkingInNow
{
	btnGetcontact.enabled = FALSE;
	btnGetMessage.enabled = FALSE;
	btnCheck.enabled = FALSE;
	btnCancel.enabled = FALSE;
	tvTextMessage.userInteractionEnabled = FALSE;	
	
	if ([[tvTextMessage.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] isEqualToString:@""]) {
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Error"
														message:NSLocalizedString(@"checkinWithOutMessage",@"")
													   delegate:nil
											  cancelButtonTitle:@"Ok"
											  otherButtonTitles:nil];
		[alert show];
		[self performSelector:@selector(dimisAlert:) withObject:alert afterDelay:2];
		[alert release];
		btnCheck.enabled = TRUE;
		btnCancel.enabled = TRUE;
		btnGetMessage.enabled = TRUE;
		btnGetcontact.enabled = TRUE;
		tvTextMessage.userInteractionEnabled = TRUE;
		return;
		
	}
	else {
		lblGetMessage.text = tvTextMessage.text;
		[self sendCheckin];
	}
	
	actChecking.hidden = NO;
	[actChecking startAnimating];
	
	
}

#pragma mark textviewdelegate

- (void)textViewDidChange:(UITextView *)textView{
	
	if (textView.text.length >=75) {
		textView.text = [tvTextMessage.text substringToIndex:75];
		/*
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil 
													   message:NSLocalizedString(@"messageLimit",@"")
													  delegate:nil
											 cancelButtonTitle:@"OK"
											 otherButtonTitles:nil];
		[alert show];
		[alert release];
		 */
	}
	labelCountMessage.text = [NSString stringWithFormat:@"(there are %d characters remaining)",(75 - [textView.text length])];

}

#pragma mark Actionsheet
//get contact to send checkin
- (IBAction)GetContact {
	editIndex = 1;
	actionSheet1.actionSheetStyle = UIActionSheetStyleBlackOpaque;
	[actionSheet1 showInView:appDelegate.window];
	//[actionSheet1 release];
}	
//get mesage checkin
- (IBAction)getMessageCheckIn {
	messageBackground = [[UIView alloc] initWithFrame:CGRectMake(10, 50, 300, 360)];
	messageBackground.backgroundColor = [UIColor blackColor];
	[messageBackground.layer setCornerRadius:15];
	[messageBackground addSubview:tableViewCheckIn];
	[self.view addSubview:messageBackground];
	
}

#pragma mark -
#pragma mark Table view data source
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
	NSString *data = [arrayCheckin objectAtIndex:indexPath.row];
	lblGetMessage.text = data;
	tvTextMessage.text = data;	
	if (indexPath.row==([arrayCheckin count]-1)) 
	{
		tvTextMessage.hidden = NO;
		tvTextMessage.text = @"";
		labelCountMessage.hidden = NO;
	}else 
	{
		tvTextMessage.hidden = YES;
		labelCountMessage.hidden = YES;
		NSInteger i = [[countArray objectAtIndex:[indexPath row]]intValue];
		i++;
		NSString *count =[[NSString alloc] initWithFormat:@"%d",i];
		[countArray  replaceObjectAtIndex:[indexPath row] withObject:count];
		NSString *fileCount=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"count.plist"];
		[countArray writeToFile:fileCount atomically:YES];
		[count release];

	}
	[tableViewCheckIn removeFromSuperview];	
	[messageBackground removeFromSuperview];
	
	[tableView deselectRowAtIndexPath:indexPath animated:YES];
	
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	return [arrayCheckin count];
}	

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
	static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableViewCheckIn dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:nil] autorelease];
    }
    
    // Configure the cell...
	NSString *message = [NSString stringWithFormat:@"%@",[arrayCheckin objectAtIndex:indexPath.row]];
	cell.textLabel.text = message;
	
	if (([message isEqual:@"Medical Alert"]) || ([message isEqual:@"Fire Alert"]) ||([message isEqual:@"Family Emergency"]) ||([message isEqual:@"Weather Alert "]) ||([message isEqual:@"Security Problem"]))
	{
	cell.textLabel.textColor = [UIColor redColor];	
	}
	else 
		if ([message isEqual:@"Enter your message" ])
	{
		cell.textLabel.textColor =[UIColor colorWithRed:1.0/255 green:220.0/255 blue:100.0/255 alpha:1.0]	;
	}
	else 
	if (([message isEqual:@"I'm ok" ]) ||([message isEqual:@"I will be late" ]) ||([message isEqual:@"I will call later" ]) ||([message isEqual:@"Please call me asap" ] )||([message isEqual:@"Problems Resolved" ]) )
	{
		cell.textLabel.textColor =[UIColor colorWithRed:1.0/255 green:1.0/255 blue:1.0/255 alpha:1.0]	;
		}
	else 
	{
		cell.textLabel.textColor =[UIColor colorWithRed:1.0/255 green:100.0/255 blue:100.0/255 alpha:1.0]	;
	}
	return cell;
	
}

#pragma mark -
#pragma mark actionSheetDelegate

-(void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex{
	if (buttonIndex != [actionSheet cancelButtonIndex]) {
		NSLog(@"%d",buttonIndex);
		NSString *value;
		if(editIndex==1)
		{
			if (buttonIndex == ([groupArray count] )) 
			{
				lblContact.text =	@"All Groups";
				[appDelegate.settingArray setObject:@"0" forKey:CK_CheckingIn];

			
			}
			else
			if (buttonIndex == [groupArray count] +1)
			{
				[appDelegate.settingArray setObject:@"-1" forKey:CK_CheckingIn];
				picker = [[ABPeoplePickerNavigationController alloc] init];
			//	NSLog(@"pickr alloc + init");
				picker.peoplePickerDelegate = self;
				[self presentModalViewController:picker animated:YES];
				[picker release];
			
			
			}
			else
			{
					
			NSInteger i = buttonIndex ;
			lblContact.text = [groupArray objectAtIndex:i];
			if (i < [groupArray  count])
				{
					[appDelegate.settingArray setObject:[typeArray objectAtIndex:i] forKey:CK_CheckingIn];

				}
			}
				}
			
	}
}

#pragma mark  dismisAlertview
- (void) dimisAlert:(UIAlertView *)alertView {
	[alertView dismissWithClickedButtonIndex:0 animated:TRUE];
}
#pragma mark alertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	if (buttonIndex == 0)
	{
		isSendWithAudio = YES;
        appDelegate.uploader.isSendAlert = YES;
	}
	else 
	{
		isSendWithAudio= NO;
		
	}
	[self checkingInNow];

}

#pragma mark IBAction

- (IBAction)textFieldDoneEditing:(id)sender {
	[sender resignFirstResponder];	
}

- (IBAction)backgroundTap:(id)sender {
	//[test resignFirstResponder];
	[tvTextMessage resignFirstResponder];	
}
- (IBAction)CheckingNow
{
	UIAlertView *alert =[[UIAlertView alloc] initWithTitle:@"Check-in Now" 
											message:@"Do you want to send audio and images with this broadcast?" 
											delegate:self
										 cancelButtonTitle:@"YES" 
										 otherButtonTitles:@"NO",nil];
	[alert show];
	[alert release];
}
-(IBAction)cancelAlert:(id)sender{
	[self.navigationController popViewControllerAnimated:YES];
	//[self.navigationController ];
}
- (IBAction)ClearMessage {
	tvTextMessage.text = @"";
	lblContact.text = @"Family";
	lblGetMessage.text = @"Enter your message";
	tvTextMessage.hidden = NO;
	tvTextMessage.userInteractionEnabled = YES;
}
- (void)saveLastGroup
{
	NSString *defaultFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"defaultGroup.plist"];
	
	if ([[NSFileManager defaultManager ] fileExistsAtPath:defaultFile])
	{
		NSMutableArray *defaultGroupArr = [[NSMutableArray alloc] initWithContentsOfFile:defaultFile];
		//NSLog(@"%@",defaultGroupArr);
		[appDelegate.settingArray setObject:[defaultGroupArr objectAtIndex:1] forKey:CK_CheckingIn];
		lblContact.text = [defaultGroupArr objectAtIndex:0];
		[defaultGroupArr release];
	}
	else
	{
		NSLog(@"file not exist");
		NSMutableArray *defaultGroupArr = [[NSMutableArray alloc]initWithObjects:@"Family",[appDelegate.settingArray objectForKey:ST_SendToAlert],nil];
		//NSLog(@"default group : %@",[defaultGroupArr objectAtIndex:0]);
		lblContact.text = [defaultGroupArr objectAtIndex:0];
		//NSLog(@"array default group : %@",defaultGroupArr);
		[defaultGroupArr writeToFile:defaultFile atomically:YES];
		[defaultGroupArr release];
	}
	
}
#pragma mark PeoplePickerDelegate
- (BOOL)peoplePickerNavigationController:(ABPeoplePickerNavigationController *)peoplePicker shouldContinueAfterSelectingPerson:(ABRecordRef)person{
	ABMultiValueRef multi = ABRecordCopyValue(person, kABPersonPhoneProperty);
	if(ABMultiValueGetCount(multi)>0)
	{
		NSString *phoneNumber = (NSString*)ABMultiValueCopyValueAtIndex(multi, 0);
		lblContact.text = phoneNumber;
		[phoneNumber release];
	}
	else {
		lblContact.text= @""; 
	}
	CFRelease(multi);
	[self dismissModalViewControllerAnimated:YES];
	return NO;
}

- (void)peoplePickerNavigationControllerDidCancel:(ABPeoplePickerNavigationController *)peoplePicker{
	[self dismissModalViewControllerAnimated:YES];
}

- (BOOL)peoplePickerNavigationController:(ABPeoplePickerNavigationController *)peoplePicker shouldContinueAfterSelectingPerson:(ABRecordRef)person property:(ABPropertyID)property identifier:(ABMultiValueIdentifier)identifier{
    return NO;
}

#pragma mark -
#pragma mark finishRequest

- (void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector{
	if (flag ==1) 
	{
	//NSLog(@" %@ ",arrayData);
	btnCheck.enabled = TRUE;
	btnCancel.enabled = TRUE;
	btnGetMessage.enabled = TRUE;
	btnGetcontact.enabled = TRUE;
	tvTextMessage.userInteractionEnabled = TRUE;
	//lblGetMessage.text = @"I'm ok";
	lblStatusUpload.hidden = YES;
	[tableViewCheckIn reloadData];
	
		NSString *defaultFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"defaultGroup.plist"];
		NSMutableArray *defaultGroupArr = [[NSMutableArray alloc] init];
		[defaultGroupArr addObject:lblContact.text];
		[defaultGroupArr addObject:[appDelegate.settingArray objectForKey:CK_CheckingIn]];
		//NSLog(@"set default group : %@",defaultGroupArr);
		[defaultGroupArr writeToFile:defaultFile atomically:YES];
		[defaultGroupArr release]; 
		
	if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"]) {


		
		if (isSendWithAudio)
		{

            appDelegate.uploader.isSendAlert = YES;
            appDelegate.uploader.isAlert = YES;
            appDelegate.uploader.uploadId = [[[arrayData objectForKey:@"response"] objectForKey:@"id"] intValue];
            appDelegate.flagsentalert = 2;
			[self sendAudioImage];
		}
		else 
		{
			
			UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Success"
															message:NSLocalizedString(@"chekin_success",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
			[alert show];
			[self performSelector:@selector(dimisAlert:) withObject:alert afterDelay:2];
			[alert release];
		}

	}
	else {
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Error"
														message:@"Error with send sms"
													   delegate:nil
											  cancelButtonTitle:@"Ok"
											  otherButtonTitles:nil];
		[alert show];
		[self performSelector:@selector(dimisAlert:) withObject:alert afterDelay:2];
		[alert release];		
	}
	[actChecking stopAnimating];
	actChecking.hidden = YES;
	}
	else 
	if (flag == 2) 
	{
		
		//// set default group
		/*
		NSString *defaultFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"defaultGroup.plist"];
		
		if ([[NSFileManager defaultManager ] fileExistsAtPath:defaultFile])
		{
			NSLog(@"file exist");
			NSMutableArray *defaultGroupArr = [[NSMutableArray alloc] initWithContentsOfFile:defaultFile];
			NSLog(@"%@",defaultGroupArr);
			[appDelegate.settingArray setObject:[defaultGroupArr objectAtIndex:1] forKey:CK_CheckingIn];
			lblContact.text = [defaultGroupArr objectAtIndex:0];
			[defaultGroupArr release];
		}
		else
		{
			NSLog(@"file not exist");
			NSMutableArray *defaultGroupArr = [[NSMutableArray alloc]initWithObjects:@"Family",[appDelegate.settingArray objectForKey:ST_SendToAlert],nil];
			NSLog(@"default group : %@",[defaultGroupArr objectAtIndex:0]);
			lblContact.text = [defaultGroupArr objectAtIndex:0];
			NSLog(@"array default group : %@",defaultGroupArr);
			[defaultGroupArr writeToFile:defaultFile atomically:YES];
			[defaultGroupArr release];
		}
		 */
		
		[self saveLastGroup];
		/////
		groupArray = [[NSMutableArray alloc ] init];
		typeArray = [[NSMutableArray alloc ] init];
		if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"]) 
		{
			///
			actionSheet1 = [[UIActionSheet alloc] initWithTitle:@""
																	  delegate:self 
															 cancelButtonTitle:nil
														destructiveButtonTitle:nil 
															 otherButtonTitles:nil];
			
			////
			
			
			NSDictionary *data = [[arrayData objectForKey:@"response"] objectForKey:@"data"];
			NSLog(@"data : %@",data);
			for(NSDictionary *dict in data)
			{
				[actionSheet1 addButtonWithTitle:[dict objectForKey:@"name"]];
				[groupArray  addObject:[dict objectForKey:@"name"]];
				[typeArray addObject:[dict objectForKey:@"id"]];
				
			}
			[actionSheet1 addButtonWithTitle:@"All Groups"];
			[actionSheet1 addButtonWithTitle:@"Single Contact"];
			
		}else
		{
			
		}
		
	}

}

#pragma mark -
#pragma mark connectionFail

- (void)cantConnection:(NSError *)error andRestConnection:(id)connector{
	btnCheck.enabled = TRUE;
	btnCancel.enabled = TRUE;
	btnGetMessage.enabled = TRUE;
	btnGetcontact.enabled = TRUE;
	tvTextMessage.userInteractionEnabled = TRUE;	
	
	[actChecking stopAnimating];
	actChecking.hidden = YES;
	alertView();
	[appDelegate.statusView hideStatus];
}
//function send checkin to server
- (void)sendCheckin {
//	newFlag = 2;
	lblStatusUpload.text = @"Checking In...";
	lblStatusUpload.hidden = NO;
	
	NSString *message = [[NSString alloc ] initWithFormat:@"%@",tvTextMessage.text];

	if ((tvTextMessage.hidden == NO) && (![ message isEqualToString:@""]))
	{
		NSString *file=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"message.plist"];
		if ([arrayCheckin count ] == 11 ) 
		{
			//[arrayCheckin removeObjectAtIndex:[arrayCheckin count]-2];
			NSInteger min = [[countArray objectAtIndex:1] intValue];
			NSInteger min_index =8 ;
			for( NSInteger i =1;i<10; i++)
			{
				if (min >= [[countArray objectAtIndex:i] intValue]) 
				{
					min_index = i;
					min =[[countArray objectAtIndex:i] intValue];
				}
				else 
				{
				}
			}
			[arrayCheckin replaceObjectAtIndex:min_index withObject:message];
			[countArray replaceObjectAtIndex:min_index withObject:@"1"];
			NSString *fileCount=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"count.plist"];
			[countArray writeToFile:fileCount atomically:YES];
		}
		[arrayCheckin writeToFile:file atomically:YES];
	}
	[message release];
	tvTextMessage.hidden = YES;
	labelCountMessage.hidden = YES;
	NSArray *key = [NSArray arrayWithObjects:@"token",@"phoneId",@"type",@"toGroup",@"singleContact",@"message",nil];
	NSArray *obj = [NSArray arrayWithObjects:appDelegate.apiKey,[NSString stringWithFormat:@"%d",appDelegate.phoneID],@"2",
					[appDelegate.settingArray objectForKey:CK_CheckingIn],lblContact.text,
					tvTextMessage.text,nil];
	NSDictionary *param =[NSDictionary dictionaryWithObjects:obj forKeys:key];
	flag = 1;
	[restConnection postPath:[NSString stringWithFormat:@"/alert?latitude=%@&longtitude=%@&format=json",appDelegate.latitudeString,appDelegate.longitudeString] withOptions:param];
}

#pragma mark -
#pragma mark UploaderDelegate
- (void)uploadFinish 
{
	//NSLog(@"up load finish");
 
}

- (void)requestUploadIdFinish:(NSInteger)uploadId 
{
	//NSLog(@"*****request up load finish********");
}

@end
