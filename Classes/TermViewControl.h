//
//  TermViewControl.h
//  SOSBEACON
//
//  Created by cncsoft on 9/14/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface TermViewControl : UIViewController <UIWebViewDelegate> {

	UIWebView *loadWeb;
	UIActivityIndicatorView *actWeb;
}
@property (nonatomic, retain) IBOutlet UIWebView *loadWeb;
@property (nonatomic, retain) IBOutlet UIActivityIndicatorView *actWeb;

- (IBAction) backHome;

@end
