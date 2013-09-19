//
//  SplashView.h
//  SOSBEACON
//
//  Created by cncsoft on 8/25/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
//#import "SOSBEACONAppDelegate.m";
@interface SplashView : UIViewController {

	NSTimer *timer;
	UIActivityIndicatorView *actSplash;
	UIView *viewSplash;
	UIImageView *splashImageView;
	BOOL noConnection;
	//SOSBEACONAppDelegate *appDelegate;

}

@property (nonatomic, retain) NSTimer *timer;
@property (nonatomic, retain) IBOutlet UIActivityIndicatorView *actSplash;
@property (nonatomic, retain) IBOutlet UIView *viewSplash;
@property (nonatomic, retain) IBOutlet UIImageView *splashImageView;
@property BOOL noConnection;
-(void)ShowLoading;
- (void)removeView;
@end
