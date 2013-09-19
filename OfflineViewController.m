//
//  OfflineViewController.m
//  SOSBEACON
//
//  Created by bon on 12/23/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import "OfflineViewController.h"
#import <MessageUI/MessageUI.h>

@implementation OfflineViewController

@synthesize groups;

// The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
/*
 - (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
 self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
 if (self) {
 // Custom initialization.
 }
 return self;
 }
 */


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	appDelegate = (SOSBEACONAppDelegate *)[[UIApplication sharedApplication] delegate];
	[srollView setContentSize:CGSizeMake(320, 800)];
	//imageView.frame = CGRectMake(0, 0, 320, 460);
    NSFileManager *fileManager = [[NSFileManager alloc] init];
    NSString *fileUserId = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"UserID.plist"];    
    if ([fileManager fileExistsAtPath:fileUserId]) {
        NSDictionary *dictUserId = [NSDictionary dictionaryWithContentsOfFile:fileUserId];
        appDelegate.userID = [[dictUserId objectForKey:@"userId"] integerValue];
    }
    else {
        appDelegate.userID = 0;
    }
    
    NSString *defaultFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"defaultGroup.plist"];
    if ([fileManager fileExistsAtPath:defaultFile]) {
        NSMutableArray *defaultGroupArr = [NSMutableArray arrayWithContentsOfFile:defaultFile];
        appDelegate.defaultGroupId = [defaultGroupArr objectAtIndex:0];        
    }
    else {
        appDelegate.defaultGroupId = [NSString stringWithString:@"0"];
    }

	actionSheet1 = [[UIActionSheet alloc] initWithTitle:@""
											   delegate:self 
									  cancelButtonTitle:nil
								 destructiveButtonTitle:nil 
									  otherButtonTitles:nil];
    
    NSString *groupsPath = [DOCUMENTS_FOLDER stringByAppendingPathComponent:[NSString stringWithFormat:@"allGroups.plist"]];        
    if ([fileManager fileExistsAtPath:groupsPath]) {
        self.groups = [NSArray arrayWithContentsOfFile:groupsPath];
        for (NSDictionary *group in groups) {
            [actionSheet1 addButtonWithTitle:[group objectForKey:@"name"]];
            if ([[group objectForKey:@"id"] isEqual:appDelegate.defaultGroupId]) {
                labelGroup.text = [group objectForKey:@"name"];
            }
        }
        [actionSheet1 addButtonWithTitle:@"All Groups"];        
    }
    else {
        self.groups = nil;        
        [actionSheet1 addButtonWithTitle:@"All Groups"];
        labelGroup.text = @"All Groups";
    }	
    [fileManager release];    
	/*
	 tableViewCheckIn = [[UITableView alloc] initWithFrame:CGRectMake(10, 200, 280, 320) style:UITableViewStyleGrouped];
	 tableViewCheckIn.delegate = self;
	 tableViewCheckIn.dataSource = self;	
	 */
	//tableViewCheckIn.style = UITableViewStyleGrouped ;
	tableArr = [[NSMutableArray alloc] init];
	[tableArr addObject:@"School Notice"];
	[tableArr addObject:@"Emergency Alert"];
	[messageBackground addSubview:tableViewCheckIn];
	tableViewCheckIn.backgroundColor = [UIColor clearColor];
//	selectGroup = @"0";
	selectType_1 = YES;
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
	[srollView release];
	[labelCountMessage release];
	[textViewMessage release];
	[actionSheet1 release];
	[tableViewCheckIn release];
	[messageBackground release];
	[tableArr release];
	[labelType release];
	[labelGroup release];
	[selectGroup release];
    self.groups = nil;
    [super dealloc];
}

