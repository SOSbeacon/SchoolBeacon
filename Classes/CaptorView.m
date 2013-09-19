//
//  CaptorView.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 10/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import "CaptorView.h"
#import "AudioRecorder.h"
#import "SOSBEACONAppDelegate.h"
#import "RestConnection.h"
#import "CameraView.h"
#import "Uploader.h"
#import <QuartzCore/QuartzCore.h>
#import <math.h>
#import "SaveImageOperation.h"
static inline double radians (double degrees) {return degrees * M_PI/180;}


@implementation CaptorView
@synthesize countLabel;
@synthesize audioRecorder;
@synthesize picker;
@synthesize label1,label2,label3,isCheckIn,label4;
@synthesize vwToolbarHolder;
@synthesize busy;
@synthesize lnlTopMessage;
@synthesize vwTop;
@synthesize isauto;
@synthesize captureButton;
- (id)init {
	if(self=[super init])
	{
	}
	return self;
}

- (void)viewDidLoad {
	self.lnlTopMessage.text = @"";
	[busy stopAnimating];
	busy.hidden = YES;
	//stop.enabled = NO;
	 [super viewDidLoad];
	uploading.hidden = YES;
	isauto = YES;
	mainOpQueue=[[NSOperationQueue alloc] init];
	[mainOpQueue setMaxConcurrentOperationCount:1];
	appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
    captureSetting = 15;
	[audioRecorder initAudio];
	 audioRecorder.hidden=NO;
	
	capCount = 0;
	[appDelegate.uploader setCaptorView:self];
	[self newCaptor];	
	label1.text = @"Start Audio Recording Now ";
	label2.text = @"Narrate the Screen or Record Your Message";
	label1.textAlignment = UITextAlignmentCenter;
	label2.textAlignment = UITextAlignmentCenter;
    label4.hidden = YES;
	}	

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    //NSLog(@"@@@@@@@@@@@@@@ memory error capture!!!");
}

- (void)viewDidUnload {
    [super viewDidUnload];
}

- (void)dealloc {
	NSLog(@"***************  DEALLOC CapterView  **************");
	[vwTop release];
	[busy release];
	[vwToolbarHolder release];
	[picker release];
	[audioRecorder removeFromSuperview];
	[audioRecorder release];
	[super dealloc];
}

#pragma mark Action

//-(void)playaudio
//{
//    NSURL *bipURL = [NSURL fileURLWithPath:[[NSBundle mainBundle] pathForResource:@"imageAudio" ofType:@"caf"]];
//	NSError *error;
//	AVAudioPlayer *soundBip = [[AVAudioPlayer alloc] initWithContentsOfURL:bipURL error:&error];
//    soundBip.volume = 1.0;
//	[soundBip play];    
//}

-(IBAction)newCamera {
	if(capCount==-1)
    {
        //NSLog(@"*********   capCount == -1    **********");
        return;
    }
    
    picker = [[CameraView alloc] init] ;
	picker.delegate = self;
	picker.captorView=self;
	picker.cameraOverlayView =vwToolbarHolder;
	[self presentModalViewController:picker animated:NO];

}

- (IBAction) back{

	[self dismissModalViewControllerAnimated:YES];
}


#pragma mark UIImagePickerControllerDelegate


//- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingMediaWithInfo:(NSDictionary *)info
//{
//    NSLog(@"----------------------------");
//   // NSLog(@" image infor : %@",info);
//    UIImage *inImage =[info objectForKey:@"UIImagePickerControllerOriginalImage"];
//   // NSLog(@"image orientaion :%d",inImage.imageOrientation);
//    capCount++;
//	NSDateFormatter *formatter;
//	NSString        *dateString;
//	formatter = [[NSDateFormatter alloc] init];
//	[formatter setDateFormat:@"HH:mm:ss"];
//	dateString = [formatter stringFromDate:[NSDate date]];
//	[formatter release];
//    
//    
//	SaveImageOperation *op=[[SaveImageOperation alloc] initWithImage:inImage];
//    op.imageOritation = imageOritation;
//	op.mainDelegate=self;
//	op.fileToWriteTo=[PHOTO_FOLDER stringByAppendingFormat:@"/A%d_%d_%@.jpg",currentBlock,capCount,dateString];
//	[mainOpQueue addOperation:op];
//	[op release];
//	[self performSelector:@selector(enableCameraButton) withObject:nil afterDelay:0.3];
//    
//    
//}
- (void)imagePickerController:(UIImagePickerController *)picker1 didFinishPickingImage:(UIImage *)inImage editingInfo:(NSDictionary *)editingInfo {
    
	capCount++;
	NSDateFormatter *formatter;
	NSString        *dateString;
	formatter = [[NSDateFormatter alloc] init];
	[formatter setDateFormat:@"HH:mm:ss"];
	dateString = [formatter stringFromDate:[NSDate date]];
	[formatter release];
    
    
	SaveImageOperation *op=[[SaveImageOperation alloc] initWithImage:inImage];
    op.imageOritation = imageOritation;
	op.mainDelegate=self;
	op.fileToWriteTo=[PHOTO_FOLDER stringByAppendingFormat:@"/A%d_%d_%@.jpg",currentBlock,capCount,dateString];
	[mainOpQueue addOperation:op];
	[op release];
	[self enableCameraButton];

}
- (void)enableCameraButton
{
	captureButton.hidden = NO;
	picker.capCount ++;
    picker.count = picker.capCount;
}

