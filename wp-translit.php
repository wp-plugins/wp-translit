<?php
/*
    WP Translit transliterate Serbian Cyrillic to Latin script in WordPress blog's
    Copyright (C) 2008 Aleksandar Urošević <urke@users.sourceforge.net>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

Plugin Name: WP Translit
Plugin URI: http://blog.urosevic.net/wp-translit/
Description: Transliterate text from Serbian Cyrillic to Latin script in posts and pages.
Author: Aleksandar Urošević
Version: 0.3.5a
Author URI: http://urosevic.net

Thanks to:
	http://www.emanueleferonato.com/2008/02/15/how-to-create-a-wordpress-widget/
	http://lonewolf-online.net/computers/wordpress/create-widgets-control-panels/
	http://kimmo.suominen.com/sw/srlatin/
	http://us3.php.net/ob_start
*/

	add_action("plugins_loaded", "init_wpt");
	add_action("translit",       "wpt_translit");
	add_action('init',           'wpt_textdomain');

function init_wpt()
{
	register_sidebar_widget("WP Translit", "wpt_widget");
	register_widget_control("WP Translit", "wpt_widget_control");
	// define dhr_lang only once, on init;
	hdr_lang();
}

function wpt_textdomain()
{
	load_plugin_textdomain( 'wpt', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/lang' );
}
	
function wpt_widget_control()
{
	// Configuration panel for WP Translit Widget
	$options = get_option("widget_wpt");
	if (!is_array( $options ))
	{
		$options = array(
		'title' => 'Избор писма',
		'style' => 'list',
		'float' => 'single,page'
		);
	}

	if ($_POST['wpt-Submit'])
	{
		$options['title'] = htmlspecialchars($_POST['wpt-wTitle']);
		$options['style'] = htmlspecialchars($_POST['wpt-wStyle']);
		$wFloat = $_POST["wpt-wFloat"];
		if ( is_array( $wFloat ) ) {
			foreach ( $wFloat as $wf_page )
                        {
				$tmp .= $wf_page.",";
			}
			$wFloat = substr($tmp, 0, -1);
		}
		$options['float'] = $wFloat;
		update_option("widget_wpt", $options);
	}

	// Form generator
	$wf_float = explode(",", $options['float']);
	?>
	<p>
	<label for="wpt-wTitle"><?php _e('Title', 'wpt'); ?>: </label><br />
	<input type="text" id="wpt-wTitle" name="wpt-wTitle" value="<?php echo $options['title'];?>" /><br /><br />
	<label for="wpt-wStyle"><?php _e('Style', 'wpt'); ?>: </label><br />
	<input type="radio" id="wpt-wStyle" name="wpt-wStyle" value="list" <?php if ( $options['style'] == "list" ) { echo "checked"; } ?>/> <?php _e('Dropdown list', 'wpt'); ?><br/>
	<input type="radio" id="wpt-wStyle" name="wpt-wStyle" value="links" <?php if ( $options['style'] == "links" ) { echo "checked"; } ?>/> <?php _e('Unordered list', 'wpt'); ?><br/><br/>
	<label for="wpt-wFloat" name="wpt-wFloat"><?php _e('Floating widget', 'wpt'); ?>:<br/><em>(<?php _e("on which page to display floating widget", 'wpt'); ?>)</em></label><br />
	<input type="checkbox" id="wpt-wFloat" name="wpt-wFloat[]" value="home" <?php if ( in_array("home", $wf_float) ) { echo 'checked="yes"'; } ?> /> <?php _e('Home', 'wpt'); ?><br />
	<input type="checkbox" id="wpt-wFloat" name="wpt-wFloat[]" value="single" <?php if ( in_array("single", $wf_float) ) { echo 'checked="yes"'; } ?> /> <?php _e('Single Post', 'wpt'); ?><br />
	<input type="checkbox" id="wpt-wFloat" name="wpt-wFloat[]" value="page" <?php if ( in_array("page", $wf_float) ) { echo 'checked="yes"'; } ?> /> <?php _e('Page', 'wpt'); ?><br />
	<input type="checkbox" id="wpt-wFloat" name="wpt-wFloat[]" value="arch" <?php if ( in_array("arch", $wf_float) ) { echo 'checked="yes"'; } ?> /> <?php _e('Archives', 'wpt'); ?><br />
	<input type="checkbox" id="wpt-wFloat" name="wpt-wFloat[]" value="search" <?php if ( in_array("search", $wf_float) ) { echo 'checked="yes"'; } ?> /> <?php _e('Search', 'wpt'); ?><br />
	<input type="hidden" id="wpt-Submit" name="wpt-Submit" value="1" />
	</p>
<?php
} // function wpt_widget_control

// Functions to print widget in sidebar
function wpt_widget($args)
{
	extract($args);
	
	$options = get_option("widget_wpt");
	if (!is_array( $options ))
	{
		$options = array(
		'title' => 'Избор писма',
		'style' => 'list',
		'float' => 'single,page'
		);
	}

	echo $before_widget;
	echo $before_title;
	echo $options['title'];
	echo $after_title;

	// Which wiget style to print out?
	if ( $options['style'] == "list" )
        {
		wpt_widget_list();
	} else {
		wpt_widget_links();
	}
	echo $after_widget;
}

// Function to print floating widget
function wpt_widget_float()
{
	$options = get_option("widget_wpt");
	if (!is_array( $options ))
	{
		$options = array(
		'title' => 'Избор писма',
		'style' => 'list',
		'float' => 'single,page'
		);
	}
        $fw_out = 0;
        // prvo ako treba podelim float u niz
        if ( strpos($options['float'], ",") > 1 )
        {
                $float = explode(",", $options['float']);
                foreach ( $float as $tmp )
                {
                        $fw_out = $fw_out + wpt_fwout($tmp);
                }
	} elseif ( strlen($options['float']) != 0 )
        {
                $fw_out = wpt_fwout($options['float']);
	}

        // at end print (ro not) floating widget
        if ( $fw_out != 0 )
        {
                printf ('<div id="wpt_widget_float"><h2>%s</h2>', $options['title']);
                // Which wiget style to print out?
                if ( $options['style'] == "list" )
                {
                	wpt_widget_list();
                } else {
                	wpt_widget_links();
                }
                echo '</div>';
        }
} // function wpt_widget_float

// Function to test if we need to print floating widget on current page style
function wpt_fwout($page_type)
{
        $wpt_fwout = 0;
	switch($page_type)
        {
		case "home":
			if (is_home()) $wpt_fwout = 1;
			break;
		case "single":
			if (is_single()) $wpt_fwout = 1;
			break;
		case "page":
			if (is_page()) $wpt_fwout = 1;
			break;
		case "arch":
			if (is_archive()) $wpt_fwout = 1;
			break;
		case "search":
			if (is_search()) $wpt_fwout = 1;
			break;
                default:
	}
        return $wpt_fwout;
} // function wpt_fwout($page_type)

function wpt_widget_float_css()
{
print <<<EOF
<!-- WP Translit flying widget style -->
<style type="text/css" media="screen">
#wpt_widget_float {
	position: fixed;
	top: 0;
	right: 0;
	border: none;
	padding: 10px;
	background: inherit !important;
	color: #111;
	text-align: left; }
#wpt_widget_float h2 {
	font-family: 'Lucida Grande', Verdana, Sans-Serif;
	font-size: 1.2em;
	margin-top: 0; padding-top: 0; }
