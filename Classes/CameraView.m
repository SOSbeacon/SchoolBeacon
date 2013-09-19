//
//  CameraView.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/9/10.
//  Copyright 2010 CNC. All rights reserved.
//

#define kTimeWaitForCap 7

#import "SOSBEACONAppDelegate.h"
#import "CameraView.h"
#import "CaptorView.h"

@implementation CameraView
@synthesize capCount;
@synthesize captorView;
@synthesize autoMode;
@synthesize countTime;
@synthesize count;
- (id) init
{
	countTime = 30;
	capCount = 0;
	self = [super init];
	if (self != nil) {
		if([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeCamera])
		{
			self.sourceType = UIImagePickerControllerSourceTypeCamera;
			NSLog(@"source **** CAMERA");
		}
		else 
        {
            self.sourceType = UIImagePickerControllerSourceTypePhotoLibrary;
            NSLog(@"source **** PHOTOLIBRARY");
 
        }
		
		self.showsCameraControls = NO;
		autoMode=YES;
		appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];

	}
    else
    {
        NSLog(@"can not init ");
    }
	return self;
}

- (void)stopCapture {
	capCount = -1;
	if (countDownTimer!=nil)
		[countDownTimer invalidate];
}

- (void)startCapture {
	captorView.label3.text = @"Photo : 0";
	capCount = 0;
    NSInteger duration = [appDelegate.recordDuration integerValue];
    switch (duration) {
        case 1:
        case 2:    
        case 6:
            countTime = duration * 30;
            break;
        case 3:
        case 4:
        case 5:
            countTime = (duration * 30) - 30;
            break;
        default:
            break;
    }
	countDown=10;
	[self updateCountDown];
	countDownTimer=[NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(timerTick) userInfo:nil repeats:YES];
}


- (void)captureImage 
{
	if(capCount==-1) return;
	[self takePicture];
	captorView.captureButton.hidden = YES;
}

-(void)timerTick{
	
	countTime--;
	countDown--;
/*
	if (((countTime  == 20)||(countTime == 10)) && captorView.isauto) 
	{
		[self captureImage];
	}
   	if (((countTime == 13) || (countTime == 23)) && captorView.isauto) 
	{
		countDown = 3;
		[appDelegate playSound3];
	}
*/ 
/** 
 *  edited by Thao Nguyen Huy
 *  July 23, 2012    
 */
    if (countTime % 10 == 0 && countTime / 10 != 0 && captorView.isauto) {
        [self captureImage];
    }
    if (countTime % 10 == 3 && countTime / 10 != 0 && captorView.isauto) {
        countDown = 3;
        [appDelegate playSound3];
    }
	if (countDown >0 && countDown <=3 && captorView.isauto) 
	{
		captorView.busy.hidden = NO;
		[captorView.busy startAnimating];
		captorView.lnlTopMessage.textColor =[ UIColor redColor];
		captorView.lnlTopMessage.text=[NSString stringWithFormat:@"Next Photo in %d secs",countDown];
		captorView.captureButton.hidden = YES;
	}
	else
	{
		[captorView.busy stopAnimating];
		captorView.busy.hidden = YES;
		captorView.lnlTopMessage.text=[NSString stringWithFormat:@" "];
	}
	
	if (countTime == 0) 
	{
		[countDownTimer invalidate];
		[captorView stopCaptorOnCamera:nil];	
	}	
	captorView.label3.text = [NSString stringWithFormat:@"Photo : %d",capCount];
	captorView.countLabel.text = [NSString stringWithFormat:@"%d",countTime];
	//NSLog(@"timerTick",nil);
}

-(void)updateCountDown {
    
}

- (void)dealloc {
	NSLog(@"********** DEALLOC Camera View  ************");
    [super dealloc];
}

- (void)viewDidDisappear:(BOOL)animated {
	[super viewDidDisappear:animated];
}

-(void) viewDidAppear: (BOOL)animated {
	[super viewDidAppear:animated];	
	[self startCapture];
}

@end