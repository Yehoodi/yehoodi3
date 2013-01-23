Mail = Class.create();

Mail.prototype = {

	form: null,
	
	initialize : function(form)
	{
		this.form = $(form);
		this.controller = 'mail';

		// Observe change on mail list box
	    $('select_mail_types').observe('change', this.makeSelection.bindAsEventListener(this));
		
		// Observe click on Delete Mail link
	    $('a_markDelete').observe('click', this.deleteMail.bindAsEventListener(this));
		
		// Observe click on Mark As Read Mail link
	    $('a_markRead').observe('click', this.markAsRead.bindAsEventListener(this));
		
		// Observe click on Mark As UnRead Mail link
	    $('a_markUnread').observe('click', this.markAsNew.bindAsEventListener(this));
		
	},
	
	//
	// Auto-selects the appropriate checkboxes
	//
	makeSelection: function(event) {
		// get the value of the select box: None, All, Read, Unread
		var mailSelectValue = $('select_mail_types').options[$('select_mail_types').selectedIndex].text;
		// all checkboxes on this page
		var i=this.form.getElements('checkbox');

		switch(mailSelectValue) {
			case 'None':
				i.each(function(item)
					{
						item.checked=false;
						//$('select_mail_types').selectedIndex = 0;
						$('select_mail_types').options[$('select_mail_types').selectedIndex = 0];
					}
				);
				break;
				
			case 'All':
				i.each(function(item)
					{
						item.checked=true;
					}
				);
				break;
				
			case 'Read':
				i.each(function(item)
					{
						if(item.className == 'checkbox_messageSelect_read') {
							item.checked=true;
						} else {
							item.checked=false;
						}
					}
				);
				break;

			case 'Unread':
				i.each(function(item)
					{
						//console.log(item);
						if(item.className == 'checkbox_messageSelect_new') {
							item.checked=true;
						} else {
							item.checked=false;
						}
					}
				);
				break;
				
		}

	},
	
	//
	// Deletes checked mail
	//
	deleteMail : function(e) {

		// get all checkbox elements from the form
		var i=this.form.getInputs('checkbox');
		var selectedMail = new Array();
		var elements = new Array();
		
		i.each(function(item)
		{
			// collect the element ids that are checked
			// and the actual elements themselves
			if(item.checked == true) {
				selectedMail.push(item.id);
			}
		});
		
		if(selectedMail.size() > 0) {

			if(!confirm('Delete selected conversations?')) {
				Event.stop(e);
				return false;
			}

			new Ajax.Request( '/mailajax/deletemail/',
			{
			  method: 'post',
			  parameters: {'threads[]': selectedMail},
			  onComplete: function(){
					self.location.href = "/mail";
			  },
			  onFailure: function(){ alert('Something went wrong...');
			  		self.location.href = "/mail";
			  }
			});
		}

		// Change select list box back to "---" option
		$('select_mail_types').options[$('select_mail_types').selectedIndex = 0];
	},

	//
	// Marks mail as read
	//
	markAsRead : function(e) {

		// get all checkbox elements from the form
		var i = this.form.getInputs('checkbox');
		var selectedMail = new Array();
		var elements = new Array();
		var inboxSpan = $('a_inboxCounter');
		var inboxCount = inboxSpan.innerHTML;
		
		i.each(function(item)
		{
			// collect the element ids that are checked
			// and the actual elements themselves
			if(item.checked == true) {
				selectedMail.push(item.id);
				elements.push(item);
			}
		});
		
		var newMailCount = selectedMail.size();

		if(selectedMail.size() > 0) {

			if(!confirm('Mark selected mail as read?')) {
				Event.stop(e);
				return false;
			}

			new Ajax.Request( '/mailajax/markasread/',
			{
			  method: 'post',
			  parameters: {'threads[]': selectedMail},
			  onComplete: function(){
					elements.each(function(el) {
						// Update it to 'mail_read', uncheck it and push it to the array
						$('a_'+el.id).className = 'mail_read';
						el.className = 'checkbox_messageSelect_read'
						el.checked = false;
						
						// update the inbox count
						var newCount = inboxCount - newMailCount;
						
						if (newCount < 1) {
						    newCount = 0;
						}

						inboxSpan.update(newCount);

						// update the header and footer
						if ($('unreadMailHeader')) {
						  $('unreadMailHeader').update('(' + newCount + ')');
						}
						
						if ($('unreadMailFooter')) {
						  $('unreadMailFooter').update('(' + newCount + ')');
						}
					});
			  },
			  onFailure: function(){ alert('Something went wrong...');
			  		//self.location.href = "/mail";
			  }
			});
		}

		// Change select list box back to "---" option
		$('select_mail_types').options[$('select_mail_types').selectedIndex = 0];
	},

	//
	// Marks mail as new
	//
	markAsNew : function(e) {

		// get all checkbox elements from the form
		var i = this.form.getInputs('checkbox');
		var selectedMail = new Array();
		var elements = new Array();
		$('a_inboxCounter').cleanWhitespace();
		var inboxSpan = $('a_inboxCounter');
		var inboxCount = parseInt(inboxSpan.innerHTML);
		
		i.each(function(item)
		{
			// collect the element ids that are checked
			// and the actual elements themselves
			if(item.checked == true) {
				selectedMail.push(item.id);
				elements.push(item);
			}
		});

		var newMailCount = selectedMail.size();

		if(selectedMail.size() > 0) {

			if(!confirm('Mark selected mail as new?')) {
				Event.stop(e);
				return false;
			}

			new Ajax.Request( '/mailajax/markasnew/',
			{
			  method: 'post',
			  parameters: {'threads[]': selectedMail},
			  onComplete: function(){
					elements.each(function(el) {
						// Update it to 'mail_read', uncheck it and push it to the array
						$('a_'+el.id).className = 'mail_new';
						el.className = 'checkbox_messageSelect_new'
						el.checked = false;
						
						// update the inbox count
						var newCount = inboxCount + newMailCount;

						if (newCount < 1) {
						    newCount = 0;
						}
						
						inboxSpan.update(newCount);
						
						// update the header and footer
						if ($('unreadMailHeader')) {
						  $('unreadMailHeader').update('(' + newCount + ')');
						}
						
						if ($('unreadMailFooter')) {
						  $('unreadMailFooter').update('(' + newCount + ')');
						}
					});
			  },
			  onFailure: function(){ alert('Something went wrong...');
			  		//self.location.href = "/mail";
			  }
			});
		}

		// Change select list box back to "---" option
		$('select_mail_types').options[$('select_mail_types').selectedIndex = 0];
	}
}
