//
//  TermViewControl.m
//  SOSBEACON
//
//  Created by cncsoft on 9/14/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "TermViewControl.h"


@implementation TermViewControl
@synthesize loadWeb,actWeb;

- (void)viewWillAppear:(BOOL)animated {
	[self.loadWeb loadRequest:[NSURLRequest requestWithURL:[NSURL URLWithString:@"http://www.sosbeacon.com/web/about/terms"]]];
}

- (void)viewDidLoad {
    [super viewDidLoad];
	loadWeb.autoresizesSubviews = YES;
	loadWeb.delegate = self;
}

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	//NSLog(@"memory error webview %%%%%%%%%%%%");
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
	[actWeb stopAnimating];
	actWeb.hidden = YES;
	
	
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}
- (void)dealloc {
	[actWeb release];
	[loadWeb release];
    [super dealloc];
}

- (IBAction) backHome {
	[self dismissModalViewControllerAnimated:YES];
}

#pragma mark webviewdelegate

- (void)webViewDidStartLoad:(UIWebView *)loadWeb {
	actWeb.hidden = NO;
	[actWeb startAnimating];
}

- (void)webViewDidFinishLoad:(UIWebView *)loadWeb {
	[actWeb stopAnimating];
	actWeb.hidden = YES;
}

- (void)webView:(UIWebView *)loadWeb didFailLoadWithError:(NSError *)error{
	[actWeb stopAnimating];
	actWeb.hidden = YES;
}

@end
