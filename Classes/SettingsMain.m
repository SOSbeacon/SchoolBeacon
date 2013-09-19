//
//  SettingsMain.m
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/18/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import "SettingsMain.h"
#import "SettingsStorageView.h"
#import "SettingsAccountView.h"
#import "SettingsAlertView.h"
#import "SettingsSamaritanView.h"
#import "SettingsAboutView.h"
#import "TellUs.h"
#import "EmailView.h"
@implementation SettingsMain
@synthesize mainTable;
@synthesize flag;

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	if (flag == 1) 
	{
		NSString *pass = [appDelegate.informationArray objectForKey:@"password"];
        //NSLog(@"read file");
		NSString *password =[[NSString alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER]];
        //NSString *password =[[NSString alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER] encoding:[NSString defaultCStringEncoding] error:nil];
		if ([password isEqualToString:@"1"] && ([pass length] == 0)) 
		{
			flag = 2;
			UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"Establish",@"")
														  delegate:self cancelButtonTitle:@"Yes"
												 otherButtonTitles:@"Not Now",nil];
			[alert show];
			[alert release];;
		}
		else
		if(appDelegate.contactCount == 0)
			{
				flag = 3;
				//NSLog(@"show alert");
				//NSLog(@" -----------------");
				UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"doYouWantAddcontact",@"")
															  delegate:self cancelButtonTitle:@"Yes"
													 otherButtonTitles:@"Not Now",nil];
				[alert show];
				[alert release];
				
			}
			else {
				appDelegate.flagSetting =4;
				appDelegate.tabBarController.selectedIndex =0;
			}
		[password release];
		
		
	}
	else 
		if (flag == 2) 
		{
			if (buttonIndex == 0) {
				
				UIViewController *detailViewController;
				detailViewController = [[SettingsAccountView alloc] initWithNibName:@"SettingsAccountView" bundle:nil];
				[self.navigationController pushViewController:detailViewController animated:YES];
				[detailViewController release];
				appDelegate.flagSetting =4;
			}
			else
				if(buttonIndex == 1)
				{
					if(appDelegate.contactCount == 0)
					{
						flag = 3;
					//	NSLog(@"show alert");
					//	NSLog(@" -----------------");
						UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"doYouWantAddcontact",@"")
																	  delegate:self cancelButtonTitle:@"Yes"
															 otherButtonTitles:@"Not Now",nil];
						[alert show];
						[alert release];
						
					}
					else {
						appDelegate.flagSetting =4;
						appDelegate.tabBarController.selectedIndex =0;
					}
					
					
					
				}
		}
	if (flag == 3) 
	{
		if (buttonIndex == 0)
		{
			appDelegate.flagSetting = 4;
			appDelegate.tabBarController.selectedIndex = 2;
		}
		if (buttonIndex ==1) 
		{
			appDelegate.flagSetting =4;
			appDelegate.tabBarController.selectedIndex =0;
		}
		
	}
	
	
}


-(void)viewWillAppear:(BOOL)animated
{
	//NSLog(@"**************");
	if (appDelegate.flagSetting == 100)
	{
		appDelegate.flagSetting = 101;
		UIViewController *detailViewController;
		detailViewController = [[SettingsAlertView alloc] initWithNibName:@"SettingsAlertView" bundle:nil];
		[self.navigationController pushViewController:detailViewController animated:YES];
		[detailViewController autorelease];
				
	}
/*    
	else
	if (appDelegate.flagSetting == 3) 
	{
		//NSLog(@"--------");
		NSString *pass = [appDelegate.informationArray objectForKey:@"password"];
        //NSLog(@"read file");
		NSString *password =[[NSString alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER]];
       //  NSString *password =[[NSString alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER] encoding:[NSString defaultCStringEncoding] error:nil];
        
		NSString *phoneString =[(NSString *)[appDelegate.settingArray objectForKey:ST_EmergencySetting] retain];
		//NSLog(@"%@  %d",password,[pass length]);

		if ([phoneString isEqualToString:@"0"])
		{
			//NSLog(@" hoi phone number ");
			flag = 1;
			UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"EmegencyNotSet",@"")
														  delegate:self cancelButtonTitle:@"Ok"
												 otherButtonTitles:nil];
			[alert show];
			[alert release];
			
		}
		else
		if (([password isEqualToString:@"1"]) && ([pass length] == 0)) 
			{
				flag = 2;
				//NSLog(@"hoi pass");
				UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"Establish",@"")
															delegate:self cancelButtonTitle:@"Yes"
													otherButtonTitles:@"Not Now",nil];
				[alert show];
				[alert release];;
			}
		else
		if(appDelegate.contactCount == 0)
		{
			flag = 3;
		//	NSLog(@" hoi con tact");
			UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"doYouWantAddcontact",@"")
														  delegate:self cancelButtonTitle:@"Yes"
												 otherButtonTitles:@"Not Now",nil];
			[alert show];
			[alert release];
			
		}
		else {
			appDelegate.flagSetting =4;
			appDelegate.tabBarController.selectedIndex =0;
		}


		[password release];
		[phoneString release];
	}
*/
}

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	emailer=[[EmailView alloc] init];				 
	UIBarButtonItem *backButton = [[UIBarButtonItem alloc] 
								   initWithTitle:@"Back" style:UIBarButtonItemStylePlain target:nil action:nil];
	[self.navigationItem setBackBarButtonItem:backButton];
	[backButton release];						 
									 
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	self.title=@"Settings";
	aryDataSource=[[NSArray alloc] initWithObjects:
				   @"Account Information",
				   @"Alert Settings",
				   @"Storage Settings",
				   @"Contact SchoolBeacon",
				   @"Tell a Friend",
				   @"About",nil];
    [super viewDidLoad];
	/*
	NSString *phoneString =[[appDelegate.settingArray objectForKey:ST_EmergencySetting] retain];
	NSString *password =[[NSString alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER]];

	if ([phoneString isEqualToString:@"0"])
	{
		NSLog(@" number = 0");
		
		flag = 1;
		UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:@"Emergency phone number not yet set for your location"
													  delegate:self cancelButtonTitle:@"YES"
											 otherButtonTitles:@"NO",nil];
		[alert show];
		[alert release];
		
	}
	else 
		if ([password isEqualToString:@"1"]) 
		{
			NSLog(@"view did load call alert");
			flag = 2;
			UIAlertView *alert =[[UIAlertView alloc] initWithTitle:nil message:@"Do you want to establish a login and password for security?"
														  delegate:self cancelButtonTitle:@"YES"
												 otherButtonTitles:@"NO",nil];
			[alert show];
			[alert release];;
		}
	else {
		appDelegate.tabBarController.selectedIndex =0;
	}

	
	[password release];
	[phoneString release];
	
	*/
	
}
/*
 - (void)viewWillAppear:(BOOL)animated {
 [super viewWillAppear:animated];
 }
 */
