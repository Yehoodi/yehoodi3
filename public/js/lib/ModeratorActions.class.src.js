ModeratorActions = Class.create();

ModeratorActions.prototype = {

	initialize : function()
	{
		this.closed = $$('a.resource-mod-closed');

		// Observe clicks on our closed actions
		this.closed.each(function(item) {
		  Event.observe(item, 'click', this.toggleClosed.bindAsEventListener(this));
		}.bind(this));
	},

	//
	// Toggles the Closed Resource Status
	//
	toggleClosed :	function(e)
	{
		Event.stop(e);

		if(Event.element(e).name == "Close") {
			if(!confirm('You are about to close this topic.\n Users will NOT be able to make any more comments.')) {
				Event.stop(e);
				return false;
			}
		} else {
			if(!confirm('You are about to open this topic.\n Users will be able to post comments to it.')) {
				Event.stop(e);
				return false;
			}
		}

		var resourceId = Event.element(e).id;
		var item = $(resourceId);

		new Ajax.Request( '/moderatorajax/closed/',
		{
		  method: 'get',
		  parameters: {rsrc_id: resourceId },
		  onCreate: function(transport) {
			// Turn on status animation
			item.style.backgroundImage="url(/images/graphics/ajax-loader.gif)";
            item.style.backgroundPosition="0 0";
		  },
		  onSuccess: function(transport) {
			var response = transport.responseText;
			if(response != "") {
				
				if (response == "closed") {
					// Change the background image to Closed
					item.style.backgroundImage="url(/images/buttons/action-buttons.png)";
                    item.style.backgroundPosition="-96px -24px";
                    item.title = "Open this Topic";
				} else if (response == "open") {
					// Change the background image to Not Closed
					item.style.backgroundImage="url(/images/buttons/action-buttons.png)";
                    item.style.backgroundPosition="-96px 0";
                    item.title = "Close this Topic";
				}
			} else {
				// Turn off the resourceMatch <div>
				item.innerHTML = 'error';
			}
		  },
		  onComplete: function(){
				// Redirect to the page
				window.location.reload();
		  },
		  onFailure: function(){ alert('Something went wrong...') }
		});

	}
}

