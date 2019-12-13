# salesforce-outbound-message-tower
Salesforce outbound message receiver and broadcaster for development environment. 

![](https://img.shields.io/github/v/release/comsave/salesforce-outbound-message-tower)
![](https://img.shields.io/travis/comsave/salesforce-outbound-message-tower)

---
## Server side

Build docker image: `docker build -f ./docker/Dockerfile -t comsave/salesforce-outbound-message-tower:latest .`

Routes are as follows:
* *server* `/{channelName}/receive` you send Salesforce outbound messages here
* *client* `/{channelName}/broadcast` get the next unprocessed message 
* *client* `/{channelName}/broadcast/processed/{notificationId}` mark notification as processed 

## Client side
Automatic listening for unprocessed messages & marking as processed afterwards.

## Local Development Dependencies
```shell script
# docker
brew cask install docker
# docker-sync
gem install --user-install docker-sync
brew install unison
brew install eugenmayer/dockersync/unox
```

[salesforce-outbound-message-tower-bundle](https://github.com/comsave/salesforce-outbound-message-tower-bundle)