/*
 - (void)viewDidAppear:(BOOL)animated {
 [super viewDidAppear:animated];
 }
 */
/*
 - (void)viewWillDisappear:(BOOL)animated {
 [super viewWillDisappear:animated];
 }
 */
/*
 - (void)viewDidDisappear:(BOOL)animated {
 [super viewDidDisappear:animated];
 }
 */
/*
 // Override to allow orientations other than the default portrait orientation.
 - (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
 // Return YES for supported orientations.
 return (interfaceOrientation == UIInterfaceOrientationPortrait);
 }
 */


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
	[emailer release];
	[aryDataSource release];
	[mainTable release];
    [super dealloc];
}

#pragma mark -
#pragma mark Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections.
    return 1;
}


- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
    return 6;
}


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
		cell.accessoryType=UITableViewCellAccessoryDisclosureIndicator;
		cell.selectionStyle=UITableViewCellSelectionStyleNone;
    }
		
	
    cell.textLabel.text=[aryDataSource objectAtIndex:indexPath.row];
    
    return cell;
}


/*
 // Override to support conditional editing of the table view.
 - (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath {
 // Return NO if you do not want the specified item to be editable.
 return YES;
 }
 */


/*
 // Override to support editing the table view.
 - (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath {
 
 if (editingStyle == UITableViewCellEditingStyleDelete) {
 // Delete the row from the data source.
 [tableView deleteRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] withRowAnimation:UITableViewRowAnimationFade];
 }   
 else if (editingStyle == UITableViewCellEditingStyleInsert) {
 // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view.
 }   
 }
 */


/*
 // Override to support rearranging the table view.
 - (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath {
 }
 */


/*
 // Override to support conditional rearranging of the table view.
 - (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath {
 // Return NO if you do not want the item to be re-orderable.
 return YES;
 }
 */


#pragma mark -
#pragma mark Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    // Navigation logic may go here. Create and push another view controller.
   // SettingsAboutView
	UIViewController *detailViewController;
	switch (indexPath.row) {
		case 0:
			detailViewController = [[SettingsAccountView alloc] initWithNibName:@"SettingsAccountView" bundle:nil];
			[self.navigationController pushViewController:detailViewController animated:YES];
			[detailViewController release];
			break;
		case 1:
			detailViewController = [[SettingsAlertView alloc] initWithNibName:@"SettingsAlertView" bundle:nil];
			[self.navigationController pushViewController:detailViewController animated:YES];
			[detailViewController release];
			break;
		case 2:
			detailViewController = [[SettingsStorageView alloc] initWithNibName:@"SettingsStorageView" bundle:nil];
			[self.navigationController pushViewController:detailViewController animated:YES];
			[detailViewController release];
			break;
		case 3:
			/*
			EmailView *emailer=[[EmailView alloc] init];
			emailer.toAddresses=[NSArray array];
			emailer.subject=@"";
			emailer.body=@"";
			emailer.mainView=self.parentViewController;
			 */
			
			detailViewController = [[TellUs alloc] initWithNibName:@"TellUs" bundle:nil];
			[self.navigationController pushViewController:detailViewController animated:YES];
			[detailViewController release];
			
			break;
		case 4:
		{
			//[EmailView sendTellAFriendEmail:self tintColor:self.navigationController.navigationBar.tintColor];
			emailer.toAddresses=[NSArray array];
			emailer.subject=@"";
			emailer.body=@"";
			emailer.mainView=self.parentViewController;
			[emailer showEmail];
		//	[emailer release];
			 
		}
			break;
		case 5:
			detailViewController = [[SettingsAboutView alloc] initWithNibName:@"SettingsAboutView" bundle:nil];
			[self.navigationController pushViewController:detailViewController animated:YES];
			[detailViewController release];
			break;
		default:
			break;
	}
	/*
	if (detailViewController!=nil)
	{
	
		[self.navigationController pushViewController:detailViewController animated:YES];
		[detailViewController release];
	}*/
	 
}



@end
