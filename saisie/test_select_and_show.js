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

    var $source = $('<textarea>',
		     {
			 'class': "xml_text source",
			 disabled: "true"
		     });

    var $show = $('<textarea>',
		     {
			 'class': "xml_text show",
			 disabled: "true"
		     });

    $("body").append([ $source, $show ]);

    $source.text("salut Tal, c'est encore moi !");
    
    var S2 = new select_and_show(
	$source,
	$show,
	function ()
	{
	    console.log('before: ' + this.before);
	    console.log('selection: ' + this.select);
	    console.log('after: ' + this.after);
	}
    );

});
