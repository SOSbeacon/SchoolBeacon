//
//  SettingsAccountView.m
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/18/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import "SettingsAccountView.h"

#import "ValidateData.h"
@implementation SettingsAccountView
@synthesize txtuserName,txtEmail,txtPhoneNumber,txtPassword;
@synthesize actSetting,btnSave,rest;
// The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
       	btnSave=[[UIBarButtonItem alloc] initWithTitle:@"Save" style:UIBarButtonItemStyleBordered target:self action:@selector(SaveSetting)];
		self.navigationItem.rightBarButtonItem = btnSave;
		
    }
    return self;
}


/*
 
 if ([[NSFileManager defaultManager]  fileExistsAtPath:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] ]) 
 {
 
 NSMutableDictionary *accArray =[[NSMutableDictionary alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER]];
 NSString *strEmail = appDelegate.phone;
 NSLog(@" home view-------------> %@",strEmail);
 NSMutableArray *acc = [accArray  objectForKey:strEmail];
 if (acc == nil) 
 {
 
 
 }
 else 
 {
 
 
 */


- (IBAction)back:(id)sender {
    [self backgroundTap:nil];
    if (isEdit) {
        isPop = YES;
        flagalert =1;
        UIAlertView *alert =[ [UIAlertView alloc] initWithTitle:nil 
                                                        message:NSLocalizedString(@"SaveChange",@"")
                                                       delegate:self
                                              cancelButtonTitle:@"Yes" otherButtonTitles:@"No",nil];
        [alert show];
        [alert release];        
    }
    else {
        [self.navigationController popViewControllerAnimated:YES];
    }
}

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	UIImage *buttonImage = [[UIImage imageNamed:@"backImage.png"] stretchableImageWithLeftCapWidth:16.0 topCapHeight:2.0];
    UIButton *backButton = [UIButton buttonWithType:UIButtonTypeCustom];
    [backButton setBackgroundImage:buttonImage forState:UIControlStateNormal];
    [backButton setBackgroundImage:[UIImage imageNamed:@"backImage2.png"] forState:UIControlStateHighlighted];
    [backButton addTarget:self action:@selector(back:) forControlEvents:UIControlEventTouchUpInside];
    backButton.frame = CGRectMake(0.0, 0.0, 49, 30);
	
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc] initWithCustomView:backButton];
	self.navigationItem.leftBarButtonItem = btnBack;
	[btnBack release];	    
    isPop = NO;
    
	self.title=@"Account Info";
	rest = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
	rest.delegate =self;	
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	[self loadData];
    [super viewDidLoad];
    
    //NSLog(@" pass : %@",txtPassword.text);
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
	rest.delegate = nil;
	[rest release];
	[btnSave release];
	[actSetting release];
	[txtuserName release]; 
	[txtEmail release]; 
	[txtPhoneNumber release];
	[txtPassword release];
    [super dealloc];
}
-(void)loadData{
	
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	txtuserName.text = appDelegate.userName;
	txtEmail.text = appDelegate.email;
	txtPhoneNumber.text = appDelegate.number;
	
	if([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER]])
	{
        NSLog(@"read file");
		NSString *password =[[NSString alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER]];
       // NSString *password =[[NSString alloc ]initWithContentsOfFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER] encoding:[NSString defaultCStringEncoding] error:nil];
		if ([password isEqualToString:@"1" ]) 
		{
			txtPassword.text=@"";
		}
		else 
		{
			//NSLog(@" password :%@",password);
			txtPassword.text = password;
		}

		

		[password  release];
	}
	///////
	 
	 if ([[NSFileManager defaultManager]  fileExistsAtPath:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] ]) 
	 {
	 
		 NSMutableDictionary *accArray =[[NSMutableDictionary alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER]];
		 NSString *strEmail = [appDelegate.email retain];

		 NSMutableArray *acc = [accArray  objectForKey:strEmail];
		 if (acc == nil) 
		 {
			// NSLog(@"do no thing");
			
		 }
			else
		{
			 NSString *strpass = [ acc objectAtIndex:0]; 
			 if([strpass length]>6) txtPassword.text = strpass;
		}
	[strEmail  release];
		 [accArray release];
	 }
	 
	 //////

	
}
-(void)save
{
	save = TRUE;
	btnSave.enabled = FALSE;
	actSetting.hidden=NO;
	[actSetting startAnimating];
	
	if(![txtPhoneNumber.text isEqualToString:@""]&&!(checkPhone(txtPhoneNumber.text))) {
		UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
															message:@"Phone number is not valid."
														   delegate:nil
												  cancelButtonTitle:@"Ok"
												  otherButtonTitles:nil];
		[alertView show];
		[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
		[alertView release];
		//save = FALSE;
		btnSave.enabled = TRUE;
		[actSetting stopAnimating];
		actSetting.hidden = YES;
		return;		
	}else
		if (!([txtPassword.text isEqualToString:@""]) &&!(checkPassWord(txtPassword.text)) )
		{
			//save = FALSE;
			btnSave.enabled = TRUE;
			[actSetting stopAnimating];
			actSetting.hidden = YES;		
			UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
																message:NSLocalizedString(@"PassShort",@"")
															   delegate:nil
													  cancelButtonTitle:@"Ok"
													  otherButtonTitles:nil];
			[alertView show];
			[self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
			[alertView release];
			return;
		}
		else
			
		{          
			UIDevice *device = [UIDevice currentDevice];
			NSString  *strImei = [[NSString alloc] initWithString:[appDelegate GetUUID]];
            NSString *Version = [NSString stringWithFormat:@"Version:%@",[[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleVersion"]];
            NSLog(@"Version: %@",Version);
            NSString *model= [[UIDevice currentDevice] model];            
            NSString *systemName= [[UIDevice currentDevice] systemName];     
            NSString *systemVersion =[[UIDevice currentDevice] systemVersion]; 
            NSString *phoneInfor = [NSString stringWithFormat:@"%@;Model:%@;SystemName:%@;Systemversion:%@",Version,model,systemName,systemVersion];
            phoneInfor =[phoneInfor stringByReplacingOccurrencesOfString:@" "withString:@""];			
			NSArray *key1= [NSArray arrayWithObjects:@"format", @"_method", @"userId",@"imei",@"token",@"name",@"email",@"password",@"number",@"phoneType", @"phoneInfo", nil];
			NSArray *obj1 =[NSArray arrayWithObjects:@"json", @"put", [NSString stringWithFormat:@"%d",appDelegate.userID], strImei, appDelegate.apiKey,txtuserName.text,txtEmail.text,txtPassword.text,txtPhoneNumber.text,@"1", phoneInfor, nil];
			NSDictionary *params1 = [NSDictionary dictionaryWithObjects:obj1 forKeys:key1];
            NSString *request = [NSString stringWithString:@"http://sosbeacon.org/school/users"];
			[rest putPath:request withOptions:params1];	
			[strImei release];            
		}
}

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
	if (buttonIndex == 0) 
	{
		if (flagalert ==1)
		{
			flagalert = 2;
			[self save];
		}
	}
	else {
        [self.navigationController popViewControllerAnimated:YES];
	}

}

-(IBAction)SaveSetting
{
    [self backgroundTap:nil];
    isPop = YES;
//    if (isEdit) {
        flagalert = 2;
        [self save];        
//    }
}

- (void) DimisAlertView:(UIAlertView*)alertView {
	[alertView dismissWithClickedButtonIndex:0 animated:TRUE];
    if (alertView.tag == 112 && isPop) {
        isPop = NO;
        [self.navigationController popViewControllerAnimated:YES];
    }
}

#pragma mark IBAction 
- (void)textFieldDidEndEditing:(UITextField *)textField {
    isEdit = YES;
}

- (IBAction)backgroundTap:(id)sender {
	[txtEmail resignFirstResponder];
	[txtPassword resignFirstResponder];
	[txtPhoneNumber resignFirstResponder];
	[txtuserName resignFirstResponder];
}

#pragma mark -
#pragma mark finish request
-(void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector{
	[actSetting stopAnimating];
	actSetting.hidden = YES;
	btnSave.enabled = TRUE;
	isEdit = NO;
	//NSLog(@" setting account array data-->>: %@ ",arrayData);
    NSDictionary *responseData = [arrayData objectForKey:@"response"];
    NSString *success = [responseData objectForKey:@"success"];
    
    if ([success isEqual:@"true"])
    {
		NSString *file1=[DOCUMENTS_FOLDER stringByAppendingPathComponent:@"newlogin.plist"];
        NSDictionary *dict = [NSDictionary dictionaryWithContentsOfFile:file1];
        NSString *schoolId = [dict objectForKey:@"schoolId"];
		NSArray *key1 = [NSArray arrayWithObjects:@"schoolId",@"email", @"password",nil];
		NSArray *obj1 = [NSArray arrayWithObjects:schoolId, txtEmail.text, txtPassword.text, nil];			
		NSDictionary *param1 = [[NSDictionary alloc] initWithObjects:obj1 forKeys:key1];
		[param1 writeToFile:file1 atomically:YES];
		[param1 release];
        
        if ([txtPassword.text length]>=6) 
        {
			NSString *pass = txtPassword.text;
            [pass writeToFile:[NSString stringWithFormat:@"%@/info.plist",DOCUMENTS_FOLDER] atomically:YES encoding:NSUTF8StringEncoding error:NULL];
			///////
			if ([[NSFileManager defaultManager]  fileExistsAtPath:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] ]) 
			{
				
				NSMutableDictionary *accArray =[[NSMutableDictionary alloc] initWithContentsOfFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER]];
				NSString *strEmail = [appDelegate.email retain];
				
				NSMutableArray *acc = [accArray  objectForKey:strEmail];
				if (acc == nil) 
				{
					//NSLog(@"do no thing");
					
				}
				else
				{
					[acc replaceObjectAtIndex:0 withObject:pass];
					[accArray  setObject:acc forKey:strEmail];
					[accArray  writeToFile:[NSString stringWithFormat:@"%@/allAccount.plist",DOCUMENTS_FOLDER] atomically:YES];
					
				}
				[strEmail  release];
				[accArray autorelease];
			}
        }
        btnSave.enabled = TRUE;

        UIAlertView *alertView= [[UIAlertView alloc] initWithTitle:@""
															   message:@"Phone has been updated successfully" 
															  delegate:self 
													 cancelButtonTitle:@"Ok" 
													 otherButtonTitles:nil] ;
        alertView.tag = 112;
        [alertView show];
        [self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:CONF_DIALOG_DELAY_TIME];
        [alertView release];
        save = NO;
    }
    else {
        NSString *message= [[arrayData objectForKey:@"response"] objectForKey:@"message"];
        
        btnSave.enabled = TRUE;
        [actSetting stopAnimating];
        actSetting.hidden = YES;			
        UIAlertView *alertView= [[UIAlertView alloc] initWithTitle:@"Error"
                                                           message:message 
                                                          delegate:self 
                                                 cancelButtonTitle:@"Ok" 
                                                 otherButtonTitles:nil];
        alertView.tag = 112;
        [alertView show];
        [self performSelector:@selector(DimisAlertView:) withObject:alertView afterDelay:2];
        [alertView release];
        
    }
}

#pragma mark -
#pragma mark -network fail
-(void)cantConnection:(NSError *)error andRestConnection:(id)connector{
	btnSave.enabled = TRUE;
	[actSetting stopAnimating];
	actSetting.hidden=YES;
	if (save) {
		alertView();
		save = NO;
	}
}

@end
