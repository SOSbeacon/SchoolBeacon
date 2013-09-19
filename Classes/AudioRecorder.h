//
//  AudioRecorder.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 16/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <AVFoundation/AVFoundation.h>
#import <CoreAudio/CoreAudioTypes.h>
#import <CoreLocation/CoreLocation.h>
#import "RestConnection.h"

@class SOSBEACONAppDelegate;
@class CaptorView;

@interface AudioRecorder : UIView <AVAudioSessionDelegate,AVAudioRecorderDelegate>{
	SOSBEACONAppDelegate *appDelegate;
	AVAudioRecorder *soundRecorder;
	NSTimer *countDown;
	BOOL isRecording;
	IBOutlet UILabel *timeDisplay;
	IBOutlet UILabel *sizeDisplay;
	IBOutlet UILabel *blockDisplay;
	CaptorView *captorView;
	NSInteger block;
	BOOL isUpload;
	NSString *currentFile;
}

@property (nonatomic, retain) AVAudioRecorder *soundRecorder;
@property (nonatomic, retain) IBOutlet UILabel *timeDisplay;
@property (nonatomic, retain) IBOutlet UILabel *sizeDisplay;
@property (nonatomic, assign) NSTimer *countDown;
@property (nonatomic) NSInteger block;
- (void)initAudio;
- (IBAction)closeAndStop;
- (void)startRecord;

@end
