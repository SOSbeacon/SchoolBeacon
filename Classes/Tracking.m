//
//  Tracking.m
//  SOSBEACON
//
//  Created by Geoff Heeren on 2/9/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import "Tracking.h"
@implementation Tracking
+(void)startTracking{
	if ([self doTracking]){
		[Flurry startSession:[[[NSBundle mainBundle] infoDictionary] objectForKey:@"FlurryKey"]];
	}

}
+(void)updateLocation:(CLLocation*)location{
	if ([self doTracking]){
//		[Flurry setLocation:location];
        [Flurry setLatitude:location.coordinate.latitude
                  longitude:location.coordinate.longitude
         horizontalAccuracy:location.horizontalAccuracy
           verticalAccuracy:location.verticalAccuracy];
	}
}
+(void)trackEvent:(NSString*)eventName{
	if ([self doTracking]){
		[Flurry logEvent:eventName];
	}
}
+(void)trackException:(NSException *)exception {
	if ([self doTracking]){
		[Flurry logError:@"Uncaught" message:@"Crash!" exception:exception];
	}
}
+(BOOL)doTracking{
	if ([UIDevice currentDevice] && [UIDevice currentDevice]!=nil && [UIDevice currentDevice].model!=nil)
		return ![[UIDevice currentDevice].model isEqualToString: @"iPhone Simulator"];
	else 
		return NO;
}
@end
