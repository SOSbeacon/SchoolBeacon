//
//  AudioRecorder.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 16/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import "AudioRecorder.h"
#import "SOSBEACONAppDelegate.h"
#import "RestConnection.h"
#import "HomeView.h"
#import "CaptorView.h"
#import "StatusView.h"
#import "Uploader.h"

#define DOCUMENTS_FOLDER [NSHomeDirectory() stringByAppendingPathComponent:@"Documents"]

@implementation AudioRecorder
@synthesize soundRecorder,timeDisplay,countDown,sizeDisplay;
@synthesize block;

#pragma mark -
#pragma mark initFrame

- (id)initWithFrame:(CGRect)frame {
    if ((self = [super initWithFrame:frame])) {
        // Initialization code
    }
    return self;
}

#pragma mark initAudio
// init audio record
- (void)initAudio {

	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
    
	AVAudioSession *audioSession = [AVAudioSession sharedInstance];
   	audioSession.delegate = self;
	[audioSession setActive:YES error: nil];
	[audioSession setCategory:AVAudioSessionCategoryPlayAndRecord error:nil];
	UInt32 audioRouteOverride = kAudioSessionOverrideAudioRoute_Speaker;
	AudioSessionSetProperty(kAudioSessionProperty_OverrideAudioRoute,sizeof(audioRouteOverride),&audioRouteOverride);
	
    //[[AVAudioSession sharedInstance] setActive:NO error:nil];

//	timeDisplay.text=[NSString stringWithFormat:@"00:%@",[appDelegate.settingArray objectForKey:ST_VoiceRecordDuration]];
	sizeDisplay.text=@"0 KB";
	
	countDown = [NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(countTimer) userInfo:nil repeats:YES];
	
	if(![[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/Audio",DOCUMENTS_FOLDER]])
	{
		[[NSFileManager defaultManager] createDirectoryAtPath:[NSString stringWithFormat:@"%@/Audio",DOCUMENTS_FOLDER] withIntermediateDirectories:NO attributes:nil error:nil];
	}
}

- (void)stopRecordAudio {
	//NSLog(@"STOP Record");
	isRecording = NO;
	timeDisplay.text=@"Time: 00:00";
	sizeDisplay.text=@"Size: 0 KB";
	if(soundRecorder!=nil)
	{
		[soundRecorder stop];
		[soundRecorder release];
		soundRecorder = nil;
	}
	
	
}

- (void)startRecordAudio {
	//NSLog(@"START Recording ------>>>");
	isRecording = YES;
	[appDelegate playSound];
	NSDateFormatter *formatter;
	NSString        *dateString;
	formatter = [[NSDateFormatter alloc] init];
	[formatter setDateFormat:@"HH:mm:ss"];
	dateString = [formatter stringFromDate:[NSDate date]];
	[formatter release];
	NSMutableDictionary *recordSettings = [[NSMutableDictionary alloc] init];
	[recordSettings setValue:[NSNumber numberWithInt:kAudioFormatLinearPCM] forKey:AVFormatIDKey];
	[recordSettings setValue:[NSNumber numberWithFloat:1000.0] forKey:AVSampleRateKey]; 
	[recordSettings setValue:[NSNumber numberWithInt: 1] forKey:AVNumberOfChannelsKey];
	[recordSettings setValue:[NSNumber numberWithInt:4] forKey:AVLinearPCMBitDepthKey];
	[recordSettings setValue:[NSNumber numberWithBool:NO] forKey:AVLinearPCMIsBigEndianKey];
	[recordSettings setValue:[NSNumber numberWithBool:NO] forKey:AVLinearPCMIsFloatKey];
	currentFile = [NSString stringWithFormat:@"%@/Audio/sound%d_%@.caf",DOCUMENTS_FOLDER,block,dateString];
	NSURL *fileUrl=[[NSURL alloc] initFileURLWithPath:currentFile];
	AVAudioRecorder *newRecorder =[[AVAudioRecorder alloc] initWithURL:fileUrl
															  settings:recordSettings
																 error:nil];
	[fileUrl release];
	self.soundRecorder = newRecorder;
    
    [newRecorder release];
    [recordSettings release];
	soundRecorder.delegate = self;
	[soundRecorder prepareToRecord];
    NSInteger settingDuration = [appDelegate.recordDuration integerValue];
    NSInteger recordDuration = 0;
    switch (settingDuration) {
        case 1:
        case 2:    
        case 6:
            recordDuration = settingDuration * 30;
            break;
        case 3:
        case 4:
        case 5:
            recordDuration = (settingDuration * 30) - 30;
            break;
        default:
            break;
    }    
	[soundRecorder recordForDuration:recordDuration];
	
}

- (void)setCaptorView:(CaptorView*)captor {
	captorView=captor;
}

- (void)startRecord {
	isUpload = NO;
	isRecording = NO;
	blockDisplay.text = [NSString stringWithFormat:@"Session: %d",block];
	[self startRecordAudio];
}

- (void)endRecordBlock {
    //NSLog(@"endRecordBlock");
	if(appDelegate.uploader.autoUpload)
    {
        //NSLog(@"///   autoupload audio     ////");
        [appDelegate.uploader uploadAudio]; //upload luon sau khi ghi xong
    }
	[self stopRecordAudio]; 
	
	block--;
	if(block>0) 
	{
		[self startRecordAudio];
	}
	else
	{
		[[AVAudioSession sharedInstance] setActive:NO error:nil];
		
		
		[appDelegate.uploader endUploadAudio];
		if(!appDelegate.uploader.autoUpload) 
        {
           //NSLog(@"*************** not autoupload audio   ***************");
            [appDelegate.uploader uploadAudio];
        }	
    }
}

#pragma mark Action
- (IBAction)closeAndStop {
    isRecording = YES;
    //NSLog(@" closeAndStop  :isRecording:%d",isRecording);
	if(isRecording)
	{
		block = -1;
		[self stopRecordAudio];
	}
	else
	{
		[self endRecordBlock];
	}

}

#pragma mark -
#pragma mark AVAudioRecorderDelegate
- (void)audioRecorderDidFinishRecording:(AVAudioRecorder *) aRecorder successfully:(BOOL)flag
{
	//NSLog (@"audioRecorderDidFinishRecording successfully");
	[self endRecordBlock];
}


- (void)audioRecorderEncodeErrorDidOccur:(AVAudioRecorder *)recorder error:(NSError *)error
{
	//NSLog(@"audio recorder error :%@",error);
}


#pragma mark counttimer

-(void)stopTimer{
	[self stopRecordAudio];
	[[AVAudioSession sharedInstance] setActive:NO error: nil];
}

-(void)countTimer 
{
}

- (void)dealloc {
	//NSLog(@"DEALLOC AudioRecorder");
	[countDown invalidate];
	[timeDisplay release];
	[sizeDisplay release];
	[blockDisplay release];
	[soundRecorder release];
    [super dealloc];
}

@end
