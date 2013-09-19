//
//  GroupsView.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 08/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import "GroupsView.h"
#import "SOSBEACONAppDelegate.h"
#import "TableGroups.h"
#import "TablePersonalDetail.h"

@implementation GroupsView
@synthesize navController;
@synthesize tableGroups,tblPersonalDetail;


-(IBAction)addGroup:(id)sender
{
}

- (void)viewDidLoad {
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
    [super viewDidLoad];
	[self.view addSubview:navController.view];
	navController.view.frame = CGRectMake(0, 0, 320, 480);
    if (IS_IPHONE_5) {
        navController.view.frame = CGRectMake(0, 0, 320, 568);
    }
}

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
	if(appDelegate.logout){
		appDelegate.logout=NO;
		[navController popToRootViewControllerAnimated:YES];
		[tableGroups getData];
	}
	//[tableGroups getData];

	
}


- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


- (void)dealloc {
	[navController release];
	[tableGroups release];
    [super dealloc];
}


@end
