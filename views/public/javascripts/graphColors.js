var graphColors = (function () {
    var tagCategories = {
        "Folder topic": {
            fill: '#A6CEE3',
            stroke: '#536762'
        },
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
        "Geopolitical": {
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
        "Box": {
            fill: '#993300',
            stroke: '#502000'
        },
        "Folder": {
            fill: '#993300',
            stroke: '#502000'
        },
        "Misc": {
            fill: '#CAB2D6',
            stroke: '#656173'
        },
        ".": {
            regex: /.*/, // Catch all to match anything else
            fill: '#000000',
            stroke: '#000000'
        }
    }

    var tagCategoryColors = function (tagName, colorType) {
        if (tagName.startsWith("Folder topic")) {
            tagName = "Folder topic";
        } else {
            tagName = tagName.split(" ").trimRight(":")[0];
        }
        var category = tagCategories[tagName] || tagCategories["."];
        return category[colorType];
    }

    return {
        tagCategoryColors
    };
}());