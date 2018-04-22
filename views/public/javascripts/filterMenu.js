'use strict';

var filterMenu = (function () {
    var filter = function (filters, graph) {
        var filterArray = [];
        for (var category in filters) {
            var iter = filters[category].values();
            for (var i = 0; i < filters[category].size; i++) {
                filterArray.push(iter.next().value);
            }
        }

        var filteredGraph = graphFilterer.filterGraphData(
            filterArray, graph
        );
        graphVisualization.renderGraphOnSVG(
            filteredGraph, graphColors.getTagColor
        );

        return filters;
    };

    var toggleCategoryFilter = function (filters, baseFilters, category) {
        if (filters[category] === undefined) {
            filters[category] = new Set();
        }

        if (filters[category].size === baseFilters[category].size) {
            filters[category] = new Set();
            // filter turned off
            return false;
        } else {
            // copy the set from the base filters
            filters[category] = new Set(baseFilters[category]);
            // filter turned on
            return true;
        }
    };

    var toggleTagFilter = function (filters, baseFilters, tag, category) {
        if (filters[category] === undefined) {
            filters[category] = new Set();
        }

        if (filters[category].has(tag)) {
            filters[category].delete(tag);
            // filter turned off
            return false;
        } else {
            filters[category].add(tag);
            // filter turned on
            return true;
        }
    };

    var getBaseFilters = function (tags, graph, categories, delimiter) {
        var baseFilters = {};
        for (var category in categories) {
            baseFilters[categories[category]] = new Set();
        }
        baseFilters['Misc'] = new Set();

        for (var tag in tags) {
            var tagText = tags[tag].key;

            var category = tagText.split(delimiter)[0].trim();
            if (tagText.startsWith("Folder topic")) {
                category = "Folder topic";
            } else if (tagText.startsWith("Box") &&
                tagText.indexOf("Folder") >= 0) {
                category = "Folder";
            } else if (tagText.startsWith("Box")) {
                category = "Box";
            }

            if (baseFilters[category] === undefined) {
                category = 'Misc';
            }

            baseFilters[category].add(tagText);
        }

        return baseFilters;
    };

    var selectTagFilterButton = function (button, category, filtersTurnedOn) {
        var fill = "transparent";
        if (filtersTurnedOn) {
            fill = graphColors.getTagColor(category, "fill");
        }
        button.style.backgroundColor = fill;
    };

    var getTagFilterElement = function (filters, baseFilters, tag, category, delimiter, graph) {
        var button = document.createElement("button");
        button.classList.add("filter-button");
        var splitTag = tag.split(delimiter);
        splitTag = splitTag[splitTag.length - 1].split(category);
        splitTag = splitTag[splitTag.length - 1].trim();
        button.textContent = splitTag;

        button.onclick = function () {
            var filtersTurnedOn = toggleTagFilter(
                filters, baseFilters, tag, category
            );

            selectTagFilterButton(button, category, filtersTurnedOn);

            filter(filters, graph);
        };

        return button;
    }

    var getCategoryFilterElement = function (
        filters, baseFilters, category, delimiter, graph) {

        var container = document.createElement("div");
        container.classList.add("category-filter-container");
        container.style.borderColor = graphColors.getTagColor(
            category, "stroke"
        );

        var arrow = document.createElement("i");
        arrow.setAttribute("class", "arrow fa-li fa fa-chevron-right");
        container.appendChild(arrow);

        var button = document.createElement("button");
        button.classList.add("filter-button");
        button.textContent = category;
        container.appendChild(button);

        var tagFilters = document.createElement("ul");
        tagFilters.classList.add("tag-filter-list");
        tagFilters.style.display = "none";
        tagFilters.style.borderColor = container.style.borderColor;
        container.appendChild(tagFilters);

        var iter = baseFilters[category].values();
        for (var i = 0; i < baseFilters[category].size; i++) {
            var tag = iter.next().value;
            var tagFilterElement = getTagFilterElement(
                filters, baseFilters, tag, category, delimiter, graph
            );

            var li = document.createElement("li");
            li.appendChild(tagFilterElement);
            tagFilters.appendChild(li);
        }

        arrow.onclick = function () {
            arrow.classList.toggle("fa-chevron-right");
            arrow.classList.toggle("fa-chevron-down");
            if (tagFilters.style.display === "none") {
                tagFilters.style.display = "";
            } else {
                tagFilters.style.display = "none";
            }
        };

        button.onclick = function () {
            var filtersTurnedOn = toggleCategoryFilter(
                filters, baseFilters, category
            );

            var fill = "transparent";
            if (filtersTurnedOn) {
                fill = graphColors.getTagColor(category, "fill");
            }
            container.style.backgroundColor = fill;

            for (var i = 0; i < tagFilters.children.length; i++) {
                selectTagFilterButton(
                    tagFilters.children[i].children[0], category, false
                );
            }

            filter(filters, graph);
        };

        return container;
    };

    var generateFilterMenu = function (tags, graph, categories, delimiter) {
        delimiter = delimiter || ":";

        var baseFilters = getBaseFilters(
            tags, graph, categories, delimiter
        );
        var filters = {};

        var categoryFilterElementList = document.getElementById("tags");
        for (var category in baseFilters) {
            if (baseFilters[category].size !== 0) {
                var categoryFilterElement = getCategoryFilterElement(
                    filters, baseFilters, category, delimiter, graph
                );
                var li = document.createElement("li");
                li.appendChild(categoryFilterElement);
                categoryFilterElementList.appendChild(li);
            }
        }
    }

    return {
        generateFilterMenu
    }
}());