<div class="clear">&nbsp;</div>

{* Here are the settings for the FullCalendar *}
{if $locationAsk != 'true'}
    {literal}
    <script type="text/javascript">
    	jQuery(document).ready(function() {
    	
    		jQuery('#calendar').fullCalendar({
    			header: {
    				left: 'prev,next today',
    				center: 'title',
    				right: 'month,basicWeek'
    			},
    			defaultView: '{/literal}{$calType}',
    			{if $year}year: {$year},{/if}
    			{if $month === 0}month: 0,{else}month: {$month},{/if}
    			{if $day}date: {$day},{/if}{literal}
    			eventSources: [
    			     jQuery.fullCalendar.gcalFeed('http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic'),
    			     '/calendar/{/literal}{$calType}/{$categoryUrl}/?location={$location}{if $lon && $lat }&lon={$lon}&lat={$lat}{/if}{if $highlight}&h={$highlight}{/if}{literal}'
    		    ],
    
                eventRender: function(event, element) {
                    element.qtip({
                       content: event.description,
                       position: {
                          corner: {
                             target: 'topMiddle',
                             tooltip: 'bottomMiddle'
                          }
                       },
                       style: { 
                          width: 300,
                          padding: 5,
                          background: '#FFEE93',
                          color: '#4e4e4e',
                          textAlign: 'left',
                          border: {
                             width: 5,
                             radius: 5,
                             color: '#ffd94f'
                          },
                          tip: 'bottomMiddle',
                          name: 'cream' // Inherit the rest of the attributes from the preset dark style
                       }

                    });
                },
                
    		    loading: function(bool) { 
                    if (bool) jQuery('#loading').show(); 
                    else jQuery('#loading').hide(); 
                },
    
                viewDisplay: function(view) {
                    var view = jQuery('#calendar').fullCalendar('getView');
                    var curDate = jQuery('#calendar').fullCalendar('getDate');
                    
                    $('start').value = curDate;
                    
                    switch(view.name)
                    {
                        case 'month':
				    	jQuery('#calendar').fullCalendar('option','aspectRatio',1.35);
                          $('calType').value = view.name;
                    		new Ajax.Request( '/calendarajax/savecalendartype',
                    		{
                    		  method: 'get',
                    		  parameters: {type: 'month', month: curDate.getMonth(), day: curDate.getDate(), year: curDate.getFullYear() }
                    		});                   
                          break;
                        case 'basicWeek':
				    	 jQuery('#calendar').fullCalendar('option','aspectRatio',4);
                          $('calType').value = 'week';
                    		new Ajax.Request( '/calendarajax/savecalendartype',
                    		{
                    		  method: 'get',
                    		  parameters: {type: 'week', month: curDate.getMonth(), day: curDate.getDate(), year: curDate.getFullYear }
                    		});                   
                          break;
                        case 'basicDay':
                          $('calType').value = 'day';
                          break;
                        default:
				    	jQuery('#calendar').fullCalendar('option','aspectRatio',1.35);
                          $('calType').value = 'month';
                    		new Ajax.Request( '/calendarajax/savecalendartype',
                    		{
                    		  method: 'get',
                    		  parameters: {type: 'month', month: curDate.getMonth(), day: curDate.getDate(), year: curDate.getFullYear }
                    		});                   
                    }
                }
    		});
    	});
    </script>
    {/literal}
{else}
<table class="results empty"><tr><td>Please specify a location to search for events. City and State is all we need.</td></tr></table> 
{/if}

<div id='calendar'></div>

{* Required to remember what state the FullCalendar is in *}
<div id="hidden">
    <input type="hidden" name="calType" id="calType" value="{$calType}" />
    <input type="hidden" name="categoryUrl" id="categoryUrl" value="{$categoryUrl}" />
    <input type="hidden" name="location" id="location" value="{$location}" />
    <input type="hidden" name="start" id="start" value="{$start}" />

    <input type="hidden" id="hidden_address" value="{$address}" />
    <input type="hidden" id="hidden_longitude" value="{$lon}" />
    <input type="hidden" id="hidden_latitude" value="{$lat}" />
</div>