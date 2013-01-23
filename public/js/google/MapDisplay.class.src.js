// load the maps api, version 2
google.load('maps', '2',{"other_params":"sensor=false"});

// create the class
MapDisplay = Class.create();

MapDisplay.prototype = {

    rsrc_id   : null,   // ID of the resource being displayed
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
      + '</div>'
    ),

    markerTemplateShort : new Template(
	   '<div class="locationEmbedded">'
      + '	#{desc} <a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a><br />'
      + '</div>'
    ),

    linkPrimaryTemplate	: new Template(
    	 '<li class="primary_location" id="location_id_#{location_id}">'
    	+	'<a href="javascript:void(0);" id="location_link_#{location_id}">'
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
    	+	'<a href="javascript:void(0);" id="location_link_#{location_id}">'
        +		'#{desc}'
	    +	'</a>'
    	+	'<a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a>'
    	+'</li>'
	),

    linkAdditionalTemplate	: new Template(
    	 '<li class="additional_location" id="location_id_#{location_id}">'
    	+	'<a href="javascript:void(0);" id="location_link_#{location_id}">'
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
    	+	'<a href="javascript:void(0);" id="location_link_#{location_id}">'
        +		'#{desc}'
	    +	'</a>'
    	+	'<a href="http://maps.google.com/maps?geocode=&q=#{desc}" class="link_googleMap" id="" target="_blank"><img src="/images/icons/maps-arrow.png" title="Go to Google map" /></a>'
    	+'</li>'
	),

	initialize : function(container)
    {
        this.url       = '/submitajax/mapdisplay';
        this.rsrc_id   = $('hidden_resourceId').value;
        this.container = $(container);
        Event.observe(window, 'load', this.loadMap.bind(this));
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
        this.map.addControl(new google.maps.SmallZoomControl3D());

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
    // This handles the response from the Ajax response to retreive locations (called when
    // the map is first shown). This loops over each returned location and adds it to the
    // map with addMarkerToMap().
    //
    loadLocationsSuccess : function(transport)
    {
        var json = transport.responseText.evalJSON(true);
        
        if (json.locations == null) {
            return;
        }

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
            
            if(location.primary_location == 1) {
            	var venue = "Main Location";
            } else {
            	var venue = "Location";
            }
            
			if(!location.street_address) {
			    // use the just the description field
				var html = this.markerTemplateShort.evaluate({
			        'location_id' : location.location_id,
			        'lat'         : location.latitude,
			        'lng'         : location.longitude,
			        'desc'        : location.description
			    });
			} else {
				// we have enough fields to use them all
			    var html = this.markerTemplate.evaluate({
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
	
	        var node = Builder.build(html);

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
			        //linkNode.observe('click', this.onLinkClick.bindAsEventListener(this, node));
			        $('location_link_' + location.location_id).observe('click', this.onLinkClick.bindAsEventListener(this, node));

	        		
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
			        //linkNode.observe('click', this.onLinkClick.bindAsEventListener(this, node));
			        $('location_link_' + location.location_id).observe('click', this.onLinkClick.bindAsEventListener(this, node));
	        	}
		    }
	    }.bind(this));

        this.zoomAndCenterMap();
    },

    //
    // This adds a marker to the map based on the arguments. This includes creating an information window
    // that displays a delete button. If a marker with the given location_id already exists, then the
    // existing marker is removed, and the new one is added.
    //
    addMarkerToMap : function(id, lat, lng, desc, street, city, state, zip, country)
    {
        this.markers[id] = new google.maps.Marker(
            new google.maps.LatLng(lat, lng),
            { 'title' : desc, draggable : false }
        );
        this.markers[id].location_id = id;

        this.map.addOverlay(this.markers[id]);

        var html = this.markerTemplate.evaluate({
            'location_id' : id,
            'lat'         : lat,
            'lng'         : lng,
            'description' : desc,
            'street'      : street,
            'city'        : city,
            'state'       : state,
            'zip'         : zip,
            'country'     : country
        });

        var node = Builder.build(html);
        
        this.markers[id].bindInfoWindow(node);

        //console.log(this.markers[id]);
        return this.markers[id];
    },

    //
    // Pans the view to the marker associated to this text link and pops open
    // the info window
    //
    onLinkClick		: function(e, node)
    {
    	//console.log(node);
    	var markerId = Event.element(e).id.replace('location_link_','');
    	//console.log(this.markers[markerId]);
    	this.map.panTo(new GLatLng(this.markers[markerId].qa.y, this.markers[markerId].qa.x));
    	this.markers[markerId].openInfoWindowHtml(node, {maxWidth: '200'});
    },

    //
    // ssafely unloads map to avoid browser memory leaks
    //
    unloadMap : function()
    {
        google.maps.Unload();
    }
};
