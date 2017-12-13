(function($) {
    var Plugin = {};
    Plugin.refreshPage = function(delay) {
        delay = delay || 10000;
        window.setTimeout(function() {
            window.location.reload();
        }, delay);
    };
    Plugin.setupSearchResults = function() {
        $(document).ready(function() {
            $("#elasticsearch-help-btn").on("click", function (e) {
                $("#elasticsearch-help").toggle();
            });
        });
    };

    window.ElasticsearchPlugin = Plugin;
})(jQuery);

