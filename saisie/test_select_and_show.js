$(document).ready(function(){

    var $source = $("#acte");
    var $show = $("#sel");

    $source = $('<textarea>', { 'id': '#add_source', 'class': 'texte',
				disabled: 'true',
				'text': 'this is a test' });
    $show = $('<textarea>', { 'id': '#add_show', 'class': 'texte' });
    $('#test').append([$source, $show]);

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

    $source = $('<textarea>',
		     {
			 'class': "xml_text source",
			 disabled: "true"
		     });

    $show = $('<textarea>',
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
