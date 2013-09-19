//
//  Personal.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 08/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import <Foundation/Foundation.h>

//define satatus contact

#define CONTACT_STATUS_NORMAL 0
#define CONTACT_STATUS_NEW 1
#define CONTACT_STATUS_MODIFIED 2
#define CONTACT_STATUS_DELETED 3

@interface Personal : NSObject {
	NSInteger contactID;
	NSString *contactName;
	NSString *email;
	NSString *voidphone;
	NSString *textphone;	
	BOOL typeContact; // 1 - Default contact; 0 - Manual Contact
	NSInteger status;
}
@property (nonatomic) BOOL typeContact;
@property (nonatomic) NSInteger contactID;
@property (nonatomic,copy) NSString *contactName;
@property (nonatomic,copy) NSString *email;
@property (nonatomic,copy) NSString *voidphone;
@property (nonatomic,copy) NSString *textphone;
@property (nonatomic) NSInteger status;

-(void)copyObject:(Personal*)object;

@end
