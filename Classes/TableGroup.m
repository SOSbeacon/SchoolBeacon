//
//  TableGroup.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 14/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import "TableGroup.h"
#import "TablePersonalDetail.h"
#import "Personal.h"
#import "SOSBEACONAppDelegate.h"
#import "RestConnection.h"
#import "GroupPersonal.h"
#import "ValidateData.h"
#import "StatusView.h"
//#import "AddContactViewController.h"


@implementation TableGroup
@synthesize arrayContacts;
@synthesize rest,groupID, groupName;
@synthesize selectRow;
@synthesize isUpdate,isEdited;
@synthesize parentController,personalDetail;

#pragma mark -
#pragma mark View lifecycle


- (void)viewDidLoad {
	///
	UIBarButtonItem *backButton = [[UIBarButtonItem alloc] 
								   initWithTitle:@"Back" style:UIBarButtonItemStylePlain target:nil action:nil];
	[self.navigationItem setBackBarButtonItem:backButton];
	[backButton release];
	///
    [super viewDidLoad];
	[self displayButtonAdd];
	arrayContacts = [[NSMutableArray alloc] init];
	rest = [[RestConnection alloc] initWithBaseURL:SERVER_URL];	
	rest.delegate=self;

	appDelegate = (SOSBEACONAppDelegate *)[[UIApplication sharedApplication] delegate];
	[rest getPath:[NSString stringWithFormat:@"/contacts?token=%@&groupId=%d&format=json",
				   appDelegate.apiKey,groupID ]withOptions:nil];
	isUpdate = YES;
	UIBarButtonItem *cancelButton = [[UIBarButtonItem alloc]
                                     initWithTitle:@"Back"
                                     style:UIBarButtonItemStylePlain
                                     target:self
                                     action:@selector(cancel:)];
    self.navigationItem.leftBarButtonItem = cancelButton;
    [cancelButton release];	
	
	actContact = [[UIActivityIndicatorView alloc] init];
	actContact.frame = CGRectMake(140, 130, 30, 30);
	actContact.activityIndicatorViewStyle = 2;
	actContact.hidden = NO;
	[actContact startAnimating];
	
	isUpdateToServer = NO;
	isEdited = NO;	
	appDelegate.saveContact = NO;
	//NSLog(@"TableGroup view");
}

- (void)viewDidUnload {
	
    // Relinquish ownership of anything that can be recreated in viewDidLoad or on demand.
    // For example: self.myOutlet = nil;
}

- (void)dealloc {
	[arrayContactsOrigin release];
	[personal1 release];
	[actContact release];
	[rest release];
	[arrayContacts release];
	[groupName release];
    [super dealloc];
}



#pragma mark -
#pragma mark Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
	[tableView addSubview:actContact];
    // Return the number of sections.	
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
    if(section==0) 
	return [arrayContacts count];
	else 
	return 1;
}
/*
-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
	return 64;
}
 */
// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
	
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:CellIdentifier] autorelease];
    }
    // Configure the cell...

	if(indexPath.section==0)
	{
		personal1 = [arrayContacts objectAtIndex:indexPath.row];
		cell.textLabel.text = personal1.contactName;
				
	}
	
	if (personal1.typeContact) {
		cell.textLabel.textColor = [UIColor grayColor];
	}
	///
	

  //  cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;

    return cell;
}

- (NSString *)tableView:(UITableView *)tableView titleForFooterInSection:(NSInteger)section {
	if (section == 0) {
		return @"Swipe contact to delete";
	}else {
		
		return @"";
	}

}

#pragma mark -
#pragma mark Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
	if(indexPath.section==0)
	{
		//appDelegate.flagforGroup = 1;
	//	NSLog(@" select contact da ton tai tren sever----------*****");
		personalDetail = [[TablePersonalDetail alloc] initWithStyle:UITableViewStyleGrouped];
		personalDetail.personal = [arrayContacts objectAtIndex:indexPath.row];
		personalDetail.title = @"Edit Contact";
		personalDetail.personalIndex = indexPath.row;
		personalDetail.tableGroupDetail = self;
		appDelegate.groupName = groupName;

		//personalDetail.groupName = [[NSString alloc] initWithString:groupName];
        personalDetail.groupName = groupName;

		[self.navigationController pushViewController:personalDetail animated:YES];
		[personalDetail release];
	}
	[tableView deselectRowAtIndexPath:indexPath animated:YES];
}


