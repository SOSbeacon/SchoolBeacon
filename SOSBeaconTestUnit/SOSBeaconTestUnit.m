//
//  SOSBeaconTestUnit.m
//  SOSBeaconTestUnit
//
//  Created by The Blue on 7/31/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "SOSBeaconTestUnit.h"

@implementation SOSBeaconTestUnit

- (void)setUp
{
    [super setUp];
    delegate = (SOSBEACONAppDelegate *)[[UIApplication sharedApplication] delegate];
}

- (void)tearDown
{
    // Tear-down code here.
    
    [super tearDown];
}

@end
