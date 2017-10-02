<?php

use Aws\Credentials\Credentials;
use Aws\Credentials\CredentialProvider;
use Aws\ElasticsearchService\ElasticsearchPhpHandler;
use Elasticsearch\ClientBuilder;

class Elasticsearch_Client {

    /**
     * Builds an instance of the PHP elasticsearch client, ensuring
     * that it is configured properly.
     *
     * @param array $options
     * @return Elasticsearch\Client
     */
    public static function create(array $options = array()) {
        // Use this to set the CURLOPT_TIMEOUT to limit how long requests may take.
        $timeout = isset($options['timeout']) ? $options['timeout'] : 90;

        // NOTE: there seems to be an issue with HTTP HEAD requests timing out
        // unles CURLOPT_NOBODY is set to true. Ideally this should be handled
        // by the elasticsearch connection object, but for now this is the workaround.
        $nobody = isset($options['nobody']) ? $options['nobody'] : false;

        $builder = ClientBuilder::create();

        // Hosts
        $hosts = self::getHosts();
        if(isset($hosts)) {
            $builder->setHosts($hosts);
        }

        // Handler
        $handler = self::getHandler();
        if(isset($handler)) {
            $builder->setHandler($handler);
        }

        // Connection Params
        $builder->setConnectionParams([
            'client' => [
                'curl' => [CURLOPT_TIMEOUT => $timeout, CURLOPT_NOBODY => $nobody]
            ]
        ]);

        // Return the Client object
        return $builder->build();
    }

    /**
     * Returns an array containing hosts in the elasticsearch cluster.
     *
     * @return array of hosts in the elasticsearch cluster
     */
    public static function getHosts() {
        $host = [
            'host' => get_option('elasticsearch_host'),
            'port' => get_option('elasticsearch_port'),
            'scheme' => get_option('elasticsearch_scheme'),
            'user' => get_option('elasticsearch_user'),
            'pass' => get_option('elasticsearch_pass')
        ];
        return [$host];
    }

    /**
     * Returns an HTTP handler function for use with the Elasticsearch\ClientBuilder.
     *
     * @return ElasticsearchPhpHandler|null
     */
    public static function getHandler() {
        if(get_option('elasticsearch_aws')) {
            $provider = null;
            $config = Elasticsearch_Config::load();
            if(!empty($config->aws->key) && !empty($config->aws->secret)) {
                $creds = new Credentials($config->aws->key, $config->aws->secret);
                $provider = CredentialProvider::fromCredentials($creds);
            }
            return new ElasticsearchPhpHandler($config->aws->region, $provider);
        }
        return null;
    }
}