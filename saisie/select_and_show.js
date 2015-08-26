
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
    var that = this;
    this.$select = $select;
    this.$show = $show;
    this.before = this.select = this.after = "";

    this.show_selected = function ()
    {
	var sel = selection();
	that.$show.text(sel);
	return sel;
    }

    this.stop_selection = function ()
    {
	var sel = that.show_selected();
	that.before = text.slice(0, sel.anchorOffset);
	that.select = sel.toString();
	that.after = text.slice(sel.anchorOffset + seltext.length);
	that.$select.off('mousemove mouseup');
    }

    this.start_selection = function ()
    {
	that.$select.on('mousemove', that.show_selected);
	that.$select.on('mouseup', that.stop_selection);
    }

    this.$select.on('mousedown', this.start_selection);
}

$(document).ready(function(){
    var S = new select_and_show($("#acte"), $("#sel"));
});
