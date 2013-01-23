// load the maps api, version 2
google.load('maps', '2',{"other_params":"sensor=false"});

CalBrowseLink = Class.create();

CalBrowseLink.prototype = {

    form: null,
    geocoder  : null,   // Used to look up addresses
    controller: '/calendar',

	initialize : function(form)
	{
		this.form = $(form);
        this.form.observe('submit', this.getGeoLocation.bindAsEventListener(this));

		// Instantiate the google map object
	    this.geocoder = new google.maps.ClientGeocoder();
        
        // Don't know if we will use this, but it's IP address based automatic location!!
        if (google.loader.ClientLocation) {
	        this.clientLocation = google.loader.ClientLocation;
	        //console.log(this.clientLocation);
        }

        // Collect our links
        this.eventType = $$('a.a_eventType');
		//this.location = $$('a.a_location');

		// Observe clicks on our category links
		this.eventType.each(function(item) {
		  Event.observe(item, 'click', this.changeEventType.bindAsEventListener(this));
		}.bind(this));

		// Observe click on the calendar "show" button
	    if ($('button_filter')) {
		  $('button_filter').observe('click', this.getGeoLocation.bindAsEventListener(this));
	    }

	    // Observe click on the calendar home location link
	    if ($('a_homeLocation')) {
		  $('a_homeLocation').observe('click', this.processHomeLocation.bindAsEventListener(this));
	    }

	},
	
	//
	// Build the url to jump to after re-load
	//
	changeEventType: function(event) {
		Event.stop(event);

		var calType = $('calType').value;
		var categoryUrl = Event.element(event).name;
		var location = $('location').value;
		var lon = $('hidden_longitude').value;
		var lat = $('hidden_latitude').value;
		
		var url = this.controller + '/' + calType + '/' + categoryUrl + '/?location=' + location;

		if ($('start').value) {
		  var curDateString = Date.parse($('start').value);
		  var newDate = new Date(curDateString);
		  var curYear = newDate.getFullYear();
		  var curMonth = newDate.getMonth();
		  var curDay = newDate.getDate();
		  url += '&year=' + curYear + '&month=' + curMonth + '&day=' + curDay;
		}

		if (lon && lat) {
		    url += '&lon=' + lon + '&lat=' + lat + '&loc=' + $('form_location').value.strip();;
		}
		window.location = url;
	},	
	
	//
	// Build the url to jump to after re-load
	//
	changeLocation: function(event) {
		Event.stop(event);

		var calType = $('calType').value;
		var categoryUrl = $('categoryUrl').value;
		var location = Event.element(event).name;
		var lon = $('hidden_longitude').value;
		var lat = $('hidden_latitude').value;
		
		var url = this.controller + '/' + calType + '/' + categoryUrl + '/?location=' + location;

		if ($('start').value) {
		  var curDateString = Date.parse($('start').value);
		  var newDate = new Date(curDateString);
		  var curYear = newDate.getFullYear();
		  var curMonth = newDate.getMonth();
		  var curDay = newDate.getDate();
		  url += '&year=' + curYear + '&month=' + curMonth + '&day=' + curDay;
		}
		
		if (lon && lat) {
		    url += '&lon=' + lon + '&lat=' + lat + '&loc=' + $('form_location').value.strip();;
		}
		window.location = url;
	},

    //
    // Process Home Location Link
    //
    processHomeLocation: function(e) {
        Event.stop(e);
        
        // Get the user's home location from the $identity
		new Ajax.Request( '/calendarajax/gethomelocation',
		{
            method: 'get',
            onComplete: function(transport){
              var response = transport.responseText;
              $('form_location').value = response;
			  var url = $('a_homeLocation').href;
			  window.location = url;
            }
		});
		
    },
	
    //
	// Calendar "Show" button
	//
	getGeoLocation: function(e) {
        Event.stop(e);
        
    	var address = $('form_location').value.strip();
        
        // If the Location field is blank, the user doesn't want us to know (even though we DO!)
		// Just submit the form
        if (address.length == 0) {
            //this.form.submit();
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
        
		// Set the lat and long
		var address = placemark.address;
		var latitude = placemark.Point.coordinates[1];
		var longitude = placemark.Point.coordinates[0];
		
		$('hidden_address').value = address;
		$('hidden_longitude').value = longitude;
		$('hidden_latitude').value = latitude;
		
		var locCookie = new Hash();
		locCookie.lon = parseFloat(longitude);
		locCookie.lat = parseFloat(latitude);
		locCookie.loc = address.toString();

		this.setCookie('y3loc', locCookie.toJSON());

		// Build the url
		var calType = $('calType').value;
		var categoryUrl = $('categoryUrl').value;
		var location = 'other';
		
		var url = this.controller + '/' + calType + '/' + categoryUrl + '/?location=' + location;

		if ($('start').value) {
		  var curDateString = Date.parse($('start').value);
		  var newDate = new Date(curDateString);
		  var curYear = newDate.getFullYear();
		  var curMonth = newDate.getMonth();
		  var curDay = newDate.getDate();
		  url += '&year=' + curYear + '&month=' + curMonth + '&day=' + curDay;
		}
		
		url += '&lon=' + longitude + '&lat=' + latitude + '&loc=' + address;
        
		window.location = url;
    },
    
    setCookie: function(c_name,value)
    {
        var expiredays = 30;
        var exdate = new Date();
        exdate.setDate(exdate.getDate()+expiredays);
        document.cookie = c_name + "=" + encodeURI(value) + ";domain=yehoodi.com;path=" + this.controller + ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
    }
}