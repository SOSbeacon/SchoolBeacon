//
//  TellUs.m
//  SOSBEACON
//
//  Created by Kevin Hoang on 11/12/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "SOSBEACONAppDelegate.h"
#import "TellUs.h"

@implementation TellUs
/*
 // The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    if (self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil]) {
        // Custom initialization
    }
    return self;
}
*/


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
    [super viewDidLoad];
	self.title =@"Contact SchoolBeacon";
	restConnection = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	restConnection.delegate = self;
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	scollView.contentSize = CGSizeMake(320, 800);
	//[actSending startAnimating];
	
}


/*
// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}
*/

- (void)didReceiveMemoryWarning {
	// Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	
	// Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
	// Release any retained subviews of the main view.
	// e.g. self.myOutlet = nil;
}


- (void)dealloc {
	restConnection.delegate = nil;
	[restConnection release];
	[scollView release];
	[txtSubject release];
	[tvMessage release];
	[emailcc release];
	[btnSend release];
	[btnCancel release];
	[actSending release];
	[fromMail release];
    [super dealloc];
}

- (IBAction)SendMessage {
	
	if ([[txtSubject.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] isEqualToString:@""] )
	{
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
															message:NSLocalizedString(@"EnterSubjectMessage",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[alertView release];
		[txtSubject becomeFirstResponder];
		
	}
	
	else if ([[tvMessage.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] isEqualToString:@""] )
	{
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
															message:NSLocalizedString(@"EnterSubjectMessage",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[alertView release];
		[tvMessage becomeFirstResponder];
		
	}
	else {
		UIDevice *device = [UIDevice currentDevice];
		
		
		//NSLog(@"subject - txt %@%@",txtSubject.text,tvMessage.text);
		NSString *token =appDelegate.apiKey;
		NSString *type = @"2";
		NSString *phoneId =[NSString stringWithFormat:@"%d",appDelegate.phoneID];
		
		NSString *imei = [NSString stringWithString:[appDelegate GetUUID]];
		
		
		NSString *subject =txtSubject.text;
		NSString *messge = tvMessage.text;
		NSString *emails = emailcc.text;
		
		
		////
		NSArray *key = [[NSArray alloc] initWithObjects:@"token",@"type",@"phoneId",@"imei",@"emails",@"subject",@"message",nil];
		NSArray *obj = [[NSArray alloc] initWithObjects:token,type,phoneId,imei,emails,subject,messge,nil];
		NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj forKeys:key];
		[restConnection postPath:[NSString stringWithFormat:@"/mail?format=json"]withOptions:param];
		[key release];
		[obj release];
		[param release];
		////
						
		[actSending startAnimating];
		
		
	}
	
    
}
-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector
{
	//NSLog(@" array data : %@",arrayData);
	
	if([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"])
	{
		actSending.hidden = YES;
		txtSubject.text=@"";
		tvMessage.text=@"";
		emailcc.text = @"";
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Message"
															message:NSLocalizedString(@"MessageBeenSent",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[alertView release];
		
	}
	else 
	{
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Message"
															message:@"Email sending failed !"
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		
		[alertView release];
	}

	[actSending stopAnimating];
 
}
- (void)cantConnection:(NSError *)error andRestConnection:(id)connector
{
}
- (IBAction)ClearMessage {
	tvMessage.text = @"";
	txtSubject.text=@"";
	emailcc.text = @"";
	tvMessage.userInteractionEnabled = YES;
}

- (IBAction)textFieldDoneEditing:(id)sender {
	[sender resignFirstResponder];	
}

- (IBAction)backgroundTap:(id)sender {
	[tvMessage resignFirstResponder];
	[txtSubject resignFirstResponder];
	[emailcc resignFirstResponder];
}

- (void)textViewDidChange:(UITextView *)textView{
	
	
	if (textView.text.length >=95) {
		textView.text = [tvMessage.text substringToIndex:95];
	}
}
/*
- (void)messageSent:(SKPSMTPMessage *)message
{
    [message release];
    txtSubject.text=@"";
	tvMessage.text=@"";
	UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Message"
														message:@"Your message has been sent"
													   delegate:nil
											  cancelButtonTitle:@"Ok"
											  otherButtonTitles:nil];
	[alertView show];
	
	[alertView release];
    NSLog(@"delegate - message sent");
	[actSending stopAnimating];
}

- (void)messageFailed:(SKPSMTPMessage *)message error:(NSError *)error
{
    [message release];
    UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Message"
														message:@"Email sending failed !"
													   delegate:nil
											  cancelButtonTitle:@"Ok"
											  otherButtonTitles:nil];
	[alertView show];
	
	[alertView release];
    NSLog(@"delegate - error(%d): %@", [error code], [error localizedDescription]);
	[actSending stopAnimating];
}
*/



@end
