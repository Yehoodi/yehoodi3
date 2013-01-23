<?php
    
/**
 * Used maily on the search page, this Smarty function
 * will return a portion of the text surrounding the
 * search terms with higlighting provided for the search
 * terms.
 * 
 * Used in conjunction with the smarty modifiers "truncate:###"
 * this will ellipse the start and ending of the outputted text.
 * 
 * A typical usage on the template would be:
 * 
 * {highlighttext|truncate:255:"&hellip;" text=$com->comment words=$words}
 *
 * @param array $params
 * @param object $smarty
 * @return string
 */
function smarty_function_highlighttext($params, $smarty)
{
	// get the params
	$text		= isset($params['text']) ? $params['text'] : null;
    $words		= isset($params['words']) ? $params['words'] : null;
    $colors		= isset($params['colors']) ? $params['colors'] : null;

    // vars
    $preString = null;
    
    // clean up the returned text from the resource stripping html, etc...
    $text = UtilityController::cleanupTitle($text);
    
    // set up the highlight colors
    if(is_null($colors) || !is_array($colors))
    {
            $colors = array('yellow', 'pink', 'green');
    }

    $i = 0;
    /*** the maximum key number ***/
    $num_colors = max(array_keys($colors));

    if (!is_array($words)) {
    	return $text;			// No words so we are done here...
    }

    /*** loop of the array of words ***/
    foreach ($words as $word)
    {
            /*** quote the text for regex ***/
            $word = preg_quote($word);
            /*** highlight the words ***/
            if($word) {
                $text = preg_replace("/\b($word)\b/i", '<span class="highlight_'.$colors[$i].'">\1</span>', $text);
                if($i==$num_colors){ $i = 0; } else { $i++; }
            }
    }
    
    // locate the first '<span class' and use as the post text string
    if($postString = stristr($text,'<span')) {
    
	    // Now build the pre text string. We want the words up to the first found search word
	    $preWordCount = 6; // how many words before the first found search word to show
	    $pos = stripos($text, '<span'); //position og the first <span
	    
	    $preText = substr($text, 0, $pos);
	    
	    // read the text before the first search word into an array
	    $preTextWords = explode(' ', trim($preText));
	
		// Get the number of words leading up to the search word
	    if (count($preTextWords) > 6) {
			$preString .= "...";
			$preTextWords = array_slice($preTextWords, -6);
		}	
	
		// rebuild the pre string using only the nth words up to the search word
		for($i = 0; $i <= count($preTextWords); $i++) {
			$preString .= " ".$preTextWords[$i];
		}		
	
		// put the pre and post together
		$text = $preString." ".$postString;
    }
	
    /*** return the text ***/
    return $text;
}
?>