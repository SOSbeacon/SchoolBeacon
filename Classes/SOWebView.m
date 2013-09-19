//
//  SOWebView.m
//  SOSBEACON
//
//  Created by cncsoft on 7/30/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "SOWebView.h"
#import "ValidateData.h"

@implementation SOWebView
@synthesize loadWeb;


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
#pragma mark void

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
	[self.loadWeb loadRequest:[NSURLRequest requestWithURL:[NSURL URLWithString:[NSString stringWithFormat:SERVER_URL@"/web/alert/latest?token=%@",appDelegate.apiKey]]]];
}

- (void)viewDidLoad {
	//self.navigationController.title = @"SOSbeacon";
    [super viewDidLoad];
	loadWeb.autoresizesSubviews = YES;
	appDelegate = (SOSBEACONAppDelegate *)[[UIApplication sharedApplication] delegate];
	loadWeb.delegate = self;
}

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	self.loadWeb = nil; 
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
