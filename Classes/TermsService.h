//
//  TermsService.h
//  SOSBEACON
//
//  Created by cncsoft on 9/8/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import"SOSBEACONAppDelegate.h"
#import"SplashView.h"
@interface TermsService : UIViewController {
	SOSBEACONAppDelegate *appdelegate;
	SplashView *splashview;

}
- (IBAction)Exit;
- (IBAction)TermsWeb;
- (IBAction)signUp;

@end
