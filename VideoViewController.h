//
//  VideoViewController.h
//  SOSBEACON
//
//  Created by bon on 8/16/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface VideoViewController : UIViewController {
	IBOutlet UIWebView *webview;
	NSInteger flag;

}
@property(nonatomic)NSInteger flag;
-(void)remove;
-(void)doneButtonPress:(id)sender;
@end
