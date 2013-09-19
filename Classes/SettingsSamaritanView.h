//
//  SettingsSamaritanView.h
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/18/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SOSBEACONAppDelegate.h"
#import "RestConnection.h"
@interface SettingsSamaritanView : UIViewController<UIActionSheetDelegate,RestConnectionDelegate,UIAlertViewDelegate> {

	SOSBEACONAppDelegate *appDelegate;
	NSString *selectSamaritanRange;
	NSString *selectReciveRange;
	
	BOOL allowSendSamaritan;
	
	NSInteger editIndex;
	BOOL isEdit;
	BOOL save;
	IBOutlet UIScrollView *scoll;
	NSInteger flagalert;
	
}
@property(nonatomic, retain) UIScrollView *scoll;
@property (nonatomic,retain) RestConnection *rest;
@property (nonatomic, retain) IBOutlet UISwitch *samaritanStatus;
@property (nonatomic, retain) IBOutlet UISwitch *receiverSamaritan;
@property (nonatomic, retain) IBOutlet UIButton *receiveRangeStatus;
@property (nonatomic,retain) IBOutlet UIButton *btnRangeSamaritan;

@property (nonatomic,retain) IBOutlet UILabel *receiveRange;
@property (nonatomic,retain) IBOutlet UILabel *lblSamaritanRange;

@property (nonatomic, retain) IBOutlet UIBarButtonItem *btnSave;
@property (nonatomic,retain) IBOutlet UIActivityIndicatorView *actSetting;
- (void)delayAlert;
- (IBAction)ReceiverSamaritanSwitch:(id)sender;
- (IBAction)SamaritanSatusSwitch:(id)sender;

- (IBAction)rangeSatus:(id)sender;
- (IBAction)ReceiveRangeSamaritan:(id)sender;
-(void)save;
@end
