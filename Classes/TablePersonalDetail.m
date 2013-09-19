//
//  TablePersonalDetail.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 16/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import "TablePersonalDetail.h"
#import "Personal.h"
#import "SOSBEACONAppDelegate.h"
#import "TableGroup.h"
#import "ValidateData.h"
#import "RestConnection.h"

#import <AddressBook/AddressBook.h>

@implementation TablePersonalDetail
@synthesize personal;
@synthesize personalTemp;
@synthesize fieldLabels;
@synthesize textFieldBeingEdited;
@synthesize personalIndex;
@synthesize tableGroupDetail;
@synthesize restConnection,groupID, groupName;


#pragma mark - Memory management
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
	// NSLog(@"Memory error table detail!!!!!!!!!");
}

#pragma mark -
#pragma mark lyfecycle view

- (void)viewDidLoad {
    [super viewDidLoad];
	
	saveButton.enabled = YES;
	actContact = [[UIActivityIndicatorView alloc] init];
	actContact.frame = CGRectMake(120, -30, 30, 30);
	actContact.activityIndicatorViewStyle = 2;
	actContact.hidden = YES;
	[actContact stopAnimating];
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	restConnection = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	restConnection.delegate=self;
	
    fieldLabels = [[NSArray alloc] initWithObjects:@"Name:", @"Email:", @"VoicePhone:", @"TextPhone:", nil];
	
	UIBarButtonItem *cancelButton = [[UIBarButtonItem alloc]
                                     initWithTitle:@"Back"
                                     style:UIBarButtonItemStylePlain
                                     target:self
                                     action:@selector(cancel:)];
    self.navigationItem.leftBarButtonItem = cancelButton;
    [cancelButton release];
    
    saveButton = [[UIBarButtonItem alloc]
                                   initWithTitle:@"Save" 
                                   style:UIBarButtonItemStylePlain
                                   target:self
                                   action:@selector(save:)];
    self.navigationItem.rightBarButtonItem = saveButton;
    [saveButton release];

	if(personal==nil)
	{
		personalTemp = [[Personal alloc] init];
		personalIndex = -1;
	}
	else {
		
		personalTemp = [[Personal alloc] init];
		[personalTemp copyObject:personal];
	}
}

#pragma mark -
#pragma mark Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections. 
	
	if(personalIndex==-1) return 2;
	else return 2;
	 
	/*
	if (appDelegate.flagforGroup == 1) 
	{
		appDelegate.flagforGroup =10;
		return 1;
	}
	else {
		return 2;
	}
*/
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
	if(section==0)
		return kNumberOfRow;
	else return 1;
}

// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
	
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
		
		if (personal.typeContact) {
			
			//// sua add contact button;
			cell.userInteractionEnabled = FALSE;
			//cell.userInteractionEnabled = TRUE;
			saveButton.enabled = FALSE;
		}else {
			saveButton.enabled = TRUE;
			cell.userInteractionEnabled = TRUE;
		}
		
		if(indexPath.section==0)
		{
			UILabel *label = [[UILabel alloc] initWithFrame:CGRectMake(10, 10, 90, 25)];
			label.textAlignment = UITextAlignmentRight;
			label.tag = 1059;
			label.font = [UIFont boldSystemFontOfSize:14];
			[cell.contentView addSubview:label];
			[label release];
		
			UITextField *textField = [[UITextField alloc] initWithFrame:CGRectMake(110, 12, cell.contentView.frame.size.width-150, 25)];
			textField.clearsOnBeginEditing = NO;
			[textField setDelegate:self];
			[textField addTarget:self action:@selector(textFieldDone:) forControlEvents:UIControlEventEditingDidEndOnExit];
			[cell.contentView addSubview:textField];
		}
		cell.textLabel.textAlignment = UITextAlignmentCenter;
		cell.selectionStyle=UITableViewCellSelectionStyleNone;
		[cell.contentView addSubview:actContact];

	}
    // Configure the cell...
	if(indexPath.section==0)
	{
		int row = indexPath.row;
		UILabel *label = (UILabel *)[cell viewWithTag:1059];
		UITextField *textField = nil;
		for (UIView *oneView in cell.contentView.subviews)
		{
			if ([oneView isMemberOfClass:[UITextField class]])
				textField = (UITextField *)oneView;
		}
		if (indexPath.row < 4) {
			label.text = [fieldLabels objectAtIndex:row];
		}
		else {
			//label.text = @"Group:";
		}

		
		switch (row) {
			case kNameRowIndex:
				textField.text = personalTemp.contactName;
				textField.keyboardType = UIKeyboardTypeDefault;
				break;
			case kEmailRowIndex:
				textField.text = personalTemp.email;
				textField.keyboardType = UIKeyboardTypeEmailAddress;
				break;
			case kPhoneRowIndex:
				textField.text = personalTemp.voidphone;
				textField.keyboardType = UIKeyboardTypeNumbersAndPunctuation;
				break;
			case kTextPhoneRowIndex:///edit
				textField.text = personalTemp.textphone;
				textField.keyboardType = UIKeyboardTypeNumbersAndPunctuation;
				break;
				/*
			case kGroupIndex:
				textField.text = groupName;
				textField.userInteractionEnabled = NO;
				break;
				 */
			default:
				break;
		}
		textField.tag = row;
		if (isAddContact && row==0) {
			[textField becomeFirstResponder];
			isAddContact=NO;
		}
	}
	else if(indexPath.section==1)
	{
		if (personal.typeContact)
		{
			cell.textLabel.font = [UIFont systemFontOfSize:16.0];
			cell.textLabel.text = @"Default contact cannot be changed";

		}
		else 
		cell.textLabel.text = @"Add from phone contact list";
	}

	return cell;
}

