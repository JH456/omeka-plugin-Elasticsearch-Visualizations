<ul id="section-nav" class="navigation">
    <?php
    echo Elasticsearch_Utils::nav_li($tab == 'server',      url('elasticsearch/admin/server'),      __('Server'));
    echo Elasticsearch_Utils::nav_li($tab == 'reindex',     url('elasticsearch/admin/reindex'),     __('Index'));
    ?>
</ul>
