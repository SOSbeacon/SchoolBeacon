//
//  TableGroups.h
//  SOSBEACON
//
//  Created by cncsoft on 6/24/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "RestConnection.h"
#import "TableGroup.h"
#import "SOSBEACONAppDelegate.h"

@interface TableGroups : UITableViewController<RestConnectionDelegate>{
	SOSBEACONAppDelegate *appDe;
	RestConnection *rest;
	NSMutableArray *arrayGroup;
	BOOL isEdit;
	NSIndexPath *index;
	TableGroup *tbGroup; 
	NSInteger loadIndex;
	UIActivityIndicatorView *actGroup;
	NSInteger flag;
	IBOutlet UITableView *tableView1;
	UITextField *textField;	
	NSInteger flagforAlert;
	NSInteger row;

}
@property(nonatomic) 	NSInteger flag;
@property (nonatomic, retain) NSIndexPath *index;
@property (nonatomic, retain) RestConnection *rest;
@property (nonatomic, retain) NSMutableArray *arrayGroup;
@property (nonatomic, retain) TableGroup *tbGroup; 

@property (nonatomic)BOOL isEdit;
-(IBAction)editButtonPress:(id)sender;
- (void)getData;
-(IBAction)addGroupButtonPress:(id)sender;
@end
