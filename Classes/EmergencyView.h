//
//  EmergencyView.h
//  SOSBEACON
//
//  Created by cncsoft on 9/13/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface EmergencyView : UIViewController <UIWebViewDelegate> {

	UIWebView *loadWeb;
	IBOutlet UIActivityIndicatorView *actEmergency;
}
@property (nonatomic, retain) IBOutlet UIWebView *loadWeb;

- (IBAction) backHome;

@end
