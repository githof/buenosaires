$(document).ready(function(){
    var S = new select_and_show(
	$("#acte"),
	$("#sel"), 
	function ()
	{
	    console.log('before: ' + this.before);
	    console.log('selection: ' + this.select);
	    console.log('after: ' + this.after);
	}
    );
});
