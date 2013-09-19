//
//  NSDictionary+Utils.m
//  TNHRequestHelper
//
//  Created by Thao Nguyen Huy on 7/16/12.
//  Copyright (c) 2012 Coding Life. All rights reserved.
//

#import "NSDictionary+Utils.h"

@implementation NSDictionary (Utils)

#pragma mark -
#pragma mark - Utils Methods
/**
 * This method convert a dictionary contain strings,
 * numbers, arrays, and dictionaries
 * to a query string, use for methods in Restful web service.
 */
- (NSMutableString *)toQueryString {
    NSMutableString *_ret = [[[NSMutableString alloc] init] autorelease];
    NSMutableArray *_pairs = [[NSMutableArray alloc] init];
    NSArray *_allKeys = [self allKeys];
    for (id key in _allKeys) {
        id val = [self objectForKey:key];
        if ([val isKindOfClass:[NSDictionary class]]) {
            NSMutableString *sub = [val toQueryString];
            if (_ret.length == 0) {
                [_ret appendString:sub];
            }
            else [_ret appendFormat:@"&%@", sub];
        }
        else {
            if ([val isKindOfClass:[NSArray class]]) {
                for (id sub in val) {
                    NSString *pair = [NSString stringWithFormat:@"%@=%@", key, sub];
                    [_pairs addObject:pair];
                }
            }
            else {
                NSString *pair = [NSString stringWithFormat:@"%@=%@", key, val];
                [_pairs addObject:pair];
            }
        }
    }
    _ret = [NSMutableString stringWithString:[_pairs componentsJoinedByString:@"&"]];
    [_pairs release];
    return _ret;
}

/**
 * This method convert a dictionary contain strings,
 * numbers, arrays, and dictionaries
 * to a mutable dictionary, use for methods in Restful web service.
 */
- (NSMutableDictionary *)toMutableDictionary {
    NSMutableDictionary *_ret = [[[NSMutableDictionary alloc] init] autorelease];
    NSArray *_allKeys = [self allKeys];
    for (id key in _allKeys) {
        id val = [self objectForKey:key];        
        if ([val isKindOfClass:[NSDictionary class]]) {
            NSMutableDictionary *sub = [val toMutableDictionary];
            [_ret setValuesForKeysWithDictionary:sub];
        }
        else {
            if ([val isKindOfClass:[NSArray class]]) {
                for (id sub in val)
                    [_ret setObject:sub forKey:key];
            }
            else [_ret setObject:val forKey:key];
        }
    }
    return _ret;
}

@end
