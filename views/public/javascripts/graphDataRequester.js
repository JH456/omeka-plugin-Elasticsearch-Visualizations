var graphDataRequester = (function() {

    function appendURLParam(url, paramName, paramVal) {
        if (url.indexOf('?') !== -1) {
            return url + '&' + paramName + '=' + paramVal;
        } else {
            return url + '?' + paramName + '=' + paramVal;
        }
    }

    function addChunkToCompleteData(includedNodeSet, dataChunk, completeData) {
        var documentsToLinks = {}
        for (var i = 0; i < dataChunk.links.length; i++) {
            var link = dataChunk.links[i]
            var documentName = link.source
            if (!documentsToLinks[documentName]) {
                documentsToLinks[documentName] = [link]
            } else {
                documentsToLinks[documentName].push(link)
            }
        }
        for (var i = 0; i < dataChunk.nodes.length; i++) {
            var nodeId = dataChunk.nodes[i].id
            if (!includedNodeSet.has(nodeId)) {
                includedNodeSet.add(nodeId);
                completeData.nodes.push(dataChunk.nodes[i]);
                var links = documentsToLinks[nodeId]
                if (links) {
                    for (var j = 0; j < links.length; j++) {
                        completeData.links.push(links[j])
                    }
                }
            }
        }
    }

    function requestCompleteGraphData() {
        return new Promise(function(resolve, reject) {
            jQuery.post(appendURLParam(window.location.href, 'graphData', 0), {}, function(partialData) {
                var totalResults = partialData.totalResults;
                var limit = partialData.limit;

                var completeData = {}

                completeData.nodes = []
                completeData.links = []

                var includedNodeSet = new Set()
                addChunkToCompleteData(includedNodeSet, partialData, completeData)
                if (totalResults <= limit) {
                    resolve(completeData)
                } else  {
                    var remainingRequests = Math.ceil((totalResults - limit) / limit);
                    var totalRequests = remainingRequests;
                    for (var i = 1; i <= totalRequests; i++) {
                        jQuery.post(appendURLParam(window.location.href, 'graphData', i * limit), {}, function(dataChunk) {
                            remainingRequests--;
                            addChunkToCompleteData(includedNodeSet, dataChunk, completeData)
                            if (remainingRequests === 0) {
                                resolve(completeData)
                            }
                        }, 'json');
                    }
                }
            }, 'json');
        })
    }

    return {
        requestCompleteGraphData
    }
}())
