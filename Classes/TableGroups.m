//
//  TableGroups.m
//  SOSBEACON
//
//  Created by cncsoft on 6/24/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "TableGroups.h"
#import "GroupPersonal.h"
#import "TableGroup.h"
#import "Personal.h"
#import "SOSBEACONAppDelegate.h"
#import "RestConnection.h"
#import "ValidateData.h"

@implementation TableGroups
@synthesize arrayGroup, tbGroup;
@synthesize isEdit;
@synthesize rest,index;
@synthesize flag;


-(void)viewWillAppear:(BOOL)animated
{

	//NSLog(@"view will appear");
	
	// [self getData];
	
}
-(void)viewDidAppear:(BOOL)animated
{
	//NSLog(@"view did appear");
}
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	if (flagforAlert == 1) ///add contact
	{
		if (buttonIndex == 1) 
		{
		//	NSLog(@"button index =0");
		}
		else 
		if (buttonIndex ==0)
		{
					
			if ([textField.text isEqualToString:@""])
			{
				//Please enter group name
				UIAlertView *alert =[[UIAlertView alloc]initWithTitle:nil message:NSLocalizedString(@"EnterGroupName",@"") 
													delegate:nil 
													cancelButtonTitle:@"Ok" 
													otherButtonTitles:nil];
				[alert show];
				[alert release];
				return;
			}
			///////
			
			//NSLog(@"text field: %@",textField.text);
					flag = 3;
					
			//[rest postPath:[NSString stringWithFormat:@"/groups?name=%@&phoneId=%d&token=%@&format=json",textField.text,appDe.phoneID,appDe.apiKey] withOptions:nil];
			/////
			printf("\n add group");
			NSArray *key = [[NSArray alloc] initWithObjects:@"name",@"phoneId",@"token",nil];			
			NSArray *obj = [[NSArray alloc] initWithObjects:textField.text,[NSString stringWithFormat:@"%d",appDe.phoneID],appDe.apiKey,nil];
			NSDictionary *param =[[NSDictionary alloc] initWithObjects:obj forKeys:key];
			[rest postPath:[NSString stringWithFormat:@"/groups?format=json"] withOptions:param];
			
			[key release];
			[obj release];
			[param release];
			////
			 
		}	
	}
	else 
	if (flagforAlert == 2)////edit contact
	{
		
		if (buttonIndex == 1) 
		{
			//NSLog(@"button index =0");
		}
		else 
		if (buttonIndex ==0)
			{
				//NSLog(@"button index =1");
				//NSLog(@"text field: %@",textField.text);
				//NSLog(@"row = %d",row);
				GroupPersonal *groupPersonal =[arrayGroup objectAtIndex:row];
				flag =4;
		//[rest putPath:[NSString stringWithFormat:@"/groups/%@?id=%@&name=%@&token=%@&format=json",groupPersonal.idGroup,groupPersonal.idGroup,textField.text,appDe.apiKey] withOptions:nil];
				printf("\n edit group");
				NSArray *key = [[NSArray alloc] initWithObjects:@"id",@"name",@"token",nil];			
				NSArray *obj = [[NSArray alloc] initWithObjects:groupPersonal.idGroup,textField.text,appDe.apiKey,nil];
				NSDictionary *param =[[NSDictionary alloc] initWithObjects:obj forKeys:key];
				[rest putPath:[NSString stringWithFormat:@"/groups/%@?format=json",groupPersonal.idGroup] withOptions:param];
				[key release];
				[obj release];
				[param release];

				
				
			}	
	}else
	if (flagforAlert == 11)///delete contact
	{
		if (buttonIndex == 1) 
		{
			
		}
		else 
		if (buttonIndex == 0)
		{
			
			if (row <3)
			{
				{
				//NSLog(@"group dc bao ve");
					
					UIAlertView *alert = [[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"DeleteDefaulGroup",@"") 
																   delegate:nil 
														  cancelButtonTitle:@"Ok" 
														  otherButtonTitles:nil];
					[alert show];
					[alert release];
					
					
				}
				return;
			}
			GroupPersonal *groupP = [arrayGroup objectAtIndex:row];
			flag =2;
			/*
			printf("\n add group");
			NSArray *key = [[NSArray alloc] initWithObjects:@"id",@"token",@"format",nil];
			NSArray *obj = [[NSArray alloc] initWithObjects:groupP.idGroup,appDe.apiKey,@"json",nil];
			NSDictionary *param = [[NSDictionary alloc] initWithObjects:obj	forKeys:key];
			*/
			//[rest deletePath:[NSString stringWithFormat:@"/groups/%@",groupP.idGroup]  withOptions:param];

			[rest deletePath:[NSString stringWithFormat:@"/groups/%@?id=%@&token=%@&format=json",groupP.idGroup,groupP.idGroup,appDe.apiKey]  withOptions:nil];
			
		}	

	}	
	

	
		}

