//
//  EmailView.h
//  
//
//  Created by Geoff Heeren on 10/21/09.
//  Copyright 2009 AppTight, Inc. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MessageUI/MessageUI.h>

@interface EmailView : UIView<MFMailComposeViewControllerDelegate> {

	NSArray *toAddresses;
	NSArray *ccAddresses;
	NSArray *bccAddresses;
	
	NSString *subject;
	NSString *body;
	UIImage *emailPic;
	UIViewController *mainView;
	UIColor *tintColor;
	
}
@property (retain,nonatomic) NSArray *toAddresses;
@property (retain,nonatomic) NSArray *ccAddresses;
@property (retain,nonatomic) NSArray *bccAddresses;

@property (retain,nonatomic) NSString *subject;
@property (retain,nonatomic) NSString *body;

@property (retain,nonatomic) UIImage *emailPic;

@property (retain,nonatomic) UIViewController *mainView;

@property (retain,nonatomic) UIColor *tintColor;
@property (retain,nonatomic) NSArray *images;

- (void)showEmail;
+(void)sendTellAFriendEmail:(UIViewController *)ctrl tintColor:(UIColor *)color;
@end
