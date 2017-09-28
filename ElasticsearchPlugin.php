<?php

/**
 * @package Elasticsearch
 * @subpackage elasticsearch
 * @copyright 2017 President and Fellows of Harvard College
 * @license https://opensource.org/licenses/BSD-3-Clause
 */

define('ELASTICSEARCH_PLUGIN_DIR', dirname(__FILE__));
require (ELASTICSEARCH_PLUGIN_DIR.'/autoload.php');

class ElasticsearchPlugin extends Omeka_Plugin_AbstractPlugin {
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'define_routes',
        'after_save_record',
        'after_save_item',
        'after_save_element',
        'before_delete_record',
        'before_delete_item',
        'before_delete_element'
    );

    protected $_filters = array(
        'admin_navigation_main',
        'search_form_default_action'
    );

    public function hookInstall() {
        $this->_setOptions();
    }

    public function hookUninstall() {
        $this->_clearOptions();
    }

    public function hookUpgrade($args) {
    }

    public function hookDefineRoutes($args) {
        $config = new Zend_Config_Ini(ELASTICSEARCH_PLUGIN_DIR.'/routes.ini');
        $args['router']->addConfig($config);
    }

    public function hookAfterSaveRecord($args) {
        $record = $args['record'];
    }

    public function hookAfterSaveItem($args) {
        $record = $args['record'];
        $config = Elasticsearch_Utils::getConfig();
        Elasticsearch_Helper_Index::indexItem($config->index->name, $record);
    }

    public function hookAfterSaveElement($args) {
        $record = $args['record'];
        $insert = $args['insert'];
    }

    public function hookBeforeDeleteRecord($args) {
        $record = $args['record'];
    }

    public function hookBeforeDeleteItem($args) {
        $record = $args['record'];
        $config = Elasticsearch_Utils::getConfig();
        Elasticsearch_Helper_Index::deleteItem($config->index->name, $record);
    }

    public function hookBeforeDeleteElement($args) {
        $record = $args['record'];
    }

    public function filterAdminNavigationMain($nav) {
        $nav[] = array(
            'label' => __('Elasticsearch'),
            'uri' => url('elasticsearch/admin/server')
        );
        return $nav;
    }

    public function filterSearchFormDefaultAction($uri) {
        if (!is_admin_theme()) {
            $uri = url('elasticsearch/search/interceptor');
        }
        return $uri;
    }

    protected function _setOptions() {
        set_option('elasticsearch_endpoint', 'http://localhost:9200');
    }

    protected function _clearOptions() {
        delete_option('elasticsearch_endpoint');
    }
}
