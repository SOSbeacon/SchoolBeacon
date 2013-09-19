//
//  TellUs.h
//  SOSBEACON
//
//  Created by Kevin Hoang on 11/12/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "RestConnection.h"
@class SOSBEACONAppDelegate;
@class RestConnection;


@interface TellUs : UIViewController <UITextViewDelegate,RestConnectionDelegate> {
	SOSBEACONAppDelegate *appDelegate;
	RestConnection *restConnection;
	IBOutlet UIScrollView *scollView;
	IBOutlet UITextField *txtSubject;
	IBOutlet UITextView *tvMessage;
	IBOutlet UITextField *emailcc;
	
	IBOutlet UIButton *btnSend;
	IBOutlet UIButton *btnCancel;
	IBOutlet UIActivityIndicatorView *actSending;
	NSString *fromMail;
	
}
- (IBAction)SendMessage;
- (IBAction)ClearMessage;
- (IBAction)textFieldDoneEditing:(id)sender;
- (IBAction)backgroundTap:(id)sender;
@end
