<?php
/**
 * This is a class that is used to parse a string and format it
 * for BBCode. This class implements the use of a unique
 * identifier, for the purpose of saving resources, post-database.
 *
 * @author          Matt Carroll <admin@develogix.com>
 * @copyright       Copyright 2004, 2005, 2006, 2007, 2008, Matt Carroll
 *                  http://gnu.org/copyleft/gpl.html GNU GPL
 * @version         $Id: bbcode.v3.0.class.php,v 3.0 2008/10/16 00:00:00 logi Exp $
 *
 * This version updates the class to PHP5, as well as implementing a new method of parsing.
 *
 * private @param str       string to be parsed
 * public  @param uid       unique identifier with a length of 10 characters
 * private @param action    'pre' or 'post' database
 * private @param action    'reset' is for clearing the bbcode_uid and still displaying the bbcode (for user editing)
 *
 * private @param added     array holding added simple tags and data 
 *
 * private @param geshi     true to allow bbGeshi, false to disallow
 * private @param ls        true to allow bbList, false to disallow
 * private @param simple    true to allow bbSimple, false to disallow
 * private @param quote     true to allow bbQuote, false to disallow
 * private @param mail      true to allow bbMail, false to disallow
 * private @param url       true to allow bbUrl, false to disallow
 * private @param img       true to allow bbImg, false to disallow
 * private @param color     true to allow bbColor, false to disallow
 * private @param size      true to allow bbSize, false to disallow
 *   
 * private @param imgLimit  amount of images to be parsed (-1 for unlimited)
 */
class BBcode {
    private $str      = "";
    public  $uid      = NULL;
    private $action   = NULL;

    private $added    = array();

    private $geshi    = TRUE;
    private $ls       = TRUE;
    private $simple   = TRUE;
    private $quote    = TRUE;
    private $mail     = TRUE;
    private $url      = TRUE;
    private $img      = TRUE;
    private $color    = TRUE;
    private $size     = TRUE;

    private $imgLimit = -1;

    public function __construct(){

    }

    /**
     * @param  str    string to be parsed
     * @param  action 'pre' or 'post' database, null for standard parse
     * @param  uid    unique identifier with a length of 8 characters, null for standard parse
     *
     * @return parsed string
     */
    public function parse($str, $action = NULL, $uid = NULL){
        $this->str    = $str;
        $this->action = ($action === 'pre' OR $action === 'post' OR $action === 'reset'  OR $action === NULL) ? $action : NULL;
        $this->uid    = (
            ($uid === NULL AND $this->action === 'pre' OR $this->action === NULL) ? $this->makeUID() : ((($this->action === 'post' OR $this->action === 'reset') AND (strlen($uid) === 10)) ? $uid : NULL));

        if($this->action === 'pre'){
            $this->bbGeshi();
            $this->bbList();
            $this->bbSimple();
            $this->bbQuote();
            $this->bbMail();
            $this->bbUrl();
            $this->bbImg();
            $this->bbColor();
            $this->bbSize();

            return $this->str;
        }
        else if($this->action === 'post' OR $this->action === NULL){
            $this->bbGeshi();
            $this->bbList();
            $this->bbSimple();
            $this->bbQuote();
            $this->bbMail();
            $this->bbUrl();
            $this->bbImg();
            $this->bbColor();
            $this->bbSize();

            $this->str = $this->str."\n";
            $match = array('#\r\n\r\n#msi', 
            			   '#(?<!(</div>))\r\n#msi', 
            			   '#(<(/)?p>)?<(/)?(div( class="(.*?)")?|ul|ol|li|h[1-6])>(<(/)?p>)?#msi', 
            			   '#\n</p>#m');
            $replace = array("\r\n", 
            				 '</p>'."\r\n".'<p>', 
            				 '<$3$4>', 
            				 '');
            $this->str = preg_replace($match, $replace, $this->str);

            return substr($this->str, 0, strlen($this->str) - 1);
        }
        else if ($this->action === 'reset'){
            $this->bbGeshi();
            $this->bbList();
            $this->bbSimple();
            $this->bbQuote();
            $this->bbMail();
            $this->bbUrl();
            $this->bbImg();
            $this->bbColor();
            $this->bbSize();
        	
        	return $this->str;
        }
    }