#pragma mark -
#pragma mark Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
	if(indexPath.section==1)
	{
		ABPeoplePickerNavigationController *picker = [[ABPeoplePickerNavigationController alloc] init];
		picker.peoplePickerDelegate = self;
		
		[appDelegate.tabBarController presentModalViewController:picker animated:YES];
		
		[picker release];
		
		
		}

	[tableView deselectRowAtIndexPath:indexPath animated:YES];
}

#pragma mark PeoplePickerDelegate
- (void)peoplePickerNavigationControllerDidCancel:(ABPeoplePickerNavigationController *)peoplePicker {
    // assigning control back to the main controller
	[appDelegate.tabBarController dismissModalViewControllerAnimated:YES];
}

- (BOOL)peoplePickerNavigationController: (ABPeoplePickerNavigationController *)peoplePicker shouldContinueAfterSelectingPerson:(ABRecordRef)person {
	personalTemp.voidphone = @"";
    NSString *firstName= (NSString *)ABRecordCopyValue(person, kABPersonFirstNameProperty);

    NSString *lastName = (NSString *)ABRecordCopyValue(person, kABPersonLastNameProperty);

	ABMultiValueRef emails = ABRecordCopyValue(person, kABPersonEmailProperty);
	if(ABMultiValueGetCount(emails)>0)
	{
		NSString *email = (NSString*)ABMultiValueCopyValueAtIndex(emails, 0);
       // NSString *email =[[NSString alloc] initWithString:(NSString*)ABMultiValueCopyValueAtIndex(emails, 0)];
		personalTemp.email = email;
        
        //NSLog(@"release email in table personalDetail");
    [email  release];
	}
	else {
		personalTemp.email = @""; 
	}
	CFRelease(emails);
	NSString *fullName = nil;
	
	if(firstName) 
		fullName=firstName;
	if(lastName) {
//	fullName = fullName==nil ? fullName=lastName : [fullName stringByAppendingFormat:@" %@",lastName];
	
		if (fullName == nil)
		{
			fullName = lastName;
		}
		else
		{
			fullName = [fullName stringByAppendingFormat:@"%@",lastName];
		}
	}
	
	
	if (fullName>0) {
		personalTemp.contactName =fullName;
	}
	else {
		personalTemp.contactName = @"";
	}	
	
	ABMultiValueRef multi = ABRecordCopyValue(person, kABPersonPhoneProperty);
	if(ABMultiValueGetCount(multi)>0)
	{
		NSString *phoneNumber = (NSString*)ABMultiValueCopyValueAtIndex(multi, 0);
		personalTemp.textphone = phoneNumber;
		[phoneNumber release];
	}
	else {
		personalTemp.textphone = @""; 
	}
	CFRelease(multi);
	[self.tableView reloadData];
	isEdited=TRUE;
	appDelegate.savePerson	=YES;
	isAddContact=YES;
    [appDelegate.tabBarController dismissModalViewControllerAnimated:YES];
	[firstName release];
	[lastName release];
    return NO;
}
- (BOOL)peoplePickerNavigationController:(ABPeoplePickerNavigationController *)peoplePicker shouldContinueAfterSelectingPerson:(ABRecordRef)person property:(ABPropertyID)property identifier:(ABMultiValueIdentifier)identifier{
    return NO;
}

