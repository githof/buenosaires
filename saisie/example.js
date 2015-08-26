
function example(value)
{
    var that = this;
    this.value = value;

    this.go = function()
    {
	console.log(that.value);
    }

    $("#target").on('mousedown', this.go);
}

$(document).ready(function(){
    var ex = new example('bob');
    ex.go();
});
