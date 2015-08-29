$(document).ready(function(){

    var $source = $("#acte");
    var $show = $("#sel");

    var S = new select_and_show(
	$source,
	$show, 
	function ()
	{
	    console.log('before: ' + this.before);
	    console.log('selection: ' + this.select);
	    console.log('after: ' + this.after);
	}
    );

    $source = $('<p>',
		     {
			 'class': "texte",
		     });

    $show = $('<p>',
		     {
			 'class': "texte",
		     });

    $("#test").append([ $source, $show ]);

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
