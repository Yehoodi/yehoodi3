     <div class="row" id="div_extraFramInfo">	
          <label>Extra Info:</label>
          <textarea class="input_text" name="extraFramInfo" id="extraFramInfo">{$fp->extraFramInfo}</textarea>		
		  <br /><span>Basic HTML tags are allowed in here. Don't go crazy.</span>
		  {include file='lib/error.tpl' error=$fp->getError('extraFramInfo')}
     </div>