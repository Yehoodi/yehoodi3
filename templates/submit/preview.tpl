<h2 id="previewTitle">Title</h2>

<div id="div_topicAuthor" class="grid_2 alpha">
    <div class="div_topicAuthor">
     {if $fp->user->userMeta->avatar->getId()}
     	<img src="{avatarfilename id=$fp->user->userMeta->avatar->getId()}" alt="" class="avatar" /><br />
     { /if }
     <strong><a href="{geturl controller = 'profile'}{$fp->user->user_name}">{$fp->user->user_name|escape}</a></strong>
     </div>
</div>
<div id="details{$rsrc->meta->_id}" class="grid_9">
    <div class="div_detailsContent">
        <h2 id="h_eventDate">Start Date&ndash;End Date</h2>     
        <ul class="meta">
            <li><span id="liType"><a class="tags">topic</a></span> &gt; <span id="liCategory"><a class="tags">category</a></span></li>
            <li id="liPostedby" class="final">Posted <strong>{$date}</strong></li>
        </ul>
        {include file='modules/module.wmdpreview.tpl' content=$fp->descrip}
     </div>
</div>