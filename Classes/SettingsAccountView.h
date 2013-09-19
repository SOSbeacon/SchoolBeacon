//
//  SettingsAccountView.h
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/18/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "RestConnection.h"
#import "SOSBEACONAppDelegate.h"

@interface SettingsAccountView : UIViewController <RestConnectionDelegate,UITextFieldDelegate>{
	BOOL save;
	BOOL isEdit;
    BOOL isPop;
	SOSBEACONAppDelegate *appDelegate;
	NSInteger flagalert;
}
@property (nonatomic,retain) RestConnection *rest;
@property(retain, nonatomic) IBOutlet UITextField *txtuserName;
@property(retain, nonatomic) IBOutlet UITextField *txtEmail;
@property(retain, nonatomic) IBOutlet UITextField *txtPhoneNumber;
@property(retain, nonatomic) IBOutlet UITextField *txtPassword;
@property (nonatomic,retain) IBOutlet UIActivityIndicatorView *actSetting;
@property (nonatomic, retain) IBOutlet UIBarButtonItem *btnSave;
-(IBAction)cannotEditPhone;
-(void)loadData;
- (IBAction)backgroundTap:(id)sender;
-(void)save;
@end
