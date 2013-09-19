//
//  PhotoUploader.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/10/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "PhotoUploader.h"
#import "Uploader.h"
#import "ValidateData.h"

@implementation PhotoUploader
@synthesize endUpload;

- (void)clear
{
    totalCount = 0;
    sucessCount = 0;
    failCount = 0;
}
- (id) init
{
	self = [super init];
	if (self != nil) {
		if(![[NSFileManager defaultManager] fileExistsAtPath:PHOTO_FOLDER])
		{
			[[NSFileManager defaultManager] createDirectoryAtPath:PHOTO_FOLDER withIntermediateDirectories:NO attributes:nil error:nil];
		}
		appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
		restConnection = [[RestConnection alloc] initWithBaseURL:SERVER_URL];
		restConnection.delegate=self;
		upNext = NO;
		endUpload = NO;
	}
	return self;
}

- (void) dealloc
{
	if(array!=nil) [array release];
	[super dealloc];
}

- (void)uploadAll:(Uploader*)sender {
	//NSLog(@"PhotoUploader:uploadAll:0",nil);
	if(array!=nil)
	{
		if([array count]>0)
		{
			upNext = YES;
            [self clear];
			return;
		}
	}

	uploader=sender;
	NSArray *fileList = [[NSFileManager defaultManager] contentsOfDirectoryAtPath:PHOTO_FOLDER error:nil];
	array = [[NSMutableArray alloc] init];
	
	for (NSString *str in fileList) {
		NSRange foundRange=[[str lowercaseString] rangeOfString:@".jpg"];
		if(foundRange.location != NSNotFound) {
			[array addObject:str];
		}
	}
	[self uploadPhoto];
}

- (void)uploadPhoto 
{

	if([array count]>0)
	{
   // [uploader setTitle2:[NSString stringWithFormat:@"Uploading photo : %@",[array objectAtIndex:0]]];
    [uploader setTitle2:[NSString stringWithFormat:@"Uploading photo : %d",sucessCount+failCount +1]];
		NSArray *key;
		NSArray *obj;
		if (appDelegate.uploader.isAlert==TRUE) {
/*		
			key = [NSArray arrayWithObjects:@"phoneId",@"alertId",@"token",@"type",nil];
			obj = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.phoneID],[NSString stringWithFormat:@"%d",uploader.uploadId],appDelegate.apiKey,@"0",nil];
*/ 
			key = [NSArray arrayWithObjects:@"format", @"_method", @"alertId",@"token",@"type", @"userId", @"schoolId", nil];
			obj = [NSArray arrayWithObjects:@"json", @"post", appDelegate.alertId, appDelegate.apiKey, @"0", [NSString stringWithFormat:@"%d", appDelegate.userID], appDelegate.schoolId, nil];			
		}
/*
		else {
			key = [NSArray arrayWithObjects:@"phoneId",@"alertlogType",@"token",@"type",nil];
			obj = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.phoneID],@"2",appDelegate.apiKey,@"0",nil];
			
		}
*/
		NSDictionary *params = [NSDictionary dictionaryWithObjects:obj forKeys:key];
		
		NSData *theData = [NSData dataWithContentsOfFile:[PHOTO_FOLDER stringByAppendingPathComponent:[array objectAtIndex:0]]];
		//NSLog(@" image size %d",[theData length]);
		[restConnection uploadPath:@"http://sosbeacon.org/school/data" withOptions:params withFileData:theData];
	}
	else
	{
		[uploader setTitle2:@"Upload photos done!"];
		[array release];
		array = nil;
		
		if(upNext)
		{
			upNext = NO;
			[self uploadAll:uploader];
		}
		else
		{
			//send alert here
			if(endUpload)
			{
				endUpload=NO;
				uploader.isPhotoUpOK = YES;
			}
            [self clear];
			[uploader finishUpload];
		}
	}
}

- (void)removeAllOldFile {
	NSArray *fileList = [[NSFileManager defaultManager] contentsOfDirectoryAtPath:PHOTO_FOLDER error:nil];
	for (NSString *str in fileList) {
		NSRange foundRange=[[str lowercaseString] rangeOfString:@".jpg"];
		if(foundRange.location != NSNotFound) {
			[[NSFileManager defaultManager] removeItemAtPath:[PHOTO_FOLDER stringByAppendingPathComponent:str] error:nil];
		}
	}
}

#pragma mark -
#pragma mark finishRequest
- (void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector {
	if([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"])
	{
        
        sucessCount ++;
        if (failCount >0)
        {
            [uploader setTitle3:[NSString stringWithFormat:@"Sucessful : %d ; Failed : %d " ,sucessCount,failCount ]];
        }
        else
        {
            [uploader setTitle3:[NSString stringWithFormat:@"Sucessful : %d " ,sucessCount ]];

        }   
	//	NSLog(@"upload photo ok : %@",[array objectAtIndex:0]);
		[[NSFileManager defaultManager] removeItemAtPath:[PHOTO_FOLDER stringByAppendingPathComponent:[array objectAtIndex:0]] error:nil];
		[array removeObjectAtIndex:0];
		[self uploadPhoto];
	}
	else
	{
		
		failCount ++;
        if (failCount >0)
        {
            [uploader setTitle3:[NSString stringWithFormat:@"Sucessful : %d ; Failed : %d " ,sucessCount,failCount ]];
        }
        else
        {
            [uploader setTitle3:[NSString stringWithFormat:@"Sucessful : %d " ,sucessCount ]];
            
        } 
		[uploader setTitle2:[NSString stringWithFormat:@"Upload fails photo : %@",[array objectAtIndex:0]]];
		[array removeObjectAtIndex:0];
		//[self uploadPhoto];
        [self performSelector:@selector(uploadPhoto) withObject:nil afterDelay:1.5];
	}
}

- (void) DimisAlertView:(UIAlertView*)alertView {
	[alertView dismissWithClickedButtonIndex:0 animated:TRUE];
}

- (void)cantConnection:(NSError*)error andRestConnection:(id)connector {
    //NSLog(@"send image error : %@",error);
    
	failCount= failCount + [array count];
    [uploader setTitle3:[NSString stringWithFormat:@"Sucessful : %d ; Failed : %d " ,sucessCount,failCount ]];
	alertView();
	
    if (array) {
        [array release];
        array = nil;
        [self clear];
    }
	if(endUpload)
	{
		endUpload=NO;
		uploader.isPhotoUpOK = YES;
	}
    [self clear];
    [uploader performSelector:@selector(finishUpload) withObject:nil afterDelay:1.5];
	//[uploader finishUpload];
	
}

@end
