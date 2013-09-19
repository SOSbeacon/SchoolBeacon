//
//  AudioUploader.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/14/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "RestConnection.h"
#import "SOSBEACONAppDelegate.h"
#define AUDIO_FOLDER [NSHomeDirectory() stringByAppendingPathComponent:@"Documents/Audio"]

@class Uploader;

@interface AudioUploader : NSObject <RestConnectionDelegate> {
	SOSBEACONAppDelegate *appDelegate;
	RestConnection *restConnection;
	NSMutableArray *array;
	Uploader *uploader;
	BOOL upNext;
	BOOL endUpload;
    NSInteger CountUpload;
    NSTimer *timer;
    NSInteger countTime;

}

@property(nonatomic) BOOL endUpload;

- (void)uploadAll:(Uploader*)sender;
- (void)uploadAudio;

- (void)removeAllOldFile;

@end
