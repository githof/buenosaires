
function create_selectable()
{
    $element = $('<textarea>',
		 {
		     disabled: "true"
		 });
    return $element;
}

function create_elements()
{
    this.$selectable = new selectable();
    this.$selection_display = new selection_display();
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
		   $('<dd>').append(this.selectable)
	       ]),
	      this.$selection_display
	  ])
    );
}

function taggable_element(id, tag, $container)
{
    this.id = id;
    this.tag = tag;
    this.container = container;
    this.create_selectable = create_selectable;
    this.create_elements = create_elements;
    this.create_elements();
}

$(document).ready(function(){
    $acte = new taggable_element("test_tag", "acte", $("#test"));
});
