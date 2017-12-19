<table>
    <thead>
    <tr>
        <th>Topic</th>
        <th>Examples</th>
    </tr>
    </thead>
    <tr>
        <td>
            Search by field:
        </td>
        <td>
            Some possible fields you can search by include:
            <i>title</i>,
            <i>description</i>,
            <i>collection</i>,
            <i>exhibit</i>,
            <i>itemtype</i>,
            <i>resulttype</i>,
            <i>featured</i>,
            <i>tags</i>,
            <i>created</i>,
            <i>updated</i>.
            <br><br>
            Examples:
            <br>
            <code>title:"Inhabited Spaces"</code><br>
            <code>collection:Map*</code><br>
            <code>itemtype:("Historical Map" OR "Still Image")</code><br>
            <code>resulttype:Exhibit</code><br>
            <code>featured:true</code><br>
            <code>tags:forts</code><br>
            <code>created:[2017-10-07 TO 2017-10-14]</code>
            <code>updated:>=2017-11-01</code><br>
        </td>
    </tr>
    <tr>
        <td>Search using boolean operators and wildcards (defaults to OR):</td>
        <td>
            <code>paris AND fortifications</code><br>
            <code>title:paris AND itemType:Text</code><br>
            <code>featured:true</code><br>
            <code>184?s OR 185?s</code><br>
            <code>updated:[2017-12-01 TO *] AND resulttype:exhibit</code>
        </td>
    </tr>
    <tr>
        <td>Boost searches:</td>
        <td>Use the boost operator ^ to make one term more relevant than another. For example if we wanted to boost the term "paris" (the default boost is 1):<br><br>
            <code>paris^2 western</code><br>
        </td>
    </tr>
</table>