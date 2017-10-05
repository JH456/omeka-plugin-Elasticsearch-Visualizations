# Omeka Elasticsearch Plugin

This plugin integrates [elasticsearch](https://www.elastic.co/products/elasticsearch) with Omeka, overriding the default search. This plugin is inspired by the excellent [SolrSearch](https://github.com/scholarslab/SolrSearch) plugin. Elasticsearch is similar to Solr in that it allows you to take advantage of faceting (aggregations), snippet highlighting, and  advanced full text search capabilities. 

This plugin was designed to integrate with [Amazon's Elasticsearch Service](https://aws.amazon.com/elasticsearch-service/), so you don't need to run your own cluster to take advantage of its capabilities. The plugin can be configured to integrate with other Elasticsearch services as well.

## Requirements

This plugin assumes you are running Omeka v2.5+ and PHP v7.x. It may work with PHP v5.x, but that hasn't been tested.

## Setup

1. Copy the example configuration file: `cp elasticsearch.ini.example elasticsearch.ini`. The default configuration assumes you have elasticsearch running locally on `localhost:9200`.
2. Install the plugin on the Omeka Admin interface. It will show up on the admin navigation as _Elasticsearch_. 
3. Test your settings by clicking the **Save Settings** button on the _Elasticsearch_ admin UI.
4. Index your site content by going to the _Index_ tab and clicking the **Clear and Reindex** button.
4. Try a search query by using the search bar on the public site. 

## Configuration

### Configuration for local Elasticsearch

The default settings should be sufficient to connect to a local installation of Elasticsearch running on port 9200. Simply copy the example config file and install the plugin as described in the _Setup_ section. 

### Configuration for AWS Elasticsearch

Host settings and credentials for connecting to [AWS Elasticsearch](https://aws.amazon.com/elasticsearch-service/) are configured in `elasticsearch.ini`. Set `service = "aws"` and then update the `[aws]` section. See example below.

Credentials are used to sign requests with [Signature v4](http://docs.aws.amazon.com/general/latest/gr/signature-version-4.html). You can either hard-code the AWS access key and secret, or you can leave them blank and the plugin will attempt to load them from the environment (e.g. ENV variables, AWS profile, etc). If you are using IAM roles to control access, then  leave the key/secret blank.

Example Configuration:

```ini
index = "history123"
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

An example access policy that allows a `<USER>` to do anything on the elasticsearch domain and a `<ROLE>` to submit any HTTP request might look like this:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": {
        "AWS": ["arn:aws:iam::<ACCOUNT>:user/<USER>"]
      },
      "Action": "es:*",
      "Resource": "arn:aws:es:<REGION>:<ACCOUNT>:domain/<DOMAIN>/*"
    },
    {
      "Effect": "Allow",
      "Principal": {
        "AWS": ["arn:aws:iam::<ACCOUNT>:role/<ROLE>"]
      },
      "Action": "es:ESHttp*",
      "Resource": "arn:aws:es:<REGION>:<ACCOUNT>:domain/<DOMAIN>/*"
    }
  ]
}
```

## Unit Tests

1. Follow the [getting started instructions](https://phpunit.de/getting-started.html) to download and install _phpunit_. 
2. Run the tests from the root directory: `phpunit --configuration phpunit.xml` 