-(void)sendSMS
{
	[textViewMessage resignFirstResponder];
	MFMessageComposeViewController *controller = [[[MFMessageComposeViewController alloc] init] autorelease];	
	UIDevice *device = [UIDevice currentDevice];
	NSString *strImei = [NSString stringWithString:[appDelegate GetUUID]];
	NSString *message = [NSString stringWithFormat:@"OF|%@",strImei];
	if (selectType_1)
	{
		message = [NSString stringWithFormat:@"%@|%@",message,@"2"];
	}
	else 
	{
		message = [NSString stringWithFormat:@"%@|%@",message,@"0"];
	}
	
	if ([appDelegate.longitudeString length] == 0)
	{
		NSLog(@"long == null");
		appDelegate.longitudeString = @"0";
	}
	if ([appDelegate.latitudeString length] == 0 )
	{
		NSLog(@"lat = null");
		appDelegate.latitudeString = @"0";
	}
	
	message = [NSString stringWithFormat:@"%@|%@|%@|%@|%@",message,labelGroup.text,appDelegate.latitudeString,appDelegate.longitudeString,textViewMessage.text];
	
	NSLog(@"message :%@",message);
	
	
    /*
    //Block send SMS
    [self.view removeFromSuperview];
    [appDelegate showLoading];
    return;
    //End
    */
    
	if( ! [MFMessageComposeViewController canSendText])
		return;
	
	controller.body = message;
	controller.recipients = [NSArray arrayWithObjects:@"+14156898484", nil]; //+14156898484
	controller.messageComposeDelegate = self;
	[self presentModalViewController:controller animated:YES];
//	selectGroup = @"0";
	selectType_1 = YES;
	textViewMessage.text = @"";
	labelType.text = @"School Notice";
	labelGroup.text = @"All Groups";  
    if (self.groups != nil) {
        for (NSDictionary *group in groups) {
            [actionSheet1 addButtonWithTitle:[group objectForKey:@"name"]];
            if ([[group objectForKey:@"id"] isEqual:appDelegate.defaultGroupId]) {
                labelGroup.text = [group objectForKey:@"name"];
            }
        }        
    }
	[tableViewCheckIn reloadData];
	labelCountMessage.text = [NSString stringWithFormat:@"(there are %d characters remaining)",75];
}

-(IBAction)showSMSWarningDialog
{
	UIAlertView *alert  =[[UIAlertView alloc] 
						  initWithTitle:@""
						  message:@"The SMS sending screen will appear. Please do not modify anything and press Send button to send the broadcast" 
						  delegate:self 
						  cancelButtonTitle:@"OK" 
						  otherButtonTitles:nil];
	[alert show];
	[alert release];
	
	
}
- (IBAction)CancelButtonPress
{
	//[[[UIApplication ]sharedApplication] per]

	//[self.view removeFromSuperview];
	//[appDelegate showSplash];
	
	exit(100);
	//[self performSelector:@selector(exitapp) withObject:nil afterDelay:3];
	
}

- (void)exitapp
{
	exit(0);
}
-(IBAction)BackGroundTap
{
	[textViewMessage resignFirstResponder];
}
- (IBAction)GetContact {
	[textViewMessage resignFirstResponder];
	actionSheet1.actionSheetStyle = UIActionSheetStyleBlackOpaque;
	[actionSheet1 showInView:appDelegate.window];
}	

- (IBAction)CallEmergencePhone
{
	[self sendSMS];
	
	
	[textViewMessage resignFirstResponder];	
	NSString *emergenceFile = [DOCUMENTS_FOLDER stringByAppendingPathComponent:@"emergencyNumber.plist"];
	NSMutableDictionary *emergencePhone = [NSMutableDictionary dictionaryWithContentsOfFile:emergenceFile];
	NSString *phoneNum = [emergencePhone objectForKey:@"emerPhone"];
	//NSLog(@"phone number : %@",phoneNum);
	NSURL *phoneNumberURL = [NSURL URLWithString:[NSString stringWithFormat:@"tel:%@",phoneNum]];
	[[UIApplication sharedApplication] openURL:phoneNumberURL];
	
}
-(IBAction)GetAlertType;
{
	/*
	 if (messageBackground == nil)
	 {
	 messageBackground = [[UIView alloc] initWithFrame:CGRectMake(0, 10, 320, 460)];
	 messageBackground.backgroundColor = [UIColor clearColor];
	 //messageBackground.alpha = 0.7;
	 //[messageBackground.layer setCornerRadius:15];
	 }
	 */
	[textViewMessage resignFirstResponder];
	[self.view addSubview:messageBackground];
}
#pragma mark alert delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	[self sendSMS];
}
#pragma mark textViewDelegate
- (void)textViewDidChange:(UITextView *)textView{
	
	if (textView.text.length >=75) {
		textView.text = [textView.text substringToIndex:75];

	}
	labelCountMessage.text = [NSString stringWithFormat:@"(there are %d characters remaining)",(75 - [textView.text length])];

}

