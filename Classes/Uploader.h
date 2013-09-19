//
//  Uploader.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/14/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "PhotoUploader.h"
#import "AudioUploader.h"
#import "SOSBEACONAppDelegate.h"

@class CaptorView;

@protocol UploaderDelegate <NSObject>
- (void)uploadFinish;
- (void)requestUploadIdFinish:(NSInteger)uploadId;
@end


@interface Uploader : NSObject <RestConnectionDelegate> {
	SOSBEACONAppDelegate *appDelegate;
	id <UploaderDelegate> delegate;
	BOOL isAudioUpOK;
	BOOL isPhotoUpOK;
	
	PhotoUploader *photoUploader;
	AudioUploader *audioUploader;
	
	RestConnection *restConnection;
	
	CaptorView *captorView;
	
	BOOL isSendAlert; //YES : send alert after upload done
	BOOL autoUpload; //YES : auto upload file
	NSInteger uploadId;
	BOOL isAlert;
	NSInteger flag;
	
	
}

@property (nonatomic,assign) id <UploaderDelegate> delegate;
@property (nonatomic) BOOL isAudioUpOK;
@property (nonatomic) BOOL isPhotoUpOK;
@property (nonatomic) BOOL isSendAlert;
@property (nonatomic) BOOL isAlert;
@property (nonatomic) BOOL autoUpload;
@property (nonatomic) NSInteger uploadId;

- (void)setCaptorView:(CaptorView*)object;
- (void)uploadPhoto;
- (void)uploadAudio;
- (void)setTitle1:(NSString*)title;
- (void)setTitle2:(NSString*)title;
- (void)setTitle3:(NSString*)title;
- (void)finishUpload;
- (void)finishWait;
- (void)sendAlert;
- (void)sendImOkAlert;
- (void)endUploadPhoto;
- (void)endUploadAudio;

- (void)removeAllFileCache;

@end
