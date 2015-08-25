
var element;
var text;

/*
  Attention, ça ne fonctionne que si le texte ne contient aucun double blanc (espaces, sauts de ligne, etc.)
  cf. innerText vs. textContent
*/

/*
  From
  https://stackoverflow.com/questions/985272/selecting-text-in-an-element-akin-to-highlighting-with-your-mouse/987376#987376
*/
function select_text(id) {
    var doc = document
        , text = doc.getElementById(id)
        , range, selection
    ;    
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}

function unselect_all()
{
}

function selection()
{
    var sel;
    if(window.getSelection){
	sel = window.getSelection()
    }
    else if(document.getSelection){
	sel = document.getSelection()
    }
    else if(document.selection){
	sel = document.selection.createRange()
    }
    return sel
}

function get_text(id)
{
    var sel;

    select_text(id);
    sel = selection();
    unselect_all();
    return sel.toString();
}

function trim_text_in_element(id)
{
    var text = get_text(id)

    $('#'+id).text(text);
    return text;
}

function show_text(where, text)
{
    $(where).text(text);
}

function text_before(sel)
{
    return text.slice(0, sel.anchorOffset);
}

function text_after(sel)
{
    seltext = sel.toString();
    return text.slice(sel.anchorOffset + seltext.length);
}

function show_selected()
{
    var sel = selection();
    show_text('#before', text_before(sel));
    show_text('#sel', sel);
    show_text('#after', text_after(sel));
}

function stop_selection()
{
    show_selected();
    element.off('mousemove mouseup');
}

function start_selection()
{
    element.on('mousemove', show_selected);
    element.on('mouseup', stop_selection);
}

$(document).ready(function(){
    element = $("#acte");
    text = trim_text_in_element("acte");
    element.on('mousedown', start_selection);
});

