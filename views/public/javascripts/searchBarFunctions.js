function myFunction() {

    var div = document.createElement('div');
    div.className = 'w3-panel w3-row w3-teal';

    var content = document.createTextNode('HI');
    div.appendChild(content);

    var close = document.createElement('div');
    close.className = 'closeTag';
    div.appendChild(close);

    document.body.appendChild(div);
}
