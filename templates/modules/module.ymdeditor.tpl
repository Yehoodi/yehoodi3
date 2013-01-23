<!-- YMD Editor Starts Here -->
<div class="fieldset" id="div_fieldsReply">		
     <div class="row" id="div_replyRow">	
          <label>Enter your message:</label>
          <div id="controls">
               <button type="button" id="but_bold" name="bold"><img src="/images/buttons/edit-bold.png" alt="Bold" /></button>
               <button type="button" id="but_italic" name="italic"><img src="/images/buttons/edit-italic.png" alt="Italic" /></button>
               <button type="button" id="but_underline" name="underline"><img src="/images/buttons/edit-underline.png" alt="Underline" /></button>
               <button type="button" id="but_link" name="url"><img src="/images/buttons/chain.png" alt="Link" /></button>
               <button type="button" id="but_img" name="img"><img src="/images/buttons/image.png" alt="Image" /></button>
               <button type="button" id="but_quote" name="quote"><img src="/images/buttons/edit-quote.png" alt="Quote" /></button>
               <button type="button" id="but_code" name="code"><img src="/images/buttons/edit-code.png" alt="Code" /></button>
               <!--<button type="button" id="but_list" name="list">list</button>-->
          </div>
          <textarea class="" name="{$textarea_name}" id="text_area" tabindex="3">{$content}</textarea>		
		  {include file='lib/error.tpl' error=$fp->getError($textarea_name)}		
     </div>     			
</div>
<!-- YMD Editor Ends Here -->