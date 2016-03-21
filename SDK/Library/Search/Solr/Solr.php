<?php

namespace SDK\Library\Search\Solr;

class Solr
{
    private static $_client = array();
    private static $_query = null;

    /**
     * @param $options
     * @return null|\SolrClient
     * @throws \Exception
     */
    public static function client($options, $core = 'default')
    {
        self::extension();
        if (count($options) > 1) {
            $server = $options[array_rand($options, 1)];
        } else {
            $server = $options[0];
        }
        $server['path'] = $server['path'] . $core;

        $key = array($server['hostname'], $server['port'], $server['path']);
        $key = implode(':', $key);

        if (!isset(self::$_client[$key]) || self::$_client[$key] === null) {
            self::$_client[$key] = new \SolrClient($server);
        }
        return self::$_client[$key];
    }

    /**
     * @return null|\SolrQuery
     * @throws \Exception
     */
    public static function query()
    {
        return self::$_query = new \SolrQuery();
    }

    public static function extension()
    {
        if (!($load = extension_loaded('solr'))) {
            throw new \Exception("solr extension load failed");
        }
        return $load;
    }
}
