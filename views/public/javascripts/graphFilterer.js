var graphFilterer = (function() {
    function passesFilters(tagName, regexFilters) {
        return !regexFilters.some(function(regex) {
            return regex.test(tagName)
        })
    }

    function filterTagsFromGraphData(exclusionFilterRegexStrings, graphData) {
        if (!exclusionFilterRegexStrings || exclusionFilterRegexStrings.length === 0) {
            return graphData
        }
        var regexFilters = []
        for (var i = 0; i < exclusionFilterRegexStrings.length; i++) {
            regexFilters.push(new RegExp(exclusionFilterRegexStrings[i]))
        }
        return  {
            nodes: graphData.nodes.filter(function(node) {
                    return passesFilters(node.id, regexFilters)
                }),
            links: graphData.links.filter(function(link) {
                    return passesFilters(link.target.id, regexFilters)
                })
        }
    }

    function filterRareTags(graphData, minimumMentionCount) {
        var tagCounts = {}
        for (var i = 0; i < graphData.links.length; i++) {
            var tagName = graphData.links[i].target.id
            if (tagCounts[tagName]) {
                tagCounts[tagName]++
            } else  {
                tagCounts[tagName] = 1
            }
        }
        var i = 0
        while (i < graphData.links.length) {
            var tagName = graphData.links[i].target.id
            var tagCount = tagCounts[tagName]
            if (tagCount < minimumMentionCount) {
                graphData.links.splice(i, 1)
            } else {
                i++
            }
        }
        i = 0
        while (i < graphData.nodes.length) {
            var tagName = graphData.nodes[i].id
            var tagCount = tagCounts[tagName] || minimumMentionCount
            if (tagCount < minimumMentionCount) {
                graphData.nodes.splice(i, 1)
            } else {
                i++
            }
        }

        return graphData
    }

    function filterGraphData(exclusionFilterRegexStrings, completeData) {
        var filteredData = filterRareTags(
            filterTagsFromGraphData(exclusionFilterRegexStrings, completeData),
            2)
        return filteredData
    }

    return {
        filterGraphData,
        filterRareTags,
        filterTagsFromGraphData
    }
}())
