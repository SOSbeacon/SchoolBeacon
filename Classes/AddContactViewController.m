//
//  AddContactViewController.m
//  SOSBEACON
//
//  Created by hung le on 7/11/11.
//  Copyright 2011 CNCSoft. All rights reserved.
//

#import "AddContactViewController.h"
#import "TablePersonalDetail.h"
#import "GDataContacts.h"
#import "GDataServiceGoogle.h"
#import "GDataServiceGoogleContact.h"
#import "GDataName.h"
#import "ContactListViewController.h"
#import "SOSBEACONAppDelegate.h"


@implementation AddContactViewController
@synthesize groupID, groupName, gettingContact, indicator;

// The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
/*
 - (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
 self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
 if (self) {
 // Custom initialization.
 }
 return self;
 }
 */


 // Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
 - (void)viewDidLoad {
	 
	 indicator.hidden = YES;
	 gettingContact = NO;
	 username = [[NSString alloc] init];
	 password = [[NSString alloc] init];
	 [super viewDidLoad];
 }
 
/*
 // Override to allow orientations other than the default portrait orientation.
 - (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
 // Return YES for supported orientations.
 return (interfaceOrientation == UIInterfaceOrientationPortrait);
 }
 */

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc. that aren't in use.
}

- (void)viewDidUnload {
    [super viewDidUnload];
	tableView = nil;
	indicator = nil;
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


- (void)dealloc {
	[indicator release];
	[tableView release];
	[groupName release];
	[username release];
	[password release];
    [super dealloc];
}

#pragma mark UITableViewDelegate
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	return 3;
}

- (UITableViewCell*)tableView:(UITableView *)tableView1 cellForRowAtIndexPath:(NSIndexPath *)indexPath {
	static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
    }
    switch (indexPath.row) {
		case 0:
			cell.textLabel.text = @"Enter contact";
			break;
		case 1:
			cell.textLabel.text = @"Import Google contacts";
			break;
		case 2:
			cell.textLabel.text = @"Import Yahoo contacts";
			break;
		default:
			break;
	}
    cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
    return cell;
}
- (void)tableView:(UITableView *)tableView1 didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
	[tableView1 deselectRowAtIndexPath:indexPath animated:NO];
	if (!gettingContact) {
		switch (indexPath.row) {
			case 0:{
				gettingContact = NO;
				TablePersonalDetail *personalDetail = [[TablePersonalDetail alloc] initWithStyle:UITableViewStyleGrouped];
				personalDetail.title = @"Add Contact";
				personalDetail.tableGroupDetail=[[self.navigationController viewControllers] objectAtIndex:1];
				personalDetail.groupID=groupID;
                NSLog(@"personalDetail.groupName AddcontactView");
				//personalDetail.groupName = [[NSString alloc] initWithString:groupName];
                personalDetail.groupName = groupName;
				personalDetail.personal = nil;
				personalDetail.personalIndex = 0;
				[self.navigationController pushViewController:personalDetail animated:YES];
				[personalDetail release];
			}
				break;
			case 1:
			{
				UITextField *textField;
				UITextField *textField2;
				
				UIAlertView *prompt = [[UIAlertView alloc] initWithTitle:@"Gmail Account." 
																 message:@"\n\n\n" // IMPORTANT
																delegate:self 
													   cancelButtonTitle:@"Cancel" 
													   otherButtonTitles:@"Login", nil];
				
				textField = [[UITextField alloc] initWithFrame:CGRectMake(12.0, 50.0, 260.0, 25.0)]; 
				[textField setBackgroundColor:[UIColor whiteColor]];
				[textField setPlaceholder:@"username"];
				[prompt addSubview:textField];
				
				textField2 = [[UITextField alloc] initWithFrame:CGRectMake(12.0, 85.0, 260.0, 25.0)]; 
				[textField2 setBackgroundColor:[UIColor whiteColor]];
				[textField2 setPlaceholder:@"password"];
				[textField2 setSecureTextEntry:YES];
				[prompt addSubview:textField2];
				
				// set place
				[prompt setTransform:CGAffineTransformMakeTranslation(0.0, 0.0)];
				[prompt show];
				[prompt release];
				
				// set cursor and show keyboard
				[textField becomeFirstResponder];
				[textField release];
				[textField2 release];
			}
				break;
			case 2:
			{
				gettingContact = YES;
				indicator.hidden = NO;
				[indicator startAnimating];
				SOSBEACONAppDelegate *appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
				[appDelegate handlePostLaunch];
			}
				break;
				
			default:
				break;
		}
	}
}

#pragma mark -
#pragma mark UIAlertViewDelegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
	if (buttonIndex == 1) {
		gettingContact = YES;
		indicator.hidden = NO;
		[indicator startAnimating];
		[username release];
		if ([[[alertView subviews] objectAtIndex:5] text] == nil) {
			username = [[NSString alloc] initWithString:@""];
		}
		else {
			username = [[NSString alloc] initWithString:[[[alertView subviews] objectAtIndex:5] text]];
			if ([username rangeOfString:@"@"].location == NSNotFound) {
				// if no domain was supplied, add @gmail.com
				username = [[username stringByAppendingString:@"@gmail.com"] retain];
			}
		}
		
		[password release];
		if ([[[alertView subviews] objectAtIndex:6] text] == nil) {
			password = [[NSString alloc] initWithString:@""];
		}
		else {
			password = [[NSString alloc] initWithString:[[[alertView subviews] objectAtIndex:6] text]];
		}
		[self getContacts];
	}
}

