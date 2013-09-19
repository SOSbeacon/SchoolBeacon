//
//  TNHRequestHelper.h
//  Coding Life
//
//  Created by Thao Nguyen Huy on 6/20/12.
//  Copyright (c) 2012 CNC Software. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "ASIHTTPRequest.h"
#import "ASIFormDataRequest.h"
#import "JSON.h"

@interface TNHRequestHelper : NSObject

+ (void)sendPostRequest:(NSString *)request withParams:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller;

+ (void)sendGetRequest:(NSString *)request withParams:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller;

+ (void)sendPutRequest:(NSString *)request withParams:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller;

+ (void)sendDeleteRequest:(NSString *)request withParams:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller;

+ (void)sendPostRequest:(NSString *)request tag:(NSInteger)tag params:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller;

+ (void)sendGetRequest:(NSString *)request tag:(NSInteger)tag params:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller;

+ (void)sendPutRequest:(NSString *)request tag:(NSInteger)tag params:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller;

+ (void)sendDeleteRequest:(NSString *)request tag:(NSInteger)tag params:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller;

@end
