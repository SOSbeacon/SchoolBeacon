//
//  TermsService.m
//  SOSBEACON
//
//  Created by cncsoft on 9/8/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "TermsService.h"
#import "TermViewControl.h"
#import"SplashView.h"
@implementation TermsService

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
	appdelegate = (SOSBEACONAppDelegate*)[[UIApplication sharedApplication] delegate];
    NSLog(@"frame:%@",NSStringFromCGRect(self.view.frame));
    [super viewDidLoad];
}


/*
// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}
*/

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    
}

- (void)viewDidUnload {
    [super viewDidUnload];
}

- (void)dealloc {
	if (splashview) 
	[splashview release];
    [super dealloc];
}

- (IBAction)signUp {
	
	//splashview =[[SplashView alloc] init];
	//[appdelegate.window  addSubview:splashview.view];
	[self.view removeFromSuperview];
}

- (IBAction)TermsWeb {
	[self.view removeFromSuperview];
	TermViewControl *term = [[TermViewControl alloc] init];
	[self presentModalViewController:term animated:YES];
	[term release];
}

- (IBAction)Exit{
	exit(0);
}


@end
