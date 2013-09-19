//
//  StatusView.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 9/13/10.
//  Copyright 2010 CNC. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface StatusView : UIView {
	IBOutlet UILabel *statusLabel;
	IBOutlet UIActivityIndicatorView *activity1;
	NSTimer *timer1;
	BOOL isShow;
}

- (void)setStatusTitle:(NSString*)text;
- (void)showStatus:(NSString*)text;
- (void)beginHideStatus;
- (void)hideStatus;
- (void)endHideStatus;

@end
