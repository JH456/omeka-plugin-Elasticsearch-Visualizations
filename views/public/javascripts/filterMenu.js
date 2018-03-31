'use strict';

var filterMenu = (function () {
    function filter(selectedDict, completeGraph) {
        console.log(completeGraph)
        var filterText = []
        for (var tag in selectedDict) {
            if (!selectedDict[tag]) {
                var str = document.getElementById(tag).outerText
                filterText.push(str + " ")
            }
        }
        console.log(filterText)
        graphVisualization.renderGraphOnSVG(graphFilterer.filterGraphData(filterText, completeGraph), graphColors.tagCategoryColors)
    }

    function generateFilterMenu(tags, completeGraph) {
        console.log(completeGraph)
        var keywords = {
            "Folder topic": [],
            "Person": [],
            "Facility": [],
            "Organization": [],
            "Geopolitical Entity": [],
            "Location": [],
            "Event": [],
            "Law": [],
            "Misc": [],
        };
        var colors = {
            "Folder topic": "#A6CEE3",
            "Person": "#1F78B5",
            "Facility": "#B2DF8A",
            "Organization": "#33A02C",
            "Geopolitical Entity": "#FB9A99",
            "Location": "#E31A1C",
            "Event": "#FDBF6F",
            "Law": "#FF7F00",
            "Misc": "#CAB2D6"
        }
        for (var i = 0; i < tags.length; i++) {
            var tag = tags[i].key;
            var parts = tag.split(":");
            if (parts === null) {
                keywords["Misc"].push(tag);
            } else if (parts[0] in keywords) {
                keywords[parts[0]].push(parts[1]);
            } else {
                keywords["Misc"].push(parts[0]);
            }
        }
        var counter = 0;
        var root = document.getElementById("tags");
        var selectedDict = {};
        var downDict = {};
        for (var word in keywords) {
            var li = document.createElement("li");
            var id = "element" + counter.toString();
            li.setAttribute("id", id);
            li.setAttribute("style", "border-style:solid;border-radius:25px;text-align:center;margin:5px;padding:4px;cursor:pointer;user-select:none;font-weight:bold")
            li.style.borderColor = graphColors.tagCategoryColors(word + ": ", 'stroke')
            li.style.backgroundColor = graphColors.tagCategoryColors(word + ": ", 'fill')
            //li.style.borderColor = colors[word];
            //li.style.backgroundColor = colors[word];
            var i = document.createElement("i");
            i.setAttribute("class", "fa-li fa fa-chevron-right arrow");
            i.setAttribute("style", "top:0.5em;left:-2.0em;");
            i.setAttribute("id", "btn" + counter.toString());
            i.addEventListener("click", function () {
                id = event.path[0].id;
                var down = false;

                if (id in downDict) {
                    down = downDict[id];
                }
                var num = id.replace("btn", "");
                var child = document.getElementById("child" + num);
                if (down) {
                    document.getElementById(id).setAttribute("class", "fa-li fa fa-chevron-right arrow");
                    child.style.display = "none";
                    downDict[id] = false;
                } else {
                    document.getElementById(id).setAttribute("class", "fa-li fa fa-chevron-down arrow");
                    child.style.display = "block";
                    downDict[id] = true;
                }
            });
            li.addEventListener("click", function () {
                id = event.path[0].id
                var selected = true;
                if (id in selectedDict) {
                    selected = selectedDict[id]
                }
                var pair = document.getElementById("btn" + id.replace("element", ""));
                if (selected) {
                    selectedDict[id] = false;
                    document.getElementById(id).style.backgroundColor = "transparent"
                    filter(selectedDict, completeGraph)
                } else {
                    document.getElementById(id).style.backgroundColor = graphColors.tagCategoryColors(event.target.outerText.trim() + ": ", 'fill')
                    selectedDict[id] = true;
                    filter(selectedDict, completeGraph)
                }
            });
            li.appendChild(i);
            li.appendChild(document.createTextNode(word));
            var ul = document.createElement("ul");
            ul.setAttribute("id", "child" + counter.toString());
            ul.setAttribute("style", "display:none;list-style:none");
            for (var i = 0; i < keywords[word].length; i++) {
                var liTemp = document.createElement("li");
                var subTag = keywords[word][i];
                liTemp.appendChild(document.createTextNode(subTag));
                liTemp.setAttribute("style", "font-weight: normal;")
                counter += 1;
                liTemp.setAttribute("id", "subElement" + counter.toString());
                //liTemp.setAttribute("style", "display:none")
                ul.appendChild(liTemp);
            }
            counter += 1;
            li.appendChild(ul);
            root.appendChild(li);
        }
    };

    return {
        generateFilterMenu
    }
}());