- (void)textViewDidBeginEditing:(UITextView *)textView {
    CGRect newFrame = self.view.frame;
    newFrame.origin.y = -12;
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.3];
    self.view.frame = newFrame;
    [UIView commitAnimations];
}

- (void)textViewDidEndEditing:(UITextView *)textView {
    CGRect newFrame = self.view.frame;
    newFrame.origin.y = 20;
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.3];
    self.view.frame = newFrame;
    [UIView commitAnimations];
}

#pragma mark -
#pragma mark MFMessageComposeViewController
- (void)messageComposeViewController:(MFMessageComposeViewController *)controller didFinishWithResult:(MessageComposeResult)result
{

	switch (result) {
		case MessageComposeResultCancelled:
        {
			//NSLog(@"Cancelled");
			break;
        }
		case MessageComposeResultFailed:
		{
			UIAlertView *alert = [[UIAlertView alloc]
								  initWithTitle:@"SMS"
								  message:@"Unknown Error"
								  delegate:nil
								  cancelButtonTitle:@"OK"
								  otherButtonTitles: nil];
			[alert show];
			[alert release];
            break;
		}
		case MessageComposeResultSent:
		{
            //NSLog(@"SEND");
			[appDelegate showLoading];
            break;
		}
		default:
			break;
	}
	
	[self dismissModalViewControllerAnimated:YES];
}
#pragma mark -
#pragma mark actionSheetDelegate

-(void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex
{
    NSLog(@"button index: %d", buttonIndex);
    labelGroup.text = [actionSheet buttonTitleAtIndex:buttonIndex];
/*    
	switch (buttonIndex) {
		case 0:
		{
			//NSLog(@"Family");
			selectGroup = @"0";
			labelGroup.text = @"Family";
			
		}
			break;
		case 1:
			
		{
			//NSLog(@"Friends");
			selectGroup = @"1";
			labelGroup.text = @"Friends";
		}
			break;
		case 2:
		{
			//NSLog(@"Family & Friends");
			selectGroup = @"3";
			labelGroup.text = @"Family & Friends";
			
		}
			break;
		case 3:
		{
			//NSLog(@"Neigh");
			selectGroup = @"2";
			labelGroup.text = @"Neighborhood watch";
		}
			break;
		case 4:
		{
			//NSLog(@"allGroup");
			selectGroup = @"4";
			labelGroup.text = @"All Groups";
			
		}
			break;	
		default:
			break;
	}
*/ 
}
#pragma mark -
#pragma mark Table view data source
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
	NSInteger row = [indexPath row];
	switch (row) {
		case 0:
			if (selectType_1)
			{
				
			}else 
			{
				selectType_1 =! selectType_1;
			}
			
			
			break;
		case 1:
			if (!selectType_1)
			{
				
			}else 
			{
				selectType_1 =! selectType_1;
			}
			
			break;
		default:
			break;
	}
	labelType.text = [tableArr objectAtIndex:row];
	[tableView reloadData];
	[messageBackground performSelector:@selector(removeFromSuperview) withObject:nil afterDelay:0.15];
	
	
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	return [tableArr count];
}	

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
	static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableViewCheckIn dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:nil] autorelease];
    }
    NSInteger row = [indexPath row];
	//cell.selectionStyle  = UITableViewCellAccessoryCheckmark;
	cell.textLabel.font = [UIFont systemFontOfSize:16];
	cell.textLabel.text = [tableArr objectAtIndex:row];
	switch (row) {
		case 0:
			if (selectType_1)
			{
				cell.accessoryType = UITableViewCellAccessoryCheckmark;
			}
			else 
			{
				cell.accessoryType = UITableViewCellAccessoryNone;
				
			}
			
			break;
		case 1:
			if (!selectType_1)
			{
				cell.accessoryType = UITableViewCellAccessoryCheckmark;
			}
			else 
			{
				cell.accessoryType = UITableViewCellAccessoryNone;
				
			}
			
			break;
		default:
			cell.accessoryType = UITableViewCellAccessoryNone;
			break;
	}
	
	return cell;
}





@end
