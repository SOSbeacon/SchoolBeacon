    //
//  GroupPersonal.m
//  SOSBEACON
//
//  Created by cncsoft on 6/24/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "GroupPersonal.h"

@implementation GroupPersonal
@synthesize phone_id;
@synthesize idGroup;
@synthesize nameGroup;

- (void)dealloc {
	[idGroup release];
	[phone_id release];
	[nameGroup release];
	[super dealloc];
}

@end
