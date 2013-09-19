//
//  EmergencyView.m
//  SOSBEACON
//
//  Created by cncsoft on 9/13/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "EmergencyView.h"


@implementation EmergencyView
@synthesize loadWeb;

#pragma mark void
- (void)viewDidLoad {
    [super viewDidLoad];
	loadWeb.autoresizesSubviews = YES;
	loadWeb.delegate = self;
	NSURL *url = [[NSURL alloc] initFileURLWithPath:[[NSBundle mainBundle] pathForResource:@"PhoneList" ofType:@"html"]];
	[self.loadWeb loadRequest:[NSURLRequest requestWithURL:url]];
	[url release];	
}

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {	
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}
- (void)dealloc {
	[actEmergency release];
	[loadWeb release];
    [super dealloc];
}

- (IBAction) backHome {
	[self dismissModalViewControllerAnimated:YES];
}

#pragma mark webviewdelegate

- (void)webViewDidStartLoad:(UIWebView *)loadWeb {
	actEmergency.hidden = NO;
	[actEmergency startAnimating];

}

- (void)webViewDidFinishLoad:(UIWebView *)loadWeb {
	actEmergency.hidden = YES;
	[actEmergency stopAnimating];

}

- (void)webView:(UIWebView *)loadWeb didFailLoadWithError:(NSError *)error{
	actEmergency.hidden = YES;
	[actEmergency stopAnimating];

}

@end
