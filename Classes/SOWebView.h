//
//  SOWebView.h
//  SOSBEACON
//
//  Created by cncsoft on 7/30/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SOSBEACONAppDelegate.h"

@interface SOWebView : UIViewController <UIWebViewDelegate>{
	UIWebView *loadWeb;
	SOSBEACONAppDelegate *appDelegate;
	IBOutlet UIActivityIndicatorView *actWeb;
}
@property (nonatomic, retain) IBOutlet UIWebView *loadWeb;
- (IBAction) backHome;
@end