-(void)finishedSavingImage
{
    imageCount++;
   // NSLog(@"finish save image : %d pick.count= %d",imageCount,picker.count);
    if( imageCount== (picker.count +1))
    {
       [appDelegate.uploader uploadPhoto]; 
    }
    
}
- (void)imagePickerControllerDidCancel:(UIImagePickerController *)picker1 {
	[picker dismissModalViewControllerAnimated:NO];
}

#pragma mark -
#pragma mark Other
- (IBAction)stopCaptor:(UIBarButtonItem*)sender {
	if(sender) sender.enabled = NO;
	capCount=-1;
	audioRecorder.block = -1; //stop next bock
	[audioRecorder closeAndStop];
	[self dismissModalViewControllerAnimated:YES];
	[appDelegate.uploader endUploadPhoto];
	[appDelegate.uploader endUploadAudio];

	[appDelegate.uploader uploadPhoto];
	[appDelegate.uploader uploadAudio];

}

- (IBAction)stopCaptorOnCamera:(id)sender {
	label1.text = @"Audio record process....";
	label2.text = @"Photo capture process...";
    label4.hidden = NO;
    label4.text =[NSString stringWithFormat:@"Sucessful : 0 "];
	uploading.hidden = NO;
	label1.textAlignment = UITextAlignmentCenter;
	label2.textAlignment = UITextAlignmentCenter;
	//picker.count = picker.capCount;
	capCount=-1;
	[picker stopCapture];
	audioRecorder.block = -1; //stop next block
	[audioRecorder closeAndStop]; 
	[picker dismissModalViewControllerAnimated:NO];
	
	[appDelegate.uploader endUploadPhoto];
	[appDelegate.uploader endUploadAudio];
    [self finishedSavingImage];
    /*
	if (picker.count == 0)
	{
		[appDelegate.uploader uploadPhoto];
	}   
	else
	if (picker.count == 1)
	{
		[appDelegate.uploader performSelector:@selector(uploadPhoto) withObject:nil afterDelay:5.0];
	}
	else
	if (picker.count <= 2)
	{
		[appDelegate.uploader performSelector:@selector(uploadPhoto) withObject:nil afterDelay:9.0];
	}
	else
	if (picker.count <= 5 )
	{
		[appDelegate.uploader performSelector:@selector(uploadPhoto) withObject:nil afterDelay:15.0];
		
	}
	else
		if(picker.count <= 7)
		{
			[appDelegate.uploader performSelector:@selector(uploadPhoto) withObject:nil afterDelay:22.0];
					}
	else
			if(picker.count <= 10)
			{
				[appDelegate.uploader performSelector:@selector(uploadPhoto) withObject:nil afterDelay:35.0];
				
			}
	else
	{
		[appDelegate.uploader performSelector:@selector(uploadPhoto) withObject:nil afterDelay:60.0];

	}
     */
}

- (IBAction)btnCameraTapped:(id)sender {
	isauto = NO;
	captureButton.hidden = YES;
    
    NSInteger orientation = [[UIDevice currentDevice] orientation];
    switch (orientation) {
        case 1:
            imageOritation = 3;
            break;
        case 2:
            imageOritation = 2;
            break;
        case 3:
            imageOritation = 0;
            break;
        case 4:
            imageOritation = 1;
            break;
        default:
            break;
    }
    [picker captureImage];
  //[self playaudio];
	}

- (void)newCheckin {
	capCount=0;
	currentBlock=1;
    block = 1;
	audioRecorder.block=block;
	[audioRecorder performSelector:@selector(startRecord) withObject:nil afterDelay:1];
	[self performSelector:@selector(newCamera) withObject:nil afterDelay:3];
}

- (void)newCaptor {
	//remove all capture
	[appDelegate.uploader removeAllFileCache];
	capCount=0;
	currentBlock=1;
    block = 1;
	audioRecorder.block=block;
	[audioRecorder performSelector:@selector(startRecord) withObject:nil afterDelay:1];
	[self performSelector:@selector(newCamera) withObject:nil afterDelay:3];
	countDown=3;
}

-(void)timerTick{
	countDown--;
	[self updateCountDown];
//	NSLog(@"timerTick",nil);
}

-(void)updateCountDown{
	lnlTopMessage.text=[NSString stringWithFormat:@"Next Photp in %i secs",countDown];
}

@end
