
function select_and_show($select, $show)
{
    this.$select = $select;
    this.$show = $show;

    this.show_selected = function ()
    {
    }

    this.stop_selection = function ()
    {
    }

    this.start_selection = function ()
    {
	this.$select.on('mousemove', this.show_selected);
	this.$select.on('mouseup', this.stop_selection);
    }

    this.$select.on('mousedown', this.start_selection);
}

