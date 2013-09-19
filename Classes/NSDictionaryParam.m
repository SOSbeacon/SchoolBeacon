//
//  NSDictionaryParam.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 15/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import "NSDictionaryParam.h"
#import "NSStringEscaping.h"

@implementation NSDictionary (ParamUtils)
- (NSString *)toQueryString {
    NSMutableArray *pairs = [[[NSMutableArray alloc] init] autorelease]; 
    for (id key in [self allKeys]) { 
        id value = [self objectForKey:key]; 
        if ([value isKindOfClass:[NSArray class]]) { 
            for (id val in value) { 
                [pairs addObject:[NSString stringWithFormat:@"%@=%@",key, [val stringByPreparingForURL]]];   
            } 
        } else {
			NSString *value1;
			if([value isKindOfClass:[NSNumber class]])
			{
				value1 = [NSString stringWithFormat:@"%@",value];
			}
			else if([value isKindOfClass:[NSDecimalNumber class]]) {
				value1 = [NSString stringWithFormat:@"%@",value];
			}
			else {
				value1 = value;
			}
            [pairs addObject:[NSString stringWithFormat:@"%@=%@",key, [value1 stringByPreparingForURL]]]; 
        } 
    } 
    return [pairs componentsJoinedByString:@"&"]; 
}

- (NSData*)toData
{
	NSString* boundary = [NSString stringWithString:@"_insert_some_boundary_here_"];
	NSArray* keys = [self allKeys];
	NSMutableData* result = [[NSMutableData alloc] initWithCapacity:100];
	
	int i;
	for (i = 0; i < [keys count]; i++) 
	{
		id value = [self valueForKey: [keys objectAtIndex: i]];
		[result appendData:[[NSString stringWithFormat:@"--%@\n", boundary] dataUsingEncoding:NSASCIIStringEncoding]];
		if ([value class] == [NSString class] || [value class] == [NSConstantString class])
		{
			[result appendData:[[NSString stringWithFormat:@"Content-Disposition: form-data; name=\"%@\"\n\n", [keys objectAtIndex:i]] dataUsingEncoding:NSASCIIStringEncoding]];
			[result appendData:[[NSString stringWithFormat:@"%@",value] dataUsingEncoding:NSASCIIStringEncoding]];
		}
		else if ([value class] == [NSURL class] && [value isFileURL])
		{
			[result appendData:[[NSString stringWithFormat:@"Content-Disposition: form-data; name=\"%@\"; filename=\"%@\"\n", [keys objectAtIndex:i], [[value path] lastPathComponent]] dataUsingEncoding:NSASCIIStringEncoding]];
			[result appendData:[[NSString stringWithString:@"Content-Type: application/octet-stream\n\n"] dataUsingEncoding:NSASCIIStringEncoding]];
			[result appendData:[NSData dataWithContentsOfFile:[value path]]];
		}
		[result appendData:[[NSString stringWithString:@"\n"] dataUsingEncoding:NSASCIIStringEncoding]];
	}
	[result appendData:[[NSString stringWithFormat:@"--%@--\n", boundary] dataUsingEncoding:NSASCIIStringEncoding]];
	
	return [result autorelease];
}
@end
