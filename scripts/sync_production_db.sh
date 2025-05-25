#!/bin/bash

# Uses the defualt creds in ~/.aws/credentials

export AWS_ACCESS_KEY_ID=$(op item get "AWS Access Key (cloudmanic)" --fields "access key id" --reveal)
export AWS_SECRET_ACCESS_KEY=$(op item get "AWS Access Key (cloudmanic)" --fields "secret access key" --reveal)
rm ../database/rental-notice.sqlite*
litestream restore -o ../database/rental-notice.sqlite s3://rental-notice/db-backups/rental-notice.sqlite