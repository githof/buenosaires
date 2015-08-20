
var element;
var text;

/*
  Attention, ça ne fonctionne que si le texte ne contient aucun double blanc (espaces, sauts de ligne, etc.)
  cf. innerText vs. textContent
*/

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
    text = element.text();
    element.on('mousedown', start_selection);
});

