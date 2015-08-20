
var element;

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
//    sel.anchorOffset
    return "";
}

function text_after(sel)
{
    return "";
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
    element.on('mousedown', start_selection);
});