- (UITableViewCellEditingStyle)tableView:(UITableView *)tableView editingStyleForRowAtIndexPath:(NSIndexPath *)indexPath {
	personal1 = [arrayContacts objectAtIndex:indexPath.row];
	if (personal1.typeContact) {
		return UITableViewCellEditingStyleNone;
	}
	else {
		return UITableViewCellEditingStyleDelete;
	}
	
}

- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle 
forRowAtIndexPath:(NSIndexPath *)indexPath {
	personal1 = [arrayContacts objectAtIndex:indexPath.row];
    if (!personal1.typeContact) {
		NSUInteger row = [indexPath row];
		//Personal *person = [arrayContacts objectAtIndex:row];
		//NSLog(@"person  ===%@",person);
		[self.arrayContacts removeObjectAtIndex:row];
		[tableView deleteRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] 
						 withRowAnimation:UITableViewRowAnimationFade];
		
		isEdited = YES;
		appDelegate.saveContact = YES;
	}
	else 
	{
		return;
	}
}


-(void)displayButtonAdd{
	UIBarButtonItem *button = [[UIBarButtonItem alloc] initWithTitle:@"Add"
															   style:UIBarButtonItemStyleBordered 
															  target:self
															  action:@selector(addClick)];
	
	[self.navigationItem setRightBarButtonItem:button animated:YES];	
	[button release];
}
/*
-(void)addClick {
	/*personalDetail = [[TablePersonalDetail alloc] initWithStyle:UITableViewStyleGrouped];
	personalDetail.title = @"Add Contact";
	personalDetail.tableGroupDetail=self;
	personalDetail.groupID=groupID;
	[self.navigationController pushViewController:personalDetail animated:YES];
	[personalDetail release];//
	
	AddContactViewController *addContactViewController = [[AddContactViewController alloc] init];
	appDelegate.addContactViewController = addContactViewController;
	addContactViewController.title = @"Add Contact";
	addContactViewController.groupID = groupID;
    //NSLog(@"fix leak 15.10.2011");
	//addContactViewController.groupName = [[NSString alloc] initWithString:groupName];
    addContactViewController.groupName = groupName;

	[self.navigationController pushViewController:addContactViewController animated:YES];
	[addContactViewController release];
}*/

-(IBAction)cancel:(id)sender{
	appDelegate.saveContact = NO;
	[self checkEditContact];
}

- (void)checkEditContact {
	if (isEdited) {
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Contact" message:NSLocalizedString(@"ConfimChange",@"") 
													   delegate:self 
											  cancelButtonTitle:@"Yes"
											  otherButtonTitles:@"No",nil];
		[alert show];
		[alert release];
	}else {
		[self.navigationController popViewControllerAnimated:YES];
	}
	
}

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex{
	if (buttonIndex == 0) {
		

		[self saveContactToServer];
		
	}
	[self.navigationController popViewControllerAnimated:YES];	
}

- (void)saveContactToServer {
	isUpdateToServer = YES;
	[appDelegate.statusView showStatus:@"Contact to being updated..."];
	
	
	//add contact with status delete
	for(Personal *personOrigin in arrayContactsOrigin)
	{
		BOOL isContactDelete = YES;
		for(Personal *person in arrayContacts)
		{
			if(person.contactID == personOrigin.contactID)
			{
				isContactDelete = NO;
				break;
			}
		}
		
		if(isContactDelete)
		{
			personOrigin.status = CONTACT_STATUS_DELETED;
			[arrayContacts addObject:personOrigin];
		}
	}
	
	//remove contact normal
	int i=0;
	while (i<[arrayContacts count]) 
	{
		Personal *person = [arrayContacts objectAtIndex:i];
		
		if(person.status == CONTACT_STATUS_NORMAL)
		{
			[arrayContacts removeObjectAtIndex:i];
		}
		else if(person.typeContact==YES) {
			[arrayContacts removeObjectAtIndex:i];
		}
		else 
		{
			i++;
		}
		
	}
	
	[self updateFirstContact];
	
}

