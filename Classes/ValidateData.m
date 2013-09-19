//
//  ValidateData.m
//  SOSBEACON
//
//  Created by cncsoft on 6/30/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "ValidateData.h"
#pragma mark ValidateData

//valid email

BOOL checkMail(NSString *email) {
	NSString *emailRegEx = @"[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,20}";
	NSPredicate *regExPredicate =[NSPredicate predicateWithFormat:@"SELF MATCHES %@", emailRegEx];
	BOOL myStringMatchesRegEx = [regExPredicate evaluateWithObject:email];
	if(!myStringMatchesRegEx){
		return FALSE;
	}
	return TRUE;
}
//valid phone usa
BOOL checkPhone(NSString *phone){
		
	NSString *phoneRegEx = @"[0-9]+";
	NSPredicate *regExPredicate =[NSPredicate predicateWithFormat:@"SELF MATCHES %@", phoneRegEx];
	BOOL myStringMatchesRegEx = [regExPredicate evaluateWithObject:phone];
	if(!myStringMatchesRegEx){
		return FALSE;
		
	}
	return TRUE;
}

BOOL checkPassWord(NSString *pass){
	NSString *passRegEx = @"[A-Z0-9a-z]{6,24}+";
	NSPredicate *regExPredicate =[NSPredicate predicateWithFormat:@"SELF MATCHES %@", passRegEx];
	BOOL myStringMatchesRegEx = [regExPredicate evaluateWithObject:pass];
	if(!myStringMatchesRegEx){
		return FALSE;
	}
	return TRUE;
}
// Function check space
BOOL checkSpace (NSString *nameSpace){
	NSString *stringName = [nameSpace stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];

	if ([stringName isEqualToString:@""]) {
		
	//	NSLog(@"False");
		return FALSE;
	}else {
	//	NSLog(@"True");
		return TRUE;
	}

}

void alertView(){
	/*
	UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error Internet Connection"
													    message:NSLocalizedString(@"Cannotgetdata",@"")
													   delegate:nil
											  cancelButtonTitle:@"OK"
											  otherButtonTitles:nil];
	[alertView show];
	[alertView release];
	
	return;
	 */
}