    /**
     * adds a "simple" bbcode tag
     * please ensure that new lines are "\r\n"
     * @param tag    opening and closing bbcode tag
     * @param before text that goes in place of [$tag]
     * @param after  text that goes in place of [/$tag]
     */
    public function addSimple($tag, $before, $after, $tabs){
        $this->added[] = array($tag, $before, $after, $tabs);
    }

    /**
     * @param var function name to disallow
     */
    public function disallow($var){
        $this->{$var} = FALSE;
    }

    /**
     * @param var function name to allow
     */
    public function allow($var){
        $this->{$var} = TRUE;
    }

    /**
     * sets image limit
     * @param limit amount of images to be parsed (-1 for unlimited)
     */
    public function imgLimit($limit){
        $this->imgLimit = $limit;
    }

    /**
     * @return uid generated unique identifier with a length of 10 characters
     */
    public function makeUID(){
        return substr(md5(mt_rand()), 0, 10);
    }

    /**
     * TODO
     * - implement
     */
    private function bbGeshi(){

    }

    /**
     * parses string for [list], [*]
     */
    private function bbList(){
        if($this->ls === TRUE){
            if($this->action === 'pre' OR $this->action === NULL){
                $match = array(
                    '#\[list\](.*?)\[/list\]#si',
                '#\[\*\](.*?)\[/\*\]#si'
                );
                $replace = array(
                    '[list:'.$this->uid.']$1[/list:'.$this->uid.']',
                    '[*:'.$this->uid.']$1[/*:'.$this->uid.']'
                    );
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'post' OR $this->action === NULL){
                $match = array(
                    '[list:'.$this->uid.']',
                    '[/list:'.$this->uid.']',
                    '[*:'.$this->uid.']',
                    '[/*:'.$this->uid.']'
                    );
                $replace = array(
                    '<ul>',
                    '</ul>',
                    '<li>',
                    '</li>'
                    );
                $this->str = str_replace($match, $replace, $this->str);
            }

            if($this->action === 'reset'){
                $match = array(
                    '[list:'.$this->uid.']',
                    '[/list:'.$this->uid.']',
                    '[*:'.$this->uid.']',
                    '[/*:'.$this->uid.']'
                    );
                $replace = array(
                    '[list]',
                    '[/list]',
                    '[li]',
                    '[/li]'
                    );
                $this->str = str_replace($match, $replace, $this->str);
            }
        }
    }

