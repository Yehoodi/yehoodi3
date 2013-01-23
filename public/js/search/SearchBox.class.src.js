SearchBox = Class.create();

SearchBox.prototype = {

	a: null,
	
	initialize : function(a)
	{
		this.searchBox = $('input_searchBox');

		// Observe the searchBox
		Event.observe(this.searchBox,'click', this.clearSearchBox.bindAsEventListener(this));
		
		// Observe the searchBox
		//Event.observe(this.searchBox,'blur', this.fillSearchBox.bindAsEventListener(this));
		
		if (this.searchBox.value == '') {
			$('input_searchBox').value = 'Search';
		}
	},
	
	//
	// Clear the search box
	//
	clearSearchBox:	function(e)
	{
		this.searchBox.value = '';
	},
	
	//
	// fill the search box
	//
	fillSearchBox:	function(e)
	{
		if (this.searchBox.value == '') {
			this.searchBox.value = 'Search';
		}
	}
};