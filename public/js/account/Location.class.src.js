// load the maps api, version 2
google.load('maps', '2',{"other_params":"sensor=false"});

// create the class
Location = Class.create();

Location.prototype = {

    geocoder  : null,   // Used to look up addresses

    initialize : function(form)
    {
        this.form           = $(form);
        this.form.observe('submit', this.onSubmit.bindAsEventListener(this));
        this.geocoder = new google.maps.ClientGeocoder();
        
        // Don't know if we will use this, but it's IP address based automatic location!!
        if (google.loader.ClientLocation) {
	        this.clientLocation = google.loader.ClientLocation;
        }
    },
    

    //
    // This is the event handler for when the form to add a new location is
    // submitted. This will initiate a request to the geocoder.
    //
    onSubmit : function(e)
    {
        Event.stop(e);
        
    	var address = $('form_location').value.strip();
        
        // If the Location field is blank, the user doesn't want us to know (even though we DO!)
		// Just submit the form
        if (address.length == 0) {
            this.form.submit();
            return;
        }
        
        // continue processing the address
        this.geocoder.getLocations(address, this.createPoint.bind(this));
    },

    //
    // This handles the response from the geoencoder by submitting the 
    // first match back to the server using Ajax to save it to the database
    //
    createPoint : function(locations)
    {
        if (locations.Status.code != G_GEO_SUCCESS) {
            // something went wrong:
            var msg = '';
            switch (locations.Status.code) {
                case G_GEO_BAD_REQUEST:
                    msg = 'Unable to parse request';
                    break;
                case G_GEO_MISSING_QUERY:
                    msg = 'Query not specified';
                    break;
                case G_GEO_UNKNOWN_ADDRESS:
                    msg = 'Unable to find address';
                    break;
                case G_GEO_UNAVAILABLE_ADDRESS:
                    msg = 'Forbidden address';
                    break;
                case G_GEO_BAD_KEY:
                    msg = 'Invalid API key';
                    break;
                case G_GEO_TOO_MANY_QUERIES:
                    msg = 'Too many geocoder queries';
                    break;
                case G_GEO_SERVER_ERROR:
                default:
                    msg = 'Unknown server error occurred';
            }
            alert(msg);
            return;
        }

        var accuracy = locations.Placemark[0].AddressDetails.Accuracy;
        if (accuracy < 4) {
        	alert('The location you entered is too general.\n Please enter a more specific location, i.e. "Brooklyn, New York".');
        	return;
        }
        
        var placemark = locations.Placemark[0];
        
        // add the lat, long and update the address to the form

        // Disable the submit button
		$('button_settings').disable();
		
		this.postwith( this.form.action,
			{	
				notify_by_email	:		this.form.form_notify_by_email.checked,
				filter_dirty	:		this.form.form_filter_dirty.checked,
				unit			:		this.form.form_unit.value,
				user_invisible	:		this.form.form_user_invisible.checked,
				distance		:		this.form.form_distance.value,
				location		:		placemark.address,
				latitude		:		placemark.Point.coordinates[1],
				longitude		:		placemark.Point.coordinates[0]
			}
		);
    },

	//
	// Submit the form
	//
	postwith :	function(to,p) 
	{
		var myForm = document.createElement("form");

		myForm.method="post" ;
		myForm.action = to ;

		for (var k in p) {
			var myInput = document.createElement("input") ;
			var myTextArea = document.createElement("textarea") ;

			if(k == 'bio') {
				myTextArea.setAttribute("id", "form_bio");
				myTextArea.setAttribute("name", k);
				myTextArea.innerHTML = p[k];
				myForm.appendChild(myTextArea) ;
			} else {
				myInput.setAttribute("name", k) ;
				myInput.setAttribute("value", p[k]);
			}
			myForm.appendChild(myInput) ;
		}

		document.body.appendChild(myForm);
		//return;
		//console.log(myForm);return;
		myForm.submit() ;
		document.body.removeChild(myForm);
	},

	//
    // Unloads the google map
    //
	unloadMap : function()
    {
        google.maps.Unload();
    }
};
