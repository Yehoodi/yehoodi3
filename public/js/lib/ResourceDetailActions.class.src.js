ResourceDetailActions = Class.create();

ResourceDetailActions.prototype = {

	initialize : function()
	{
		this.bookmark = $$('a.resource-bookmark');

		// Observe clicks on our bookmark links
		this.bookmark.each(function(item) {
		  Event.observe(item, 'click', this.toggleBookmark.bindAsEventListener(this));
		}.bind(this));

		this.vote = $$('a.resource-vote');

		// Observe clicks on our vote links
		this.vote.each(function(item) {
		  Event.observe(item, 'click', this.toggleVote.bindAsEventListener(this));
		}.bind(this));

		this.calendar = $$('a.resource-calendar');

		// Observe clicks on our add to calendar links
		this.calendar.each(function(item) {
		  Event.observe(item, 'click', this.toggleCalendar.bindAsEventListener(this));
		}.bind(this));

		this.notify = $$('a.resource-notify');

		// Observe clicks on our add to watched (notify) links
		this.notify.each(function(item) {
		  Event.observe(item, 'click', this.toggleNotify.bindAsEventListener(this));
		}.bind(this));

		this.deleteDraft = $$('a.resource-delete');

		// Observe clicks on our add to delete draft links
		this.deleteDraft.each(function(item) {
		  Event.observe(item, 'click', this.confirmDeleteDraft.bindAsEventListener(this));
		}.bind(this));

		this.report = $$('a.resource-report');

		// Observe clicks on our mod report links
		this.report.each(function(item) {
		  Event.observe(item, 'click', this.reportToMods.bindAsEventListener(this));
		}.bind(this));
	},

	//
	// Toggles the Bookmark
	//
	toggleBookmark :	function(e)
	{
		var resourceId = Event.element(e).id;
		var item = $(resourceId);

		new Ajax.Request( '/discussionajax/bookmark/',
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
				
				if (response == "true") {
					// Change the background image to Bookmarked
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="0 -24px";
                    item.title = "Remove from Bookmarks";
				} else {
					// Change the background image to Not Bookmarked
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="0 0";
					item.title = "Add to Bookmarks";
				}
			} else {
				// Turn off the resourceMatch <div>
				item.innerHTML = 'error';
			}
		  },
		  onFailure: function(){ alert('Something went wrong...') }
		});
	},
	
	//
	// Toggles the Vote
	//
	toggleVote :	function(e)
	{
		var resourceId = Event.element(e).id;
		var item = $(resourceId);
		var id = resourceId.replace("a_vote_","");
		var voteNum = null;
		var voteTotal = 0;

		new Ajax.Request( '/discussionajax/vote/',
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
				if (response == "true") {
					// Change the background image to Voted
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="-48px -24px";
					item.title = "Remove Vote";

					// Update the vote counter
					if (voteNum = $('voteNum' + id)) {
						voteTotal = parseInt(voteNum.innerHTML) + 1
					}
				} else {
					// Change the background image to Not Voted
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="-48px 0";
					item.title = "Add Vote";

					// Update the vote counter
					if (voteNum = $('voteNum' + id)) {
						voteTotal = parseInt(voteNum.innerHTML) - 1
					}
				}
			} else {
				// Turn off the resourceMatch <div>
				item.innerHTML = 'error';
			}
		  },
		  onComplete: function(){
				//Nothing to do
				voteNum.innerHTML = voteTotal;
		  },
		  onFailure: function(){ alert('Something went wrong...') }
		});
	},

	//
	// Toggles the Notify (watch)
	//
	toggleNotify :	function(e)
	{
		var resourceId = Event.element(e).id;
		var item = $(resourceId);

		new Ajax.Request( '/discussionajax/notify/',
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
				
				if (response == "true") {
					// Change the background image to Watched
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="-24px -24px";
                    item.title = "Stop emailing me about this topic";
			} else {
					// Change the background image to Not Watched
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="-24px 0";
                    item.title = "Email me when someone comments about this topic";
				}
			} else {
				// Turn off the resourceMatch <div>
				item.innerHTML = 'error';
			}
		  },
		  onFailure: function(){ alert('Something went wrong...') }
		});
	},
	
	//
	// Reports the resource to the moderators
	//
	reportToMods :	function(e)
	{
		if(!confirm('Report this topic to the Yehoodistrators? By clicking OK you are sending an alert about spam, abusive post or some other type of action that goes against the Yehoodi User Agreement. Are you absolutely sure?')) {
			Event.stop(e);
			return false;
		}

		var resourceId = Event.element(e).id;
		var item = $(resourceId);

		new Ajax.Request( '/discussionajax/report/',
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
				
				if (response == "true") {
					// Change the background image to Reported!
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="-72px -24px";
                    item.title = "Topic Reported to Yehoodistrators!";
			} else {
					// Don't change the background because this is a one time action
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
					item.style.backgroundPosition="-72px -24px";
                    //item.title = "Report this to Yehoodistrators";
				}
			} else {
				// Turn off the resourceMatch <div>
				item.innerHTML = 'error';
			}
		  },
		  onFailure: function(){ alert('Something went wrong...') }
		});
	},
	
	//
	// Toggles the Calendar
	//
	toggleCalendar :	function(e)
	{
		var resourceId = Event.element(e).id;
		var item = $(resourceId);

		new Ajax.Request( '/discussionajax/calendar/',
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
				if (response == "true") {
					// Change the background image to On calendar
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="-1682px -24px";
                } else {
					// Change the background image to Add to calendar
					item.style.backgroundImage="url(/images/buttons/big-action-buttons.png)";
                    item.style.backgroundPosition="-168px 0px";
                }
			} else {
				// Turn off the resourceMatch <div>
				item.innerHTML = 'error';
			}
		  },
		  onComplete: function(){
				//Nothing to do
		  },
		  onFailure: function(){ alert('Something went wrong...') }
		});
	},
	
	//
	// Confirmation of draft deletion
	//
	confirmDeleteDraft :	function(e)
	{
		if(!confirm('Delete this draft? (There is no undo)')) {
			Event.stop(e);
			return false;
		}
	}
}