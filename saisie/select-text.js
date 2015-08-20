
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

function show_text(text)
{
    $("#selected").text(text);
}

function show_selected()
{
    var sel = selection();
    show_text(sel.anchorOffset + ':' + sel);
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