#wpt_widget_float ul, #wpt_widget_float ul li {
	margin: 0 0 0 6px;
	padding: 0; }
fieldset { border: none !important; padding: 0 !important; margin: 0 !important; }
</style>
<!-- /WP Translit flying widget style -->

EOF;
}

function wpt_widget_list()
{
	if ( isset($_REQUEST['lng']) ) {
		$wpt_lang = $_REQUEST['lng'];
	} elseif ( isset( $_COOKIE["wpt_lang"] ) ) {
		$wpt_lang = $_COOKIE["wpt_lang"];
	} else {
		$wpt_lang = $GLOBALS['hdr_lang'];
	}

	switch($wpt_lang) {
		case "lat": $lc1 = 'selected="selected"'; break;
		default:    $cc1 = 'selected="selected"';
	}

	print <<<EOF
<!-- WP Translit Widget (list) -->
<form action="${uri_adresa}" method="post"><fieldset>
<select name="lng" id="lng" onchange="this.form.submit()">
<option value="cir" $cc1>ћирилица</option>
<option value="lat" $lc1>latinica</option>
</select>
</fieldset></form>
<!-- /WP Translit Widget (list) -->
EOF;
} // wpt_widget_list()

function wpt_widget_links()
{
	if ( isset($_REQUEST['lng']) ) {
		$wpt_lang = $_REQUEST['lng'];
	} elseif ( isset($_COOKIE["wpt_lang"]) ) {
		$wpt_lang = $_COOKIE["wpt_lang"];
	} else {
		$wpt_lang = $GLOBALS['hdr_lang'];
	}
	
	$cc1 = '<a href="?lng=cir">';
	$lc1 = '<a href="?lng=lat">';
	$lc2 = $ac2 = $cc2 = "</a>";
	
	switch($wpt_lang) {
		case "lat": $lc1 = '<strong>'; $lc2 = "</strong>"; break;
		default:    $cc1 = '<strong>'; $cc2 = "</strong>";
	}

	print <<<EOF
<!-- WP Translit Widget (links) -->
<ul>
<li>${cc1}ћирилица${cc2}</li>
<li>${lc1}latinica${lc2}</li>
</ul>
<!-- /WP Translit Widget (links) -->
EOF;
} // wpt_widget_links()

