//
//  SplashView.m
//  SOSBEACON
//
//  Created by cncsoft on 8/25/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "SplashView.h"

@implementation SplashView
@synthesize actSplash, timer,viewSplash,splashImageView, noConnection;

/*
 // The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    if ((self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil])) {
        // Custom initialization
    }
    return self;
}
*/


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
    [super viewDidLoad];
	//appDelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
	viewSplash.frame = CGRectMake(0, 0, 320, 480);
	[actSplash startAnimating];
	
    /*
    if (!noConnection) {
		timer = [NSTimer scheduledTimerWithTimeInterval:1.0 target:self selector:@selector(fadeScreen) userInfo:nil repeats:NO];
	}
    */
}

/*
// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}
*/

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
	self.splashImageView = nil;
	self.viewSplash = nil;
	self.actSplash = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)dealloc {
	[splashImageView release];
	[viewSplash release];
	[actSplash release];
    [super dealloc];
}

#pragma mark Other Function
- (void)fadeScreen
{
	[timer invalidate];
	[UIView beginAnimations:nil context:nil];
	[UIView setAnimationDuration:1.0];      
	[UIView setAnimationDelegate:self];  
	[UIView setAnimationDidStopSelector:@selector(finishedFading)];	
	self.view.alpha = 0.0;    
	[UIView commitAnimations];
}
- (void) finishedFading
{
	[UIView beginAnimations:nil context:nil]; 
	[UIView setAnimationDuration:0.5];        
	self.view.alpha = 1.0;
	[UIView commitAnimations]; 
	///[splashImageView removeFromSuperview];
	[self.view removeFromSuperview];
//	[self dismissModalViewControllerAnimated:NO];
	
}
-(void)ShowLoading
{

}
- (void)removeView
{
	[self.view removeFromSuperview];
}
@end
