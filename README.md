## Cloud Ip-Address Bot Detector Example Project
A project to detect bot-ip-addresses coming as a Pub/Sub message and convert them to a different format and save them in a storage bucket.

## Project Structure:
To run this project on google cloud platform you need to follow the following services:

- Cloud Functions
- Cloud Pub/Sub
- Cloud Storage
- Cloud Scheduler
- Cloud VPC
- Cloud Redis


## Step by step guideline:

- Create a new project in GCP.
- Enable Cloud Functions API.
- Create a new service account with Cloud Functions Developer role.
- Create 2 new Pub/Sub topics called `UnmodifiedKeyEvent` and `ModifiedKeyEvent` with schemas in the PubSubTopics
  folder.
- Create a VPC connector called `cloud-function-connector` with region `europe-west3`.
- Create a new storage bucket in GCS in region `europe-west3` connected to the VPC connector.
- Create a new Redis instance in GCP with region `europe-west3` connected to the VPC connector.
- Create 3 new functions in GCF called `convertMessage`, `saveConvertedToStorage` and `updateRedisCloudFunction`
  connected to the VPC connector.
- update all 3 functions with the source code in CloudFunctions folder and use the service account created in step 3.
- update the `convertMessage` function To be triggered by Pub/Sub topic called `UnmodifiedKeyEvent` and to be connected
  to the VPC connector to be able to access the Redis instance and convert the message and publish it to Pub/Sub topic
  called `ModifiedKeyEvent`.
- update the `saveConvertedToStorage` function To be triggered by Pub/Sub topic called `ModifiedKeyEvent` and to be
  connected to the VPC connector and save the converted message to the storage bucket.
- update the `updateRedisCloudFunction` function to check
  out https://antoinevastel.com/data/avastel-longtime-bot-ips.txt and update the Redis instance with the new list of
  IPs.
- Create a new Cloud Scheduler job to trigger the `updateRedisCloudFunction` function every 24 hour.
- Finnish.
- Enjoy!

### Benefits of using Cloud Functions:

- No need to manage servers, infrastructure or scaling.
- Pay only for what you use.
- Easy to use and deploy.

## useful commands:

To List Cloud functions

    gcloud functions list

To describe a function

    gcloud functions describe <function-name>

To describe a function with region and json format

    gcloud functions describe <function-name> --region=<region> --format=json

To get iam policy of a function with region and json format

    gcloud functions get-iam-policy <function-name> --region=<region> --format=json

To get source of a function with region

        gcloud functions source download <function-name> --region=<region>