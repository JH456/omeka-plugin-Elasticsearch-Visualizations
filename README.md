# Omeka Elasticsearch Plugin

This plugin integrates [elasticsearch](https://www.elastic.co/products/elasticsearch) with Omeka.

## Quickstart

1. Copy the example configuration file: `cp elasticsearch.ini.example elasticsearch.ini`. The default configuration assumes you have elasticsearch running locally on `localhost:9200`.
2. Install the plugin on the Omeka Admin interface. It will show up on the admin navigation as _Elasticsearch_. 
3. Test your settings by clicking the **Save Settings** button on the _Elasticsearch_ admin UI.
4. Index your site content by going to the _Index_ tab and clicking the **Clear and Reindex** button.
4. Try a search query by using the search bar on the public site. 

## Configuration for AWS Elasticsearch

Host settings are configured in `elasticsearch.ini` along with any credentials required to connect to the service. To connect to [AWS Elasticsearch](https://aws.amazon.com/elasticsearch-service/), set the service property to **aws** and then configure that section as shown below.

Note that you can specify your AWS access key/secret, or if omitted, the plugin will attempt to load the credentials from the environment automatically. This is useful if you want to take advantage of role-based policies.

Example:

```ini
index = "my_omeka_site_index"
service = "aws"

[default]
host = "localhost"
port = "9200"
scheme = "http"
user = ""
pass = ""

[aws]
key = "MY_AWS_ACCESS_KEY_ID"
secret = "MY_AWS_SECRET_ACCESS_KEY"
region = "us-east-1"
host = "search-mydomain.us-east-1.es.amazonaws.com"
port = "443"
scheme = "https"
```

## Unit Tests

1. Follow the [getting started instructions](https://phpunit.de/getting-started.html) to download and install _phpunit_. 
2. Run the tests from the root directory: `phpunit --configuration phpunit.xml` 
