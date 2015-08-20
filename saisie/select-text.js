
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

var nb_show = 0;

function test_show()
{
    show_text(++nb_show + ' bob');
}

function stop_selection()
{
    show_selected();
    $("#acte").off('mousemove mouseup');
}

function start_selection()
{
    $("#acte").on('mousemove', show_selected);
    $("#acte").on('mouseup', stop_selection);
}

$(document).ready(function(){
    $("#acte").on('mousedown', start_selection);
});

