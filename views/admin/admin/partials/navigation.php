<ul id="section-nav" class="navigation">
    <?php
    echo Elasticsearch_Utils::nav_li($tab == 'server',      url('elasticsearch/server'),      __('Server'));
    echo Elasticsearch_Utils::nav_li($tab == 'reindex',     url('elasticsearch/reindex'),     __('Index'));
    ?>
</ul>