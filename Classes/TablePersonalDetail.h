//
//  TablePersonalDetail.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 16/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <AddressBookUI/AddressBookUI.h>
#import "RestConnection.h"
#import "SOSBEACONAppDelegate.h"

@class Personal;
@class TableGroup;

#define kNameRowIndex 0
#define kEmailRowIndex 1
#define kPhoneRowIndex 2
#define kTextPhoneRowIndex 3
#define kGroupIndex 4
#define kNumberOfRow 4

@interface TablePersonalDetail : UITableViewController <UITextFieldDelegate,
ABPeoplePickerNavigationControllerDelegate,RestConnectionDelegate,UIAlertViewDelegate>
{
	SOSBEACONAppDelegate *appDelegate;
	RestConnection *restConnection;
	Personal *personal;
	Personal *personalTemp;
	TableGroup *tableGroupDetail;
	
	NSArray *fieldLabels;
	UITextField *textFieldBeingEdited;
	NSInteger personalIndex;
	NSInteger groupID;
	NSString *groupName;
	UIActivityIndicatorView *actContact;
	UIBarButtonItem *saveButton;
	BOOL isEdited;
	
	BOOL isAddContact;

}

@property (nonatomic, retain) RestConnection *restConnection;
@property (nonatomic, retain) TableGroup *tableGroupDetail;
@property (nonatomic, retain) Personal *personal;
@property (nonatomic, retain) Personal *personalTemp;
@property (nonatomic, retain) NSArray *fieldLabels;
@property (nonatomic, retain) UITextField *textFieldBeingEdited;

@property (nonatomic) NSInteger personalIndex;
@property (nonatomic) NSInteger groupID;
@property (nonatomic, retain) NSString *groupName;



- (IBAction)cancel:(id)sender;
- (IBAction)save:(id)sender;
- (void)checkEditContact;
- (void)saveIfOutTab;

@end
