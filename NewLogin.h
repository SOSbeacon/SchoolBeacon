//
//  NewLogin.h
//  SOSBEACON
//
//  Created by bon on 7/27/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "RestConnection.h"
#import "SOSBEACONAppDelegate.h"
#import "VideoViewController.h"
#import "TNHRequestHelper.h"

@interface NewLogin : UIViewController  <RestConnectionDelegate, UIAlertViewDelegate, ASIHTTPRequestDelegate, UIActionSheetDelegate> {
	IBOutlet UITextField    *emailTextField;
    IBOutlet UITextField    *passwordTextField;
	IBOutlet UIButton       *submit_button;
	IBOutlet UIButton       *cancel_button;
	IBOutlet UIActivityIndicatorView *actSignup;
	
	
	NSInteger   flagForRequest;
	NSString   *strImei;
	NSString   *strEmail;
    NSString   *strPassword;
    NSString   *strSchoolId;
	RestConnection          *restConnection;
	SOSBEACONAppDelegate    *appDelegate;
	VideoViewController     *video;    
	NSInteger   flagForAlert;
	NSString   *token;
	NSInteger   flag;
	NSInteger   contactCount;
	NSTimer    *countDownTimer;
	NSInteger   countTime;
	NSString   *activMessage;
	NSInteger   countActive;
	UIAlertView *alert1;
	BOOL        isDissmiss;
    
    NSMutableArray *_schools;
}
@property(nonatomic,retain) UITextField   *emailTextField;
@property(nonatomic,retain) UITextField   *passwordTextField;
@property(nonatomic,retain) UIButton      *submit_button;
@property(nonatomic,retain) UIButton      *cancel_button;
@property(nonatomic,retain) VideoViewController *video;
@property(nonatomic,retain) RestConnection *restConnection;
@property(nonatomic,retain) NSString *token;

@property(nonatomic)NSInteger flag;
@property(nonatomic)NSInteger flagForAlert;
@property(nonatomic)NSInteger flagForRequest;

@property(nonatomic,retain)NSString *strImei;
@property(nonatomic,retain)NSString *strEmail;
@property(nonatomic,retain)NSString *strPassword;
@property(nonatomic,retain)NSString *strSchoolId;

@property(nonatomic,retain)NSMutableArray *schools;

-(void)timerTick;
-(void)process;
-(IBAction)cancelButtonPress:(id)sender;
-(IBAction)submitButtonPress:(id)sender;
-(IBAction)backgroundTap:(id)sender;
-(void)getdata;
-(void)DimisAlertView1;
-(void)showLoading;

@end
