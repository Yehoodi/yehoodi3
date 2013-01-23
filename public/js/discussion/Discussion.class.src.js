Discussion = Class.create();

Discussion.prototype = {
	
	initialize : function(a)
	{
		this.controller = 'discussion';
		this.form = $('form_settingsDiscussion')
		this.category = $$('span.category');
		
		this.featuredList = $('li_categoriesFeatured');

		// Observe clicks on our category links
		this.category.each(function(item) {
		  Event.observe(item, 'click', this.showCategories.bindAsEventListener(this));
		}.bind(this));
		
		// for the main submit button
		Event.observe(this.featuredList,'mouseout', this.closeList.bindAsEventListener(this));
		
	},
	
	//
	//
	//
	showCategories: function(e) 
	{ 
		Event.stop(e);
		
		var theTab = Event.element(e).id;
		//console.log(theTab);
		//console.log($('ul_'+theTab).className);
		//return;
		
		var theUl = $('ul_'+theTab);
		//console.log(theUl);
		//return;
				
		if (theUl.className == "hide") 
		{
			theUl.className = "show";
			theUl.show();
		}
		else 
		{
			if (theUl.className == "show")
     		{
				theUl.className = "hide";
				theUl.hide();
     		}
		}
	},
	
	//
	//
	//
	closeList: function(e)
	{	
		//console.log(e);
		return;
		//console.log(Event.element(e).parentNode.id);
		var id = Event.element(e).parentNode.id;
		if(id != 'ul_categoriesFeatured' && id != 'li_categoriesFeatured') {
			$('ul_categoriesFeatured').className = "hide";
			$('ul_categoriesFeatured').hide();
		}
		//console.log($('ul_categoriesFeatured').parentNode.parentNode);
	
	},
	
	//
	// Processes the navigation to the
	// new page as determined by the
	// discussion bar
	//
	processPageNav: function(event) {
		
		Event.stop(event);
		var viewButtons = this.form.getInputs('radio', 'radio_view')
		
		var rsrcText = $('select_resource_types').options[$('select_resource_types').selectedIndex].title;
		var catUrlText = $('select_category_types').options[$('select_category_types').selectedIndex].value;
		var order = $('select_sort_types').options[$('select_sort_types').selectedIndex].value;
		var view = viewButtons.find( function(radio) { 
										return radio.checked; 
									 }).value
		var resource = 'all';
		
		if( rsrcText == "All resource types") {
			resource = "all";
		} else {
			resource = rsrcText;
		}
		
		self.location.href = "/" +
							this.controller + "/" +
							resource + "/" +
							catUrlText + "/" +
							order + "?view=" +
							view;

/*		console.log( "/" +
							this.controller + "/" +
							resource + "/" +
							catUrlText + "/" +
							order + "?view=" +
							view);*/
	}
}
