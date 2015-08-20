
function text_selected()
{
    var sel = '';
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

function show_selected(text)
{
    $("#selected").text(text);
}

var nb_show = 0;

function test_show(text)
{
    show_selected(++nb_show + ' ' + text);
}

$(document).ready(function(){
    test_show("");
    $("#acte").bind('mouseup', test_show("hey!"));
});

