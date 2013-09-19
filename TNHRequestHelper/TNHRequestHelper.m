//
//  TNHRequestHelper.m
//  Coding Life
//
//  Created by Thao Nguyen Huy on 6/20/12.
//  Copyright (c) 2012 CNC Software. All rights reserved.
//

#import "TNHRequestHelper.h"

@implementation TNHRequestHelper

#pragma mark -
#pragma mark - Request methods
// writed by Thao Nguyen Huy
// coding Life
+ (void)sendPostRequest:(NSString *)request withParams:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller {
    NSString *URLString = [request stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIFormDataRequest *formDataRequest = [ASIFormDataRequest requestWithURL:URL];
    [formDataRequest setRequestMethod:@"POST"];
    for (NSString *key in [params allKeys]) {
        [formDataRequest setPostValue:[params objectForKey:key] forKey:key];
    }
    formDataRequest.delegate = controller;
    formDataRequest.tag = 1;
    [formDataRequest startAsynchronous];
}

+ (void)sendGetRequest:(NSString *)request withParams:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller {
    NSString *URLString = [request stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIHTTPRequest *httpRequest = [ASIHTTPRequest requestWithURL:URL];
    [httpRequest setRequestMethod:@"GET"];
    for (NSString *key in [params allKeys]) {
        [httpRequest addRequestHeader:key value:[params objectForKey:key]];
    }
    httpRequest.delegate = controller;
    httpRequest.tag = 2;
    [httpRequest startAsynchronous];    
}

+ (void)sendPutRequest:(NSString *)request withParams:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller {
    NSString *URLString = [request stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIHTTPRequest *httpRequest = [ASIHTTPRequest requestWithURL:URL];
    [httpRequest setRequestMethod:@"PUT"];
    [httpRequest setValuesForKeysWithDictionary:params];
//    for (NSString *key in [params allKeys]) {
//        [httpRequest addRequestHeader:key value:[params objectForKey:key]];
//    }    
    httpRequest.delegate = controller;
    httpRequest.tag = 3;
    [httpRequest startAsynchronous];    
}

+ (void)sendDeleteRequest:(NSString *)request withParams:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller {
    NSString *URLString = [request stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIHTTPRequest *httpRequest = [ASIHTTPRequest requestWithURL:URL];
    [httpRequest setRequestMethod:@"DELETE"];
    [httpRequest setValuesForKeysWithDictionary:params];
    httpRequest.delegate = controller;
    httpRequest.tag = 4;
    [httpRequest startAsynchronous];    
}

+ (void)sendPostRequest:(NSString *)request tag:(NSInteger)tag params:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller {
    NSString *URLString = [request stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIFormDataRequest *formDataRequest = [ASIFormDataRequest requestWithURL:URL];
    [formDataRequest setRequestMethod:@"POST"];
    for (NSString *key in [params allKeys]) {
        [formDataRequest setPostValue:[params objectForKey:key] forKey:key];
    }
    formDataRequest.delegate = controller;
    formDataRequest.tag = tag;
    [formDataRequest startAsynchronous];    
}

+ (void)sendGetRequest:(NSString *)request tag:(NSInteger)tag params:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller {
    NSString *URLString = [request stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIHTTPRequest *httpRequest = [ASIHTTPRequest requestWithURL:URL];
    [httpRequest setRequestMethod:@"GET"];
    for (NSString *key in [params allKeys]) {
        [httpRequest addRequestHeader:key value:[params objectForKey:key]];
    }
    httpRequest.delegate = controller;
    httpRequest.tag = tag;
    [httpRequest startAsynchronous];    
}

+ (void)sendPutRequest:(NSString *)request tag:(NSInteger)tag params:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller {
    NSString *URLString = [request stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIHTTPRequest *httpRequest = [ASIHTTPRequest requestWithURL:URL];
    [httpRequest setRequestMethod:@"PUT"];
    [httpRequest setValuesForKeysWithDictionary:params];
    httpRequest.delegate = controller;
    httpRequest.tag = tag;
    [httpRequest startAsynchronous];     
}

+ (void)sendDeleteRequest:(NSString *)request tag:(NSInteger)tag params:(NSDictionary *)params receiver:(id<ASIHTTPRequestDelegate>)controller {
    NSString *URLString = [request stringByAddingPercentEscapesUsringEncoding:NSUTF8StringEncoding];
    NSURL *URL = [NSURL URLWithString:URLString];
    ASIHTTPRequest *httpRequest = [ASIHTTPRequest requestWithURL:URL];
    [httpRequest setRequestMethod:@"DELETE"];
    [httpRequest setValuesForKeysWithDictionary:params];
    httpRequest.delegate = controller;
    httpRequest.tag = tag;
    [httpRequest startAsynchronous];    
}

@end
