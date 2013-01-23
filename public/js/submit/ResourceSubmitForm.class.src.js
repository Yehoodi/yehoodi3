ResourceSubmitForm = Class.create();

ResourceSubmitForm.prototype = {

	form: null,
	
	initialize : function(form)
	{
		this.form = $(form);
		this.resource = $$('a.a_resourceTypeId');

		// for the main submit button
		Event.observe('button_submit','click', this.onSubmit.bindAsEventListener(this));
		
		if($('button_draft')) {
			Event.observe($('button_draft'),'click', this.onSubmit.bindAsEventListener(this));
		}
		
		// for the resource box
		// Observe clicks on our topic (resources) links
		this.resource.each(function(item) {
		  Event.observe(item, 'click', this.changeCurrentResource.bindAsEventListener(this));
		}.bind(this));

		// Update the preview text
		this.resource.each(function(item) {
		  Event.observe(item, 'click', this.updatePreviewResource.bindAsEventListener(this));
		}.bind(this));

		// for the category box
		//$('select_categoryTypeId').observe('change', this.updateFormFields.bindAsEventListener(this));
		$('select_categoryTypeId').observe('change', this.updatePreviewCategory.bindAsEventListener(this));

		// update the visible event frequency fields
		$('select_repetition').observe('change', this.updateEventFreqFields.bindAsEventListener(this));
		
		// One-Time Event: Update the My event span_dateText
		$('select_durationOne').observe('change', this.updateOneTimeDuration.bindAsEventListener(this));
		$('select_startMonth').observe('change', this.updateFrequencyText.bindAsEventListener(this));
		$('select_startDay').observe('change', this.updateFrequencyText.bindAsEventListener(this));
		$('select_startYear').observe('change', this.updateFrequencyText.bindAsEventListener(this));
		
		$('select_endMonth').observe('change', this.updateFrequencyText.bindAsEventListener(this));
		$('select_endDay').observe('change', this.updateFrequencyText.bindAsEventListener(this));
		$('select_endYear').observe('change', this.updateFrequencyText.bindAsEventListener(this));
		
		// Preview resource observers
		$('input_title').observe('change', this.updatePreviewTitle.bindAsEventListener(this));
		
		// Title Match observer
		$('input_title').observe('change', this.showTitleMatch.bindAsEventListener(this));
		
		// Add Location Enter Key disable observer
		$('input_location').observe('keypress', this.killEnterKey.bindAsEventListener(this));
		
		// Image buttons holder
		if($('div_imageHider')) {
			$('div_imageHider').observe('click', this.showImageDiv.bindAsEventListener(this));
		}
		
		// Image buttons holder
		if($('button_imageDelete')) {
			$('button_imageDelete').observe('click', this.confirmImageDelete.bindAsEventListener(this));
		}
		
		// Map buttons holder
		$('div_mapHider').observe('click', this.showMapDiv.bindAsEventListener(this));
		
		// Extended information holder - For Moderators ONLY
		if($('div_extendedHider')) {
			$('div_extendedHider').observe('click', this.showExtendedDiv.bindAsEventListener(this));
		}
		
		// Extended information holder - For Moderators ONLY
		if($('div_extendedFramHider')) {
			$('div_extendedFramHider').observe('click', this.showExtendedFramDiv.bindAsEventListener(this));
		}
		
		// When the DOM is ready, update the Resource Preview
		Event.observe(window, 'load', this.updatePreviewResource.bind(this));

		// When the page is being unloaded...
		//Event.observe(window, 'beforeunload', this.confirmUnload.bind(this));

		// When the page is unloaded confirm if the user really wants to leave
		$('button_cancel').observe('click', this.confirmUnload.bindAsEventListener(this));
		
		// The preview image caption
		if ($('p_resourceImageCaption')) {
		    $('input_caption').observe('keyup', this.updatePreviewCaption.bindAsEventListener(this));
		}

		//this.resetErrors();
		this.updateFormFields();
		this.updateEventFreqFields();
		this.updatePreviewResource();
		this.updatePreviewCategory();
		this.updatePreviewTitle();
		

		// set focus on the user name field
        $('input_title').focus();
	},
	
	//
	// Clears errors on the form
	//
	resetErrors : function()
	{
		this.form.getElementsBySelector('.error').invoke('hide');
		$('errorNotice').hide();

	},
	
	//
	// displays form errors
	//
	showError : function(key, val)
	{
		var formElement = this.form[key];
		var container = formElement.up().down('.error');
		
		if (container) {
			container.update(val);
			container.show();
			
			$('errorNotice').show();
		}
	},
	
	//
	// Show the image div
	//
	showImageDiv : function()
	{
		if (!$('div_imageContainer').visible()) {
			Effect.Appear('div_imageContainer');
			$('div_imageHider').className = 'a_expand';
		} else {
			$('div_imageContainer').hide();
			$('div_imageHider').className = 'a_expanded';
		}
	},
	
	//
	// Show the map div
	//
	showMapDiv : function()
	{
		if (!$('div_inputLocationRow').visible()) {
			$('div_inputLocationRow').show();
			$('div_mapHider').className = 'a_expand';
		} else {
			$('div_inputLocationRow').hide();
			$('div_mapHider').className = 'a_expanded';
		}
	},
	
	//
	// Show the Podcast div
	//
	showExtendedDiv : function()
	{
		if (!$('div_extendedInfo').visible()) {
			Effect.BlindDown('div_extendedInfo');
			$('div_extendedHider').className = 'a_expand';
		} else {
			$('div_extendedInfo').hide();
			$('div_extendedHider').className = 'a_expanded';
		}
	},
	
	//
	// Show the Frim Fram div
	//
	showExtendedFramDiv : function()
	{
		if (!$('div_extendedFramInfo').visible()) {
			Effect.BlindDown('div_extendedFramInfo');
			$('div_extendedFramHider').className = 'a_expand';
		} else {
			$('div_extendedFramInfo').hide();
			$('div_extendedFramHider').className = 'a_expanded';
		}
	},
	
	//
	// Pre AJAX validation form submit
	//
	onSubmit : function(e)
	{
		Event.stop(e);
		
		// What was clicked? Post or Save Draft?
		this.clicked = Event.element(e).value;
		
		// Force stripping out the other buttons!!
		var params = this.form.serialize().replace("&prevImage=Upload","");
		params = params.replace("&Cancel=Cancel","");
		params = params.replace("&deleteImage=Remove%20Image","");
		
		if (this.clicked == "Save Draft") {
			params = params.replace("&Submit=Post","&Submit=Save%20Draft");
			params = params.replace("&Submit=Save%20Changes%20and%20Post","&Submit=Save%20Draft");
		}
		//console.log(params);return;
		
		var options = {
			parameters : params,
			method : this.form.method,
			onSuccess : this.onFormSuccess.bind(this)
		};
		
		this.resetErrors();
		new Ajax.Request(this.form.action, options);
	},
	
	//
	// POST Ajax form submit. All values are good.
	//
	onFormSuccess : function(transport)
	{
		// This is checking for a valid user and redirecting to login page
		// if the user somehow fell asleep at the wheel and is trying to post.
		if(transport.responseText == 'invalid user') {
			window.location.href = '/account/login/?timeout=true';
		}
		
		var json = transport.responseText.evalJSON(true);
		var errors = $H(json.errors);
		var postVars = $H({	rsrc_type_id: 		this.form.rsrc_type_id.value,
									rName		:		this.form.hidden_resourceValue.value,
									cat_id		:		this.form.cat_id.value,
									title		:		this.form.title.value,
									url_text	:		this.form.url_text.value,
									inputDescription: 	this.form.wmd_input.value,
									id			:		this.form.id.value,
									//repetition	:		this.form.repetition.value,
									repetition	:		'none',
									startDateMonth:		this.form.startDateMonth.value,
									startDateDay:		this.form.startDateDay.value,
									startDateYear:		this.form.startDateYear.value,
									endDateMonth:		this.form.endDateMonth.value,
									endDateDay	:		this.form.endDateDay.value,
									endDateYear	:		this.form.endDateYear.value,
									duration	:		this.form.duration.value,
									//repeatsEvery:		this.form.repeatsEvery.value,
									rsrc_id		:		this.form.rsrc_id.value,
									location	:		this.form.location.value,
									Submit		:		this.clicked,
									hidden_rsrcTypeChange:	this.form.hidden_rsrcTypeChange.value,
									hidden_token	:	this.form.hidden_token.value,
									notify_by_email	:	this.form.form_notify_by_email.checked});

		// For the Mods 
		if(this.form.rsrcDateMonth) {
			postVars.merge({				rsrcDateMonth:		this.form.rsrcDateMonth.value,
											rsrcDateDay:		this.form.rsrcDateDay.value,
											rsrcDateYear:		this.form.rsrcDateYear.value,
											rsrcTimeHour:		this.form.rsrcTimeHour.value,
											rsrcTimeMinute:		this.form.rsrcTimeMinute.value
			});
		}

		// For the Mods
		if(this.form.is_active) {
			postVars.merge({				is_active:		this.form.is_active.value
			});
		}

		// For the Mods (Image caption)
		if(this.form.caption) {
			postVars.merge({				caption:		this.form.caption.value
			});
		}
		
		// Extended Podcast info
		if(this.form.show_name) {
			postVars.merge({				show_name:		this.form.show_name.value,
											show_code:		this.form.show_code.value,
											show_episode:	this.form.show_episode.value,
									internal_page_url:		this.form.internal_page_url.value,
							internal_page_link_text:		this.form.internal_page_link_text.value,
											media_url:		this.form.media_url.value,
											flash_url:		this.form.flash_url.value,
											shownotes:		this.form.shownotes.value,
											artist:			this.form.artist.value,
											album:			this.form.album.value
			});
		}

		// Extended Frim Fram info
		if(this.form.extraFramInfo) {
			postVars.merge({				extraFramInfo:	this.form.extraFramInfo.value
			});
		}

		if(errors.size() > 0) {
			errors.each(function(pair) {
				this.showError(pair.key, pair.value);
			}.bind(this));
		}
		else {
			//console.log(this.form.getInputs('submit'));

			// Disable the submit button
			$('button_submit').disable();
			
			if($('button_draft')) {
				$('button_draft').disable();
			}
			
			this.postwith(this.form.action, postVars);

		}
	},
	
	//
	// Updates the topic highlight when the link is clicked
	//
	changeCurrentResource : function(e)
	{
		// stop the event
		Event.stop(e);
		var curResource = Event.element(e).id;
		
		// change the topic highlight
		this.resource.each(function(item) {
			if(item.id == curResource) {
				item.className = "a_resourceTypeId currentResource";
			} else {
				item.className = "a_resourceTypeId";
			}
		});

		// strip the leading "r_"
		curResource = curResource.replace("r_","");
		
		// update the hidden field that holds the rsrcId for the FormProcessor
		$('rsrc_type_id').value = curResource;

		// update the category list boxes
		this.updateCategoryFields(e);
	},
	
	//
	// Updates the category list via Ajax when the resource link is clicked
	//
	updateCategoryFields : function(e)
	{
		var resource = Event.element(e).id;
		resource = resource.replace("r_","");
		//console.log(resource);
		
		new Ajax.Updater('div_listCategoryRow', '/submitajax/ajaxselectcategory/',
		{
			method: 'get',
			parameters: {select_resourceTypeId: resource},
			evalScripts: true,
			onSuccess: this.updateFormFields(e)
		});

	},
	
	//
	// Updates the visible form fields when the resource list is changed
	//
	updateFormFields : function(e)
	{
		// effect duration in seconds
		var dur = 1.0;
		
		// clear the current errors
		//this.resetErrors();
		
		if (e) {
			//console.log('Category='+Event.element(e).id);return;
			var resource = Event.element(e).id;
			resource = resource.replace("r_","");
		} else {
			//console.log(e);
			//console.log('From a pageload');
			var resource = $('hidden_resourceValue').value;
		}
		// Hide/Show form fields based on resource
		switch(resource) {
			
			case 'featured': // Yehoodi Featured Stories (Mods Only)
			case '1':
				$('div_fieldsEvent').hide();					// hide the event field
				$('div_inputUrlRow').show();					// hide the url field
				$('div_inputLocationRow').hide();				// hide the event field
				$('hidden_resourceValue').value = 'featured';	// set the hidden resource name field to feature for state change after post
				$('div_fieldsLocation').show();					// hide the map section
				$('div_fieldsExtended').show();					// The extended information section
				
				// The Resource Heading
				$('h2_resourceHeading').update("Yehoodi Featured story (Mods Only)");
				$('h_eventDate').hide();						// hide the event fields
				break;
				
			case 'lindy': // Lindy Hop Stuff
			case '2':
				$('div_fieldsEvent').hide();					// hide the event field
				$('div_inputLocationRow').hide();				// hide the event field
				$('div_inputUrlRow').hide();					// hide the url field
				//$('div_fieldsImage').hide();					// hide the thumbnail upload
				$('div_fieldsLocation').hide();					// hide the map section
				$('hidden_resourceValue').value = 'lindy';
				//$('div_fieldsExtended').hide();					// The extended information section
				
				// The Resource Heading
				$('h2_resourceHeading').update("Lindy Hop's the thing");
				$('h_eventDate').hide();						// show the event fields
				break;
				
			case 'event': // Events
			case '3':
				$('div_fieldsEvent').show();
				$('div_inputLocationRow').show();			// show the event field
				$('div_fieldsLocation').show();					// show the map section
				$('div_inputUrlRow').show();					// hide the url field
				$('hidden_resourceValue').value = 'event';
				//$('div_fieldsExtended').hide();					// The extended information section

				// The Resource Heading
				$('h2_resourceHeading').update("Post your Lindy Hop event to the world!");
				$('h_eventDate').show();
				break;
				
			case 'lounge': // The Lounge
			case '4':
				$('div_fieldsEvent').hide();
				$('div_inputLocationRow').hide();			// hide the event field
				$('div_inputUrlRow').hide();					// hide the url field
				$('div_fieldsLocation').hide();					// hide the map section
				$('hidden_resourceValue').value = 'lounge';
				//$('div_fieldsExtended').hide();					// The extended information section

				// The Resource Heading
				$('h2_resourceHeading').update("Off the cob stuff in The Lounge");
				$('h_eventDate').hide();						// hide the event fields
				break;
				
			case 'biz': // Swing Biz
			case '5':
				$('div_fieldsEvent').hide();
				$('div_inputLocationRow').hide();				// hide the event field
				$('div_inputUrlRow').show();					// show the url field
				$('div_fieldsLocation').hide();					// hide the map section
				$('hidden_resourceValue').value = 'biz';
				//$('div_fieldsExtended').hide();					// The extended information section

				// The Resource Heading
				$('h2_resourceHeading').update("This business of swing");
				$('h_eventDate').hide();						// hide the event fields
				break;
								
			case 'admin': // Admin
			case '99':
				$('div_fieldsEvent').show();
				$('div_inputLocationRow').show();			// show the event field
				$('div_fieldsLocation').show();					// show the map section
				$('div_inputUrlRow').show();					// hide the url field
				$('hidden_resourceValue').value = 'admin';
				//$('div_fieldsExtended').hide();					// The extended information section

				// The Resource Heading
				$('h2_resourceHeading').update("Yehoodi Admin Only");
				$('h_eventDate').show();
				break;

			default:
				$('div_fieldsEvent').show();
				$('hidden_resourceValue').value = 'lounge';
				// The Resource Heading
				$('h2_resourceHeading').update("Off the cob stuff in The Lounge");
				$('h_eventDate').show();						// hide the event fields
				//$('div_fieldsExtended').hide();					// The extended information section
				break;
		}
	},
	
	//
	// Updates the resource name in the preview when the resource list is changed
	//
	updatePreviewResource : function(e)
	{
		// Update the Resource name
		$('liType').innerHTML = $('div_listResourceRow').getElementsByClassName('a_resourceTypeId currentResource')[0].innerHTML;
	},
	
	//
	// Updates the category name in the preview when the resource list is changed
	//
	updatePreviewCategory : function(e)
	{
		// Update the Category name
		$('liCategory').innerHTML = $('select_categoryTypeId').options[$('select_categoryTypeId').selectedIndex].text;
	},
	
	//
	// Updates the title the preview when the title field is changed
	//
	updatePreviewTextArea : function(e)
	{
		// Update the textarea
		if ($('input_').value.strip() == "")
		{
			$('previewTitle').innerHTML = 'Your title goes here';
		} else {
			$('previewTitle').innerHTML = $('input_title').value;
		}
	},

	//
	// Confirm delete resource image?
	//
	confirmImageDelete :	function(e)
	{
		if(!confirm('Remove image? (This will take place immediately)')) {
			Event.stop(e);
			return false;
		}
	},

	//
	// Submit the form
	//
	postwith :	function(to,p) 
	{
		var myForm = document.createElement("form");

		myForm.method="post" ;
		myForm.action = to ;

		p.each(function(pair) {
			var myInput = document.createElement("input") ;
			var myTextArea = document.createElement("textarea") ;

			if(pair.key == 'inputDescription' || pair.key == 'shownotes' || pair.key == 'extraFramInfo') {
				var description = pair.value;
				
				// Strip HTML (maybe only do this for Internet Explorer)
				myTextArea.setAttribute("id", "text_area");
				myTextArea.setAttribute("name", pair.key);
				if (Prototype.Browser.IE) {
					//description = description.replace(/(<([^>]+)>)/ig,"");
					myTextArea.innerText = pair.value;
				} else {
					myTextArea.innerHTML = pair.value;
				}
				myForm.appendChild(myTextArea) ;
			} else {
				myInput.setAttribute("name", pair.key) ;
				myInput.setAttribute("value", pair.value);
			}
			myForm.appendChild(myInput) ;
		});

		//alert(description);
		document.body.appendChild(myForm);
		//return;
		//console.log(myForm);return;
		myForm.submit() ;
		document.body.removeChild(myForm);
	},

	//
	// Updates the title in the Preview area after the user
	// tabs away from the field
	//
	updatePreviewTitle	:	function()
	{
		var title = $('input_title');
		if (title.value.length <= 60) {
			$('previewTitle').innerHTML = "<a>" + title.value + "</a>";
		} else {
			$('previewTitle').innerHTML = "<a>" + "Title too long" + "</a>";
		}
	},
	
	//
	// Updates the caption in the Preview image area when
	// the user tabs away from the field
	//
	updatePreviewCaption :	function()
	{
		var caption = $('input_caption');
		if (caption.value.length <= 255) {
			$('p_resourceImageCaption').update(caption.value).setStyle({ background: '' });
		} else {
			$('p_resourceImageCaption').update("Caption too long!!!").setStyle({ background: 'red' });
		}
	},
	
	//
	// Updates the visible event frequency fields. option_repetitionNone, option_repetitionWeekly or option_repetitionMonthly.
	//
	updateEventFreqFields : function(e)
	{
		var freq = $('select_repetition').value;
		
		// Hide/Show form fields based on event frequency
		switch(freq) {
			
			case 'none':
				$('div_listEndDateRow').hide();
				$('div_listDurationRow').show();
				//$('div_listRepeatRow').hide();
				$('hidden_fequencyValue').value = 'none';
				
				this.updateOneTimeDuration();
				break;
				
			case 'weekly':
				$('div_listEndDateRow').show();
				$('div_listDurationRow').hide();
				//$('div_listRepeatRow').show();
				$('hidden_fequencyValue').value = 'weekly';
				
				this.updateWeeklyDuration();
				break;
				
			case 'monthly':
				$('div_listEndDateRow').show();
				$('div_listDurationRow').hide();
				//$('div_listRepeatRow').hide();
				$('hidden_fequencyValue').value = 'monthly';
				
				this.updateMonthDuration();
				break;
		}
	},
	
	//
	// Called to update the span_dateText box to the current human readable text
	// based on the event frequency listbox
	//
	updateFrequencyText: function()
	{
	
		switch($('select_repetition').value) {
			
			case "none":
				this.updateOneTimeDuration();
				break;
				
			case "weekly":
				this.updateWeeklyDuration();
				break;
	
			case "monthly":
				this.updateMonthDuration();
				break;
	
			default:
				break;
		}
	
	},

	//
	// Checks for duplicate resource titles and reports them to the user
	//
	showTitleMatch : function()
	{
		var query = $('input_title').value;
		
		if ( query == '')
			return;
		
		new Ajax.Request( '/submitajax/ajaxtitlematch/',
		{
		  method: 'get',
		  parameters: {title: query },
		  onCreate: function(transport) {
			// Turn on status animation
		  	$("img_spinnerTitle").setAttribute("src", "/images/graphics/ajax-loader.gif");
		  },
		  onSuccess: function(transport) {
			var response = transport.responseText;
			//alert(response);
			if(response != "") {
				
				// Load the innerHTML
				$('div_containerTitleMatch').innerHTML = response;
				Effect.Appear('div_containerTitleMatch', {duration: 1.0});
				
				// Turn off Status Animation
				$("img_spinnerTitle").setAttribute("src", "/images/graphics/spacer.gif");
				
			} else {
				// Turn off the resourceMatch <div>
				$('div_containerTitleMatch').hide();
			}
		      	// Close the </ul>
		  },
		  onComplete: function(){
			// Turn off status animation
		  	$("img_spinnerTitle").setAttribute("src", "/images/graphics/spacer.gif");
		  },
		  onFailure: function(){ alert('Something went wrong...') }
		});

	},
	
	/**
	//
	// Kill the enter key to submit the form
	// (This doesn't work for Opera)
	//
	*/
	killEnterKey: function(e)
	{
		//console.log(e.which);
	     //alert(e.which);
		 var key;     
	     if(window.event)
	          key = window.event.keyCode; //IE
	     else
	          key = e.which; //firefox     
	
	     if (key == 13) {
	     	Event.stop(e);
	     }
	     
	     return (key != 13);
	},
	
	/**
	//
	// Human Text for Event functions
	//
	*/
	updateOneTimeDuration:	function()
	{
		var freq = $('select_durationOne').value;
		if($('select_startYear').value == "" || $('select_startMonth').value == "" || $('select_startDay').value == "" ) {
			return;
		}
		var minutes = 1000 * 60;
		var hours = minutes * 60;
		var days = hours * 24;
		var years = days * 365;
		
		// This gets the year directly from the Calendar's Output string "2008"
		var startYear = Math.abs($('select_startYear').value);
		
		// This gets the day of week directly from the Calendar's Output string "Thu", "Fri"
		var startDay = Math.abs($('select_startDay').value);
	
		// This gets the month directly from the Calendar's Output string "May", "Sep", "Oct"
		var startMonth = Math.abs($('select_startMonth').value);
		
		// This gets the full date from the Calendar's Output string
		var startFullDate = date("l, F j, Y", mktime(0,0,0,startMonth,startDay,startYear));
	
		// Update the One-Time duration text box
		var durText = $('span_dateText');
		
		// Update the Preview box
		var prevDateText = $('h_eventDate');
		
		// Get the micro timestamp for the date
		var tStamp = Date.parse(startFullDate);

		switch(freq) {
			
			case '1':
				durText.innerHTML = "One day event on " + startFullDate;
				prevDateText.innerHTML = startFullDate;
				break;
	
			case '2':
				var endFullDate = date("l, F j, Y",(tStamp/1000)+(days * (freq-1)/1000));
				durText.innerHTML = "Two day event from " + startFullDate + " to " + endFullDate;
				prevDateText.innerHTML = startFullDate + ' &ndash; ' + endFullDate;
				break;
	
			case '3':
				var endFullDate = date("l, F j, Y",(tStamp/1000)+(days * (freq-1)/1000));
				durText.innerHTML = "Three day event from " + startFullDate + " to " + endFullDate;
				prevDateText.innerHTML = startFullDate + ' &ndash; ' + endFullDate;
				break;
	
			case '4':
				var endFullDate = date("l, F j, Y",(tStamp/1000)+(days * (freq-1)/1000));
				durText.innerHTML = "Four day event from " + startFullDate + " to " + endFullDate;
				prevDateText.innerHTML = startFullDate + ' &ndash; ' + endFullDate;
				break;
	
			case '5':
				var endFullDate = date("l, F j, Y",(tStamp/1000)+(days * (freq-1)/1000));
				durText.innerHTML = "Five day event from " + startFullDate + " to " + endFullDate;
				prevDateText.innerHTML = startFullDate + ' &ndash; ' + endFullDate;
				break;
	
			case '6':
				var endFullDate = date("l, F j, Y",(tStamp/1000)+(days * (freq-1)/1000));
				durText.innerHTML = "Six day event from " + startFullDate + " to " + endFullDate;
				prevDateText.innerHTML = startFullDate + ' &ndash; ' + endFullDate;
				break;
	
			case '7':
				var endFullDate = date("l, F j, Y",(tStamp/1000)+(days * (freq-1)/1000));
				durText.innerHTML = "One week event, " + startFullDate + " through " + endFullDate;
				prevDateText.innerHTML = startFullDate + ' &ndash; ' + endFullDate;
				break;
		}
	},
	
	//
	// option_repetitionWeekly readable text
	//
	updateWeeklyDuration: function() {
		if($('select_startYear').value == "" || $('select_startMonth').value == "" || $('select_startDay').value == "" ) {
			return;
		}

		var minutes = 1000 * 60;
		var hours = minutes * 60;
		var days = hours * 24;
		var years = days * 365;
		
		// This gets the year directly from the Calendar's Output string "2008"
		var startYear = Math.abs($('select_startYear').value);
		var startDay = Math.abs($('select_startDay').value);
		var startMonth = Math.abs($('select_startMonth').value);
		var startDate = Math.abs($('select_startDay').value);  // The numeric day of the week
	
		var mkDateTime = mktime(0,0,0,startMonth,startDay,startYear);
		
		// This gets the day of week directly from the Calendar's Output string "Thu", "Fri"
		var startDay = date("D", mkDateTime);
		var startDayFull = date("l", mkDateTime);
	
		// This gets the month directly from the Calendar's Output string "May", "Sep", "Oct"
		var startMonth = date("M", mkDateTime);
		
		// This gets the full date from the Calendar's Output string
		var startFullDate = date("l, F j, Y", mkDateTime);
	
		// This gets the year directly from the Calendar's Output string "2008"
		var endYear = Math.abs($('select_endYear').value);
		
		// This gets the day of week directly from the Calendar's Output string "Thu", "Fri"
		var endDay = Math.abs($('select_endDay').value);
	
		// This gets the month directly from the Calendar's Output string "May", "Sep", "Oct"
		var endMonth = Math.abs($('select_endMonth').value);
		
		// This gets the date from endDate
		var endFullDate = date("l, F j, Y", mktime(0,0,0,endMonth,endDay,endYear));
	
		// Update the One-Time duration text box
		var durText = $('span_dateText');

		// Update the Preview box
		var prevDateText = $('h_eventDate');
		
		// Get the micro timestamp for the date
		var tStamp = Date.parse(startFullDate);
		var tStampEnd = Date.parse(endFullDate);
	
		durText.innerHTML = "Weekly every " + startDayFull;
		prevDateText.innerHTML = startFullDate + ' &ndash; ' + endFullDate;
		
		if(tStamp > tStampEnd) {
			durText.innerHTML += " until ?????";
		} else {
			durText.innerHTML += " until " + endFullDate;
		}
		//$('span_dateText').innerHTML = durText.innerHTML;
	},
	
	//
	// Monthly readable text
	//
	updateMonthDuration: function()
	{
		if($('select_startYear').value == "" || $('select_startMonth').value == "" || $('select_startDay').value == "" ) {
			return;
		}

		var minutes = 1000 * 60;
		var hours = minutes * 60;
		var days = hours * 24;
		var years = days * 365;

		// This gets the year directly from the Calendar's Output string "2008"
		var startYear = Math.abs($('select_startYear').value);
		var startDay = Math.abs($('select_startDay').value);
		var startMonth = Math.abs($('select_startMonth').value);
		var startDate = Math.abs($('select_startDay').value);  // The numeric day of the week
	
		var mkDateTime = mktime(0,0,0,startMonth,startDay,startYear);
		
		// This gets the day of week directly from the Calendar's Output string "Thu", "Fri"
		var startDay = date("D", mkDateTime);
		var startDayFull = date("l", mkDateTime);
	
		// This gets the month directly from the Calendar's Output string "May", "Sep", "Oct"
		var startMonth = date("M", mkDateTime);
		
		// This gets the full date from the Calendar's Output string
		var startFullDate = date("l, F j, Y", mkDateTime);
	
		// This gets the year directly from the Calendar's Output string "2008"
		var endYear = Math.abs($('select_endYear').value);
		
		// This gets the day of week directly from the Calendar's Output string "Thu", "Fri"
		var endDay = Math.abs($('select_endDay').value);
	
		// This gets the month directly from the Calendar's Output string "May", "Sep", "Oct"
		var endMonth = Math.abs($('select_endMonth').value);
		
		// This gets the date from endDate
		var endFullDate = date("l, F j, Y", mktime(0,0,0,endMonth,endDay,endYear));
	
		// Update the One-Time duration text box
		var durText = $('span_dateText');
		
		// Update the Preview box
		var prevDateText = $('h_eventDate');
		
		// Get the micro timestamp for the date
		var tStamp = Date.parse(startFullDate);
		var tStampEnd = Date.parse(endFullDate);
	
		//Convert Start Month to numeric
		var startMonthNum;
		var lastMonthDate;
		switch(startMonth) {
			case "Jan":
				startMonthNum = 1;
				lastMonthDate = 31;
				break;
			case "Feb":
				startMonthNum = 2;
				if (startYear % 4 == 0 && startYear % 100 != 0 || startYear % 400 == 0) {
					lastMonthDate = 29;
				} else {
					lastMonthDate = 28;
				}
				break;
			case "Mar":
				startMonthNum = 3;
				lastMonthDate = 31;
				break;
			case "Apr":
				startMonthNum = 4;
				lastMonthDate = 30;
				break;
			case "May":
				startMonthNum = 5;
				lastMonthDate = 31;
				break;
			case "Jun":
				startMonthNum = 6;
				lastMonthDate = 30;
				break;
			case "Jul":
				startMonthNum = 7;
				lastMonthDate = 31;
				break;
			case "Aug":
				startMonthNum = 8;
				lastMonthDate = 31;
				break;
			case "Sep":
				startMonthNum = 9;
				lastMonthDate = 30;
				break;
			case "Oct":
				startMonthNum = 10;
				lastMonthDate = 31;
				break;
			case "Nov":
				startMonthNum = 11;
				lastMonthDate = 30;
				break;
			case "Dec":
				startMonthNum = 12;
				lastMonthDate = 31;
				break;
		}
	
		// Update the Repeats Weekly Field
		if ($('select_repetition').value == "Weekly") {
			
			var repeatsEvery = $('submit-repeats-every');
			switch(startDay) {
				
				case "Sun":
					repeatsEvery.selectedIndex = 0;
					break;
		
				case "Mon":
					repeatsEvery.selectedIndex = 1;
					break;
		
				case "Tue":
					repeatsEvery.selectedIndex = 2;
					break;
		
				case "Wed":
					repeatsEvery.selectedIndex = 3;
					break;
		
				case "Thu":
					repeatsEvery.selectedIndex = 4;
					break;
		
				case "Fri":
					repeatsEvery.selectedIndex = 5;
					break;
		
				case "Sat":
					repeatsEvery.selectedIndex = 6;
					break;
			}
		}
	
		// Update the Repeats Monthly Field
		if ($('select_repetition').value == "monthly") {
	
			// Get the weekday of the first of the month
			var firstWeekday = date("D", mktime(0, 0, 0, startMonthNum, 1, startYear));
			
			// Count the number of weekdays in the month
			var weekDayCount = 0;
			var monthRepeatWord = "first";
			for(i=1; i<=lastMonthDate; i++) {
			    if( date("D", mktime(0, 0, 0, startMonthNum, i, startYear)) == startDay ) {
			    	weekDayCount++;
			    	// Is the current date the same as the user picked?
			    	if(i == startDate) {
			    		switch(weekDayCount) {
							case 1:
								monthRepeatWord = "first";
								break;
							case 2:
								monthRepeatWord = "second";
								break;
							case 3:
								monthRepeatWord = "third";
								break;
							case 4:
								monthRepeatWord = "fourth";
								break;
							case 5:
								monthRepeatWord = "last";
								break;
			    		}
		    		}
		    	}
		    }
			// Update the One-Time duration text box
			var durText = $('span_dateText');

			durText.innerHTML = "Monthly on the " + monthRepeatWord + " " + startDayFull;
			prevDateText.innerHTML = startFullDate + ' &ndash; ' + endFullDate;
			
			if(tStamp > tStampEnd) {
				durText.innerHTML += " until ?????";
			} else {
				durText.innerHTML += " until " + endFullDate;
			}
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
	// Hides the google map
	//
	hideMap	:	function()
	{
		if ($('div_inputLocationRow').visible()) {
			$('div_inputLocationRow').hide();	
		}
	},
	
	//
	// Hides the date frequency controls
	//
	hideFreq :	function()
	{
		if($('div_fieldsEvent').visible()) {
			$('div_fieldsEvent').hide();
		}
	},

	//
	// Confirmation pop-up before leaving the form
	//
	confirmUnload :	function(e)
	{
/*		if(confirm('Are you sure you want to leave this page?\nYou have started writing or editing a post.\nClick OK to continue, or Cancel to stay on this page.')) {
			Event.stop(e);
		}*/
		
		var textArea = $('wmd_input');
		
		if(textArea.value.length > 0) {
			if(!confirm("Are you sure you want to leave this page?\nYou have started writing or editing a post.\nClick OK to continue, or Cancel to stay on this page.")) {
				Event.stop(e);
				return false;
			} else {
				Event.stop(e);
				window.location.href = '/';
			}
		}
	}
};