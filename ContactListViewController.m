//
//  GmailContactListViewController.m
//  SOSBEACON
//
//  Created by hung le on 7/12/11.
//  Copyright 2011 CNCSoft. All rights reserved.
//

#import "ContactListViewController.h"
#import "Personal.h"
#import "TableGroup.h"
#import "SOSBEACONAppDelegate.h"

@implementation ContactListViewController
@synthesize contactList;

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
	selectedEmail = [[NSMutableArray alloc] init];
	selectedIndexList = [[NSMutableArray alloc] init];
//	appDelegate = (SOSBEACONAppDelegate *)[[UIApplication sharedApplication] delegate];
	UIBarButtonItem *saveButton = [[UIBarButtonItem alloc] initWithTitle:@"Import" style:UIBarButtonItemStyleBordered target:self action:@selector(save)];
	self.navigationItem.rightBarButtonItem = saveButton;
	[saveButton release];
    [super viewDidLoad];
	
	if ([contactList  count]== 0)
	{

	UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:@"Your contact record is empty or has no phone number or email adress" 
												  delegate:self
										 cancelButtonTitle:@"Ok"
										 otherButtonTitles:nil];
	[alert show];
	[alert release];
	}
	
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
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


- (void)dealloc {
	[contactList release];
	[selectedEmail release];
	[selectedIndexList release];
    [super dealloc];
}

#pragma mark UITableViewDelegate
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	return [contactList count];
}

- (UITableViewCell*)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
	static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:CellIdentifier] autorelease];
    }
	
	[cell setAccessoryType:UITableViewCellAccessoryNone];
	for (int i = 0; i < [selectedIndexList count]; i++) {
		if (indexPath == [selectedIndexList objectAtIndex:i]) {
			[cell setAccessoryType:UITableViewCellAccessoryCheckmark];
		}
	}
	cell.selectionStyle = UITableViewCellSelectionStyleNone;
    cell.textLabel.text = [[contactList objectAtIndex:indexPath.row] objectForKey:@"email"];
	cell.detailTextLabel.text = [NSString stringWithFormat:@"%@  %@",[[contactList objectAtIndex:indexPath.row] objectForKey:@"name"],[[contactList objectAtIndex:indexPath.row] objectForKey:@"phone"]];
    return cell;
}
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
	UITableViewCell *cell = [tableView cellForRowAtIndexPath:indexPath];
	for (int i = 0; i < [selectedIndexList count]; i++) {
		if (indexPath == [selectedIndexList objectAtIndex:i]) {
			[cell setAccessoryType:UITableViewCellAccessoryNone];
			[selectedIndexList removeObject:indexPath];
			return;
		}
	}
	[selectedIndexList addObject:indexPath];
	[cell setAccessoryType:UITableViewCellAccessoryCheckmark];
}

#pragma mark -
#pragma mark Action
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	TableGroup *tableGroup = [[self.navigationController viewControllers] objectAtIndex:1];

	if (flag == 1) 
	{
		flag = 10;
		/*
			if (buttonIndex == 0)
			{
				[self.navigationController popToViewController:tableGroup animated:YES];

			}
		 */
	}
	else
	if(flag == 2)
	{
		flag = 10;
		if (buttonIndex == 0)
		{
			for (int i = 0; i < [selectedIndexList count]; i++) 
			{
				NSInteger index = [[selectedIndexList objectAtIndex:i] row];
				[selectedEmail addObject:[contactList objectAtIndex:index]];
				Personal *aPersonal = [[Personal alloc] init];
				aPersonal.contactName = [[contactList objectAtIndex:index] objectForKey:@"name"];
				aPersonal.email = [[contactList objectAtIndex:index] objectForKey:@"email"];
				aPersonal.voidphone = [[contactList objectAtIndex:index] objectForKey:@"phone"];
				aPersonal.textphone = @"";
				
				aPersonal.status = CONTACT_STATUS_NEW;
				[tableGroup.arrayContacts addObject:aPersonal];
				tableGroup.isEdited = YES;
				SOSBEACONAppDelegate *appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
				appDelegate.saveContact = YES;
				[tableGroup.tableView reloadData];
				//[self.navigationController popViewControllerAnimated:YES];
				[self.navigationController popToViewController:tableGroup animated:YES];
				[aPersonal release];

			}
		//	NSLog(@"%@",selectedEmail);
		}
		else 
		if(buttonIndex == 1)
		{
			[self.navigationController popToViewController:tableGroup animated:YES];

		}

	}

}

- (void)save {
	TableGroup *tableGroup = [[self.navigationController viewControllers] objectAtIndex:1];
	//NSLog(@"alo");
	if ([selectedIndexList count]== 0) 
	{
		flag = 1;
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"SelectContact",@"") 
											delegate:self
											 cancelButtonTitle:@"Yes"
											 otherButtonTitles:nil];
		[alert show];
		[alert release];
	}
	else
	{
		flag =2;
		NSString *mesage =[NSString stringWithFormat:@"Are you sure you want to import these contact to %@",tableGroup.groupName];
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:mesage
													  delegate:self
											 cancelButtonTitle:@"Yes"
											 otherButtonTitles:@"No",nil];
		[alert show];
		[alert release];
	}
	/*
	for (int i = 0; i < [selectedIndexList count]; i++) 
	{
		NSInteger index = [[selectedIndexList objectAtIndex:i] row];
		[selectedEmail addObject:[contactList objectAtIndex:index]];
		Personal *aPersonal = [[Personal alloc] init];
		aPersonal.contactName = [[contactList objectAtIndex:index] objectForKey:@"name"];
		aPersonal.email = [[contactList objectAtIndex:index] objectForKey:@"email"];
		aPersonal.voidphone = [[contactList objectAtIndex:index] objectForKey:@"phone"];
		aPersonal.textphone = @"";
		
		aPersonal.status = CONTACT_STATUS_NEW;
		[tableGroup.arrayContacts addObject:aPersonal];
		tableGroup.isEdited = YES;
		SOSBEACONAppDelegate *appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
		appDelegate.saveContact = YES;
		[tableGroup.tableView reloadData];
		//[self.navigationController popViewControllerAnimated:YES];
		[self.navigationController popToViewController:tableGroup animated:YES];
	}
	NSLog(@"%@",selectedEmail);
	 */
}

@end
