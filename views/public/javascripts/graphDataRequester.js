var graphDataRequester = (function() {

    function setURLParam(paramName, paramVal) {
        var url = window.location.href;
        if (url.indexOf('?') !== -1) {
            var paramStart = url.indexOf(paramName + '=');
            if (paramStart !== -1) {
                var valueStart = paramStart + paramName.length + 1;
                var valueEnd = url.indexOf('&', valueStart);
                if (valueEnd === -1) {
                    valueEnd = url.length;
                }
                return url.slice(0, valueStart) + paramVal + url.slice(valueEnd);
            }
            return url + '&' + paramName + '=' + paramVal;
        } else {
            return url + '?' + paramName + '=' + paramVal;
        }
    }

    function getURLParam(paramName) {
        var url = window.location.href;
        var paramsStart = url.indexOf('?') + 1;
        if (paramsStart === 0) {
            return undefined;
        }
        var paramsString = url.slice(paramsStart);
        var params = paramsString.split('&');
        for (var i = 0; i < params.length; i++) {
            var param = params[i].split('=');
            if (param[0] === paramName) {
                return param[1];
            }
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
            jQuery.post(setURLParam('graphData', 0), {}, function(partialData) {
                var totalResults = partialData.totalResults;
                var limit = partialData.limit;
                console.log(partialData);

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
                        jQuery.post(setURLParam('graphData', i * limit), {}, function(dataChunk) {
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
        requestCompleteGraphData,
        getURLParam,
        setURLParam
    }
}())
