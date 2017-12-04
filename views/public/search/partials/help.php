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
            Possible fields you can search by include:
            <i>title</i>, <i>description</i>, <i>collection</i>, <i>exhibit</i>, <i>itemType</i>, <i>resulttype</i>,
            <i>featured</i>, and <i>tags</i>. Examples:
            <br><br>
            <code>title:"Inhabited Spaces"</code><br>
            <code>collection:Map*</code><br>
            <code>itemType:("Historical Map" OR "Still Image")</code><br>
            <code>resulttype:Exhibit</code><br>
            <code>featured:true</code><br>
            <code>tags:forts</code><br>
        </td>
    </tr>
    <tr>
        <td>Search using boolean operators and wildcards:</td>
        <td>
            <code>paris AND fortifications</code><br>
            <code>title:paris AND itemType:Text</code><br>
            <code>featured:true</code><br>
            <code>184?s OR 185?s</code><br>
        </td>
    </tr>
    <tr>
        <td>Boost searches:</td>
        <td>Use the boost operator ^ to make one term more relevant than another. For example if we wanted to boost the term "paris" (the default boost is 1):<br><br>
            <code>paris^2 western</code><br>
        </td>
    </tr>
</table>