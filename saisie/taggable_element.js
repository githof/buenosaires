
function selectable()
{
    this.$element = $('<textarea>',
		      {
			  'class': "xml_text",
			  disabled: "true"
		      });
}

function selection_display()
{
    this.$element = $('<p>',
		      {
			  'class': "xml_text"
		      });
}

function create_elements()
{
    this.selectable = new selectable();
    this.selection_display = new selection_display();
    this.$container.append(
	$('<section>',
	  {
	      "class": "taggable_element",
	      id: this.id
	  }).append([
	      $('<dl>',
		{
		    "class": "xml"
		}
	       ).append([
		   $('<dt>',
		     {
			 text: this.tag
		     }),
		   $('<dd>').append(this.selectable.$element)
	       ]),
	      this.selection_display.$element
	  ])
    );
}

function taggable_element(id, tag, $container)
{
    this.id = id;
    this.tag = tag;
    this.$container = $container;
    this.create_elements = create_elements;
    this.create_elements();
}

$(document).ready(function(){
    $acte = new taggable_element("test_tag", "acte", $("#test"));
});
