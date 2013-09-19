//
//  PhotoUploader.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/10/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "RestConnection.h"
#import "SOSBEACONAppDelegate.h"
#define PHOTO_FOLDER [NSHomeDirectory() stringByAppendingPathComponent:@"Documents/Photo"]

@class Uploader;

@interface PhotoUploader : NSObject <RestConnectionDelegate> {
	SOSBEACONAppDelegate *appDelegate;
	RestConnection *restConnection;
	NSMutableArray *array;
	Uploader *uploader;
	BOOL upNext;
	BOOL endUpload;
    NSInteger totalCount;
    NSInteger sucessCount;
    NSInteger failCount;
}

@property(nonatomic) BOOL endUpload;

- (void)uploadAll:(Uploader*)sender;
- (void)uploadPhoto;

- (void)removeAllOldFile;

@end
