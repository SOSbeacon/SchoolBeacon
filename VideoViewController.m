//
//  VideoViewController.m
//  SOSBEACON
//
//  Created by bon on 8/16/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import "VideoViewController.h"


@implementation VideoViewController
@synthesize flag;
-(void)doneButtonPress:(id)sender
{
	flag = 2;
	[self dismissModalViewControllerAnimated:YES];
}
// The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
/*
- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization.
    }
    return self;
}
*/


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	
    [super viewDidLoad];
	NSString *urlAdress = @"http://sosbeacon.org:8085/web/about/take-the-tour";
	NSURL *url=[NSURL URLWithString:urlAdress];
	NSURLRequest *requestObject =[NSURLRequest requestWithURL:url];
	[webview loadRequest:requestObject];
	//if(flag == 1) [self performSelector:@selector(remove) withObject:nil afterDelay:221.0];
	
	
}
-(void)remove
{			
			[self dismissModalViewControllerAnimated:YES];
	//[self removeFromSuperview];
		

}

/*
// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations.
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}
*/

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc. that aren't in use.
}

- (void)viewDidUnload {
	webview = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


- (void)dealloc {
	[webview release];
    [super dealloc];
}


@end
