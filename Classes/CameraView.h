//
//  CameraView.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/9/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <AVFoundation/AVFoundation.h>

@class SOSBEACONAppDelegate;
@class CaptorView;

@interface CameraView : UIImagePickerController {
	SOSBEACONAppDelegate *appDelegate;
	NSInteger capCount;
	NSInteger timeWaitForCap;
	CaptorView *captorView;
	NSInteger countDown;
	NSTimer *countDownTimer;
	NSInteger countTime;
	NSInteger count;
	
}
@property(nonatomic)	NSInteger count;
@property(nonatomic)NSInteger countTime;
@property(nonatomic)NSInteger capCount;
@property (nonatomic,assign) CaptorView *captorView;
@property(assign) BOOL autoMode;
- (void)stopCapture;
- (void)startCapture;
- (void)updateCountDown;
- (void)captureImage;
@end
