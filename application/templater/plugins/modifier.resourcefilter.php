<?php
/**
 * Cleans up the html with the allowed tags in the array
 * at the top of this class
 *
 * @param unknown_type $html
 * @return unknown
 */
function smarty_modifier_resourcefilter($string)
{
	// This is our whitelist of allowed html tags for resources
	$tags = array(
	    //'a'      => array('href', 'target', 'name'),
	    //'img'    => array('src', 'alt', 'width', 'height'),
	    //'b'      => array(),
	    //'strong' => array(),
	    //'em'     => array(),
	    //'i'      => array(),
	    //'ul'     => array(),
	    //'li'     => array(),
	    //'ol'     => array(),
	    //'p'      => array(),
	    //'br'     => array()
	 );

	$chain = new Zend_Filter();
    $chain->addFilter(new Zend_Filter_StripTags($tags));
    $chain->addFilter(new Zend_Filter_StringTrim());

    $string = $chain->filter($string);

    $tmp = $string;
    while (1) {
        // Try and replace an occurrence of javascript:
        $string = preg_replace('/(<[^>]*)javascript:([^>]*>)/i',
                             '$1$2',
                             $string);

        // If nothing changed this iteration then break the loop
        if ($string == $tmp)
            break;

        $tmp = $string;
    }

    return $string;

}