-(IBAction)editButtonPress:(id)sender
{
//	NSLog(@"edit");
	UIButton *senderButton = (UIButton *)sender;
	UITableViewCell *buttonCell= (UITableViewCell *)[senderButton superview];
	
	NSUInteger buttonRow = [[self.tableView indexPathForCell:buttonCell] row];
//	NSLog(@"row = %d",buttonRow);
	row= buttonRow;
	////***
	flagforAlert = 2;
	UIAlertView *prompt = [[UIAlertView alloc] initWithTitle:@"Edit Group" 
													 message:@"\n\n" // IMPORTANT
													delegate:self 
										   cancelButtonTitle:@"Save" 
										   otherButtonTitles:@"Cancel", nil];
	
	textField = [[UITextField alloc] initWithFrame:CGRectMake(12.0, 50.0, 260.0, 25.0)]; 
	[textField setBackgroundColor:[UIColor whiteColor]];
	//
	GroupPersonal *groupPersonal =[arrayGroup objectAtIndex:buttonRow];
	//[textField setPlaceholder:groupPersonal.nameGroup];
	textField.text = groupPersonal.nameGroup;
	//
	[prompt addSubview:textField];
	
	
	[prompt setTransform:CGAffineTransformMakeTranslation(0.0, 0.0)];
	[prompt show];
	[prompt release];
	
	
	[textField becomeFirstResponder];
	
	///***
	


}
-(IBAction)addGroupButtonPress:(id)sender
{
	
	flagforAlert = 1;
	UIAlertView *prompt = [[UIAlertView alloc] initWithTitle:@"Add Group" 
													 message:@"\n\n" // IMPORTANT
													delegate:self 
										   cancelButtonTitle:@"Save" 
										   otherButtonTitles:@"Cancel", nil];
	
	textField = [[UITextField alloc] initWithFrame:CGRectMake(12.0, 50.0, 260.0, 25.0)]; 
	[textField setBackgroundColor:[UIColor whiteColor]];
	[textField setPlaceholder:@"Enter Group Name"];
	[prompt addSubview:textField];
	
	
	[prompt setTransform:CGAffineTransformMakeTranslation(0.0, 0.0)];
	[prompt show];
	[prompt release];
	

	[textField becomeFirstResponder];
	//[textField release];
	//[textField2 release];
	//NSLog(@"add group");
}
- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
}

- (void)viewDidLoad {
	
//	NSLog(@"load table groups");
    [super viewDidLoad];
	arrayGroup = [[NSMutableArray alloc] init]; //init array group contact
	rest = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	rest.delegate=self;
	appDe = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	[self getData];
	actGroup = [[UIActivityIndicatorView alloc] init];
	actGroup.frame = CGRectMake(140, 130, 30, 30);
	actGroup.activityIndicatorViewStyle = 2;
	actGroup.hidden = NO;
	[actGroup startAnimating];
	
	UIBarButtonItem *editButton= [[UIBarButtonItem alloc]
								  initWithTitle:@"Add" 
								  style:UIBarButtonItemStyleBordered 
								  target:self
								  action:@selector(addGroupButtonPress:)];
	self.navigationItem.rightBarButtonItem = editButton;
	[editButton  release];
	 
	 
}
//function get group
- (void)getData {
	[arrayGroup removeAllObjects];

	flag =1;
	[rest getPath:[NSString stringWithFormat:@"/groups?phoneId=%d&token=%@&format=json",appDe.phoneID,appDe.apiKey] withOptions:nil];
}
- (void)viewDidUnload {
	[arrayGroup release];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)dealloc {
	[textField release];
	[tableView1 release];
	[actGroup release];
    [super dealloc];
}

#pragma mark -
#pragma mark Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections.
	[tableView addSubview:actGroup];
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
    if(section==0) 
		return [arrayGroup count];
	else 
		return 1;
}	

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
    }
    
    // Configure the cell...
	if(indexPath.section==0)
	{
		GroupPersonal *groupP = [arrayGroup objectAtIndex:indexPath.row];
		cell.textLabel.text = groupP.nameGroup;
	
	UIImage *buttonUpImage = [UIImage imageNamed:@"button_up.png"];
	UIImage *buttonDownImage = [UIImage imageNamed:@"button_down.png"];
	
	UIButton *button = [UIButton buttonWithType:UIButtonTypeCustom];
	
	button.frame = CGRectMake(0, 0, buttonUpImage.size.width, buttonUpImage.size.height);
	
	[button setBackgroundImage:buttonDownImage forState:UIControlStateHighlighted];
	[button setBackgroundImage:buttonUpImage forState:UIControlStateNormal];
	
		
		
	[button setTitle:@"Edit" forState:UIControlStateNormal];
	[button addTarget:self action:@selector(editButtonPress:)
	 forControlEvents:UIControlEventTouchUpInside];
	
	if (indexPath.row >2) 	cell.accessoryView = button;
	else  cell.accessoryView = UITableViewCellAccessoryNone;

	
	}
	
	
	
	return cell;
}