    /**
     * parses string for [b], [i], [u], [s], [em], [sup], [sub], and [code]
     */
    private function bbSimple(){
        if($this->simple === TRUE){
            if($this->action === 'pre' OR $this->action === NULL){
                $match = array('#\[b\](.*?)\[/b\]#si', '#\[i\](.*?)\[/i\]#si', '#\[u\](.*?)\[/u\]#si', '#\[s\](.*?)\[/s\]#si', '#\[em\](.*?)\[/em\]#si', '#\[sup\](.*?)\[/sup\]#si', '#\[sub\](.*?)\[/sub\]#si', '#\[code\](.*?)\[/code\]#si');
                $replace = array('[b:'.$this->uid.']$1[/b:'.$this->uid.']', '[i:'.$this->uid.']$1[/i:'.$this->uid.']', '[u:'.$this->uid.']$1[/u:'.$this->uid.']', '[s:'.$this->uid.']$1[/s:'.$this->uid.']', '[em:'.$this->uid.']$1[/em:'.$this->uid.']', '[sup:'.$this->uid.']$1[/sup:'.$this->uid.']', '[sub:'.$this->uid.']$1[/sub:'.$this->uid.']', '[code:'.$this->uid.']$1[/code:'.$this->uid.']');
                foreach($this->added AS $arr){
                    $match[]   = '#\['.$arr[0].'\](.*?)\[/'.$arr[0].'\]#si';
                    $replace[] = '['.$arr[0].':'.$this->uid.']$1[/'.$arr[0].':'.$this->uid.']';
                }
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'post' OR $this->action === NULL){
                $match = array('[b:'.$this->uid.']', '[/b:'.$this->uid.']', '[i:'.$this->uid.']', '[/i:'.$this->uid.']', '[u:'.$this->uid.']', '[/u:'.$this->uid.']', '[s:'.$this->uid.']', '[/s:'.$this->uid.']', '[em:'.$this->uid.']', '[/em:'.$this->uid.']', '[sup:'.$this->uid.']', '[/sup:'.$this->uid.']', '[sub:'.$this->uid.']', '[/sub:'.$this->uid.']', '[code:'.$this->uid.']', '[/code:'.$this->uid.']');
                $replace = array('<strong>', '</strong>', '<em>', '</em>', '<span style="text-decoration: underline;">', '</span>', '<del>', '</del>', '<em>', '</em>', '<sup>', '</sup>', '<sub>', '</sub>', '<code>', '</code>');
                foreach($this->added AS $arr){
                    $match[]   = '['.$arr[0].':'.$this->uid.']';
                    $replace[] = $arr[1];
                    $match[]   = '[/'.$arr[0].':'.$this->uid.']';
                    $replace[] = $arr[2];
                }
                $this->str = str_replace($match, $replace, $this->str);
            }

            if($this->action === 'reset'){
                $match = array('[b:'.$this->uid.']', '[/b:'.$this->uid.']', '[i:'.$this->uid.']', '[/i:'.$this->uid.']', '[u:'.$this->uid.']', '[/u:'.$this->uid.']', '[s:'.$this->uid.']', '[/s:'.$this->uid.']', '[em:'.$this->uid.']', '[/em:'.$this->uid.']', '[sup:'.$this->uid.']', '[/sup:'.$this->uid.']', '[sub:'.$this->uid.']', '[/sub:'.$this->uid.']', '[code:'.$this->uid.']', '[/code:'.$this->uid.']');
                $replace = array('[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]', '[i]', '[/i]', '[sup]', '[/sup]', '[sub]', '[/sub]', '[code]', '[/code]');
                foreach($this->added AS $arr){
                    $match[]   = '['.$arr[0].':'.$this->uid.']';
                    $replace[] = $arr[1];
                    $match[]   = '[/'.$arr[0].':'.$this->uid.']';
                    $replace[] = $arr[2];
                }
                $this->str = str_replace($match, $replace, $this->str);
            }
        }
    }

    /**
     * parses string for [quote=*] and [quote]
     */
    private function bbQuote(){
        if($this->quote === TRUE){
            if($this->action === 'pre' OR $this->action === NULL){
                $match = array('#\[quote=(.*?)\](.*?)\[/quote\]#si',
                			   '#\[quote\](.*?)\[/quote\]#si');
                
                $replace = array('[quote=$1:'.$this->uid.']$2[/quote:'.$this->uid.']',
                				 '[quote:'.$this->uid.']$1[/quote:'.$this->uid.']');
                
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'post' OR $this->action === NULL){
                $match = array('#\[quote:'.$this->uid.'=(.*?)\](.*?)\[/quote:'.$this->uid.'\]#si',
                			   '#\[quote:'.$this->uid.'\](.*?)\[/quote:'.$this->uid.'\]#si',
                			   '#\[quote=(.*?):'.$this->uid.'\](.*?)\[/quote:'.$this->uid.'\]#si',
                			   '#\[quote=(.*?)\](.*?)\[/quote\]#si');
                
                $replace = array('<blockquote><div><strong>Quoted from <em>$1</em></strong><br />$2</div></blockquote>', 
                				 '<blockquote><div><strong>Quote</strong><br />$1</div></blockquote>',
                				 '<blockquote><div><strong>Quoted from <em>$1</em></strong><br />$2</div></blockquote>',
                				 '<blockquote><div><strong>Quoted from <em>$1</em></strong><br />$2</div></blockquote>');
                
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'reset'){
                $match = array('#\[quote:'.$this->uid.'=(.*?)\](.*?)\[/quote:'.$this->uid.'\]#si',
                			   '#\[quote:'.$this->uid.'\](.*?)\[/quote:'.$this->uid.'\]#si',
                			   '#\[quote=(.*?):'.$this->uid.'\](.*?)\[/quote:'.$this->uid.'\]#si',
                			   '#\[quote=(.*?)\](.*?)\[/quote\]#si');
                			   
                $replace = array('[quote=$1]$2[/quote]',
                				 '[quote]$1[/quote]',
                				 '[quote=$1]$2[/quote]',
                				 '[quote=$1]$2[/quote]');
                				 
                $this->str = preg_replace($match, $replace, $this->str);
                // Added to remove the old style of [quote="name"]
                $this->str = str_replace('&quot;', '', $this->str);
                $this->str = str_replace(':'.$this->uid,'', $this->str);
            }
        }
    }


