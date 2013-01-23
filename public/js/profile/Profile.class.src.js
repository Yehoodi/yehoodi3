Profile = Class.create();

Profile.prototype = {
	
	initialize : function(a)
	{
		this.controller = 'profile';
		
		this.ignore = $$('a.user-ignore');

		// Observe clicks on our ignore links
		this.ignore.each(function(item) {
		  Event.observe(item, 'click', this.toggleIgnore.bindAsEventListener(this));
		}.bind(this));
		
	},
	
	//
	// Toggles the Ignore
	//
	toggleIgnore :	function(e)
	{
		var memberId = Event.element(e).id;
		var item = $(memberId);

		new Ajax.Request( '/profileajax/ignore/',
		{
		  method: 'get',
		  parameters: {member_id: memberId },
		  onCreate: function(transport) {
			// Turn on status animation
			item.style.backgroundImage="url(/images/graphics/ajax-loader.gif)";
            item.style.backgroundPosition="0 0";
		  },
		  onSuccess: function(transport) {
			var response = transport.responseText;
			if(response != "") {
				
				if (response == "true") {
					// Change the background image to Ignored
					item.style.backgroundImage="url(/images/buttons/action-buttons.png)";
                    item.style.backgroundPosition="-120px -24px";
                    item.title="Stop ignoring user's Comments";
				} else {
					// Change the background image to Not Ignored
					item.style.backgroundImage="url(/images/buttons/action-buttons.png)";
                    item.style.backgroundPosition="-120px 0";
                    item.title="Ignore user's Comments";
				}
			} else {
				// Turn off the resourceMatch <div>
				item.innerHTML = 'error';
			}
		  },
		  onFailure: function(){ alert('Something went wrong...') }
		});
	}
}
