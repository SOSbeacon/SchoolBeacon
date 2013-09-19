//
//  NSDictionary+Utils.h
//  TNHRequestHelper
//
//  Created by Thao Nguyen Huy on 7/16/12.
//  Copyright (c) 2012 Coding Life. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSDictionary (Utils)

- (NSMutableString *)toQueryString;
- (NSMutableDictionary *)toMutableDictionary;

@end
