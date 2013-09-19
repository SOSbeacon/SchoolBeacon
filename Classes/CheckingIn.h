//
//  CheckingIn.h
//  SOSBEACON
//
//  Created by cncsoft on 9/10/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "HomeView.h"
#import "RestConnection.h"
#import "SOSBEACONAppDelegate.h"
#import <AddressBookUI/AddressBookUI.h>
#import "Uploader.h"
#import "SlideToCancelViewController.h"

#define CK_CheckingIn @"checkingIn"

@interface CheckingIn : UIViewController <UITableViewDelegate,UITableViewDataSource,SlideToCancelDelegate,ABPeoplePickerNavigationControllerDelegate,UITextViewDelegate,RestConnectionDelegate,UIActionSheetDelegate,UploaderDelegate>{
	SOSBEACONAppDelegate *appDelegate;
	RestConnection *restConnection;
	ABPeoplePickerNavigationController *picker;
	SlideToCancelViewController *slideCheckin;
	NSInteger captureSetting;
	
	UILabel *lblContact;
	IBOutlet UILabel *lblGetMessage;
	NSInteger editIndex;
	UITextView *tvTextMessage;
	UIScrollView *scrollView;
	
	IBOutlet UIButton *btnGetcontact;
	IBOutlet UIButton *btnGetMessage;
	IBOutlet UIButton *btnCheck;
	IBOutlet UIButton *btnCancel;
	
	
	IBOutlet UIActivityIndicatorView *actChecking;
	IBOutlet UILabel *lblStatusUpload;
	NSMutableArray *array;
	NSMutableArray *arrayPhoto;
	NSInteger loadIndex;
	NSMutableArray *arrayCheckin;
	UIView *messageBackground;
	UITableView *tableViewCheckIn;
	NSMutableArray *countArray;
	NSInteger flag;
	NSMutableArray *groupArray;
	NSMutableArray *typeArray;
	UIActionSheet *actionSheet1;
	//NSInteger newFlag;
	BOOL isSendWithAudio; 
    NSString *familyGroupId;
	IBOutlet UILabel *labelCountMessage;
}
@property(nonatomic,retain) NSMutableArray *groupArray;
@property(nonatomic,retain) NSMutableArray *typeArray;
@property(nonatomic,retain) UIActionSheet *actionSheet1;
@property(nonatomic, retain)NSMutableArray *countArray;
@property(nonatomic, retain)	NSMutableArray *arrayCheckin;
@property (nonatomic, retain) IBOutlet UIScrollView *scrollView;
@property (nonatomic, retain) IBOutlet UITextView *tvTextMessage;
@property (nonatomic, retain) IBOutlet UILabel *lblContact;
@property (nonatomic, retain) RestConnection *restConnection;

-(IBAction)cancelAlert:(id)sender;
- (IBAction)GetContact;
- (IBAction)backgroundTap:(id)sender;
- (IBAction)textFieldDoneEditing:(id)sender;
- (IBAction)CheckingNow;
- (IBAction)ClearMessage;
- (void)sendCheckin;
- (IBAction)getMessageCheckIn;
- (void)requestUploadIdFinish:(NSInteger)uploadId;
-(void)sendAudioImage;
-(void)checkingInNow;
- (void)saveLastGroup;
@end
