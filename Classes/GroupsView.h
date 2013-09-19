//
//  GroupsView.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 08/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SOSBEACONAppDelegate.h"
@class TableGroups;
@class TablePersonalDetail;

@interface GroupsView : UIViewController{
	UINavigationController *navController;
	TableGroups *tableGroups;
	TablePersonalDetail *tblPersonalDetail;
	SOSBEACONAppDelegate *appDelegate;
}

@property (nonatomic, retain) IBOutlet UINavigationController *navController;
@property (nonatomic, retain) IBOutlet TableGroups *tableGroups;
@property (nonatomic, retain) IBOutlet TablePersonalDetail *tblPersonalDetail;
-(IBAction)addGroup:(id)sender;

@end