#pragma mark -
#pragma mark Memory management

- (void)viewDidUnload {
    // Relinquish ownership of anything that can be recreated in viewDidLoad or on demand.
    // For example: self.myOutlet = nil;
}

- (void)dealloc {
	[restConnection release];
	[actContact release];
	[textFieldBeingEdited release];
	[tableGroupDetail release];
	[personal release];
	[personalTemp release];
	[fieldLabels release];
	[groupName release];
    [super dealloc];
}

#pragma mark Action
- (void) DimisAlertView:(UIAlertView*)alertView {
	[alertView dismissWithClickedButtonIndex:0 animated:TRUE];
}

-(IBAction)cancel:(id)sender{
	appDelegate.saveContact=NO;
	appDelegate.savePerson=NO;
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
		
	}
	else {
		[self.navigationController popViewControllerAnimated:YES];
	}
	
}
#pragma mark -saveContact

- (IBAction)save:(id)sender {
	[textFieldBeingEdited resignFirstResponder];
	
	appDelegate.saveContact = YES;
	saveButton.enabled = NO;
	actContact.hidden = NO;
	[actContact startAnimating];
	tableGroupDetail.isUpdate = YES;
	[tableGroupDetail.tableView reloadData];
	
	if(textFieldBeingEdited!=nil) 
	{
		switch (textFieldBeingEdited.tag) {
			case kNameRowIndex:
				personalTemp.contactName = textFieldBeingEdited.text;
				break;
			case kEmailRowIndex:
				personalTemp.email = textFieldBeingEdited.text;
				break;
			case kPhoneRowIndex:	
				personalTemp.voidphone = textFieldBeingEdited.text;
				
				break;
			case kTextPhoneRowIndex:
				personalTemp.textphone = textFieldBeingEdited.text;
				break;
			default:
				break;
		};
	}
	/////
	/*
	NSLog(@" text phone ");
	NSString *phone = [NSString stringWithString:personalTemp.textphone];
	char a =[phone characterAtIndex:0];
	if (a == '1')
	{
		NSLog(@"alo");
		NSString *phone1 = [[phone substringFromIndex:1] retain];
		

	
	personalTemp.textphone = phone1;
	NSLog(@" text phone : %@",personalTemp.textphone);
	}
	 */
	////////
	if([personalTemp.contactName isEqualToString:@""])
	{
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
															message:NSLocalizedString(@"EnterNameAndPhone",@"")														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		return;
	}
	if (!checkSpace(personalTemp.contactName)) {
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
															message:NSLocalizedString(@"EnterNameAndPhone",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		return;
		
	}
	
	BOOL isHaveMail = YES;
	
	if ([personalTemp.email isEqualToString:@""]) 
	{
		isHaveMail=NO;		
	}
	
	BOOL isHavePhone = YES;
	if([personalTemp.textphone isEqualToString:@""]) 
	{
		isHavePhone = NO;
	}
	
	BOOL isVoicePhone = NO;
	if ([personalTemp.voidphone isEqualToString:@""]) {
		isVoicePhone = YES;
	}
	NSString * cutStringPhone1=[NSString stringWithFormat:@"%@",personalTemp.voidphone];
	
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@"(" withString:@""];
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@")" withString:@""];
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@"-" withString:@""];
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@"." withString:@""];
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@" " withString:@""];
	if (!isVoicePhone&&!checkPhone(cutStringPhone1)) {
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Phone number Error"
															message:NSLocalizedString(@"VoicePhoneNotValid",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		return;
		
	}
	
	if(!isHaveMail&&!isHavePhone)
	{
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Message"
															message:NSLocalizedString(@"EnterNameAndPhone",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		return;
	}
	
	//chech mail , phone
	
	if (!(isHavePhone&&!isHaveMail)&&!checkMail(personalTemp.email)) {
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Email Error"
															message:NSLocalizedString(@"EmailNotValid",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		return;
	}
	NSString * cutStringPhone=[NSString stringWithFormat:@"%@",personalTemp.textphone];
	
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@"(" withString:@""];
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@")" withString:@""];
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@"-" withString:@""];
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@"." withString:@""];
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@" " withString:@""];
	
	if(!(isHaveMail&&!isHavePhone)&&!checkPhone(cutStringPhone)){
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Phone number Error"
															message:NSLocalizedString(@"TextPhoneNotValid",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		return;
	}
	
	//////******************
	/*
	NSString *phonevoid = [NSString stringWithString:personalTemp.voidphone];
	if (phonevoid != @"") 
	{
		char b =[phonevoid characterAtIndex:0];
		if (b == '1')
		{
			NSLog(@"textphone: %@",personalTemp.voidphone);
			NSString *phonevoid1 = [[phonevoid substringFromIndex:1] retain];
			personalTemp.voidphone = phonevoid1;
			NSLog(@"textphone after cut: %@",personalTemp.voidphone);

		}
	}
	
	
	
	NSString *phone = [NSString stringWithString:personalTemp.textphone];
	if (phone != @"")
	{			
		char a =[phone characterAtIndex:0];
		if (a == '1')
		{
			NSLog(@"alo");
			NSLog(@"textphone: %@",personalTemp.textphone);
			NSString *phone1 = [[phone substringFromIndex:1] retain];
			personalTemp.textphone = phone1;
			NSLog(@"textphone after cut: %@",personalTemp.textphone);

		}
	}
	
	 */
	////////////*************
	
	if (personalIndex == -1) {
		//Add new contact
		personalTemp.status = CONTACT_STATUS_NEW;
		[tableGroupDetail.arrayContacts addObject:personalTemp];
	}else {
		//Edit contact
		if(personalTemp.status!= CONTACT_STATUS_NEW)
			personalTemp.status = CONTACT_STATUS_MODIFIED;
		[personal copyObject:personalTemp];
	}
	
	tableGroupDetail.isEdited = YES;
	appDelegate.saveContact = YES;
	[tableGroupDetail.tableView reloadData];
	//[self.navigationController popViewControllerAnimated:YES];
	[self.navigationController popToViewController:[[self.navigationController viewControllers] objectAtIndex:1] animated:YES];
	
}
- (void)saveIfOutTab {
	
	appDelegate.saveContact = YES;
	saveButton.enabled = NO;
	actContact.hidden = NO;
	[actContact startAnimating];
	tableGroupDetail.isUpdate = YES;
	[tableGroupDetail.tableView reloadData];
	
	if(textFieldBeingEdited!=nil) 
	{
		switch (textFieldBeingEdited.tag) {
			case kNameRowIndex:
				personalTemp.contactName = textFieldBeingEdited.text;
				break;
			case kEmailRowIndex:
				personalTemp.email = textFieldBeingEdited.text;
				break;
			case kPhoneRowIndex:	
				
				personalTemp.voidphone = textFieldBeingEdited.text;
				break;
			case kTextPhoneRowIndex:
				
				personalTemp.textphone = textFieldBeingEdited.text;
				break;
			default:
				break;
		};
	}
	
	if([personalTemp.contactName isEqualToString:@""])
	{
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
															message:@"Name must be entered"
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
				
		[alertView release];
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		return;
	}
	if (!checkSpace(personalTemp.contactName)) {
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
															message:@"Name can not be blank"
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		return;
		
	}
	
	BOOL isHaveMail = YES;
	
	if ([personalTemp.email isEqualToString:@""]) 
	{
		isHaveMail=NO;		
	}
	
	BOOL isHavePhone = YES;
	if([personalTemp.textphone isEqualToString:@""]) 
	{
		isHavePhone = NO;
	}
	
	BOOL isVoicePhone = NO;
	if ([personalTemp.voidphone isEqualToString:@""]) {
		isVoicePhone = YES;
	}
	NSString * cutStringPhone1=[NSString stringWithFormat:@"%@",personalTemp.voidphone];
	
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@"(" withString:@""];
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@")" withString:@""];
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@"-" withString:@""];
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@"." withString:@""];
	cutStringPhone1=[cutStringPhone1 stringByReplacingOccurrencesOfString:@" " withString:@""];
	
	if (!isVoicePhone&&!checkPhone(cutStringPhone1)) {
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Phone number Error"
															message:@"Voicephone is invalid"
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		return;
		
	}
	
	if(!isHaveMail&&!isHavePhone)
	{
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Message"
															message:@"Email or Textphone must be entered to proceed"
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		return;
	}
	
	//chech mail , phone
	
	if (!(isHavePhone&&!isHaveMail)&&!checkMail(personalTemp.email)) {
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Email Error"
															message:NSLocalizedString(@"EmailNotValid",@"")
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		return;
	}
	NSString * cutStringPhone=[NSString stringWithFormat:@"%@",personalTemp.textphone];
	
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@"(" withString:@""];
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@")" withString:@""];
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@"-" withString:@""];
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@"." withString:@""];
	cutStringPhone=[cutStringPhone stringByReplacingOccurrencesOfString:@" " withString:@""];
	
	if(!(isHaveMail&&!isHavePhone)&&!checkPhone(cutStringPhone)){
		actContact.hidden = YES;
		[actContact stopAnimating];
		saveButton.enabled = YES;
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Phone number Error"
															message:@"Textphone is invalid"
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		return;
	}
	
	if (personalIndex == -1) {
		//Add new contact
		personalTemp.status = CONTACT_STATUS_NEW;
		[tableGroupDetail.arrayContacts addObject:personalTemp];
	}else {
		//Edit contact
		if(personalTemp.status!=CONTACT_STATUS_NEW)
			personalTemp.status = CONTACT_STATUS_MODIFIED;
		[personal copyObject:personalTemp];
	}
	
	tableGroupDetail.isEdited = YES;
	appDelegate.saveContact = YES;
	[tableGroupDetail.tableView reloadData];
	
	
	
}


