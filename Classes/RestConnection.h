//
//  RestConnection.h
//  SOSBEACON
//
//  Created by Tran Ngoc Anh on 14/06/2010.
//  Copyright 2010 CNC. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol RestConnectionDelegate <NSObject>
- (void)finishRequest:(NSDictionary*)arrayData andRestConnection:(id)connector;
- (void)cantConnection:(NSError*)error andRestConnection:(id)connector;
@end

@interface RestConnection : NSObject {
	id <RestConnectionDelegate> delegate;
	NSString *baseURLString;
	
	NSMutableData *data1;
	NSURLConnection *urlConnection;
}

@property (nonatomic, assign) id <RestConnectionDelegate> delegate;
@property (nonatomic, copy) NSString *baseURLString;
@property (nonatomic, retain) NSMutableData *data1;

- (id)initWithBaseURL:(NSString *)baseURL;

- (void)getPath:(NSString*)path withOptions:(NSDictionary*)options;
- (void)postPath:(NSString*)path withOptions:(NSDictionary*)options;
- (void)putPath:(NSString*)path withOptions:(NSDictionary*)options;
- (void)deletePath:(NSString*)path withOptions:(NSDictionary*)options;
- (void)uploadPath:(NSString*)path withOptions:(NSDictionary*)options withFileData:(NSData*)dataF; 
- (void)uploadPath:(NSString*)path withOptions:(NSDictionary*)options withFilePath:(NSString*)filePath;
- (void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error;
@end
