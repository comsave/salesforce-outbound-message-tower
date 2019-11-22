# salesforce-outbound-message-tower
Salesforce outbound message receiver and broadcaster for development environment. 

![](https://img.shields.io/github/v/release/comsave/salesforce-outbound-message-tower)
![](https://img.shields.io/travis/comsave/salesforce-outbound-message-tower)

---
## Server side
Routes are as follows:

* *server* `/{channelName}/receive` you send Salesforce outbound messages here
* *client* `/{channelName}/broadcast` get the next unprocessed message 
* *client* `/{channelName}/broadcast/processed/{notificationId}` mark notification as processed 

## Client side
Automatic listening for unprocessed messages & marking as processed afterwards.

[salesforce-outbound-message-tower-bundle](https://github.com/comsave/salesforce-outbound-message-tower-bundle)