    /**
     * parses string for [mail=*] and [mail]
     */
    private function bbMail(){
        if($this->mail === TRUE){
            if($this->action === 'pre' OR $this->action === NULL){
                $match = array('#\[mail=([a-z0-9\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\](.*?)\[/mail\]#si', '#\[mail\]([a-z0-9\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/mail\]#si');
                $replace = array('[mail=$1:'.$this->uid.']$2[/mail:'.$this->uid.']', '[mail=$1:'.$this->uid.']$1[/mail:'.$this->uid.']');
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'post' OR $this->action === NULL){
                $match = '#\[mail=([a-z0-9\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+):'.$this->uid.'\](.*?)\[/mail\]#si';
                $replace = '<a href="mailto:$1">$2</a>';
                $this->str = preg_replace($match, $replace, $this->str);
            }
        }
    }

    /**
     * parses string for [color=*] and [color]
     */
    private function bbColor(){
        if($this->color === TRUE){
            if($this->action === 'pre' OR $this->action === NULL){
                $match = array('#\[color=(.*?)\](.*?)\[/color\]#si', '#\[color\](.*?)\[/color\]#si');
                $replace = array('[color=$1:'.$this->uid.']$2[/color:'.$this->uid.']', '[color:'.$this->uid.']$1[/color:'.$this->uid.']');
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'post' OR $this->action === NULL){
                $match = array('#\[color=(.*?):'.$this->uid.'\](.*?)\[/color:'.$this->uid.'\]#si', '#\[color=(.*?)\](.*?)\[/color\]#si');
                $replace = '<span style="color:$1; ">$2</span>';
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'reset'){
                $match = array('#\[color=(.*?):'.$this->uid.'\](.*?)\[/color:'.$this->uid.'\]#si', '#\[color=(.*?)\](.*?)\[/color\]#si');
                $replace = '[color="$1"]$2[/color]';
                $this->str = preg_replace($match, $replace, $this->str);
                // Added to remove the old style of [color="name"]
                $this->str = str_replace('&quot;', '', $this->str);
            }
        }
    }

