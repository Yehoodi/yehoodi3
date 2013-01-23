CommentSubmitForm = Class.create();

/**
*
*	Comment Submit Form
*	Handles the basic functions of 
*	the comment thread pages
*
**/
CommentSubmitForm.prototype = {

	form: null,
	a: null,
	
	initialize : function(form)
	{
		this.form = $(form);

		this.commentReply = $$('a.comment_reply');
		this.commentReplyTo = $$('a.comment_replyToComment');
		this.commentMod = $$('a.comment_mod');
		//this.quote = $$('a.comment_quote');
		this.edit = $$('a.comment_edit');
		this.showIgnoredLink = $$('a.comment_ignore');
		//this.comment = $('text_area');

		// for the main submit button
		Event.observe($('button_submit'),'click', this.onSubmit.bindAsEventListener(this));

		// Observe clicks on our comment reply links
		this.commentReply.each(function(item) {
		  Event.observe(item, 'click', this.showReplyBox.bindAsEventListener(this));
		}.bind(this));

		// Observe clicks on our comment respond to this links
		this.commentReplyTo.each(function(item) {
		  Event.observe(item, 'click', this.showReplyBox.bindAsEventListener(this));
		}.bind(this));

		// Observe clicks on our comment mod links
		this.commentMod.each(function(item) {
		  Event.observe(item, 'click', this.showEditBox.bindAsEventListener(this));
		}.bind(this));

		// Observe clicks on our comment quote links
/*		this.quote.each(function(item) {
		  Event.observe(item, 'click', this.showReplyBoxWithQuote.bindAsEventListener(this));
		}.bind(this));*/

		// Observe clicks on our edit links
		this.edit.each(function(item) {
		  Event.observe(item, 'click', this.showEditBox.bindAsEventListener(this));
		}.bind(this));

		// Observe clicks on show ignore links
		this.showIgnoredLink.each(function(item) {
		  Event.observe(item, 'click', this.showIgnoredComment.bindAsEventListener(this));
		}.bind(this));

		// Observe typing in the textarea
		//Event.observe(this.comment, 'keyup', this.updatePreview.bindAsEventListener(this));
		
		// Cancel button
	    //$('button_cancel').observe('click', this.hideReplyBox.bindAsEventListener(this));
		
		// Edit check
		if ($('hidden_commentId').value > 0) {
			$('div_authorRow').hide();
			$('div_excerptRow').hide();
			$('h_commentHeader').innerHTML = 'Edit a previous comment';
			$('div_replyForm').show();
		}		
		//this.resetErrors();
		//this.updateFormFields();
		//this.updateEventFreqFields();
		
	},
	
	showTagName: function(event) {
	    alert(Event.element(event).id);
	},

    //
    // Shows all errors on the page
    //
	showError : function(key, val)
    {
        var formElement = this.form[key];
        var container = formElement.up().down('.error');

		//console.log(formElement);
        if (container) {
            container.update(val);
            container.show();
        }
    },

    // Clears the errors from the page
	resetErrors : function()
    {
        this.form.getElementsBySelector('.error').invoke('hide');
    },

    //
	// Shows the Comment Reply Box
	// When any Reply to this thread is clicked
	//
	showReplyBox :	function(e)
	{
		var commentId = Event.element(e).id;

		// Make sure the textarea div is showing
		if (!$('wmd_input').visible()) {
			$('wmd_input').show();
		}

		// Make sure the excerpt div is showing
		if (!$('div_excerptRow').visible()) {
			$('div_excerptRow').show();
		}

		this.updateOriginalUserName(commentId);
		this.updateCommentText(commentId);
		$('hidden_replyToId').value = (commentId);

		//$('text_area').value = '';
		
		if ($('hidden_commentId').value > 0) {
			this.clearForm();
			$('h_commentHeader').innerHTML = 'Add a new comment';
			$('hidden_replyToId').value = 'r_' + $('hidden_resourceId').value;
			$('button_submit').value = 'Post';
			//$('div_replyForm').show();
		}

		$('div_replyForm').show();
		//$('div_replyForm').setStyle({position: 'relative', left: '0px', width: '100px'});

        // set focus on the user name field
        $('wmd_input').focus();
	},
	
	//
	// Shows the Comment Reply Box with the Quoted user
	//
	showReplyBoxWithQuote :	function(e)
	{
		var commentId = Event.element(e).id;

		if ($('hidden_commentId').value > 0) {
			this.clearForm();
		}

		// Hide the excerpt div since this is a quote
		if ($('div_excerptRow').visible()) {
			$('div_excerptRow').hide();
		}
		
		this.updateOriginalUserName(commentId);
		this.getQuotedComment(commentId);
		
		$('hidden_replyToId').value = 0;
		var commentBody = $('comment_body_'+commentId.replace("c_","")).innerHTML;
		//var commentQuote = "[quote]"+commentBody+"[/quote]";
		
		//$('text_area').value = commentQuote;
		
		$('div_replyForm').show();

		// set focus on the user name field
        $('text_area').focus();
	},
	
	//
	//	Get quote
	//
	getQuotedComment : function(commentId)
	{
		new Ajax.Request('/commentajax/updatequotedcomment/',
		{
			method: 'get',
			onCreate: function(transport) {
			// Turn on status animation
				$('text_area').value = '[Loading quote...]';
			},
			parameters: {id: commentId},
		    onSuccess: function(transport){
		      var response = transport.responseText || "[No username available]";
		      $('text_area').value = response;
		    },
		    onFailure: function(){ alert('Something went wrong...') }
		  });
	},
	
	//
	// Clears the form in case any edit box is up
	//
	clearForm: function()
	{
		$('hidden_commentId').value = '';
		$('text_area').innerHTML = '';
		$('hidden_commentId').value = '';
		$('div_authorRow').show();
		$('div_excerptRow').show();
	},
	
	//
	// Hides the Comment Reply Box
	//
	hideReplyBox :	function(e)
	{
		Event.stop(e);
		$('div_replyForm').hide();
	},
	
	updateOriginalUserName: function(commentId)
	{
		//console.log('Comment ID=' + commentId);
		new Ajax.Request('/commentajax/updatereplyusername/',
		{
			method: 'get',
			parameters: {id: commentId},
		    onSuccess: function(transport){
		      var response = transport.responseText || "[No username available]";
		      $('link_originalUserName').innerHTML = response;
		    },
		    onFailure: function(){ alert('Something went wrong...') }
		  });
	},

	updateCommentText: function(commentId)
	{
		new Ajax.Request('/commentajax/updatereplycomment/',
		{
			method: 'get',
			parameters: {id: commentId},
		    onSuccess: function(transport){
		      var response = transport.responseText || "[No excerpt avaiable]";
		      $('p_commentText').innerHTML = response;
		    },
		    onFailure: function(){ alert('Something went wrong...') }
		  });
	},

	//
	// Updates the Preview box
	//
	updatePreview : function()
	{
		var comment = $('text_area').value;
	},
	
	//
	// Shows the Comment Reply Box
	//
	showEditBox :	function(e)
	{
		Event.stop(e);
		window.location.href = Event.element(e).href;
	},
	
	//
	// Shows the Ignored comment
	//
	showIgnoredComment :	function(e)
	{
		var commentNum = Event.element(e).id;
		$('div_ignoredComment_'+commentNum).hide();
		Effect.Appear('comment_'+commentNum);
	},
	
	//
	// When the submit button is pressed
	//
	onSubmit : function(e)
	{
        Event.stop(e);
        
        var options = {
            parameters : this.form.serialize(),
            method     : this.form.method,
            onSuccess  : this.onFormSuccess.bind(this)
        };

        this.saveLastComment();
        this.resetErrors();
        new Ajax.Request(this.form.action, options);

	},
	
    onFormSuccess : function(transport)
    {
		// This is checking for a valid user and redirecting to login page
		// if the user somehow fell asleep at the wheel and is trying to post.
		if(transport.responseText == 'invalid user') {
			window.location.href = '/account/login/?timeout=true';
		}

		var json = transport.responseText.evalJSON(true);
        
        // more error stuff
        var errors = $H(json.errors);
        //alert(errors.keys().size());

        if (errors.size() > 0) {
            this.form.down('.error').show();
            errors.each(function(pair) {
                this.showError(pair.key, pair.value);
            }.bind(this));
        }
        else {
			$('button_submit').disable();
            this.form.submit();
        }
    },
    
	//
	// Useful function for loading .js scripts from .js
	//
	include :	function(jsFile)
	{
		new Ajax.Request(jsFile, {
			method: 'get',
			asynchronous  : false,
			onSuccess: function(transport) {
						// Evaluate the javascript
						eval(transport.responseText);
					   },
		
			onFailure : function() {
							alert("Failure including file: " + jsFile);
						}
		});
	},
	
	//
	// Save Last Comment
	//
	saveLastComment :  function()
	{
        // Save the comment in case the user gets looged out
        var userId = $('hidden_userId').value;
        var commentText = $('wmd_input').value;
        
        if (userId > 0 && commentText != '') {
            var saveopts = {
                parameters : { id: userId, text: commentText },
                method     : 'post',
                onSuccess  : function() {}
            }
            
            new Ajax.Request('/commentajax/savelastcomment/', saveopts);
        }
	}
};