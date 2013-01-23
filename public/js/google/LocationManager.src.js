// load the maps api, version 2
google.load('maps', '2',{"other_params":"sensor=false"});

// create the class
LocationManager = Class.create();

LocationManager.prototype = {

    url       : null,

    rsrc_id   : null,   // ID of the resource being managed
    container : null,   // DOM element in which map is shown
    map       : null,   // The instance of Google Maps
    geocoder  : null,   // Used to look up addresses

    markers   : new Hash({}), // holds all markers added to map

    markerTemplate : new Template(
	   '<div class="locationEmbedded">'
	 + '#{street}<br />'
      + '#{city}, '
      + '#{state} '
      + '#{zip} '
      + '	#{country} <a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a><br />'
	 + '	<input type="button" class="input_removeLocation" value="Remove Location" />'
      + '</div>'
    ),

    markerTemplateShort : new Template(
	   '<div class="locationEmbedded">'
      + '	#{desc} <a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a><br />'
	 + '	<input type="button" class="input_removeLocation" value="Remove Location" />'
      + '</div>'
    ),

    linkPrimaryTemplate	: new Template(
    	 '<li class="primary_location" id="location_id_#{location_id}">'
    	+	'<a href="javascript:void(0);" id="#{location_id}">'
     +	'#{street}<br />'
     +	'#{city}, '
     +	'#{state} '
     +	'#{zip} '
     +	'#{country}'
	+	'</a>'
    	+	'<a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a>'
    	+'</li>'
	),

    linkPrimaryTemplateShort	: new Template(
    	 '<li class="primary_location" id="location_id_#{location_id}">'
    	+	'<a href="javascript:void(0);" id="#{location_id}">'
        +		'#{desc}'
	    +	'</a>'
    	+	'<a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a>'
    	+'</li>'
	),

    linkAdditionalTemplate	: new Template(
    	 '<li class="additional_location" id="location_id_#{location_id}">'
    	+	'<a href="javascript:void(0);" id="#{location_id}">'
     +	'#{street}<br />'
     +	'#{city}, '
     +	'#{state} '
     +	'#{zip} '
     +	'#{country}'
	+	'</a>'
    	+	'<a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a>'
    	+'</li>'
	),

    linkAdditionalTemplateShort	: new Template(
    	 '<li class="additional_location" id="location_id_#{location_id}">'
    	+	'<a href="javascript:void(0);" id="#{location_id}">'
        +		'#{desc}'
	    +	'</a>'
    	+	'<a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a>'
    	+'</li>'
	),

	initialize : function(container, form)
    {
        form           = $('button_addLocation');
        this.url       = '/submitajax/locationsmanage';
        this.rsrc_id   = $('hidden_resourceId').value;
        //console.log($('hidden_resourceId').value);
        this.container = $(container);
        this.maxMarkerCount = 4;
		this.resourceLink = $$('a.a_resourceTypeId');

        this.geocoder = new google.maps.ClientGeocoder();
        
        Event.observe(window, 'load', this.loadMap.bind(this));
		$('button_addLocation').observe('click', this.onFormSubmit.bindAsEventListener(this));

		// Map buttons holder
		// Have to fire the map.resize function to reset the maps location on the page
		$('div_mapHider').observe('click', this.resizeMapDude.bindAsEventListener(this));
		this.resourceLink.each(function(item) {
			Event.observe(item, 'click', this.resizeMapDude.bindAsEventListener(this));
		}.bind(this));

    },
    
    //
    // This fixes the position for the map. Needed because we show/hide
    // the map on the page based on when we actually need it.
    //
    resizeMapDude	:	function()
    {
		this.map.checkResize();
    },
    
    //
    // This creates the actual map and displays it once the page has loaded.
    // Additionally, it will initiate the request to fetch all existing locations
    // for the current resource.
    //
    loadMap : function()
    {
        if (!google.maps.BrowserIsCompatible())
            return;

        // unload the map when the user navigates away from the map
        Event.observe(window, 'unload', this.unloadMap.bind(this));

        // instantiate the map
        this.map = new google.maps.Map2(this.container);
        this.zoomAndCenterMap();
		
        this.map.addControl(new google.maps.MenuMapTypeControl());
        this.map.addControl(new google.maps.ScaleControl());
        this.map.addControl(new google.maps.SmallZoomControl3D());

        //var overviewMap = new google.maps.OverviewMapControl();
        //this.map.addControl(overviewMap);
        //overviewMap.hide(true);

        this.map.enableDoubleClickZoom();
        this.map.enableContinuousZoom();

        // get the markers if any
        var options = {
            parameters : 'action=get&rsrc_id=' + this.rsrc_id,
            onSuccess  : this.loadLocationsSuccess.bind(this)
        }

        new Ajax.Request(this.url, options);

    },

    //
    // This automatically zooms the map in as far as possible to display
    // all of the locations. This will be called when the map is initially loaded
    // and also when a new location is added. If there are no locations to work
    // with, the map will show the location where the Savoy Ballroom once stood.
    // 
    zoomAndCenterMap : function()
    {
        var bounds = new google.maps.LatLngBounds();
        //console.log(this.markers);
        this.markers.each(function(pair) {
            bounds.extend(pair.value.getPoint());
        });

        if (bounds.isEmpty()) {
            this.map.setCenter(new google.maps.LatLng(40.820882, -73.945164),
                               16,
                               G_NORMAL_MAP);
        }
        else {
            var zoom = Math.max(1, this.map.getBoundsZoomLevel(bounds) - 1);
            this.map.setCenter(bounds.getCenter(), zoom);
        }
    },

    //
    // This adds a marker to the map based on the arguments. This includes creating an information window
    // that displays a delete button. If a marker with the given location_id already exists, then the
    // existing marker is removed, and the new one is added.
    //
    addMarkerToMap : function(id, lat, lng, desc, street, city, state, zip, country)
    {
    	this.removeMarkerFromMap(id);

        this.markers[id] = new google.maps.Marker(
            new google.maps.LatLng(lat, lng),
            { 'title' : desc, draggable : false }
        );
        this.markers[id].location_id = id;
        //console.log(this.markers[id].get(location_id));

        var that = this;
        google.maps.Event.addListener(this.markers[id], 'dragend', function() {
            that.dragComplete(this);
        });
        google.maps.Event.addListener(this.markers[id], 'dragstart', function() {
            this.closeInfoWindow();
        });

        this.map.addOverlay(this.markers[id]);

        if(street == '') {
	        // use the just the description field
        	var html = this.markerTemplateShort.evaluate({
	            'location_id' : id,
	            'lat'         : lat,
	            'lng'         : lng,
	            'desc'		  : desc
	        });
        } else {
        	// we have enough fields to use them all
	        var html = this.markerTemplate.evaluate({
	            'location_id' : id,
	            'lat'         : lat,
	            'lng'         : lng,
	            'desc'        : desc,
	            'street'      : street,
	            'city'        : city,
	            'state'       : state,
	            'zip'         : zip,
	            'country'     : country
	        });
        }
        
        var node = Builder.build(html);
        var button = node.getElementsBySelector('input')[0];

        button.setAttribute('location_id', id);
        button.observe('click', this.onRemoveMarker.bindAsEventListener(this));
        
        this.markers[id].bindInfoWindow(node);

        //console.log(this.markers[id]);
        $('button_addLocation').value = "Mark this Location!";
        return this.markers[id];
    },

    //
    // This removes a marker from the map based on the first argument. If the marker
    // doesn't exist, then nothing happens.
    //
    removeMarkerFromMap : function(location_id)
    {
    	//console.log(location_id);
    	if (!this.hasMarker(location_id)) {
    		//console.log(this.hasMarker(location_id));
        	return;
        }
        
        this.map.removeOverlay(this.markers[location_id]);
        this.markers.remove(location_id);
        
    },

    //
    // This checks whether a marker exists for the given location ID.
    //
    hasMarker : function(location_id)
    {
        var location_ids = this.markers.keys();
        //console.log(this.markers.entries());

        //console.log('Marker Array:' + location_ids.indexOf(location_id));
        return location_ids.indexOf(location_id) >= 0;
    },

    //
    // This handles the response from the Ajax response to retreive locations (called when
    // the map is first shown). This loops over each returned location and adds it to the
    // map with addMarkerToMap().
    //
    loadLocationsSuccess : function(transport)
    {
        var json = transport.responseText.evalJSON(true);
		var markerCount = json.locations.size();
        
        if (json.locations == null|| markerCount == 0) {
        	// reset the mark button
        	$('button_addLocation').value = "Mark this Location!";
        	
        	// clear and rebuild the containing <ul> for the location display
			$('ul_location').update();
            return;
        } else {
        	$('div_inputLocationRow').show();
	        this.resizeMapDude();
        }
        
		var otherLocation = $('ul_location');
		var previousLocations = '';

		// clear and rebuild the containing <ul> for the location display

		// do the location loop
		json.locations.each(function(location) {
            this.addMarkerToMap(
                location.location_id,
                location.latitude,
                location.longitude,
                location.description,
                location.street_address,
                location.city,
                location.state,
                location.zip,
                location.country,
                location.primary_location
            );
            
	        // update the mark button text and the description value
            $('button_addLocation').value = "Mark this Location!";
	        $('div_inputLocationRow').value = location.description;
			
			if(!location.street_address) {
			    // use the just the description field
				var html = this.markerTemplateShort.evaluate({
			        'location_id' : location.location_id,
			        'lat'         : location.latitude,
			        'lng'         : location.longitude,
			        'description' : location.description
			    });
			} else {
				// we have enough fields to use them all
			    var html = this.markerTemplate.evaluate({
			        'location_id' : location.location_id,
			        'lat'         : location.latitude,
			        'lng'         : location.longitude,
			        'description' : location.description,
			        'street'      : location.street_address,
			        'city'        : location.city,
			        'state'       : location.state,
			        'zip'         : location.zip,
			        'country'     : location.country
			    });
			}
	
	        var node = Builder.build(html);
	        var button = node.getElementsBySelector('input')[0];
	        
	        button.setAttribute('location_id', location.location_id);
	        button.observe('click', this.onRemoveMarker.bindAsEventListener(this));
	        
	        this.markers[location.location_id].bindInfoWindow(node);

	        if(location.location_id) {
	        	
	        	if (location.primary_location == 1) {
	        		
			        // this adds the info for the text link at the botom of the map
					if(!location.street_address) {
					    // use the just the description field
				        var linkHtml = this.linkPrimaryTemplate.evaluate({
				            'location_id' : location.location_id,
				            'lat'         : location.latitude,
				            'lng'         : location.longitude,
				            'desc'        : location.description
					    });
					} else {
						// we have enough fields to use them all
				        var linkHtml = this.linkPrimaryTemplate.evaluate({
				            'location_id' : location.location_id,
				            'lat'         : location.latitude,
				            'lng'         : location.longitude,
				            'desc'        : location.description,
				            'street'      : location.street_address,
				            'city'        : location.city,
				            'state'       : location.state,
				            'zip'         : location.zip,
				            'country'     : location.country
					    });
					}

			        // build the text link in the DOM
			        var linkNode = Builder.build(linkHtml);
			        // append it to the ul in the DOM
			        $('ul_location').appendChild(linkNode);
			        
			        // observe clicks on the text link
			        linkNode.observe('click', this.onLinkClick.bindAsEventListener(this, node));

	        		
	        	} else {

					if(!location.street_address) {
					    // use the just the description field
				        var linkHtml = this.linkAdditionalTemplateShort.evaluate({
				            'location_id' : location.location_id,
				            'lat'         : location.latitude,
				            'lng'         : location.longitude,
				            'desc'        : location.description
					    });
					} else {
						// we have enough fields to use them all
				        var linkHtml = this.linkAdditionalTemplate.evaluate({
				            'location_id' : location.location_id,
				            'lat'         : location.latitude,
				            'lng'         : location.longitude,
				            'desc'        : location.description,
				            'street'      : location.street_address,
				            'city'        : location.city,
				            'state'       : location.state,
				            'zip'         : location.zip,
				            'country'     : location.country
					    });
					}
			        // build the text link in the DOM
	
			        var linkNode = Builder.build(linkHtml);
			        // append it to the ul in the DOM
			        $('ul_location').appendChild(linkNode);
			        
			        // observe clicks on the text link
			        linkNode.observe('click', this.onLinkClick.bindAsEventListener(this, node));
	        	}
		    }
	    }.bind(this));

        this.zoomAndCenterMap();
    },

    //
    // This is the event handler for when the form to add a new location is
    // submitted. This will initiate a request to the geocoder.
    //
    onFormSubmit : function(e)
    {
        //Event.stop(e);

        //var form = Event.element(e);
        //var address = $F(form.location).strip();

        var address = $('input_location').value.strip();
        
        if (address.length == 0)
            return;
            
        // check marker count
        //console.log(this.markers.size());
        if (this.markers.size() > this.maxMarkerCount) {
        	alert('You can only set up to 5 locations. Remove some to add a new locations.')
        	return false;
        }
            
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
            //message_write(msg);
            alert(msg);
            return;
        }

        var accuracy = locations.Placemark[0].AddressDetails.Accuracy;
        if (accuracy < 5) {
        	alert('The location you entered is too general.\n In order to insure accurate information to other Yehoodites, please enter a more specific location, i.e.\n "123 Mockingbird Lane, Anytown, NY 12345".');
        	return;
        }

        var placemark = locations.Placemark[0];
		
        // I gotta use some code I found on google forums to flatten the json response because
        // of this: http://code.google.com/p/gmaps-api-issues/issues/detail?id=606
        var googleAddr = new GoogleAddress(placemark);
		
		var streetAddress;
		var city;
		var state;
		var zip;
		var country;
        
		streetAddress 	= (googleAddr.ThoroughfareName ? googleAddr.ThoroughfareName : '');
		city 			= (googleAddr.LocalityName ? googleAddr.LocalityName : '');
		state 			= (googleAddr.AdministrativeAreaName ? googleAddr.AdministrativeAreaName : '');
		zip 			= (googleAddr.PostalCodeNumber ? googleAddr.PostalCodeNumber : '');
		country 		= (googleAddr.CountryName ? googleAddr.CountryName : '');

        var options = {
            parameters : 'action=add'
                       + '&rsrc_id=' + this.rsrc_id
                       + '&description=' + escape(placemark.address)
                       + '&street_address=' + streetAddress
                       + '&city=' + city
                       + '&state=' + state
                       + '&zip=' + zip
                       + '&country=' + country
                       + '&latitude=' + placemark.Point.coordinates[1]
                       + '&longitude=' + placemark.Point.coordinates[0],
            onSuccess  : this.createPointSuccess.bind(this)
        }

        new Ajax.Request(this.url, options);
    },

    //
    // This handles the response from the Ajax request to save the point.
    // If the point was successfully saved, this will then call addMarkerToMap()
    // to display the new point on the map
    //
    createPointSuccess : function(transport)
    {
        var json = transport.responseText.evalJSON(true);

        if (json.location_id == 0) {
            //message_write('Error adding location to blog post');
            alert('Error adding location to resource');
            return;
        }

        marker = this.addMarkerToMap(
            json.location_id,
            json.latitude,
            json.longitude,
            json.description,
            json.street_address,
            json.city,
            json.state,
            json.zip,
            json.country,
            json.primary_location
        );

        $('input_location').value = '';
		$('button_addLocation').value = "Mark this Location!";
		
        this.map.addOverlay(marker);

		if(!location.street_address) {
		    // use the just the description field
			var html = this.markerTemplateShort.evaluate({
		        'location_id' : json.location_id,
		        'lat'         : json.latitude,
		        'lng'         : json.longitude,
		        'desc'	      : json.description
		    });
		} else {
			// we have enough fields to use them all
		    var html = this.markerTemplate.evaluate({
		        'location_id' : json.location_id,
		        'lat'         : json.latitude,
		        'lng'         : json.longitude,
		        'desc'        : json.description,
		        'street'      : json.street_address,
		        'city'        : json.city,
		        'state'       : json.state,
		        'zip'         : json.zip,
		        'country'     : json.country
		    });
		}

        var node = Builder.build(html);
        var button = node.getElementsBySelector('input')[0];
        
        button.setAttribute('location_id', json.location_id);
        button.observe('click', this.onRemoveMarker.bindAsEventListener(this));
        
        this.markers[json.location_id].bindInfoWindow(node);

        if(json.location_id) {
        	
        	if (json.primary_location == 1) {

		        // this adds the info for the text link at the botom of the map
				if(!json.street_address) {
				    // use the just the description field
			        var linkHtml = this.linkPrimaryTemplateShort.evaluate({
			            'location_id' : json.location_id,
			            'lat'         : json.latitude,
			            'lng'         : json.longitude,
			            'desc'	      : json.description
				    });
				} else {
					// we have enough fields to use them all
			        var linkHtml = this.linkPrimaryTemplate.evaluate({
			            'location_id' : json.location_id,
			            'lat'         : json.latitude,
			            'lng'         : json.longitude,
			            'desc'        : json.description,
			            'street' 	  : json.street_address,
			            'city' 		  : json.city,
			            'state'       : json.state,
			            'zip'         : json.zip,
			            'country'     : json.country
				    });
				}
	
		        // build the text link in the DOM
		        var linkNode = Builder.build(linkHtml);
		        // append it to the ul in the DOM
		        $('ul_location').appendChild(linkNode);
		        
		        // observe clicks on the text link
		        linkNode.observe('click', this.onLinkClick.bindAsEventListener(this, node));
        		
        	} else {

				if(!json.street_address) {
				    // use the just the description field
			        var linkHtml = this.linkAdditionalTemplateShort.evaluate({
			            'location_id' : json.location_id,
			            'lat'         : json.latitude,
			            'lng'         : json.longitude,
			            'desc'        : json.description
				    });
				} else {
					// we have enough fields to use them all
			        var linkHtml = this.linkAdditionalTemplate.evaluate({
			            'location_id' : json.location_id,
			            'lat'         : json.latitude,
			            'lng'         : json.longitude,
			            'desc'        : json.description,
			            'street' 	  : json.street_address,
			            'city' 		  : json.city,
			            'state'       : json.state,
			            'zip'         : json.zip,
			            'country'     : json.country
				    });
				}
		        // build the text link in the DOM

		        var linkNode = Builder.build(linkHtml);
		        // append it to the ul in the DOM
		        $('ul_location').appendChild(linkNode);
		        
		        // observe clicks on the text link
		        linkNode.observe('click', this.onLinkClick.bindAsEventListener(this, node));
        		
        	}

        google.maps.Event.trigger(marker, 'click');

        this.zoomAndCenterMap();

	    }
    },

    //
    // Pans the view to the marker associated to this text link and pops open
    // the info window
    //
    onLinkClick		: function(e, node)
    {
    	//console.log(node);
    	var markerId = Event.element(e).id;
    	//console.log(this.markers);
    	this.map.panTo(new GLatLng(this.markers[markerId].qa.y, this.markers[markerId].qa.x));
    	this.markers[markerId].openInfoWindowHtml(node);
    },

    //
    // This is called after a marker has been dragged and dropped to a new location.
    // We initiate the Ajax request to save the new coordinates (the move action)
    // to the database.
    //
    dragComplete : function(marker)
    {
        var point = marker.getPoint();
        var options = {
            parameters : 'action=move'
                       + '&rsrc_id=' + this.rsrc_id
                       + '&location_id=' + marker.location_id
                       + '&latitude=' + point.lat()
                       + '&longitude=' + point.lng(),
            onSuccess  : this.onDragCompleteSuccess.bind(this)
        }

        new Ajax.Request(this.url, options);
    },

    //
    // This handles the response from the Ajax request to save a dragged marker's new
    // location. Thei method expects to receive the latitude and longitude of the point
    // so it can be replotted. This means if for some reason the new coordinates were not
    // saved, the point will revert to the location saved in the database.
    //
    onDragCompleteSuccess : function(transport)
    {
        var json = transport.responseText.evalJSON(true);

        if (json.location_id && this.hasMarker(json.location_id)) {
            var point = new google.maps.LatLng(json.latitude, json.longitude);

            var marker = this.addMarkerToMap(
                json.location_id,
                json.latitude,
                json.longitude,
                json.description,
                json.street_address,
                json.city,
                json.state,
                json.zip,
                json.country
            );
            google.maps.Event.trigger(marker, 'click');
        }
    },

    //
    // This is the event handler called when the remove location button is clicked
    // on a marker's information window. This will initiate the Ajax request to delete
    // the location from the database.
    //
    onRemoveMarker : function(e)
    {
        var button = Event.element(e);
        var location_id = button.getAttribute('location_id');

        var options = {
            parameters : 'action=delete'
                       + '&rsrc_id=' + this.rsrc_id
                       + '&location_id=' + location_id,
            onSuccess  : this.onRemoveMarkerSuccess.bind(this)
        };

        new Ajax.Request(this.url, options);
    },

    //
    // This is called after the Ajax request to delete a location from the database
    // successfully returns. This function will remove the marker from the map.
    //
    onRemoveMarkerSuccess : function(transport)
    {
        var json = transport.responseText.evalJSON(true);

        if (json.location_id) {
            this.removeMarkerFromMap(json.location_id);
	        $('input_location').value = '';

        	// find the <li> element holding the location text
	        var locationElement = json.location_id;
	        
	        // does this element have the primary_location class?
	        if ($('location_id_' + locationElement).className == "primary_location") {
	        	
	        	// load the locations fresh
	        	$('ul_location').update();
	        } else {
	        	// just remove the <li> from the DOM
		        $('location_id_' + locationElement).remove();
		        this.zoomAndCenterMap();
		        return;
	        }
        }

        var options = {
            parameters : 'action=get&rsrc_id=' + this.rsrc_id,
            onSuccess  : this.loadLocationsSuccess.bind(this)
        }

        new Ajax.Request(this.url, options);
        
    },
    
    //
    // ssafely unloads map to avoid browser memory leaks
    //
    unloadMap : function()
    {
        google.maps.Unload();
    }
};


function GoogleAddress(placeMark, curDepth)
    {
        if(curDepth == null || isNaN(curDepth))
          curDepth = 1 ;
        else if (++curDepth == 10) // just to be safe do not recurse more than 10 times
          return ;

        for (var attr in placeMark)
        {
            if ((typeof(placeMark[attr]) != 'object') || (placeMark[attr] instanceof Array))
            {
                this[attr] = placeMark[attr] ;
            } else {  // recurse thru sub-objects
                GoogleAddress.call(this, placeMark[attr], curDepth) ;
            }
        }
    }

