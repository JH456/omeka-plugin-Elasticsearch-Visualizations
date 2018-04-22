var graphColors = (function () {
    var tagCategoryColors = {
        "Person": {
            fill: '#1F78B5',
            stroke: '#0f3462'
        },
        "Facility": {
            fill: '#B2DF8A',
            stroke: '#617745'
        },
        "Organization": {
            fill: '#33A02C',
            stroke: '#205016'
        },
        "Geopolitical Entity": {
            fill: '#FB9A99',
            stroke: '#765550'
        },
        "Location": {
            fill: '#E31A1C',
            stroke: '#620A0C'
        },
        "Event": {
            fill: '#FDBF6F',
            stroke: '#776737'
        },
        "Law": {
            fill: '#FF7F00',
            stroke: '#773700'
        },
        "Date": {
            fill: '#51371E',
            stroke: '#11070E',
        },
        "Misc": {
            fill: '#CAB2D6',
            stroke: '#656173'
        },
        "Box": {
            fill: '#993300',
            stroke: '#502000'
        },
        "Folder topic": {
            fill: '#A6CEE3',
            stroke: '#536762'
        },
        "Folder": {
            fill: '#A6CEE3',
            stroke: '#536762'
        },
        ".": {
            fill: '#000000',
            stroke: '#000000'
        }
    }

    var getTagCategoryList = function () {
        var tagCategoryList = [];
        for (var category in tagCategoryColors) {
            if (category !== ".") {
                tagCategoryList.push(category);
            }
        }
        return tagCategoryList;
    }

    var getTagColor = function (tagText, colorType, delimiter) {
        delimiter = delimiter || ":";

        if (typeof (tagText) !== "string") {
            tagText = ".";
        } 

        var category = tagText.split(delimiter)[0].trim();
        if (tagText.startsWith("Folder topic")) {
            category = "Folder topic";
        } else if (tagText.startsWith("Box") &&
            tagText.indexOf("Folder") >= 0) {
            category = "Folder";
        } else if (tagText.startsWith("Box")) {
            category = "Box";
        }
        
        var colors = tagCategoryColors[category] || tagCategoryColors["."];
        return colors[colorType];
    }

    return {
        getTagColor,
        getTagCategoryList
    };
}());