    /**
     * parses string for [size=*] and [size]
     */
    private function bbSize(){
        if($this->size === TRUE){
            if($this->action === 'pre' OR $this->action === NULL){
                $match = array('#\[size=(.*?)\](.*?)\[/size\]#si', '#\[size\](.*?)\[/size\]#si');
                $replace = array('[size=$1:'.$this->uid.']$2[/size:'.$this->uid.']', '[size:'.$this->uid.']$1[/size:'.$this->uid.']');
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'post' OR $this->action === NULL){
                $match = array('#\[size=(.*?):'.$this->uid.'\](.*?)\[/size:'.$this->uid.'\]#si', '#\[size=(.*?)\](.*?)\[/size\]#si');
                //$replace = "<span style=\"font-size:\\1px; line-height:normal;\">\\2</span>";
                
                $callback = 'BBCode::limitSize';
                $this->str = preg_replace_callback($match, $callback, $this->str);
            }

            if($this->action === 'reset'){
                $match = array('#\[size=(.*?):'.$this->uid.'\](.*?)\[/size:'.$this->uid.'\]#si',
                               '#\[size=(.*?)\](.*?)\[/size\]#si');
                $replace = '[size=$1]$2[/size]';
                $this->str = preg_replace($match, $replace, $this->str);
                // Added to remove the old style of [size="name"]
                $this->str = str_replace('&quot;', '', $this->str);
            }
        }
    }
    
    /**
     * Callback function to limit the maximum size of text
     * in the BBSize method above
     * 
     * Min text size = 0.7
     * Max text size = 20
     *
     * @param array $matches
     * @return string html
     */
    private static function limitSize($matches) {
        return "<span style=\"font-size:" . min(max($matches[1],0.7),20) . "px; line-height:normal;\">" . $matches[2] . "</span>";
    }


    /**
     * parses string for [url=*], [url], and unformatted URLs
     */
    private function bbUrl(){
        if($this->url === TRUE){
            if($this->action === 'pre' OR $this->action === NULL){
                $match = array('#(?<!(\]|=|\/))((http|https|ftp|irc|telnet|gopher|afs)\:\/\/|www\.)(.+?)( |\n|\r|\t|\[|$)#si',
                			   '#\[url\]([a-z0-9]+?://){1}([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)\[/url\]#is',
                			   '#\[url\]((www|ftp)\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*?)?)\[/url\]#si',
                			   '#\[url=([a-z0-9]+://)([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*?)?)\](.*?)\[/url\]#si',
                			   '#\[url=(([\w\-]+\.)*?[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)\](.*?)\[/url\]#si');

//                $match = array('#\[url\]([a-z0-9]+?://){1}([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)\[/url\]#is',
//                			   '#\[url\]((www|ftp)\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*?)?)\[/url\]#si',
//                			   '#\[url=([a-z0-9]+://)([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*?)?)\](.*?)\[/url\]#si',
//                			   '#\[url=(([\w\-]+\.)*?[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)\](.*?)\[/url\]#si');
//
                $replace = array('[url:'.$this->uid.']$1$2$4[/url:'.$this->uid.']$5',
                				 '[url:'.$this->uid.']$1$2[/url:'.$this->uid.']',
                				 '[url:'.$this->uid.']http://$1[/url:'.$this->uid.']',
                				 '[url=$1$2:'.$this->uid.']$6[/url:'.$this->uid.']',
                				 '[url=http://$1:'.$this->uid.']$5[/url:'.$this->uid.']');
                
                $this->str = preg_replace($match, $replace, $this->str);
            }

            if($this->action === 'post' OR $this->action === NULL){
                $match = array('#\[url:'.$this->uid.'\](.*?)\[/url:'.$this->uid.'\]#si',
                			   '#\[url=(.*?):'.$this->uid.'\](.*?)\[/url:'.$this->uid.'\]#si',
                			   '#\[url=(.*?)\](.*?)\[/url\]#si',
                			   '#\[url=([\w]+?://[^ \"\n\r\t<]*?)\](.*?)\[/url\]#is',
                			   '#\[url\](.*?)\[/url\]#si');
                			   
                $replace = array('<a href="$1" target="_blank">$1</a>',
                				 '<a href="$1" target="_blank">$2</a>',
                				 '<a href="$1" target="_blank">$2</a>',
                				 '<a href="$1" target="_blank">$2</a>',
                				 '<a href="$1" target="_blank">$1</a>');

                $this->str = preg_replace($match, $replace, $this->str);
            }
                				 
            if($this->action === 'reset'){
                $match = array('#\[url:'.$this->uid.'\](.*?)\[/url:'.$this->uid.'\]#si',
                			   '#\[url=(.*?):'.$this->uid.'\](.*?)\[/url:'.$this->uid.'\]#si',
                			   '#\[url=([\w]+?://[^ \"\n\r\t<]*?)\](.*?)\[/url\]#is',
                			   '#\[url\](.*?)\[/url\]#si');
                			   
                $replace = array('[url]$1[/url]',
                				 '[url=$1]$2[/url]',
                				 '[url=$1]$2[/url]',
                				 '[url]$1[/url]');
                				 
                $this->str = preg_replace($match, $replace, $this->str);

                if(!function_exists('bbUrl2')){
                    function bbUrl2($matches){
                        return '<a href="'.str_replace('&', '&amp;', str_replace('&amp;', '&', $matches[1])).'">';
                    }
                }
                $this->str = preg_replace_callback('#<a href="(.*?)">#si', 'bbUrl2' /*array($this, bbUrl2)*/ , $this->str);
            }

            if($this->action === 'reset'){
                $match = array('#\[url:'.$this->uid.'\](.*?)\[/url:'.$this->uid.'\]#si', '#\[url=(.*?):'.$this->uid.'\](.*?)\[/url:'.$this->uid.'\]#si', '#\[url=([\w]+?://[^ \"\n\r\t<]*?)\](.*?)\[/url\]#is','#\[url\](.*?)\[/url\]#si');
                $replace = array('[url=$1]$1[/url]', '[url=$1>]$2<[/url]', '[url=$1]$2[/url]', '[url]$1[/url]');
                $this->str = preg_replace($match, $replace, $this->str);
                // Added to remove the old style of [url="name"]
                $this->str = str_replace('&quot;', '', $this->str);

                if(!function_exists('bbUrl2')){
                    function bbUrl2($matches){
                        return '<a href="'.str_replace('&', '&amp;', str_replace('&amp;', '&', $matches[1])).'">';
                    }
                }
                $this->str = preg_replace_callback('#<a href="(.*?)">#si', 'bbUrl2' /*array($this, bbUrl2)*/ , $this->str);
            }
        }
    }

