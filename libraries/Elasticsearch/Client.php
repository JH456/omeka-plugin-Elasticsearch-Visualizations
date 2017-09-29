<?php

use GuzzleHttp\Ring\Client\CurlHandler;
use GuzzleHttp\Ring\Client\CurlMultiHandler;
use GuzzleHttp\Ring\Client\Middleware;
use GuzzleHttp\Psr7\Request;
use Aws\Signature\SignatureV4;
use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;

class Elasticsearch_Client {

    /**
     * Builds an instance of the PHP elasticsearch client, ensuring
     * that it is configured properly.
     *
     * @return Elasticsearch\Client
     */
    public static function create() {
        $config = Elasticsearch_Config::load();
        $builder = Elasticsearch\ClientBuilder::create();

        // Hosts
        $hosts = self::getHosts();
        if(isset($hosts)) {
            $builder->setHosts($hosts);
        }

        // Handler -- to add auth headers
        if('aws' === $config->get('service_provider', '')) {
            $builder->setHandler(self::getAwsHandler([
                'region' => $config->get('aws.region', 'us-east-1'),
                'key'    => $config->get('aws.key', null),
                'secret' => $config->get('aws.secret', null),
            ]));
        }

        // Build client
        $client = $builder->build();

        return $client;
    }

    /**
     * Returns an array containing hosts in the elasticsearch cluster.
     *
     * First checks to see if a host has been defined as an omeka option
     * and uses that. If none has been defined, it will check the config
     * for a list of hosts. Otherwise, it just returns null
     * and the client will use the default host (e.g. localhost:9200).
     *
     * Make sure to include the PORT in the host names, for example:
     *
     *  - search-domain.us-east-1.es.amazonaws.com:80
     *  - http://search-domain.us-east-1.es.amazonaws.com:80
     *
     * @return array of hosts in the elasticsearch cluster
     */
    public static function getHosts() {
        $config_hosts = Elasticsearch_Config::hosts();
        $option_host = get_option('elasticsearch_endpoint');
        if($option_host) {
            return array($option_host);
        } else if(is_array($config_hosts) && !empty($config_hosts)) {
            return $config_hosts;
        }
        return null;
    }

    /**
     * Returns an HTTP handler for the \Elasticsearch\ClientBuilder that will
     * sign AWS requests with the proper credentials.
     *
     * Can either provide the AWS 'key' and 'secret' as options, or use the
     * default provider to locate credentials in the environment.
     *
     * @param array $options
     * @param array $singleParams
     * @param array $multiParams
     * @return Closure
     */
    public static function getAwsHandler($options = [], $singleParams = [], $multiParams = []) {
        $region = $options['region'] ? $options['region'] : 'us-east-1';
        if(isset($options['key']) && isset($options['secret'])) {
            $creds = new Credentials($options['key'], $options['secret']);
        } else {
            $provider = CredentialProvider::defaultProvider();
            $creds = $provider()->wait();
        }

        $future = null;
        if (extension_loaded('curl')) {
            $config = array_merge([ 'mh' => curl_multi_init() ], $multiParams);
            if (function_exists('curl_reset')) {
                $default = new CurlHandler($singleParams);
                $future = new CurlMultiHandler($config);
            } else {
                $default = new CurlMultiHandler($config);
            }
        } else {
            throw new \RuntimeException('Elasticsearch-PHP requires cURL, or a custom HTTP handler.');
        }

        $curlHandler = $future ? Middleware::wrapFuture($default, $future) : $default;

        $awsSignedHandler = function (array $request) use ($curlHandler, $region, $creds) {
            $psr7Request = new Request(
                $request['http_method'],
                $request['uri'],
                $request['headers'],
                $request['body']
            );
            $signer = new SignatureV4('es', $region);
            $signedRequest = $signer->signRequest($psr7Request, $creds);
            $request['headers'] = $signedRequest->getHeaders();
            return $curlHandler($request);
        };

        return $awsSignedHandler;
    }
}