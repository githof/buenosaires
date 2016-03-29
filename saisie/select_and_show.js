
/*

  A select_and_show object is composed of:

  - an element showing some text
  - an element showing the content of the selection in the above text

  The elements are given as jquery objects.
  The text is splitted into three portions, which are defined as attributes of the
  object: before, select, after.
  These attributes can be accessed through the callback passed to the constructor.

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

/*
  From
  https://stackoverflow.com/questions/985272/selecting-text-in-an-element-akin-to-highlighting-with-your-mouse/987376#987376
*/
function select_text(element) {
    var doc = document
        , range, selection
    ;    
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}


function select_and_show($select, $show, then_callback)
{
    var that = this;
    this.$select = $select;
    this.$show = $show;
    this.then_callback = then_callback;
    this.text = this.before = this.select = this.after = "";

    this.extract_text = function ()
    {
	var select = that.$select.get(0);
	var text;

	select_text(select);
	text = selection().toString();
	if(text != "")
	{
	    that.$select.text(text);
	    that.text = text;
	}
	else
	    that.text = that.$select.text();
    }

    this.show_selected = function ()
    {
	var sel = selection();
	that.$show.text(sel);
	return sel;
    }

    this.stop_selection = function ()
    {
	var sel = that.show_selected();
	that.before = that.text.slice(0, sel.anchorOffset);
	that.select = sel.toString();
	that.after = that.text.slice(sel.anchorOffset + that.select.length);
	that.$select.off('mousemove mouseup');

	if(that.then_callback != null) that.then_callback();
    }

    this.start_selection = function ()
    {
	that.$select.on('mousemove', that.show_selected);
	that.$select.on('mouseup', that.stop_selection);
    }

    this.extract_text();
    this.$select.on('mousedown', this.start_selection);
}

