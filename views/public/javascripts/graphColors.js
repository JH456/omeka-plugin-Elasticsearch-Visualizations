var graphColors = (function() {
    var tagCategories = [
        {
            regex: /^Organization: .*/,
            fill: '#33A02C',
            stroke: '#205016'
        },
        {
            regex: /^Folder topic.*/,
            fill: '#A6CEE3',
            stroke: '#536762'
        },
        {
            regex: /^(Box|Folder) .*/,
            fill: '#993300',
            stroke: '#502000'
        },
        {
            regex: /^Facility: .*/,
            fill: '#B2DF8A',
            stroke: '#617745'
        },
        {
            regex: /^Geopolitical Entity: .*/,
            fill: '#FB9A99',
            stroke: '#765550'
        },
        {
            regex: /^Location: .*/,
            fill: '#E31A1C',
            stroke: '#620A0C'
        },
        {
            regex: /^Event: .*/,
            fill: '#FDBF6F',
            stroke: '#776737'
        },
        {
            regex: /^Law: .*/,
            fill: '#FF7F00',
            stroke: '#773700'
        },
        {
            regex: /^Misc: .*/,
            fill: '#CAB2D6',
            stroke: '#656173'
        },
        {
            regex: /^Person: .*/,
            fill: '#1F78B5',
            stroke: '#0f3462'
        },
        {
            regex: /item_.*/,
            fill: '#000077',
            stroke: '#000044'
        },
        {
            regex: /.*/, // Catch all to match anything else
            fill: '#000000',
            stroke: '#000000'
        }
    ]

    var tagCategoryColors = function(tagName, colorName) {

        var categoryColor = tagCategories.find(function(category) {
            return category.regex.test(tagName)
        })[colorName]

        return categoryColor
    }

    return {
        tagCategoryColors
    }
}())
