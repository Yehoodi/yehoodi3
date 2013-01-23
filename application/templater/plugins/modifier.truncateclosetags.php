<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
 
 
/**
 * Smarty truncateclosetags modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncateclosetags<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           never in the middle of a word, and
 *           appending the $etc string.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com> Modified by GameSpot
 * @param string
 * @param integer
 * @param string
 * @return string
 */

function smarty_modifier_truncateclosetags($string, $length = 80, $etc = '...', $process_more = false)
{
    if(!$process_more && !$length) {
        return '';
    }
   
    $withTagsCount = strlen($string);
    $withoutTagsCount = strlen(strip_tags($string));
    $length += $withTagsCount - $withoutTagsCount;
   
    if($process_more) {
        $more_tag_matches = array();
        preg_match('%(<more\s*/>)%i', $string, $more_tag_matches);
        if($more_tag_matches[1]) {
            $length = strpos($string, $more_tag_matches[1]) + 1 + strlen($more_tag_matches[1]);
            $ss = substr($string, 0, $length);
            $string = str_replace($more_tag_matches[1], '', $string);
            //$ssWithTagsCount = strlen($ss);
            //$ssWithoutTagsCount = strlen(strip_tags($ss));
            //$length += $ssWithTagsCount - $ssWithoutTagsCount;
        }
    }
   
    if ($length == 0) {
        return '';
    }
   
    // get the count of the truncated string
    if ($withTagsCount > $length) {
        $length -= strlen($etc);
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
            $htmlString =  substr($string, 0, $length).$etc;
    } else {
        $htmlString =  $string;
    }
 
    // New code...
    $arr_single_tags = array('meta','img','br','link','area');
   
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $htmlString, $result);
    $openedtags = $result[1];
   
    preg_match_all('#</([a-z]+)>#iU', $htmlString, $result);
   
    $closedtags = $result[1];
   
    $len_opened = count($openedtags);
   
    if (count($closedtags) == $len_opened)
    {
        $resultString = $htmlString;
    }
   
    $openedtags = array_reverse($openedtags);
   
    for ($i=0; $i < $len_opened; $i++)
    {
        if (!in_array($openedtags[$i],$arr_single_tags))
        {
            if (!in_array($openedtags[$i], $closedtags))
            {
                if (isset($openedtags[$i+1]))
                {
                    $next_tag = $openedtags[$i+1];
                    $tmp_html = $htmlString;
                    $htmlString = preg_replace('#</'.$next_tag.'#iU','</'.$openedtags[$i].'></'.$next_tag,$htmlString);
                    if($htmlString == $tmp_html)
                    {
                        //if it did not replace, do it now
                        $htmlString .= '</'.$openedtags[$i].'>';
                    }
                } else {
                    $next_tag = null;
                    $htmlString .= '</'.$openedtags[$i].'>';
                }
            }
        }
    }
    $resultString = $htmlString;
   
    return $resultString;
}
