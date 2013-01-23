<form action="{geturl controller='submit'}{if $fp->resource->getId()}{$fp->resource->getId()}{/if}" 
	enctype="multipart/form-data" 
	method="post" 
	id="form_submit">

	{include file='lib/error.tpl' error=$imageError}
	
	{if $fp->hasError()}
     <!-- general error display -->
     <div class="error" id="div_error">
     	An error has occured in the form below. Please check
     	the highlighted fields and resubmit the form.
     </div>
     <!-- end general error display -->
     {/if}

     <h2 id="h2_resourceHeading">Choose a topic below...</h2>
	
     {* This div holds the error message for post flood protection. It will tell the user to wait 60 seconds or so to post another item *}
     <div class="fieldset" id="div_fieldsGeneral">
      <div class="row" id="div_inputTitleRow">
           <input type="hidden" name="resource" />
           {include file='lib/error.tpl' error=$fp->getError('resource')}
      </div>
	</div>

	 <!-- resource and category types -->
     <div class="fieldset" id="div_fieldsGeneral">
		  <div class="row" id="div_listResourceRow">
			<label>Topic:</label>
			{foreach from=$resourceTypes key=rsrcName item=rsrcId}
				<a id="r_{$rsrcId}" class="a_resourceTypeId {if $rsrcId == $rsrc_type_id}currentResource{/if}" href="javascript:void(0);">{$rsrcName|capitalize}</a>
			{/foreach}
			<input type="hidden" value="{$rsrc_type_id}" name="rsrc_type_id" id="rsrc_type_id"/>
			<input type="hidden" id="hidden_resourceValue" name="hidden_resourceValue" value="{$hidden_resourceValue}" />
		</div>
		
		<div class="row" id="div_listCategoryRow">
            <label>Category:</label>
            <select name="cat_id" id="select_categoryTypeId" tabindex="1">
            {foreach from=$categoryTypes item=category}
                <option value="{$category.cat_id}" {if $fp->cat_id == $category.cat_id}selected{/if}>{$category.cat_type|capitalize}</option>
            {/foreach}
            </select>
          </div>
     </div>
	<!-- end resource and category types -->
	
     <!-- common -->
     <div class="fieldset" id="div_fieldsCommon">
          <div class="row" id="div_inputTitleRow">
               <label>Title:</label>
               <input class="input_text" type="text" name="title" id="input_title" value="{$fp->title|escape}" tabindex="2" />
               <img id="img_spinnerTitle" class="spinner" src="/images/graphics/spacer.gif" />
               {include file='lib/error.tpl' error=$fp->getError('title')}
          </div>
          
          <!-- title match display -->
          <div class="titleMatch warning" id="div_containerTitleMatch" style="display: none;"> </div>
          <!-- end title match display -->          

	</div>
	<!-- end common -->
	
	<!--YMD Editor Description input-->
	{include file='modules/module.wmdeditor.tpl' content=$fp->descrip textarea_name = 'inputDescription'}
	<!--end YMD Editor Description input-->
	
    <!-- media and url -->
    <div class="fieldset" id="div_fieldsMedia">
        <div class="row" id="div_inputUrlRow">
            <label>URL:</label>
            <input class="input_text" type="text" id="input_url" name="url_text" value="{$fp->url|escape}" />
            <img id="img_spinnerUrl" class="spinner" src="/images/graphics/spacer.gif" /><br />
            {include file='lib/error.tpl' error=$fp->getError('url_text')}
        </div>
    </div>
    <!-- end media and url -->

	{* Only show the thumbnail uploader to Yehoodistrators for now *}
	{if $identity->mod}
	<!-- thumbnail image -->	
	<div class="fieldset" id="div_fieldsImage">          
          <div class="hiddenController">
               <a href="javascript:void(0);" id="div_imageHider" class="a_expanded">Attach an image</a>
          </div>
          <div class="hiddenController" id="div_imageContainer" style={if $fp->image->getId() > 0 || $preview }"display:block"{else}"display:none"{/if} >
               <input type="hidden" name="id" value="{$imgPreviewObj->tempFilename}" />
               {if $fp->image->getId()}
                    <input type="submit" id="button_imageDelete" value="Remove Image" name="deleteImage">
		   Caption: <input class="input_text" type="text" id="input_caption" name="caption" value="{$fp->caption|escape}" />
                    <input type="hidden" name="image" value="{$fp->image->getId()}" />
               { elseif $preview }
		   Caption: <input class="input_text" type="text" id="input_caption" name="caption" value="{$fp->caption|escape}" />
                   	<input type="submit" id="button_imageDelete" value="Remove Image" name="deleteImage">
               { else }
                    <input type="file" name="image" id="input_image" />
                    <input type="submit" id="button_imageUpload" value="Upload" name="prevImage" class="button_submit" />
               {/if}
               {include file='lib/error.tpl' error=$imageError}
          </div>   
     </div>
	<!-- end thumbnail -->
	{/if}
	
	<!-- event-specific -->
     <div class="fieldset" id="div_fieldsEvent">
          <!--<div class="row" id="div_listRepetitionRow">
               <label for="repetition">Frequency:</label>
               <select name="repetition" id="select_repetition">
                    <option id="option_repetitionNone" value="none" {if $fp->repetition == 'none'}selected{/if}>One-Time</option>
                    <option id="option_repetitionWeekly" value="weekly" {if $fp->repetition == 'weekly'}selected{/if}>Weekly</option>
                    <option id="option_repetitionMonthly" value="monthly" {if $fp->repetition == 'monthly'}selected{/if}>Monthly</option>
               </select>
          </div>-->
		  <!--Make sure the javascript class gets re-enabled to use reprtition!!-->
          <input type="hidden" id="select_repetition" value="none" />		          
          <div class="row" id="div_listStartDateRow">
               <label for="startDate">Start Date:</label>
               {html_select_date prefix='startDate'
               day_extra='id=select_startDay'
               month_extra='id=select_startMonth'
               year_extra='id=select_startYear'
               day_value_format=%02d
               time=$fp->start_date
               end_year = +1}
			   {include file='lib/error.tpl' error=$fp->getError('startDateMonth')}
          </div>
          
          <div class="row" id="div_listEndDateRow">
               <label for="endDate">Until:</label>
               {html_select_date	prefix='endDate'
               day_extra='id=select_endDay'
               month_extra='id=select_endMonth'
               year_extra='id=select_endYear'
               day_value_format=%02d
               time=$fp->end_date
               end_year = +1}
	           {include file='lib/error.tpl' error=$fp->getError('endDateMonth')}
          </div>

          <div class="row" id="div_listDurationRow">
               <label for="">How Many Days:</label>
               <select name="duration" id="select_durationOne">
                    <option value="1" {if $fp->duration == '1'}selected{/if}>One Day</option>
                    <option value="2" {if $fp->duration == '2'}selected{/if}>Two Days</option>
                    <option value="3" {if $fp->duration == '3'}selected{/if}>Three Days</option>
                    <option value="4" {if $fp->duration == '4'}selected{/if}>Four Days</option>
                    <option value="5" {if $fp->duration == '5'}selected{/if}>Five Days</option>
                    <option value="6" {if $fp->duration == '6'}selected{/if}>Six Days</option>
                    <option value="7" {if $fp->duration == '7'}selected{/if}>One Week</option>
               </select>
		</div>
     
		<input type="hidden" id="hidden_fequencyValue" name="freqValue" value="{$freqValue}" />     
	          
          <div class="row" id="div_containerDateText">
               <!-- this holds the dynamically built 'every saturday until' etc -->
               <label for="">My event:</label>
               <span id="span_dateText">Pick your dates.</span>
          </div>
	</div>
	<!-- end event-specific -->

	<!--location specific-->
     <div class="fieldset" id="div_fieldsLocation">
          <div class="hiddenController">
               <a href="javascript:void(0);" id="div_mapHider" class="a_expand">Map a location</a>
          </div>
          <div class="hiddenController row" id="div_inputLocationRow">
               <label for="" id="label_location">Enter a location:</label>
               {* Three cases for this one *}
               {if $fp->resource->getId()}
                    <input type="hidden" name="rsrc_id" id="hidden_resourceId" value="{$fp->resource->getId()}" />
                    <input type="text" class="input_text" name="location" id="input_location" value="" />
               {else}
                    <input type="hidden" name="rsrc_id" id="hidden_resourceId" value="" />
                    <input type="text" class="input_text" name="location" id="input_location" value="" />
               {/if}		
               <input type="button" class="button_submit" id="button_addLocation" name="" value="Mark this Location" />
				
	          <span id="div_mapContainer" class="hiddenController">
	               <div id="div_containerGoogleMap"> </div>
	          </span>
              
	          <ul id="ul_location"></ul>
              {include file='lib/error.tpl' error=$fp->getError('location')}
          </div>
	</div>
	<!--end location specific-->
	
	{if $identity->mod}
	<!--start resource extended information-->
	<div class="fieldset" id="div_fieldsExtended">		
          <div class="hiddenController">
               <a href="javascript:void(0);" id="div_extendedHider" class="a_expanded">Podcasts</a>
          </div>
		<div class="hiddenController" id="div_extendedInfo" style="display: none;">
          {include file='submit/submit-extended.tpl'}
		</div>

		<div class="hiddenController">
               <a href="javascript:void(0);" id="div_extendedFramHider" class="a_expanded">Frim Fram</a>
          </div>
		<div class="hiddenController" id="div_extendedFramInfo" style="display: none;">
          {include file='submit/submit-fram.tpl'}
		</div>
	<!--end resource extended information-->
	</div>
	{/if}
	
	<div class="fieldset" id="div_fieldsNotify">
		<div class="row" id="div_notifyRow">
               <label>&nbsp;</label>
               <input type="checkbox" id="form_notify_by_email" name="notify_by_email" value="true" {if $identity->notify_by_email == 'true'}checked="checked"{/if} /> 
			   Email me when comments are made for this topic.
          </div>
	</div>

	{* If the user is a moderator then he can edit the resource date *}
	{if $identity->mod}
	<!-- start resource post date -->
	<div class="fieldset" id="div_fieldsNotify">
          <div class="row" id="div_rsrcDate">
               <label for="rsrcDate">Post Date:</label>
               {html_select_date prefix='rsrcDate'
               day_extra='id=select_rsrcDay'
               month_extra='id=select_rsrcMonth'
               year_extra='id=select_rsrcYear'
               day_value_format=%02d
               time=$fp->rsrc_date
               start_year = -9
               end_year = +1}
			&nbsp;&nbsp;Time:
               {html_select_time prefix='rsrcTime'
               use_24_hours=true
               time=$fp->rsrc_date
               display_seconds=false}
               {include file='lib/error.tpl' error=$fp->getError('rsrcDate')}
          </div>
	</div>
	<!-- end resource post date -->

	<!--start is active-->
	<div class="fieldset" id="div_fieldsIsActive">
		<div class="row" id="div_listIsActive">
               <label>Active:</label>
               <select name="is_active" id="select_isActive">
               	<option value="1" {if $fp->resource->isActive() == 1}selected{/if}>YES</option>
               	<option value="0" {if $fp->resource->isActive() == 0}selected{/if}>NO</option>
               </select>
          </div>
	</div>
	<!--end is active-->
	{/if}

    <!-- save buttons -->
    <div class="fieldset" id="div_fieldsButtons">
        <div id="errorNotice" class="error iconText iconX" style="display: none;">
           There is an error on the page. Scroll up to see what's wrong.
        </div>
        {if $fp->resource->isLive()}
            {assign var="label" value="Save Changes"}
        {elseif $fp->resource->isSaved()}
            {assign var="label" value="Save Changes and Post"}
        {else}
            {assign var="label" value="Post"}
        {/if}
        <input type="submit" name="Submit" value="{$label|escape}" class="button_submit" id="button_submit" />
        {if !$fp->resource->isLive()}
        <input type="submit" name="Draft" value="Save Draft" class="button_draft" id="button_draft" />
        {/if}
        <input type="submit" name="Cancel" value="Cancel" class="button_cancel" id="button_cancel" />
        
        
    </div>
    <!-- end save buttons -->
	
	<!-- This stores the value of the new resource type if the user chooses to change it -->
	<input type="hidden" name="rsrc_type_change" id="hidden_rsrcTypeChange" value="{$resourceTypeChange}" />
	<input type="hidden" name="hidden_token" id="hidden_token" value="{$hidden_token}" />

</form>
