//
//  StatusView.m
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/13/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import "StatusView.h"


@implementation StatusView

- (id)initWithCoder:(NSCoder *)aDecoder
{
	if(self = [super initWithCoder:aDecoder])
	{
		isShow=NO;
	}
	return self;
}

- (id)initWithFrame:(CGRect)frame {
    if (self = [super initWithFrame:frame]) {
        // Initialization code
    }
    return self;
}

/*
- (void)drawRect:(CGRect)rect {
    // Drawing code
}*/


- (void)dealloc {
    [super dealloc];
}

- (void)setStatusTitle:(NSString*)text {
	[activity1 startAnimating];
	statusLabel.text = text;
}

- (void)showStatus:(NSString*)text {
	[activity1 startAnimating];
	statusLabel.text = text;
	
	if(isShow==YES)
	{
		return;
	}
	
	isShow = YES;
	
	[UIView beginAnimations:@"Show status" context:nil];
	[UIView setAnimationDuration:0.5];
	self.frame = CGRectMake(0, 0, 320, 60);
	[UIView commitAnimations];
}

- (void)beginHideStatus {
	timer1 = [NSTimer scheduledTimerWithTimeInterval:1.5 target:self selector:@selector(hideStatus) userInfo:nil repeats:NO];
}

- (void)hideStatus {
    if(!isShow) return;
    
	[activity1 stopAnimating];
	[UIView beginAnimations:@"Show status" context:nil];
	[UIView setAnimationDuration:0.8];
	[UIView setAnimationDelegate:self];
	[UIView setAnimationDidStopSelector:@selector(endHideStatus)];
	self.frame = CGRectMake(0, -60, 320, 60);
	[UIView commitAnimations];
}

- (void)endHideStatus {
	isShow = NO;
}


@end
