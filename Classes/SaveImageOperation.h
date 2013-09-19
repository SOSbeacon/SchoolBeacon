//
//  SaveImageOperation.h
//  SOSBEACON
//
//  Created by Geoff Heeren on 6/12/11.
//  Copyright 2011 AppTight, Inc. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface SaveImageOperation : NSOperation {

	
	UIImage *imageToSave;
	NSObject *mainDelegate;
	BOOL isDone;

	NSTimeInterval startTime;
    NSInteger imageOritation;
}
@property(nonatomic)   NSInteger imageOritation; 
@property (retain) UIImage *imageToSave;
@property (retain) NSObject *mainDelegate;

@property(retain,nonatomic) NSString * fileToWriteTo;

- (id)initWithImage:(UIImage*)image ;
- (UIImage *)scaleAndRotateImage:(UIImage *)image;
@end