    /**
     * fixes problem in bbUrl that occurs when trying to run a callback
     * @param $matches matches from preg_replace_callback within bbUrl
     */

    private function bbUrl2($matches){
        return '<a href="'.str_replace('&', '&amp;', str_replace('&amp;', '&', $matches[1])).'">';
    }

    /**
     * parses string for [img], limited to $this->imgLimit amount of times
     */
    private function bbImg(){
        if($this->img === TRUE OR $this->action === NULL){
            if($this->action === 'pre'){
                $match = array('#\[img=(.*?)\]\[/img\]#si',
                			   '#\[img\](.*?)\[/img\]#si');
                $replace = array('[img=$1:'.$this->uid.'][/img:'.$this->uid.']',
                				 '[img:'.$this->uid.']$1[/img:'.$this->uid.']');
                $this->str = preg_replace($match, $replace, $this->str, $this->imgLimit);
            }

            if($this->action === 'post' OR $this->action === NULL){
                $match = array('#\[img:'.$this->uid.'\](.*?)\[/img:'.$this->uid.'\]#si',
                		 	   '#\[img=(.*?):'.$this->uid.'\]\[/img:'.$this->uid.'\]#si');
                $replace = '<img src="$1" />';
                $this->str = preg_replace($match, $replace, $this->str, $this->imgLimit);
            }   

            if($this->action === 'reset'){
                $match = array('#\[img:'.$this->uid.'\](.*?)\[/img:'.$this->uid.'\]#si',
                			   '#\[img=(.*?):'.$this->uid.'\](.*?)\[/img:'.$this->uid.'\]#si');
                			   
                $replace = array('[img]$1[/img]',
                				 '[img=$1]$2[/img]');

                $this->str = preg_replace($match, $replace, $this->str, $this->imgLimit);
                
                $this->str = str_replace('&quot;', '', $this->str);
            }   
        }
    }
}