// Function to set language from request headers to cookies
function wpt_set_lang()
{
	$lng = "";
	$wpt_lang = "";

	if ( isset($_REQUEST['lng']) )
        {
		$wpt_lang = $_REQUEST['lng'];
		if ( $wpt_lang == "cir" || $wpt_lang == "lat" )
                {
			setcookie("wpt_lang", $wpt_lang, strtotime("+3 months"), "/");
		}
	}
}

class wp_translit
{
	function wp_translit()
	{
		add_action('wp_head', array(&$this,'buffer_start'), 1);
		add_action('wp_footer', array(&$this,'buffer_end'), 1);
		// add transliteration to feed
		add_action('feed_head', array(&$this,'buffer_start'), 1);
		add_action('feed_footer', array(&$this,'buffer_end'), 1);
		add_action('rss2_head', array(&$this,'buffer_start'), 1);
		add_action('rss2_footer', array(&$this,'buffer_end'), 1);
	}
	
	function buffer_start()
	{
		if ( wp_head ) { wpt_widget_float_css(); }
		ob_start( array(&$this,"do_wptranslit") );
		wpt_set_lang();
	}
	 
	function buffer_end()
	{
		ob_end_flush();
		if ( wp_footer ) { wpt_widget_float(); }
	}
		
	// Function to do replace of text
	function do_wptranslit($text)
        {
		// set default script to 'cir' (put in widget setup?)
		$wpt_lang = "cir";
		
		// get language from REQUEST (if 'lng' exists)
		if ( isset($_REQUEST['lng']) )
                { 
			$wpt_lang = $_REQUEST['lng'];
		}
		// if no language in REQUEST, get language from cookies (if cookie 'wpt_lang' exists)
		elseif ( isset($_COOKIE['wpt_lang']) )
                {
			$wpt_lang = $_COOKIE['wpt_lang'];
		}
		// if no wpt_lang in cookies, get Accept Language from headers
		elseif ( $GLOBALS['hdr_lang'] )
		{
			$wpt_lang = $GLOBALS['hdr_lang'];
		}

		// Do we even need to do transliteration (wpt_lang is 'lat')?
		if ( $wpt_lang == "lat" )
                {
			$wpt_izlaz = "";
			// set source script - Cyrillic
			$str_from = array ("Џа", "Џе", "Џи", "Џо", "Џу", "Ња", "Ње", "Њи", "Њо", "Њу", "Ља", "Ље", "Љи", "Љо", "Љу", "а","б","в","г","д","ђ","е","ж","з","и","ј","к","л","љ","м","н","њ","о","п","р","с","т","ћ","у","ф","х","ц","ч","џ","ш","А","Б","В","Г","Д","Ђ","Е","Ж","З","И","Ј","К","Л","Љ","М","Н","Њ","О","П","Р","С","Т","Ћ","У","Ф","Х","Ц","Ч","Џ","Ш","č","Č","ć","Ć","ž","Ž","đ","Đ","š","Š");
			// set destination script to UTF-8 Serbian Latin
			$str_to = array ("Dža", "Dže", "Dži", "Džo", "Džu", "Nja", "Nje", "Nji", "Njo", "Nju", "Lja", "Lje", "Lji", "Ljo", "Lju", "a","b","v","g","d","đ","e","ž","z","i","j","k","l","lj","m","n","nj","o","p","r","s","t","ć","u","f","h","c","č","dž","š","A","B","V","G","D","Đ","E","Ž","Z","I","J","K","L","Lj","M","N","Nj","O","P","R","S","T","Ć","U","F","H","C","Č","Dž","Š","č","Č","ć","Ć","ž","Ž","đ","Đ","š","Š");
			// do simple string replace
			return str_replace($str_from, $str_to, $text);
		} else {
			// if there no need for transliteration, print out unchanged text
			return $text;
		} // wpt_lang == lat
	} // function do_wptranslit()
} // class wp_translit

// Function to get language from Accept Language headers
function hdr_lang() {
	// get language from HTTP headers and split it to array
	$languages = split(',', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
	$hdr_lang = "";
	
	// check first defined Accept Language
	$hlang = $languages[0];
	if ( ereg("^(sr|mk|bg|ru)", $hlang) && $hbr == 0 )
        {
		// for Serbian, Macedonian, Bulgarian and Russian set 'cir'
		$hdr_lang = "cir";
	} else {
		// for all other set to 'lat'
		$hdr_lang = "lat";
	}
	// set global variable
	$GLOBALS['hdr_lang'] = $hdr_lang;
}

$_wp_wp_translit =& new wp_translit;

?>