/**
/* Ravelry-Style quote boxes code.
/* Inlined JavaScript because it's just easier.
/*
**/
function showCommentReply(link, id, depth) {
	var p = link;
    var indicator;
    var pageNumber = $('hidden_pageNumber').value;

/*    while (p && p.getElementsByClassName('indicator').length == 0) {
    	p = p.parentNode;
    }

    if (p) {
        var indicators = p.getElementsByClassName('indicator');
        if (indicators.length > 0) {
            indicator = indicators[0];
        }
    }
*/
    var container = link.parentNode.parentNode.parentNode;
    var threadContainer = $(container).getElementsByClassName('thread')[0];
    var bit = $(threadContainer).getElementsByClassName('parent_comment');
    if (bit.length > 0 && Element.visible(bit[0])) {
        new Effect.BlindUp(bit[0]);
        link.innerHTML = "Show";
    } else {
        var url = "/commentajax/showcommentreply/";

/*        if (indicator) {
            Element.show(indicator);
        }
*/        new Ajax.Updater(threadContainer, url, {
			parameters: {reply_to_id: id, depth: depth, page: pageNumber},
            asynchronous: true,
            evalScripts: true,
			  onCreate: function(transport) {
				// Turn on status animation
			  	link.innerHTML = "Loading...";
			  },
            onComplete: function() {
                //Element.hide(indicator);
			  	link.innerHTML = "Hide";
                var bit = $(threadContainer).getElementsByClassName('parent_comment')[0];
                new Effect.Appear(bit);
            }
        });
    }
}