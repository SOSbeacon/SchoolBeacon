//
//  Personal.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 08/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import "Personal.h"
@implementation Personal
@synthesize contactName,voidphone,email,textphone,contactID,typeContact,status;

- (id) init
{
	self = [super init];
	if (self != nil) {
		contactID = -1;
		contactName = @"";
		voidphone = @"";
		email = @"";
		textphone = @"";
		status = CONTACT_STATUS_NORMAL;
	}
	return self;
}

- (void) dealloc
{
	[contactName release];
	[voidphone release];
	[email release];
	[textphone release];
	[super dealloc];
}

-(void)copyObject:(Personal*)object {
	if(object.contactName!=nil) self.contactName = object.contactName;
	if(object.email!=nil) self.email = object.email;
	if(object.voidphone!=nil) self.voidphone = object.voidphone;
	if(object.textphone!=nil) self.textphone = object.textphone;
	
	self.contactID = object.contactID;
	self.typeContact = object.typeContact;
	self.status = object.status;
}

@end
