<?php

class Elasticsearch_Client {

    protected static $_credentials = null;

    /**
     * Builds an instance of the PHP elasticsearch client, ensuring
     * that it is configured properly.
     *
     * @return Elasticsearch\Client
     */
    public static function create() {
        $client = Elasticsearch\ClientBuilder::create();
        $hosts = self::getHosts();
        if(isset($hosts)) {
            $client->setHosts($hosts);
        }
        $connectionParams = self::getConnectionParams();
        if(isset($connectionParams)) {
            $client->setConnectionParams($connectionParams);
        }
        return $client->build();
    }

    /**
     * Returns an array containing hosts in the elasticsearch cluster.
     *
     * First checks to see if a host has been defined as an omeka option
     * and uses that. If none has been defined, it will check the config
     * for a list of hosts. Otherwise, it just returns null
     * and the client will use the default host (e.g. localhost:9200).
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
     * Returns credentials needed to make requests to the elasticsearch REST endpoints.
     *
     * @return array
     */
    public static function getCredentials() {
        if(isset(self::$_credentials)) {
            return self::$_credentials;
        }

        $config = Elasticsearch_Config::load();
        if($config->get('aws_role_credentials', false)) {
            $aws_role = $config->aws_role_credentials;
            $aws_url = "http://169.254.169.254/latest/meta-data/iam/security-credentials/$aws_role";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $aws_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $output = curl_exec($ch);
            if($output === FALSE) {
                $err = curl_error($ch);
                error_log("curl error getting securitiy credentials: $err");
            }
            curl_close($ch);
            self::$_credentials = json_decode($output, true);
        }

        return self::$_credentials;
    }

    /**
     * Returns an array of connectionParams that can be passed to the Elasticsearch\ClientBuilder.
     *
     * This is primarily used to set authorization headers required by things like AWS.
     *
     * @return array
     */
    public static function getConnectionParams() {
        $params = array(
            'Content-type' => ['application/json'],
            'Accept' => ['application/json']
        );
        $credentials = self::getCredentials();
        if(isset($credentials)) {
            $params['x-amz-security-token'] = $credentials['token'];
        }
        return $params;
    }
}