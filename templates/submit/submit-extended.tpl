     <div class="row" id="div_showName">	
          <label>Show Name:</label>
		  <input class="input_text" type="text" id="input_showName" name="show_name" value="{$fp->show_name|escape}" />
		  <br /><span>&quot;#051 Hey Mister Jesse for March 2010&quot;, &quot;YTS_6x03 - March 15 2010&quot;</span>
		  {include file='lib/error.tpl' error=$fp->getError('show_name')}
     </div>

     <div class="row" id="div_showCode">
          <label>Show Code:</label>
		  <input class="input_text" type="text" id="input_showCode" name="show_code" value="{$fp->show_code|escape}" />
		  <br /><span>&quot;HMJ / YTS&quot;</span>
		  {include file='lib/error.tpl' error=$fp->getError('show_code')}
     </div>

     <div class="row" id="div_showEpisode">
          <label>Show Episode #:</label>
		  <input class="input_text" type="text" id="input_showEpisode" name="show_episode" value="{$fp->show_episode|escape}" />
		  <br /><span>&quot;53&quot;</span>
		  {include file='lib/error.tpl' error=$fp->getError('show_episode')}
     </div>

     <div class="row" id="div_internalPageURL">	
          <label>Internal Link URL:</label>
		  <input class="input_text" type="text" id="input_internalPageURL" name="internal_page_url" value="{$fp->internal_page_url|escape}" />
		  <br /><span>&quot;/show/heymisterjesse?episode=51&quot;</span>
		  {include file='lib/error.tpl' error=$fp->getError('internal_page_url')}
     </div>

     <div class="row" id="div_internalPageLinkText">	
          <label>Internal Link Text:</label>
		  <input class="input_text" type="text" id="input_internalPageLinkText" name="internal_page_link_text" value="{$fp->internal_page_link_text|escape}" />
		  <br /><span>&quot;Go to the show page&quot;</span>
		  {include file='lib/error.tpl' error=$fp->getError('internal_page_link_text')}
     </div>

     <div class="row" id="div_media">	
          <label>Media filename:</label>
		  <input class="input_text" type="text" id="input_mediaURL" name="media_url" value="{$fp->media_url|escape}" />
		  <br /><span>&quot;MrJesse_03012010.mp3&quot;</span>
		  {include file='lib/error.tpl' error=$fp->getError('media_url')}
     </div>

     <div class="row" id="div_flash">	
          <label>Flash URL / Embed Id:</label>
		  <input class="input_text" type="text" id="input_flashURL" name="flash_url" value="{$fp->flash_url|escape}" />
		  <br /><span>&quot;/flash/flashshows/YTS_6x01&quot; or &quot;"KvZix38meQs"&quot;</span>
		  {include file='lib/error.tpl' error=$fp->getError('flash_url')}
     </div>

     <div class="row" id="div_shownotes">	
          <label>Shownotes:</label>
          <textarea class="input_text" name="shownotes" id="text_shownotes">{$fp->shownotes}</textarea>		
		  {include file='lib/error.tpl' error=$fp->getError('shownotes')}
     </div>     			

     <div class="row" id="div_artist">	
          <label>Artist:</label>
		  <input class="input_text" type="text" id="input_artist" name="artist" value="{$fp->artist|escape}" />
		  {include file='lib/error.tpl' error=$fp->getError('artist')}
     </div>

     <div class="row" id="div_album">
          <label>Album:</label>
		  <input class="input_text" type="text" id="input_album" name="album" value="{$fp->album|escape}" />
		  <br /><span>&quot;Yehoodi Podcasts&quot;</span>
		  {include file='lib/error.tpl' error=$fp->getError('album')}
     </div>