#pragma mark finishRequest
-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(RestConnection *)connector{
	
		if ([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"]) {
			[tableGroupDetail.arrayContacts removeAllObjects];
			if(personalIndex==-1)
			{
				//add personal
				personalTemp.contactID = [[[arrayData objectForKey:@"response"] objectForKey:@"id"] intValue];
				[tableGroupDetail.arrayContacts addObject:personalTemp];
			}
			else
			{
				
			}
			UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@""
																message:@"Contact saved success"
															   delegate:nil
													  cancelButtonTitle:@"Ok"
													  otherButtonTitles:nil];
			[alertView show];
			[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
			[alertView release];
			[tableGroupDetail.tableView reloadData];
		}
		else {
			UIAlertView *alertView= [[UIAlertView alloc] initWithTitle:@"Error"
															   message:@"Error with save contact" 
															  delegate:self 
													 cancelButtonTitle:nil 
													 otherButtonTitles:@"Ok",nil];
			[alertView show];
			[alertView release];
			saveButton.enabled = YES;
			actContact.hidden = YES;
			[actContact stopAnimating];
			return;	
		}
		saveButton.enabled = YES;
		actContact.hidden = YES;
		[actContact stopAnimating];
		[self performSelector:@selector(delayPopView) withObject:nil afterDelay:CONF_DIALOG_DELAY_TIME];
		return;	
		
	

}
- (void)delayPopView {
	[self.navigationController popViewControllerAnimated:YES];
}

-(IBAction)textFieldDone:(id)sender {
    UITableViewCell *cell =
    (UITableViewCell *)[[sender superview] superview];
    UITableView *table = (UITableView *)[cell superview];
	
    NSIndexPath *textFieldIndexPath = [table indexPathForCell:cell];
    NSUInteger row = [textFieldIndexPath row];
    row++;
    if (row >= kNumberOfRow)
        row = 0;
    NSUInteger newIndex[] = {0, row};
    NSIndexPath *newPath = [[NSIndexPath alloc] initWithIndexes:newIndex length:2];
    UITableViewCell *nextCell = [self.tableView  cellForRowAtIndexPath:newPath];
	[newPath release];
    UITextField *nextField = nil;
    for (UIView *oneView in nextCell.contentView.subviews) {
        if ([oneView isMemberOfClass:[UITextField class]])
            nextField = (UITextField *)oneView;
    }
    [nextField becomeFirstResponder];
}

#pragma mark UITextFieldDelegate

- (void)textFieldDidBeginEditing:(UITextField *)textField
{
	
	if (personal.typeContact)
	{
		[textField resignFirstResponder];
	}
    textFieldBeingEdited = textField;
	isEdited=TRUE;
	
	appDelegate.savePerson = YES;
}

- (void)textFieldDidEndEditing:(UITextField *)textField
{
	/*
	 NSString *phonevoid = [NSString stringWithString:personalTemp.voidphone];
	 if (phonevoid != @"") 
	 {
	 char b =[phonevoid characterAtIndex:0];
	 if (b == '1')
	 {
	 NSLog(@"textphone: %@",personalTemp.voidphone);
	 NSString *phonevoid1 = [[phonevoid substringFromIndex:1] retain];
	 personalTemp.voidphone = phonevoid1;
	 NSLog(@"textphone after cut: %@",personalTemp.voidphone);
	 
	 }
	 }
	 */
	/*
	 NSString *phone = [NSString stringWithString:personalTemp.textphone];
	 char a =[phone characterAtIndex:0];
	 if (a == '1')
	 {
	 NSLog(@"alo");
	 NSString *phone1 = [[phone substringFromIndex:1] retain];
	 
		personalTemp.textphone = phone1;
	 NSLog(@" text phone : %@",personalTemp.textphone);
	 }
	 */
    switch (textField.tag) {
		case kNameRowIndex:
			personalTemp.contactName = textField.text;
			break;
		case kEmailRowIndex:
			personalTemp.email = textField.text;
			break;
		case kPhoneRowIndex:
			personalTemp.voidphone = textField.text;
			NSString *phonevoid = [NSString stringWithString:personalTemp.voidphone];
			if (phonevoid != @"") 
			{
				char b =[phonevoid characterAtIndex:0];
				
				///
				NSString *strcut = [phonevoid substringToIndex:2];
				//NSLog(@" %@",phonevoid);
				//NSLog(@" %@",strcut);
				if ([strcut isEqualToString:@"+1"]||[strcut  isEqualToString:@"1+"]) 
				{
					if ([phonevoid  length] >2) 
					{
						NSString *phone = [[phonevoid substringFromIndex:2] retain];
						personalTemp.voidphone = phone;
					//	NSLog(@"textphone after cut: %@",personalTemp.voidphone);
						textField.text = phone;
						//[value1 release];
						[phone release];
					}else 
					{   
						textField.text = @"";
					
					}
					
					
				}else
				///
				if (b == '1'||b=='+')
				{
					NSString *phonevoid1 = [[phonevoid substringFromIndex:1] retain];
                   // NSString *phonevoid1 = [phonevoid substringFromIndex:1];
					personalTemp.voidphone = phonevoid1;
					textField.text = phonevoid1;
                    [phonevoid1 release];
					
				}
			}
			break;
		case kTextPhoneRowIndex:
			personalTemp.textphone = textField.text;
			NSString *phone = [NSString stringWithString:personalTemp.textphone];
		
			char a =[phone characterAtIndex:0];
			////
			NSString *strcut1 = [phone substringToIndex:2];
		//	NSLog(@" %@",phone);
		///	NSLog(@" %@",strcut1);
			if ([strcut1 isEqualToString:@"+1"]||[strcut1  isEqualToString:@"1+"]) 
			{
				if ([phone  length] >2) 
				{
					NSString *phone12 = [[phone substringFromIndex:2] retain];
					personalTemp.voidphone = phone12;
					//NSLog(@"textphone after cut: %@",personalTemp.voidphone);
					textField.text = phone12;
					//[value1 release];
					[phone12 release];
				}else 
				{   
					textField.text = @"";
					
				}
			}
			///
			else
			if (a == '1')
			{
				//NSLog(@"alo");
				NSString *phone1 = [[phone substringFromIndex:1] retain];
								
				personalTemp.textphone = phone1;
				//NSLog(@" text phone : %@",personalTemp.textphone);
				textField.text = phone1;
                [phone1 release];
			}
			break;
		default:
			break;
	};
}

-(void)cantConnection:(NSError *)error andRestConnection:(id)connector{
	actContact.hidden = YES;
	[actContact stopAnimating];
	saveButton.enabled = TRUE;
	alertView();
}
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
	
	if (appDelegate.savePerson && appDelegate.saveContact) {
		if (buttonIndex == 0) { //YES
			[self saveIfOutTab];
			
		}
		else { //NO
			
		}
		
	}
	else {
		if (buttonIndex == 0) { //YES
			[self save:nil];
			
		}
		else { //NO
			
			[self.navigationController popViewControllerAnimated:YES];
			
		}
		
	}

		
}




@end

