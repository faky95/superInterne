$('#ajout_erq_fait').live('click', function(e) {
	var $collectionHolder = $('#list_erq_fait');
	$collectionHolder.data('index', $collectionHolder.find(':input').length);
	var $newDiv = $("<div></div>");
	$collectionHolder.append($newDiv);
    // prevent the link from creating a "#" on the URL
    e.preventDefault();
    // add a new tag form (see code block below)
    addErqWidget($collectionHolder, $newDiv);
});
    

function addErqWidget($collectionHolder, $newDiv) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.attr('prototype');
    // get the new index
    var index = $collectionHolder.data('index');
    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);
    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);
    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<div></div>').append(newForm);
    $newFormLi.find('input[type="file"]').uniform();
    // also add a remove button, just for this example
    $newFormLi.append('<a href="#" class="remove-tag">x</a>');
    $newDiv.before($newFormLi);
    // handle the removal, just for this example
    $('.remove-tag').click(function(e) {
        e.preventDefault();
        $(this).parent().remove();
        return false;
    });
}