//
//  NSDictionaryParam.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 15/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface NSDictionary (ParamUtils)
- (NSString*) toQueryString;
- (NSData*)toData;
@end
