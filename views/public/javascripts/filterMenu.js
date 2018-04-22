'use strict';

var filterMenu = (function () {
    function filter(selectedDict, completeGraph, oldFilter) {
        var filterText = []
        for (var tag in selectedDict) {
            if (selectedDict[tag]) {
                //var str = document.getElementById(tag).outerText
                var str = tag
                if (str != "")
                    filterText.push(str)
            }
        }
        if (oldFilter.length == filterText.length)
            return filterText
        graphVisualization.renderGraphOnSVG(graphFilterer.filterGraphData(filterText, completeGraph), graphColors.tagCategoryColors)
        return filterText
    }
    function getColorById(id, type) {
	var w = (id == "Box" ? " ." : ": ")
        var color = graphColors.tagCategoryColors(id + w, type)
	return color
    }
    function collapse(id, downDict) {
	var num = id.replace("btn", "");
        var child = document.getElementById("child" + num);
	document.getElementById(id).setAttribute("class", "fa-li fa fa-chevron-right arrow");
        child.style.display = "none";
	downDict[id] = false;
	return downDict;
    }
    function expand(id, downDict) {
	var num = id.replace("btn", "");
        var child = document.getElementById("child" + num);
	document.getElementById(id).setAttribute("class", "fa-li fa fa-chevron-down arrow");
        child.style.display = "block";
	downDict[id] = true;
	return downDict;
    }
    function selectParent(id, selectedDict) {
	selectedDict[id] = true;
	//var w = (id == "Box" ? " ." : ": ")
        //var color = graphColors.tagCategoryColors(id + w, 'fill')
	var color = getColorById(id, 'fill')
        document.getElementById(id).style.backgroundColor = color;
	return selectedDict
    }
    function deselectParent(id, selectedDict) {
	selectedDict[id] = false;
	document.getElementById(id).style.backgroundColor = "transparent";
	return selectedDict;
    }
    function selectChild(id, selectedDict) {
	if (id.startsWith("btn"))
	    return selectedDict
	var element = document.getElementById(id)
	var parent = element.parentElement.parentElement
	//selectedDict = selectParent(parent.id, selectedDict)
	selectedDict[id] = true
	element.style.backgroundColor = getColorById(parent.id, 'fill')
	return selectedDict
    }
    function deselectChild(id, selectedDict) {
	if (id.startsWith("btn"))
	    return selectedDict
	var element = document.getElementById(id)
	var parent = element.parentElement.parentElement
	//selectedDict = deselectParent(parent.id, selectedDict)
	selectedDict[id] = false
	element.style.backgroundColor = "transparent"
	return selectedDict
    }
    function generateFilterMenu(tags, completeGraph) {
        var oldFilter = []
        var keywords = {
	    "Box": [],
            "Folder topic": [],
            "Folder": [],
            "Person": [],
            "Organization": [],
            "Geopolitical Entity": [],
            "Event": [],
            "Date": [],
            "Facility": [],
            "Location": [],
            "Law": [],
        };

        var keywordIds = {}
        for (var i = 0; i < tags.length; i++) {
            var tag = tags[i].key;
	    if (tag.startsWith("Box")) {
		keywords["Box"].push(tag)
	    }
	    else {
		var s = ":"
		var parts = tag.split(s);
		if (parts === null) {
                    keywords["Misc"].push(tag);
		} else if (parts[0] in keywords) {
                    keywords[parts[0]].push(parts[1]);
		} else {
                    keywords["Misc"].push(parts[0]);
		}
	    }
        }
        var counter = 0;
        var root = document.getElementById("tags");
        var selectedDict = {};
        var downDict = {};
	
        for (var word in keywords) {
            var li = document.createElement("li");
            var id = word
            keywordIds[id] = word
            li.setAttribute("id", id);
            li.setAttribute("style", "border-style:solid;border-radius:25px;text-align:center;margin:5px;padding:4px;cursor:pointer;user-select:none;font-weight:bold")
	    var w = (word == "Box" ? " ." : ": ")
	    li.style.borderColor = graphColors.tagCategoryColors(word + w, 'stroke')
	    li.style.backgroundColor = graphColors.tagCategoryColors(word + w, 'fill')
	    
            var i = document.createElement("i");
            i.setAttribute("class", "fa-li fa fa-chevron-right arrow");
            i.setAttribute("style", "top:0.5em;left:-2.0em;");
            i.setAttribute("id", "btn" + counter.toString());
            //arrows
            i.addEventListener("click", function () {
                id = event.path[0].id;
                var down = false;

                if (id in downDict) {
                    down = downDict[id];
                }
                if (down) {
		    downDict = collapse(id, downDict)
                } else {
		    downDict = expand(id, downDict)
                }
            });
            //parent categories
            li.addEventListener("click", function () {
                id = event.path[0].id
                var selected = false;
                if (id in selectedDict) {
                    selected = selectedDict[id]
                }
                if (selected) {
		    selectedDict = (id in keywordIds ? deselectParent(id, selectedDict) : deselectChild(id, selectedDict))
                } else {
		    selectedDict = (id in keywordIds ? selectParent(id, selectedDict) : selectChild(id, selectedDict))

                }
		oldFilter = filter(selectedDict, completeGraph, oldFilter)
            });
            li.appendChild(i);
            li.appendChild(document.createTextNode(word));
	    //var div = document.createElement("div");
	    //div.setAttribute("style", "overflow-y: scroll;height: 50px;")
            var ul = document.createElement("ul");
            ul.setAttribute("id", "child" + counter.toString());
            ul.setAttribute("style", "display:none;list-style:none");
            for (var i = 0; i < keywords[word].length; i++) {
                var liTemp = document.createElement("li");
                var subTag = keywords[word][i];
                liTemp.appendChild(document.createTextNode(subTag));
                liTemp.setAttribute("style", "font-weight: normal;")
                counter += 1;
		//selectedDict[subTag] = true
		liTemp.setAttribute("id", subTag)
                ul.appendChild(liTemp);
            }
            counter += 1;
	    //div.appendChild(ul)
            //li.appendChild(div);
	    li.appendChild(ul);
            root.appendChild(li);
        }

        for (var word in keywords) {
            if (keywords.hasOwnProperty(word)) {
                selectedDict[word] = false
                document.getElementById(word).style.backgroundColor = "transparent"
		//document.getElementById(word).style.backgroundColor = getColorById(word, "fill")
            }
        }
        oldFilter = filter(selectedDict, completeGraph, oldFilter)

    };

    return {
        generateFilterMenu
    }
}());
