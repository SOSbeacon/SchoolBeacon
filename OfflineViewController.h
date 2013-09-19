//
//  OfflineViewController.h
//  SOSBEACON
//
//  Created by bon on 12/23/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MessageUI/MessageUI.h>
#import "SOSBEACONAppDelegate.h"
@interface OfflineViewController : UIViewController <MFMessageComposeViewControllerDelegate,UIActionSheetDelegate,UITableViewDelegate,UITableViewDataSource,UITextViewDelegate>
{
	IBOutlet UITextView *textViewMessage;
	UIActionSheet *actionSheet1;
	SOSBEACONAppDelegate *appDelegate;
	IBOutlet UITableView *tableViewCheckIn;
	IBOutlet UIView *messageBackground;
	NSMutableArray *tableArr;
	
	IBOutlet UILabel *labelType;
	IBOutlet UILabel *labelGroup;
	Boolean selectType_1;
	NSString *selectGroup;
	IBOutlet UILabel *labelCountMessage;
	IBOutlet UIScrollView *srollView;
	IBOutlet UIImageView *imageView;
}

@property(nonatomic, retain) NSArray *groups;

-(IBAction)showSMSWarningDialog;
-(IBAction)BackGroundTap;
- (IBAction)GetContact;
-(IBAction)GetAlertType;
- (IBAction)CancelButtonPress;
- (IBAction)CallEmergencePhone;
- (void)exitapp;

@end
