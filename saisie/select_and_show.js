
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

function select_and_show($select, $show)
{
    this.$select = $select;
    this.$show = $show;
    this.bob = 3;

    this.show_selected = function ()
    {
	var sel = selection();
	this.$show.text(sel);
    }

    this.stop_selection = function ()
    {
	this.show_selected();
	this.$select.off('mousemove mouseup');
    }

    this.start_selection = function ()
    {
	console.log('2');
	console.log(this.bob);
	this.$select.on('mousemove', this.show_selected);
	this.$select.on('mouseup', this.stop_selection);
    }

    console.log('1');
    this.$select.on('mousedown', this.start_selection);
}

$(document).ready(function(){
    var S = new select_and_show($("#acte"), $("#sel"));
});
