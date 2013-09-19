//
//  NSStringEscaping.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 15/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import "NSStringEscaping.h"

@implementation NSString (EscapingUtils)
- (NSString *)stringByPreparingForURL {
	NSString *escapedString = (NSString *)CFURLCreateStringByAddingPercentEscapes(kCFAllocatorDefault,
																				  (CFStringRef)self,
																				  NULL,
																				  (CFStringRef)@":/?=,!$&'()*+;[]@#",
																				  CFStringConvertNSStringEncodingToEncoding(NSUTF8StringEncoding));
	
	return [escapedString autorelease];
}
@end