#pragma mark -
#pragma mark GData
- (void)getGroups {
	static GDataServiceGoogleContact* service = nil;
	
	if (!service) {
		service = [[GDataServiceGoogleContact alloc] init];
		
		[service setShouldCacheDatedData:YES];
		[service setServiceShouldFollowNextLinks:YES];
	}
	
	[service setUserCredentialsWithUsername:username
								   password:password];
	//GDataServiceTicket *ticket;
	NSURL *feedURL = [GDataServiceGoogleContact groupFeedURLForUserID:kGDataServiceDefaultUser];
	GDataQueryContact *query = [GDataQueryContact contactQueryWithFeedURL:feedURL];
	[query setShouldShowDeleted:NO];
	[query setMaxResults:2000];
	
	/*
	 ticket = [service fetchFeedWithQuery:query
								delegate:self
					   didFinishSelector:@selector(groupsFetchTicket:finishedWithFeed:error:)];
	 */
	[service fetchFeedWithQuery:query
					   delegate:self
			  didFinishSelector:@selector(groupsFetchTicket:finishedWithFeed:error:)];
}

- (void)getContacts {
	static GDataServiceGoogleContact* service = nil;
	
	if (!service) {
		service = [[GDataServiceGoogleContact alloc] init];
		
		[service setShouldCacheDatedData:YES];
		[service setServiceShouldFollowNextLinks:YES];
	}
	[service setUserCredentialsWithUsername:username
								   password:password];
	//GDataServiceTicket *ticket;
	NSURL *feedURL = [GDataServiceGoogleContact contactFeedURLForUserID:kGDataServiceDefaultUser];
	GDataQueryContact *query = [GDataQueryContact contactQueryWithFeedURL:feedURL];
	[query setShouldShowDeleted:NO];
	[query setMaxResults:2000];
	
	GDataFeedContactGroup *groupFeed = mGroupFeed;
    GDataEntryContactGroup *myContactsGroup
	= [groupFeed entryForSystemGroupID:kGDataSystemGroupIDMyContacts];
	
    NSString *myContactsGroupID = [myContactsGroup identifier];
    [query setGroupIdentifier:myContactsGroupID];
	
	/*/ sua leak
	ticket = [service fetchFeedWithQuery:query
								delegate:self
					   didFinishSelector:@selector(contactsFetchTicket:finishedWithFeed:error:)];
	 */
	[service fetchFeedWithQuery:query
					   delegate:self
			  didFinishSelector:@selector(contactsFetchTicket:finishedWithFeed:error:)];
	
}
- (void)groupsFetchTicket:(GDataServiceTicket *)ticket
         finishedWithFeed:(GDataFeedContactGroup *)feed
                    error:(NSError *)error {
	[mGroupFeed autorelease];
	mGroupFeed = [feed retain];
	[self getContacts];
}


- (void)contactsFetchTicket:(GDataServiceTicket *)ticket
           finishedWithFeed:(GDataFeedContact *)feed
                      error:(NSError *)error {
	gettingContact = NO;
	indicator.hidden = YES;
	NSArray *arr = [feed entries];
	if ([arr count] == 0) {
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Message" message:@"Wrong username or password, please try again!" delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show];
		[alert release];
	}
	else {
		NSMutableArray *array = [[NSMutableArray alloc] init];
		//NSLog(@"%@",[arr objectAtIndex:30]);
		for (int i =0; i < [arr count]; i++) 
		{
			NSMutableDictionary *dic = [[NSMutableDictionary alloc] init];
			//email
			if ([(GDataEmail*)[[(GDataEntryContact*)[arr objectAtIndex:i] emailAddresses] objectAtIndex:0] address] != nil) {
				[dic setObject:[(GDataEmail*)[[(GDataEntryContact*)[arr objectAtIndex:i] emailAddresses] objectAtIndex:0] address] forKey:@"email"];
			}
			else {
				//[dic setObject:@"" forKey:@"email"];
				[dic release];
				break;
			}

			//fullname
			if ([(GDataName*)[(GDataEntryContact*)[arr objectAtIndex:i] name] fullName] != nil) {
				[dic setObject:[[(GDataName*)[(GDataEntryContact*)[arr objectAtIndex:i] name] fullName] stringValue] forKey:@"name"];
			}
			else {
				[dic setObject:@"" forKey:@"name"];
			}
			//phone
			if ([[(GDataEntryContact*)[arr objectAtIndex:i] phoneNumbers] objectAtIndex:0] != nil) {
				[dic setObject:[[[(GDataEntryContact*)[arr objectAtIndex:i] phoneNumbers] objectAtIndex:0] stringValue] forKey:@"phone"];
			}
			else {
				[dic setObject:@"" forKey:@"phone"];
			}

			//[dic setObject:[(GDataEmail*)[[(GDataEntryContact*)[arr objectAtIndex:i] phoneNumbers] objectAtIndex:0] address] forKey:@"name"];
			[array addObject:dic];
			
			[dic release];
		}
		
		ContactListViewController *gmailContactList = [[ContactListViewController alloc] init];
        NSMutableArray *contactArray = [[NSMutableArray alloc] initWithArray:array];
		//gmailContactList.contactList = [[NSMutableArray alloc] initWithArray:array];
        gmailContactList.contactList = contactArray;
		gmailContactList.title = @"Gmail Contacts";
		[self.navigationController pushViewController:gmailContactList animated:YES];
		[array release];
        [contactArray release];
		[gmailContactList release];
	}
}
@end
