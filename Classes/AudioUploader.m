//
//  AudioUploader.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/14/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "AudioUploader.h"
#import "Uploader.h"
#import "AudioRecorder.h"
@implementation AudioUploader
@synthesize endUpload;



-(void)playaudio
{
   // NSURL *bipURL = [NSURL fileURLWithPath:[AUDIO_FOLDER stringByAppendingPathComponent:[array objectAtIndex:0]]];
    NSURL *bipURL = [NSURL fileURLWithPath:[[NSBundle mainBundle] pathForResource:@"sound1_18:05:31" ofType:@"caf"]];
	NSError *error;
	AVAudioPlayer *soundBip = [[AVAudioPlayer alloc] initWithContentsOfURL:bipURL error:&error];
	if(error)
	{
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"ERROR" message:@"Can't play sound" delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show];
		[alert release];
	}
    soundBip.volume = 1.0;
	[soundBip play];    
}
- (id)init
{
	self = [super init];
	if (self != nil) {
		if(![[NSFileManager defaultManager] fileExistsAtPath:AUDIO_FOLDER])
		{
			[[NSFileManager defaultManager] createDirectoryAtPath:AUDIO_FOLDER withIntermediateDirectories:NO attributes:nil error:nil];
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
	if(array!=nil) 
	{
		if([array count]>0)
		{
			upNext = YES;
			return;
		}
	}
	
	uploader=sender;
	NSArray *fileList = [[NSFileManager defaultManager] contentsOfDirectoryAtPath:AUDIO_FOLDER error:nil];
	array = [[NSMutableArray alloc] init  ];
	
	for (NSString *str in fileList) {
		NSRange foundRange=[[str lowercaseString] rangeOfString:@".caf"];
		if(foundRange.location != NSNotFound) {
		//	NSLog(@"file : %@",str);
			[array addObject:str];
		}
	}
	
	[self uploadAudio];
}

- (void)uploadAudio {
	if([array count]>0)
	{
		[uploader setTitle1:[NSString stringWithFormat:@"Uploading audio : %@",[array objectAtIndex:0]]];
		NSArray *key;
		NSArray *obj;
		if (appDelegate.uploader.isAlert==TRUE) {
/*            
			NSLog(@"send with alert");
			key = [NSArray arrayWithObjects:@"phoneId",@"alertId",@"token",@"type",nil];
			obj = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.phoneID],[NSString stringWithFormat:@"%d",uploader.uploadId],appDelegate.apiKey,@"1",nil];
*/ 
			key = [NSArray arrayWithObjects:@"format", @"_method", @"alertId",@"token",@"type", @"userId", @"schoolId", nil];
			obj = [NSArray arrayWithObjects:@"json", @"post", appDelegate.alertId, appDelegate.apiKey, @"1", [NSString stringWithFormat:@"%d", appDelegate.userID], appDelegate.schoolId, nil];			
		}
/*        
		else {
			NSLog(@"send with no alert");
			key = [NSArray arrayWithObjects:@"phoneId",@"alertlogType",@"token",@"type",nil];
			obj = [NSArray arrayWithObjects:[NSString stringWithFormat:@"%d",appDelegate.phoneID],@"2",appDelegate.apiKey,@"1",nil];
			
		}
*/		
		NSDictionary *params = [NSDictionary dictionaryWithObjects:obj forKeys:key];
        NSLog(@"params: %@", params);
		NSData *theData = [NSData dataWithContentsOfFile:[AUDIO_FOLDER stringByAppendingPathComponent:[array objectAtIndex:0]]];
        //NSLog(@"send data to sever : %@  and audio size: %d",params,[theData length]);
		[restConnection uploadPath:@"http://sosbeacon.org/school/data" withOptions:params withFileData:theData];
        
      timer =  [NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(timerTick) userInfo:nil repeats:YES];
        countTime =0;
	}
	else
	{
		[uploader setTitle1:@"Upload audio done!"];
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
				endUpload = NO;
				uploader.isAudioUpOK = YES;
			}
			[uploader finishUpload];
		}
	}
}

- (void)removeAllOldFile {
    
    
	NSArray *fileList = [[NSFileManager defaultManager] contentsOfDirectoryAtPath:AUDIO_FOLDER error:nil];
	for (NSString *str in fileList) {
		NSRange foundRange=[[str lowercaseString] rangeOfString:@".caf"];
		if(foundRange.location != NSNotFound) {
			[[NSFileManager defaultManager] removeItemAtPath:[AUDIO_FOLDER stringByAppendingPathComponent:str] error:nil];
		}
	}
   
}

#pragma mark -
#pragma mark RestConnectionDelegate
- (void)finishRequest:(NSDictionary *)arrayData andRestConnection:(id)connector {
	//NSLog(@"finish upload audio: %@",arrayData);
    countTime =0;
    [timer invalidate];
    timer = nil;
    
	if([[[arrayData objectForKey:@"response"] objectForKey:@"success"] isEqualToString:@"true"])
	{
		[[NSFileManager defaultManager] removeItemAtPath:[AUDIO_FOLDER stringByAppendingPathComponent:[array objectAtIndex:0]] error:nil];
		[array removeObjectAtIndex:0];
		[self uploadAudio];
	}
	else
	{
        [uploader setTitle1:[NSString stringWithFormat:@"Upload audio fail: %@",[array objectAtIndex:0]]];
        [array removeObjectAtIndex:0];
        [self performSelector:@selector(uploadAudio) withObject:nil afterDelay:1.2];
	}
}

- (void)cantConnection:(NSError*)error andRestConnection:(id)connector 
{
//    NSLog(@"error when upload audio: %@",error);
//    //////
//    [uploader setTitle1:[NSString stringWithFormat:@"Upload audio fail: %@",[array objectAtIndex:0]]];
//
//    if (array) {
//        [array release];
//        array = nil;
//    }
//	if(endUpload)
//	{
//		endUpload=NO;
//         uploader.isAudioUpOK = YES;
//	}
//    [uploader performSelector:@selector(finishUpload) withObject:nil afterDelay:3.0];
//    
    

}
-(void)timerTick
{
    countTime++;
   // NSLog(@" time : %d",countTime);
    if(countTime == 60)
    {
        NSLog(@"error when upload audio can not connection ");
        [uploader setTitle1:[NSString stringWithFormat:@"Upload audio fail: %@",[array objectAtIndex:0]]];
        
        if (array) {
            [array release];
            array = nil;
        }
        if(endUpload)
        {
            endUpload=NO;
            uploader.isAudioUpOK = YES;
        }
        [uploader performSelector:@selector(finishUpload) withObject:nil afterDelay:3.0];
        
        countTime =0;
        [timer invalidate];
        timer = nil;
        
    }
}

@end
