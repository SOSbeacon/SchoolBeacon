//
//  EmailView.m
//  
//
//  Created by Geoff Heeren on 10/21/09.
//  Copyright 2009 AppTight, Inc. All rights reserved.
//

#import "EmailView.h"


@implementation EmailView

@synthesize toAddresses;
@synthesize  ccAddresses;
@synthesize  bccAddresses;
@synthesize images;
@synthesize  subject;
@synthesize  body;
@synthesize  emailPic;
@synthesize mainView;
@synthesize tintColor;
- (id)initWithFrame:(CGRect)frame {
    if (self = [super initWithFrame:frame]) {
        // Initialization code
    }
    return self;
}


- (void)drawRect:(CGRect)rect {
    // Drawing code
}


- (void)dealloc {
    
	[images release];
	[toAddresses release];
	[ccAddresses release];
	[bccAddresses release];
	[subject release];
	[body release];
	[emailPic release];
	[mainView release];
	[tintColor release];
    [super dealloc];
}
#pragma mark Email Methods
- (void)showEmail {
	//NSLog(@"showEmail",nil);
	
	if (![MFMailComposeViewController canSendMail]) {
		UIAlertView *cantMailAlert = [[UIAlertView alloc] initWithTitle:@"Email Not Avail"
																message:@"Sorry, this device is not able to send e-mail" delegate:NULL cancelButtonTitle:@"OK" otherButtonTitles:NULL];
		[cantMailAlert show]; 
		[cantMailAlert release]; 
		return;
	}
	
	MFMailComposeViewController *mailController = [[MFMailComposeViewController alloc] init];
	
	if (toAddresses!=nil && [toAddresses count]>0)
		[mailController setToRecipients:toAddresses];
	
	if (ccAddresses!=nil && [ccAddresses count]>0)
		[mailController setCcRecipients:ccAddresses];
	
	if (bccAddresses!=nil && [bccAddresses count]>0)
		[mailController setBccRecipients:bccAddresses];
	
	if (body!=nil)
		[mailController setMessageBody:body isHTML:YES];
	
	//[mailController setMessageBody:@"122" isHTML:NO];

	if (subject!=nil)
		[mailController setSubject:subject];
	
	if (emailPic!=nil)
	{
		NSData *imageData = UIImageJPEGRepresentation(self.emailPic, 0.8);
		[mailController addAttachmentData:imageData mimeType:@"image/jpg" fileName:@"image.jpg"];
	}
	if (images!=nil && [images count]>0)
	{
		NSInteger idx=1;
		for(UIImage *img in images)
		{
			[mailController addAttachmentData:UIImageJPEGRepresentation(img, 0.8) mimeType:@"image/jpg" fileName:[NSString stringWithFormat:@"Report%i.jpg",idx]];
			idx++;
		}
	}
	if (tintColor!=nil)
		mailController.navigationBar.tintColor=tintColor;
	
	
	mailController.mailComposeDelegate = self;
	[mainView presentModalViewController:mailController animated:YES];
	[mailController release];

}

- (void)mailComposeController:(MFMailComposeViewController*)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error {
	[mainView becomeFirstResponder];
	[mainView dismissModalViewControllerAnimated:YES];
	if (result==MFMailComposeResultSent)
	{
		//[Tracking trackEvent:@"Email.Sent"];
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Email Sent"
														message:@"Your email has been sent." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show]; 
		[alert release]; 
	}
	else if (result==MFMailComposeResultSaved)
	{
		//[Tracking trackEvent:@"Email.Saved"];
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Email Saved"
														message:@"Your email has been saved. " delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show]; 
		[alert release]; 
	}
	else if (result==MFMailComposeResultFailed)
	{
		
		//[Tracking trackEvent:@"Email.Failed"];
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Email Not Sent"
														message:@"Your email could not be sent please check your internet connection." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show]; 
		[alert release]; 
	}
	else if (result==MFMailComposeResultCancelled)
	{
		//[Tracking trackEvent:@"Email.Cancelled"];
	}
}
+(void)sendTellAFriendEmail:(UIViewController *)ctrl tintColor:(UIColor *)color{
	
	
	NSString *subject=@"Download ColorSmart";
	NSString *appUrl=(NSString *)[[[NSBundle mainBundle] infoDictionary] objectForKey:@"iTunesLink"];
	NSString *body=[[NSString alloc] initWithFormat:@"Check out SOSbeacon, <a href=\"%@\">SOSbeacon</a>",appUrl];
	
	//NSLog(@"showEmail");
	EmailView *emailer=[[EmailView alloc] init] ;
	
	emailer.subject=subject;
	emailer.body=body;

    [body release];
	emailer.mainView=ctrl;
	emailer.tintColor=color;
	
	[emailer showEmail];
	[emailer autorelease];
	
}
@end
