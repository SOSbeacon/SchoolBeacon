//
//  GmailContactListViewController.h
//  SOSBEACON
//
//  Created by hung le on 7/12/11.
//  Copyright 2011 CNCSoft. All rights reserved.
//

#import <UIKit/UIKit.h>
//#import"SOSBEACONAppDelegate.h"

@interface ContactListViewController : UIViewController {
	IBOutlet UITableView *table;
	NSMutableArray *contactList;
	NSMutableArray *selectedIndexList;
	NSMutableArray *selectedEmail;
	NSInteger flag;
//	SOSBEACONAppDelegate *appDelegate;

}
@property (nonatomic, retain) NSMutableArray *contactList;
@end
