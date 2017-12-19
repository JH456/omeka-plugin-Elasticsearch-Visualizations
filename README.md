# Omeka Elasticsearch Plugin

This plugin integrates [elasticsearch](https://www.elastic.co/products/elasticsearch) with [Omeka Classic](http://omeka.org/classic/), overriding the default search functionality. Inspired by the excellent [SolrSearch](https://github.com/scholarslab/SolrSearch) plugin, elasticsearch is similar to Solr in that it allows you to take advantage of faceting, snippet highlighting, and advanced full text search capabilities. 

## Requirements

- Omeka Classic v2.5+ running on PHP v7.0+
- Elasticsearch 5.5+

This plugin assumes that you already have an elasticsearch cluster setup for use with your Omeka site, but if not, you will need to set one up. Depending on your needs, you have two options:

1. You can run your own elasticsearch cluster, so you have full control over the service. See the [elasticsearch documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/index.html) for installation help.
2. You can use a service provider such as [Amazon's Elasticsearch Service](https://aws.amazon.com/elasticsearch-service/).

## Installation

1. Download and unzip the plugin to your Omeka site's plugins directory.
2. Copy the example configuration file: `cp elasticsearch.ini.example elasticsearch.ini`. The default configuration assumes you have elasticsearch running locally on `localhost:9200`. You can change this later on the admin interface, but this is a good place to set the default connection information if you know it ahead of time.
3. Install the plugin on the Omeka admin interface. If the install succeeded, you will see an _Elasticsearch_ entry appear in the admin navigation. 
4. Navigate to _Elasticsearch_ in the admin navigation and update/save your settings.
5. Navigate to the _Index_ tab of the plugin and click the **Clear and Reindex** button to index your site's content.
6. Try a search query by using the search bar on the public site. The admin site uses the existing omeka search functionality, so be sure to navigate to the public site first.

## Usage

By default, the elasticsearch plugin indexes Items, Collections, Exhibits, Simple Pages, and Neatline exhibits (if installed).  Public and non-public items are indexed, but non-public items are only returned in results if the user is logged in and has permission to see them.

The full power of the elasticsearch [query language](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html) is exposed to users, so queries can be simple keyword searches or more advanced queries using multiple operators. Some examples of more advanced queries include fuzzy searching (~ operator), boosting terms (^ operator), specifying fields to search (field:value), or using range queries to narrow the results for numeric/date fields. The results page provides some basic guidance on some of the more advanced query operators.

Results are faceted or aggregated according to the following pre-defined categories:

- Result type
- Item type
- Collection
- Exhibit
- Tag

Future development may incorporate the ability to configure or tailor the facets to the needs of a particular site.

## Configuration

### Configuration for local Elasticsearch

The default settings should be sufficient to connect to a local installation of Elasticsearch running on port 9200. Simply copy the example config file and install the plugin as described in the _Installation_ section. 

### Configuration for AWS Elasticsearch

Host settings and credentials for connecting to [AWS Elasticsearch](https://aws.amazon.com/elasticsearch-service/) are configured in `elasticsearch.ini`. 

Set `service = "aws"` and then update the `[aws]` section. 

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

Credentials are required to sign requests with [Signature v4](http://docs.aws.amazon.com/general/latest/gr/signature-version-4.html). You can either hard-code the AWS access key and secret, or you can leave them blank and the plugin will attempt to load them from the environment (e.g. ENV variables, AWS profile, etc). If you are using IAM roles to control access, then leave the key/secret blank.

You will need to configure an access policy for the elasticsearch domain so that the signed requests are permitted.

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

## Latest Build

[![Build Status](https://travis-ci.org/Harvard-ATG/omeka-plugin-Elasticsearch.svg?branch=master)](https://travis-ci.org/Harvard-ATG/omeka-plugin-Elasticsearch)
