//
//  SettingsStorageView.h
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/18/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface SettingsStorageView : UIViewController {
	NSInteger alertIndex;
}
-(IBAction)btnClearAll_Tapped:(id)sender;
-(IBAction)btnResetAll_Tapped:(id)sender;

@end