//////***************
-(void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
	//NSLog(@" delete row");
//	if (indexPath.row >2)
	{
		row =[indexPath row];
	//	GroupPersonal *groupP = [arrayGroup objectAtIndex:indexPath.row];

		
	//	NSLog(@"delete group");
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:nil message:NSLocalizedString(@"ConFimDeleteGroup",@"")
									delegate:self 
									cancelButtonTitle:@"Yes" 
									otherButtonTitles:@"No",nil];
		[alert show];
		[alert release];
		flagforAlert = 11;
		
		/*
		flag =2;
		[rest deletePath:[NSString stringWithFormat:@"/groups/%@?id=%@&token=%@&format=json",groupP.idGroup,groupP.idGroup,appDe.apiKey]  withOptions:nil];
		 */
		
	}
	/*
	else 
	{
		NSLog(@"group dc bao ve");
		
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:nil message:@"Cannot edit or delete default group" 
													   delegate:nil 
											  cancelButtonTitle:@"Ok" 
											  otherButtonTitles:nil];
		[alert show];
		[alert release];
		 
		
	}
*/
	
	

}






////////////************
////////////



#pragma mark -
#pragma mark Table view delegate

- (NSString *)tableView:(UITableView *)tableView titleForFooterInSection:(NSInteger)section {
	if (section == 0) {
		return @"Swipe group to delete";
	}else {
		
		return @"";
	}
	
}
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
	tbGroup =[[TableGroup alloc] initWithStyle:UITableViewStyleGrouped];
	tbGroup.isUpdate = NO;
	tbGroup.parentController = self;
	GroupPersonal *groupPerson = [arrayGroup objectAtIndex:indexPath.row];
	tbGroup.groupID = [groupPerson.idGroup intValue];
    //NSLog(@"fix leak 15.10.2011");
	//tbGroup.groupName = [[NSString alloc] initWithString:groupPerson.nameGroup];
	tbGroup.groupName = groupPerson.nameGroup;

	tbGroup.title = [NSString stringWithFormat:@"%@",groupPerson.nameGroup];
	
	[self.navigationController pushViewController:tbGroup animated:YES];	
	
	[tableView deselectRowAtIndexPath:indexPath animated:YES];
}
#pragma mark finishRequest

-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector{
	actGroup.hidden = YES;
	[actGroup stopAnimating];
	
	
	//NSLog(@" array data ------ %@",arrayData);
	if ( flag == 1) 
	{
		//NSLog(@"flag = 1");
	if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"]) {
		NSDictionary *data = [[arrayData objectForKey:@"response"] objectForKey:@"data"];
		
	//	[arrayGroup removeAllObjects];
		for(NSDictionary *dict in data)
		{
			GroupPersonal *groupPersonal = [[GroupPersonal alloc] init];
			groupPersonal.idGroup = [dict objectForKey:@"id"];
			groupPersonal.nameGroup = [dict objectForKey:@"name"];
			[arrayGroup addObject:groupPersonal];
			[groupPersonal release];
		}
	
		[self.tableView reloadData];
	}else {
		//NSLog(@"getcontact error");
	}
	}
	else
	if (flag ==2) 
	{
		//NSLog(@"flag = 2");
		//NSLog(@" array : %@",arrayData);
		if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"])
			{
				
				NSString *message = [[arrayData objectForKey:@"response"] objectForKey:@"message"];
				UIAlertView *alert = [[UIAlertView alloc] initWithTitle:nil message:message delegate:nil cancelButtonTitle:nil otherButtonTitles:@"OK",nil];
				[alert show];
				[alert release];
				flagforAlert =20;

				[arrayGroup removeObjectAtIndex:row];
			//	[tableView1 reloadData];
				[self.tableView reloadData];
			}
	}
		else 
		if (flag ==3)////add contact
		{
		//	NSLog(@"flag = 3");
			NSString *message = [[arrayData objectForKey:@"response"] objectForKey:@"message"];
			UIAlertView *alert = [[UIAlertView alloc] initWithTitle:nil message:message delegate:nil cancelButtonTitle:nil otherButtonTitles:@"OK",nil];
			[alert show];
			[alert release];
			flagforAlert =10;
			[textField release];
			
			
			if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"])
			{
				//NSLog(@"ngon get data");
				[self getData];
				
			}else 
			{
			//	NSLog(@"loi roi");
			}
			
			
		}
	else 
		if (flag == 4)
		{
		//	NSLog(@"flag = 4");
			NSString *message = [[arrayData objectForKey:@"response"] objectForKey:@"message"];
			UIAlertView *alert = [[UIAlertView alloc] initWithTitle:nil message:message delegate:nil cancelButtonTitle:nil otherButtonTitles:@"OK",nil];
			[alert show];
			[alert release];
			flagforAlert =10;
			[textField release];
			if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"])
			{
			//	NSLog(@"ngon get data");
				[self getData];
				
			}else 
			{
				//NSLog(@"loi roi");
			}
			
		}
				
			
				
}

- (void)cantConnection:(NSError *)error andRestConnection:(id)connector {
	actGroup.hidden = YES;
	[actGroup stopAnimating];
//	NSLog(@"error roi anh em oi ");
//	NSLog(@" error : %@",error);
	
	//flagforAlert = 2;
	alertView();
}
@end








