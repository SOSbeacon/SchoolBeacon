//
//  HomeView.h
//  SOSBEACON
//
//  Created by cncsoft on 7/30/10.
//  Copyright 2010 CNC. All rights reserved.
//	

#import <UIKit/UIKit.h>
#import "RestConnection.h"
#import "SlideToCancelViewController.h"
#import "SOSBEACONAppDelegate.h"
#import "Uploader.h"
#import "TNHRequestHelper.h"

typedef enum {ActionType_None=0,ActionType_OK=1,ActionType_Help=2}ActionType;
@interface HomeView : UIViewController<SlideToCancelDelegate,UINavigationControllerDelegate,RestConnectionDelegate,UploaderDelegate,UIActionSheetDelegate, UIScrollViewDelegate, UIAlertViewDelegate, ASIHTTPRequestDelegate> {
	RestConnection *rest;
	SOSBEACONAppDelegate *appDelegate;	
	
	SlideToCancelViewController *slideToCancel;
	SlideToCancelViewController *slideToCancel2;
	SlideToCancelViewController *slideToCancel3;
	IBOutlet UIActivityIndicatorView  *actAlert;		
	NSInteger loadIndex;
	
	ActionType currentAction;
	BOOL isSendOK;
	NSInteger flag;
	NSInteger flagforAlert;
	NSInteger newflag1;
	NSTimer *countDownTimer;
    BOOL attachMedia;
}

@property (retain, nonatomic) IBOutlet UIScrollView *_scrollView;
@property (retain, nonatomic) IBOutlet UITextView *shortMessageTextView;
@property (retain, nonatomic) IBOutlet UITextView *longMessageTextView;
@property (retain, nonatomic) IBOutlet UILabel *characterRemainLabel;
@property (retain, nonatomic) IBOutlet UILabel *broadcastTypeLabel;
@property (retain, nonatomic) IBOutlet UILabel *broadcastGroupLabel;
@property (retain, nonatomic) IBOutlet UITableView *_previewTable;
@property (retain, nonatomic) IBOutlet UIView *preview;
@property (retain, nonatomic) NSArray *contacts;
@property (retain, nonatomic) IBOutlet UIBarButtonItem *closeBtn;

@property (nonatomic, retain) RestConnection *rest;
- (void)callPanic;
- (void)requestUploadIdFinish:(NSInteger)uploadId;
-(IBAction)showImNeedHelpMenu:(id)sender;
-(IBAction)showImOKMenu:(id)sender;

-(IBAction)btnCheckIn_Tapped:(id)sender;
-(IBAction)btnCheckInGroup_Tapped:(id)sender;
-(IBAction)btnSendAlert_Tapped:(id)sender;
-(IBAction)btnEmergencyPhone_Tapped:(id)sender;
-(IBAction)btnCancelImOk_Tapped:(id)sender;
-(IBAction)btnCancelNeedHelp_Tapped:(id)sender;
-(void)showUIView:(UIView*)theView;
-(void)dismissUIView:(UIView*)theView;
-(void)doCheckIn;
-(void)doEmercgenyCall;
-(void)doAlert;
-(void)doImOkCheckIn;
-(void)showvideo;
-(void)timerTick;
-(void)hideAllUIView;

- (IBAction)backgroundTapped:(id)sender;
- (IBAction)chooseBroadcastType:(id)sender;
- (IBAction)chooseBroadCastGroup:(id)sender;
- (IBAction)previewBroadcast:(id)sender;
- (IBAction)sendBroadcast:(id)sender;
- (IBAction)cancelBroadcast:(id)sender;
- (IBAction)closePreview:(id)sender;

@end
