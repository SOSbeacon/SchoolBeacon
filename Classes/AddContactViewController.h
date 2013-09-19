//
//  AddContactViewController.h
//  SOSBEACON
//
//  Created by hung le on 7/11/11.
//  Copyright 2011 CNCSoft. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "GDataFeedContact.h"
#import "GDataFeedContactGroup.h"
#import "GData.h"


@interface AddContactViewController : UIViewController <UITableViewDelegate, UITableViewDataSource>{
	IBOutlet UITableView *tableView;
	IBOutlet UIActivityIndicatorView *indicator;
	NSInteger groupID;
	NSString *groupName;
	
	GDataFeedContact *mContactFeed;
	GDataServiceTicket *mContactFetchTicket;
	NSError *mContactFetchError;
	GDataFeedContactGroup *mGroupFeed;
	
	NSString *username;
	NSString *password;
	BOOL gettingContact;
}
@property (nonatomic, assign) NSInteger groupID;
@property (nonatomic, retain) NSString *groupName;
@property (nonatomic, assign) BOOL gettingContact;
@property (nonatomic, retain) UIActivityIndicatorView *indicator;

- (void)getContacts;
- (void)getGroups;
@end
