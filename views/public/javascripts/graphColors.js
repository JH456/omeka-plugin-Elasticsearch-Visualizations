var graphColors = (function () {
    var tagCategories = {
        "Folder topic": {
            regex: /^Folder topic.*/,
            fill: '#A6CEE3',
            stroke: '#536762'
        },
        "Person": {
            regex: /^Person: .*/,
            fill: '#1F78B5',
            stroke: '#0f3462'
        },
        "Facility": {
            regex: /^Facility: .*/,
            fill: '#B2DF8A',
            stroke: '#617745'
        },
        "Organization": {
            regex: /^Organization: .*/,
            fill: '#33A02C',
            stroke: '#205016'
        },
        "Geopolitical Entity": {
            regex: /^Geopolitical Entity: .*/,
            fill: '#FB9A99',
            stroke: '#765550'
        },
        "Location": {
            regex: /^Location: .*/,
            fill: '#E31A1C',
            stroke: '#620A0C'
        },
        "Event": {
            regex: /^Event: .*/,
            fill: '#FDBF6F',
            stroke: '#776737'
        },
        "Law": {
            regex: /^Law: .*/,
            fill: '#FF7F00',
            stroke: '#773700'
        },
        "Box": {
            regex: /^(Box|Folder) .*/,
            fill: '#993300',
            stroke: '#502000'
        },
        "Folder": {
            regex: /^Folder .*/,
            fill: '#993300',
            stroke: '#502000'
        },
        "Misc": {
            regex: /^Misc: .*/,
            fill: '#CAB2D6',
            stroke: '#656173'
        },
        "item_": {
            regex: /item_.*/,
            fill: '#000077',
            stroke: '#000044'
        },
        ".": {
            regex: /.*/, // Catch all to match anything else
            fill: '#000000',
            stroke: '#000000'
        }
    }

    var tagCategoryColors = function (tagName, colorType) {
        return tagCategories[tagName][colorType];
    }

    return {
        tagCategoryColors
    };
}());