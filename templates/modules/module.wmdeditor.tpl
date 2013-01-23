<!-- WMD Editor Starts Here -->
<div class="fieldset" id="div_fieldsReply">		
    <div class="row" id="div_replyRow">	
        <label>Enter your message:</label>          
        <div id="wmd-button-bar"></div>
        <textarea class="input_text" name="{$textarea_name}" id="wmd_input" tabindex="3" cols="10" rows="10" {if $controller == 'comment' && !$commentId} style="display: none;"{/if}>{$content}</textarea>		
        {include file='lib/error.tpl' error=$fp->getError($textarea_name)}
        <div id="wmdHelp" class="text">
            <p><strong>BBCode is no longer supported.</strong> Use <a href="/help/faq#markDown" target="_blank">Markdown</a> instead:</p>
            <ul>
                <li>Double returns between paragraphs</li>
                <li>Add 2 spaces for breaks at end</li>
                <li>*<em>italic</em>* or **<strong>bold</strong>**</li>
                <li>Quote by placing &gt; at start of line</li>
                <li>To make links: <pre>&lt;http://foo.com&gt;</pre></li>
                <li>Also use <a href="/help/faq#markDown" target="_blank">inline HTML</a></li>
            </ul>
        </div>
    </div>
</div>
<!-- WMD Editor Ends Here -->