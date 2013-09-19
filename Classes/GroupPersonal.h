//
//  GroupPersonal.h
//  SOSBEACON
//
//  Created by cncsoft on 6/24/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <Foundation/Foundation.h>

@interface GroupPersonal : NSObject {
	NSString *idGroup;
	NSString *phone_id;
	NSString *nameGroup;
	
}
@property (nonatomic, retain) NSString *nameGroup;
@property (nonatomic, retain) NSString *idGroup;
@property (nonatomic, retain) NSString *phone_id;
@end