- (void)updateFirstContact {
	if([arrayContacts count]>0)
	{
		Personal *person = [arrayContacts objectAtIndex:0];
		if(person.status==CONTACT_STATUS_MODIFIED)
		{	
			requestServer = 1;
			//NSLog(@"Edit contact");
						


			NSArray *key = [NSArray arrayWithObjects:@"token",@"name",@"email",@"voicePhone",@"textPhone",nil];
			
			NSArray *obj = [NSArray arrayWithObjects:appDelegate.apiKey,person.contactName
							,person.email,person.voidphone,person.textphone,nil];
			
			NSDictionary *p = [NSDictionary dictionaryWithObjects:obj forKeys:key];
			[rest putPath:[NSString stringWithFormat:@"/contacts/%d?format=json",person.contactID] withOptions:p];
			
		}
		else if(person.status == CONTACT_STATUS_NEW)
		{
			requestServer = 2;
			//NSLog(@"Add contact");

			
			//send request add new contact
			NSArray *key = [NSArray arrayWithObjects:@"token",@"groupId",@"name",@"email",@"voicePhone",@"textPhone",nil];
			NSArray *obj = [NSArray arrayWithObjects:appDelegate.apiKey,[NSString stringWithFormat:@"%d",groupID],person.contactName
							,person.email,person.voidphone,person.textphone,nil];
			NSDictionary *p = [NSDictionary dictionaryWithObjects:obj forKeys:key];
			[rest postPath:@"/contacts?format=json" withOptions:p];
		}
		else if(person.status == CONTACT_STATUS_DELETED)
		{
			requestServer = 3;
			//NSLog(@"Delete contact");
			//send request delete contact
			[rest deletePath:[NSString stringWithFormat:@"/contacts/%d?token=%@&format=json",person.contactID,appDelegate.apiKey] withOptions:nil];
		}
	}
	else
	{
		[appDelegate.statusView setStatusTitle:@"Contact saved successfully."];
		[appDelegate.statusView performSelector:@selector(hideStatus) withObject:nil afterDelay:1];
		[self.navigationController popViewControllerAnimated:YES];
	}

}
- (void) DimisAlertView:(UIAlertView*)alertView {
	[alertView dismissWithClickedButtonIndex:0 animated:TRUE];
}

#pragma mark -
#pragma mark finishRequest

-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector{
	 
	//NSLog(@"array datal : %@",arrayData);
	
	actContact.hidden = YES;
	[actContact stopAnimating];
	
	if(isUpdateToServer)
	{
				
		[arrayContacts removeObjectAtIndex:0];
		
		[self updateFirstContact];
		return;
		
	}

	if (isUpdate) {
		if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"]) {
			NSDictionary *data = [[arrayData objectForKey:@"response"] objectForKey:@"data"];
			if(data)
			{
				for(NSString *personNameID in data)
				{
					personal1 = [[Personal alloc] init];
					personal1.contactID = [[[data objectForKey:personNameID] objectForKey:@"id"] intValue];
					personal1.contactName = [[data objectForKey:personNameID] objectForKey:@"name"];
					
					if (![[[data objectForKey:personNameID] objectForKey:@"textPhone"] isEqual:[NSNull null]])
					{
						personal1.textphone = [[data objectForKey:personNameID] objectForKey:@"textPhone"];
					}else {
						personal1.textphone = [[data objectForKey:personNameID] objectForKey:@""];
					}
					
					if (![[[data objectForKey:personNameID] objectForKey:@"email"] isEqual:[NSNull null]]) {
						personal1.email = [[data objectForKey:personNameID] objectForKey:@"email"];
					}else {
						personal1.email = [[data objectForKey:personNameID] objectForKey:@""];
					}
					
					if (![[[data objectForKey:personNameID] objectForKey:@"voicePhone"] isEqual:[NSNull null]])
					{
						personal1.voidphone = [[data objectForKey:personNameID] objectForKey:@"voicePhone"];
					}else {
						personal1.voidphone = [[data objectForKey:personNameID] objectForKey:@""];
					}
					
					if ([[[data objectForKey:personNameID] objectForKey:@"type"] intValue]==1) {
						
						personal1.typeContact = YES; 
						//NSLog(@"don't delete contact");
					}

					[arrayContacts addObject:personal1];
					[personal1 release]; //notify
					isUpdate=NO;
					
				}
				
				//copy to origin array
				arrayContactsOrigin = [[NSArray alloc] initWithArray:arrayContacts];
				
				[self.tableView reloadData];
			}
		}
		else {
			//NSLog(@"Error load contact");
		}

	}
}


-(void)cantConnection:(NSError *)error andRestConnection:(id)connector{
	actContact.hidden = YES;
	[actContact stopAnimating];
	alertView();
}

#pragma mark -
#pragma mark Memory management	

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Relinquish ownership any cached data, images, etc that aren't in use.
}

@end

