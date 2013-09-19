//
//  SlideToCancelViewController.h
//  SOSBEACON
//
//  Created by cncsoft on 7/30/10.
//  Copyright 2010 CNC. All rights reserved.
//	

#import <UIKit/UIKit.h>

@protocol SlideToCancelDelegate;

@interface SlideToCancelViewController : UIViewController {
	id<SlideToCancelDelegate> delegate;
	
	UIImageView *sliderBackground;
	UISlider *slider;
	UILabel *label;
	NSTimer *animationTimer;
	BOOL touchIsDown;
	CGFloat gradientLocations[3];
	
	int animationTimerCount;
	NSString *labelText;
	UIImage *trackImage;
	UIImage *thumbImage;
}

@property (nonatomic, assign) id<SlideToCancelDelegate> delegate;

// This property is set to NO (disabled) on creation.
// The caller must set it to YES to animate the slider.
// It should be set to NO (disabled) when the view is not visible, in order
// to turn off the timer and conserve CPU resources.
@property (nonatomic) BOOL enabled;

// Access the UILabel, e.g. to change text or color
@property (nonatomic, readonly) UILabel *label;

@property (nonatomic, retain) NSString *labelText;

@property (nonatomic, retain) UIImage *thumbImage;
@end

@protocol SlideToCancelDelegate

@required
- (void) cancelled: (SlideToCancelViewController*)sender;

@end
