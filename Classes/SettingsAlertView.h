//
//  SettingsAlertView.h
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/18/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "RestConnection.h"
#import "SOSBEACONAppDelegate.h"
#import "ValidateData.h"
@interface SettingsAlertView : UIViewController<RestConnectionDelegate,UITextFieldDelegate,UIAlertViewDelegate,UIActionSheetDelegate> {
	NSInteger editIndex;
	BOOL save;
	BOOL isEdit;
    BOOL isPop;
	SOSBEACONAppDelegate *appDelegate;
	NSString *selectAlert;
	NSInteger flag;
	UIActionSheet *actionSheet3;
	NSMutableArray *groupArray;
	NSMutableArray *typeArray;
	IBOutlet UIScrollView *scoll;
	NSInteger flagalert;
}
@property(nonatomic, retain)UIScrollView *scoll;
@property(nonatomic, retain)NSMutableArray *groupArray;
@property(nonatomic, retain)NSMutableArray *typeArray;
@property(nonatomic, retain)UIActionSheet *actionSheet3;
@property (nonatomic)NSInteger flag;
@property (nonatomic,retain) RestConnection *rest;
@property (nonatomic, retain) IBOutlet UISwitch *incomingGovernment;
@property (nonatomic,retain) IBOutlet UITextField *txtPanicPhone;
@property (nonatomic,retain) IBOutlet UILabel *lblSendToAlert;
@property (nonatomic,retain) IBOutlet UILabel *voiceRecord;
@property (nonatomic, retain) IBOutlet UIBarButtonItem *btnSave;
@property (nonatomic,retain) IBOutlet UIActivityIndicatorView *actSetting;
@property (retain, nonatomic) NSString *recordDuration;
@property (retain, nonatomic) NSString *defaultGroupId;

- (IBAction) LoadWebView;
- (IBAction)sendToAlert ;
- (IBAction)choicesRecordingDuration;

-(void)loadData;
- (IBAction)backgroundTap:(id)sender ;
- (IBAction)textFieldDoneEditing:(id)sender ;
- (void) DimisAlertView:(UIAlertView*)alertView;
-(void)save;
@end
