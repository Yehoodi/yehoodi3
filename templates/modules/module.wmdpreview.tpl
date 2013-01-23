    {if $controller == 'submit'}    
        {if $preview}
        <div class="div_resourceImage">
        	<a href="#"><img src="{previewimagefilename tempFilename=$preview w=200}" /></a>
            <p id="p_resourceImageCaption">&nbsp;</p>
        </div>
        { elseif $fp->image->getId()}
        <div class="div_resourceImage">
        	<a href="#"><img src="{imagefilename id=$fp->image->getId() w=200}" /></a>
            <p id="p_resourceImageCaption">{$fp->image->caption|escape}</p>
        </div>
        { /if}
    {/if}
	<!--WMD Preview Starts Here-->
    <div id="wmd-preview">
    {if $content}
        {$content}
	{/if}
    </div>
	<!--WMD Preview Ends Here-->