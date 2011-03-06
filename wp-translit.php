<?php
/*
    WP Translit transliterate Serbian Cyrillic to Latin script in WordPress blog's
    Copyright (C) 2008-2011 Aleksandar Urošević <urke@users.sourceforge.net>

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
Plugin URI: http://blog.urosevic.net/wordpress/wp-translit/
Description: Transliterate text from Serbian Cyrillic to Latin script in posts, pages and feeds. After installation check <a href="options-general.php?page=wp-translit/wp-translit.php">Settings</a>.
Author: Aleksandar Urošević
Version: 0.3.8.1
Author URI: http://urosevic.net

Thanks to:
	http://www.emanueleferonato.com/2008/02/15/how-to-create-a-wordpress-widget/
	http://lonewolf-online.net/computers/wordpress/create-widgets-control-panels/
	http://kimmo.suominen.com/sw/srlatin/
	http://us3.php.net/ob_start
*/

$wpt_version = "0.3.8.1";

add_action('init', 'wpt_init');
add_action('plugins_loaded', 'wpt_register_widget');
add_action('admin_menu', 'wpt_menu');
add_action('do_translit', 'wp_translit');
add_action('wptranslit', 'wpt_inline');

if ( function_exists('wp_translit') ) {
	$blog_url = get_bloginfo('url');
	$wpt_dir = PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
}

if ( is_admin() ) {
	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'wptConfigLink' );
}

function wpt_init() {
	load_plugin_textdomain( 'wpt', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/lang' );
	hdr_lang(); // get default language from HTTP headers
	wpt_set_lang(); // determine output script
}

function wptConfigLink( $links ) { 
  $settings_link = '<a href="options-general.php?page=wp-translit/wp-translit.php">'.__('Settings').'</a>'; 
  array_unshift( $links, $settings_link ); 
  return $links; 
}

function wpt_menu() {
	add_options_page(__('WP Translit Options', 'wpt'), __('WP Translit', 'wpt'), 8, __FILE__, 'wpt_options');
}

function wpt_options() {
	global $wpt_version;
	if ( $_POST['wpt-submit'] )	{
		$options['widget_title']     = htmlspecialchars($_POST['wpt-wtitle']);
		$options['widget_style']     = $_POST['wpt-wstyle'];
		$options['gt_show']          = $_POST['wpt-gtshow'];
		$options['gt_lang']          = $_POST['wpt-gtlang'];
		$options['gt_text']          = htmlspecialchars($_POST['wpt-gttext']);
		$options['inline_class']     = strip_tags($_POST['wpt-iclass']);
		$options['inline_delimiter'] = htmlspecialchars($_POST['wpt-idelim']);
		$options['inline_prefix']    = htmlspecialchars($_POST['wpt-iprefix']);
		$options['inline_suffix']    = htmlspecialchars($_POST['wpt-isuffix']);
		update_option("wptranslit", $options);
	}

	// inicijalizujem opcije dodatka
	$options = get_option("wptranslit");
	if ( !is_array( $options ) ) {
		$options = array(
			"widget_title"     => "Избор писма",
			"widget_style"     => "list",
			"gt_show"          => true,
			"gt_lang"          => "en",
			"gt_text"          => "Read in bad English",
			"inline_delimiter" => " | ",
			"inline_prefix"    => "",
			"inline_suffix"    => "",
			"inline_class"     => "wpt_inline"
		);
		update_option("wptranslit", $options);
	}
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2><?php _e('WP Translit', 'wpt'); ?></h2>

	<form method="post" action="" id="wpt-conf">
	<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('wpt-updatesettings'); } ?>
	<p><?php echo __("Current version:", "wpt")." <strong>$wpt_version</strong>"; ?></p>
	<p><?php _e('This plugin displays links to transliterate text from Serbian Cyrillic to Serbian Latin script, or translate it with Google Translate to chosen language.', 'wpt'); ?></p>
	<h3><?php _e("Usage", "wpt"); ?></h3>
	<p><?php _e('Embed next code in template files at place where you wish to display WP Translit inline links (but not in Loop!):', 'wpt'); ?></p>
	<code>&lt;?php do_action("wptranslit"); ?&gt;</code>

	<h3><?php _e("Widget", "wpt"); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label><?php _e("Title", "wpt"); ?></label></th>
			<td><input type="text" value="<?php echo strip_tags(stripslashes($options['widget_title'])); ?>" name="wpt-wtitle" id="wpt-wtitle" size="20" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e("Style", "wpt"); ?></label></th>
			<td>
				<input type="radio" id="wpt-wstyle" name="wpt-wstyle" value="list" <?php if ( $options['widget_style'] == "list" ) { echo "checked"; } ?>/> <?php _e("Unordered list", "wpt"); ?><br/>
				<input type="radio" id="wpt-wstyle" name="wpt-wstyle" value="drop" <?php if ( $options['widget_style'] == "drop" ) { echo "checked"; } ?>/> <?php _e("Dropdown box", "wpt"); ?><br/>
			</td>
		</tr>
	</table>

	<h3><?php _e("Inline", "wpt"); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label><?php _e("Inline DIV class", "wpt"); ?></label></th>
			<td><input type="text" value="<?php echo strip_tags(stripslashes($options['inline_class'])); ?>" name="wpt-iclass" id="wpt-iclass" /> <em><?php _e("CSS class can be used in style.css to style inline links box.", "wpt"); ?></em></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e("Inline prefix", "wpt"); ?></label></th>
			<td><input type="text" value="<?php echo strip_tags(stripslashes($options['inline_prefix'])); ?>" name="wpt-iprefix" id="wpt-iprefix" /> <em><?php _e("Text to put in front of inline links.", "wpt"); ?></em></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e("Inline suffix", "wpt"); ?></label></th>
			<td><input type="text" value="<?php echo strip_tags(stripslashes($options['inline_suffix'])); ?>" name="wpt-isuffix" id="wpt-isuffix" /> <em><?php _e("Additional text or links to append behind inline links. HTML allowed.", "wpt"); ?></em></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e("Inline delimiter", "wpt"); ?></label></th>
			<td><input type="text" value="<?php echo strip_tags(stripslashes($options['inline_delimiter'])); ?>" name="wpt-idelim" id="wpt-idelim" /> <em><?php _e("Set of characters to put in middle of links.", "wpt"); ?></em></td>
		</tr>
	</table>

	<h3><?php _e("Google Translate", "wpt"); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label><?php _e("Display GT link", "wpt"); ?></label></th>
			<td><input type="checkbox" <?php echo ($options['gt_show']) ? ' checked="checked"' : ''; ?> name="wpt-gtshow" id="wpt-gtshow" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e("Link title", "wpt"); ?></label></th>
			<td><input type="text" value="<?php echo strip_tags(stripslashes($options['gt_text'])); ?>" name="wpt-gttext" id="wpt-gttext" size="20" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e("Target language", "wpt"); ?></label></th>
			<td>
				<input type="radio" id="wpt-gtlang" name="wpt-gtlang" value="en" <?php if ( $options['gt_lang'] == "en" ) { echo "checked"; } ?>/> <?php _e("English", "wpt"); ?><br/>
				<input type="radio" id="wpt-gtlang" name="wpt-gtlang" value="de" <?php if ( $options['gt_lang'] == "de" ) { echo "checked"; } ?>/> <?php _e("German", "wpt"); ?><br/>
				<input type="radio" id="wpt-gtlang" name="wpt-gtlang" value="fr" <?php if ( $options['gt_lang'] == "fr" ) { echo "checked"; } ?>/> <?php _e("French", "wpt"); ?><br/>
				<input type="radio" id="wpt-gtlang" name="wpt-gtlang" value="ru" <?php if ( $options['gt_lang'] == "ru" ) { echo "checked"; } ?>/> <?php _e("Russian", "wpt"); ?>
			</td>
		</tr>

	</table>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="option_page" value="wp-translit" />

	<p class="submit">
		<input type="submit" name="wpt-submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

	</form>
	<h3><?php _e("Support", "wpt"); ?></h3>
	<p><?php echo sprintf(__('For all questions, feature request and communication with author and users of this plugin, use our <a href="%s">support forum</a>.', 'wpt'), "http://wordpress.org/tags/wp-translit?forum_id=10"); ?>
	<h3><?php _e("Donate", "wpt"); ?></h3>
	<p><?php echo sprintf(__('If you like <a href="%s">WP Translit</a> and my other <a href="%s">WordPress extensions</a>, feel free to support my work with <a href="%s">donation</a>.', 'wpt'), "http://wordpress.org/extend/plugins/wp-translit/", "http://profiles.wordpress.org/users/urkekg/", "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Q6Q762MQ97XJ6"); ?></p>
</div>
<?php
}

/* Widget stuff */
function wpt_register_widget() {
	register_sidebar_widget('WP Translit', 'wpt_widget');
	register_widget_control('WP Translit', 'wpt_widget_control');
}

function wpt_widget_control() {
	echo "<p>".__("This widget will display WP Translit in sidebar.", "wpt")."</p>";
	echo "<p>".sprintf(__("Go to %s to configure it.", "wpt"), '<a href="options-general.php?page=wp-translit/wp-translit.php">'.__("Settings").'</a>')."</p>";
} // function wpt_widget_control

// Functions to print widget in sidebar
function wpt_widget($args) {
	extract($args);

	// inicijalizujem opcije dodatka
	$options = get_option("wptranslit");
	if ( !is_array( $options ) ) {
		$options = array(
			"widget_title"     => "Избор писма",
			"widget_style"     => "list",
			"gt_show"          => true,
			"gt_lang"          => "en",
			"gt_text"          => "Read in bad English",
			"inline_delimiter" => " | ",
			"inline_prefix"    => "",
			"inline_suffix"    => "",
			"inline_class"     => "wpt_inline"
		);
		update_option("wptranslit", $options);
	}

	echo $before_widget;
	echo $before_title;
	echo strip_tags(stripslashes($options['widget_title']));
	echo $after_title;

	// Which wiget style to print out?
	if ( $options['widget_style'] == "list" ) {
		wpt_widget_list();
	} else {
		wpt_widget_drop();
	}
	echo $after_widget;
}

function wpt_widget_list() {
	$current_uri = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	$this_page_url = "http://" . $current_uri;

	if ( isset($_REQUEST['lng']) ) {
		$wpt_lang = $_REQUEST['lng'];
	} elseif ( isset($_COOKIE['wpt_lang']) ) {
		$wpt_lang = $_COOKIE['wpt_lang'];
	} else {
		$wpt_lang = $GLOBALS['hdr_lang'];
	}
	
	// check if exist $_GET
	if ( count($_GET) > 0 ) {
		if ( !$_GET['lng'] ) {
			$cc1 = '<a href="http://'.$current_uri.'&lng=cir">';
			$lc1 = '<a href="http://'.$current_uri.'&lng=lat">';
		} else {
			$cc1 = '<a href="http://'.str_replace( array("lng=lat", "lng=cir"), 'lng=cir', $current_uri).'">';
			$lc1 = '<a href="http://'.str_replace( array("lng=lat", "lng=cir"), 'lng=lat', $current_uri).'">';
		}
	} else {
		$cc1 = '<a href="?lng=cir">';
		$lc1 = '<a href="?lng=lat">';
	}
	$lc2 = $cc2 = "</a>";
	
	switch($wpt_lang) {
		case "lat": $lc1 = "<strong>"; $lc2 = "</strong>"; break;
		default:    $cc1 = "<strong>"; $cc2 = "</strong>";
	}

	// Display GT link?
	$options = get_option("wptranslit");
	if ( $options['gt_show'] ) {
		$gtlink = '<li><a href="http://translate.google.com/translate?prev=_t&amp;ie=UTF-8&amp;sl=sr&amp;tl='.$options['gt_lang'].'&amp;u='.$this_page_url.'">'.$options['gt_text'].'</a></li>';
	}
	print <<<EOF
<!-- WP Translit Widget (list) -->
<ul>
<li>${cc1}&#x045b;&#x0438;&#x0440;&#x0438;&#x043b;&#x0438;&#x0446;&#x0430;${cc2}</li>
<li>${lc1}latinica${lc2}</li>
$gtlink
</ul>
<!-- /WP Translit Widget (list) -->
EOF;
} // wpt_widget_list()


function wpt_widget_drop() {
	if ( isset($_REQUEST['lng']) ) {
		$wpt_lang = $_REQUEST['lng'];
	} elseif ( isset( $_COOKIE['wpt_lang'] ) ) {
		$wpt_lang = $_COOKIE['wpt_lang'];
	} else {
		$wpt_lang = $GLOBALS['hdr_lang'];
	}

	switch($wpt_lang) {
		case "lat": $lc1 = 'selected="selected"'; break;
		default:    $cc1 = 'selected="selected"';
	}

	print <<<EOF
<!-- WP Translit Widget (drop) -->
<form action="" method="post"><fieldset style="border: 0;">
<select name="lng" id="lng" onchange="this.form.submit()">
<option value="cir" $cc1>&#x045b;&#x0438;&#x0440;&#x0438;&#x043b;&#x0438;&#x0446;&#x0430;</option>
<option value="lat" $lc1>latinica</option>
</select>
</fieldset></form>
<!-- /WP Translit Widget (drop) -->
EOF;
} // wpt_widget_drop()

function wpt_inline() {
	$current_uri = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	$this_page_url = "http://" . $current_uri;

	if ( isset($_REQUEST['lng']) ) {
		$wpt_lang = $_REQUEST['lng'];
	} elseif ( isset($_COOKIE['wpt_lang']) ) {
		$wpt_lang = $_COOKIE['wpt_lang'];
	} else {
		$wpt_lang = $GLOBALS['hdr_lang'];
	}
	
	// check if exist $_GET
	if ( count($_GET) > 0 ) {
		if ( !$_GET['lng'] ) {
			$cc1 = '<a href="http://'.$current_uri.'&lng=cir">';
			$lc1 = '<a href="http://'.$current_uri.'&lng=lat">';
		} else {
			$cc1 = '<a href="http://'.str_replace( array("lng=lat", "lng=cir"), 'lng=cir', $current_uri).'">';
			$lc1 = '<a href="http://'.str_replace( array("lng=lat", "lng=cir"), 'lng=lat', $current_uri).'">';
		}
	} else {
		$cc1 = '<a href="?lng=cir">';
		$lc1 = '<a href="?lng=lat">';
	}
	$lc2 = $cc2 = "</a>";
	
	switch($wpt_lang) {
		case "lat": $lc1 = "<strong>"; $lc2 = "</strong>"; break;
		default:    $cc1 = "<strong>"; $cc2 = "</strong>";
	}

	// Get inline options
	$options = get_option("wptranslit");
	// 
	$iclass  = strip_tags(stripslashes($options['inline_class']));
	if ( $iclass ) { $iclass = 'class="'.$iclass.'"'; }

	// get delimiter
	$idelim  = stripslashes($options['inline_delimiter']);

	// get prefix & suffix
	$iprefix = htmlspecialchars_decode(stripslashes($options['inline_prefix']));
	$isuffix = htmlspecialchars_decode(stripslashes($options['inline_suffix']));
	if ( $isuffix ) { $isuffix = $idelim.$isuffix; }
	
	// Display GT link?
	if ( $options['gt_show'] ) {
		$gtlink = $idelim.'<a href="http://translate.google.com/translate?prev=_t&amp;ie=UTF-8&amp;sl=sr&amp;tl='.$options['gt_lang'].'&amp;u='.$this_page_url.'">'.$options['gt_text'].'</a>';
	}
	print <<<EOF
<!-- WP Translit Widget (inline) -->
<div $iclass>${iprefix}${cc1}&#x045b;&#x0438;&#x0440;&#x0438;&#x043b;&#x0438;&#x0446;&#x0430;${cc2}${idelim}${lc1}latinica${lc2}${gtlink}${isuffix}</div>
<!-- /WP Translit Widget (inline) -->
EOF;
}

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

// Function to set language from request headers to cookies
function wpt_set_lang() {
	$lng = "";
	$wpt_lang = "";

	if ( isset($_REQUEST['lng']) ) {
		$wpt_lang = $_REQUEST['lng'];
		if ( $wpt_lang == "cir" || $wpt_lang == "lat" ) {
			setcookie("wpt_lang", $wpt_lang, strtotime("+3 months"), "/");
		}
	}
}

/* WP Translit Master Kung-Fu */
class wp_translit {
	function wp_translit() {
		add_action('wp_head', array(&$this,'buffer_start'));
		add_action('wp_footer', array(&$this,'buffer_end'));
		// add transliteration to feed
		add_action('feed_head', array(&$this,'buffer_start'), 1);
		add_action('feed_footer', array(&$this,'buffer_end'), 1);
		add_action('rss_head', array(&$this,'buffer_start'), 1);
		add_action('rss_footer', array(&$this,'buffer_end'), 1);
		add_action('rss2_head', array(&$this,'buffer_start'), 1);
		add_action('rss2_footer', array(&$this,'buffer_end'), 1);
	}
	
	function buffer_start() {
		ob_start( array(&$this,"do_wptranslit") );
	}
	 
	function buffer_end() {
		ob_end_flush();
	}
		
	// Function to do replace of text
	function do_wptranslit($text) {
		// set default script to 'cir' (put in widget setup?)
		$wpt_lang = "cir";
		
		// get language from REQUEST (if 'lng' exists)
		if ( isset($_REQUEST['lng']) ) { 
			$wpt_lang = $_REQUEST['lng'];
		}
		// if no language in REQUEST, get language from cookies (if cookie 'wpt_lang' exists)
		elseif ( isset($_COOKIE['wpt_lang']) ) {
			$wpt_lang = $_COOKIE['wpt_lang'];
		}
		// if no wpt_lang in cookies, get Accept Language from headers
		elseif ( $GLOBALS['hdr_lang'] ) {
			$wpt_lang = $GLOBALS['hdr_lang'];
		}

		// Do we even need to do transliteration (wpt_lang is 'lat')?
		if ( $wpt_lang == "lat" ) {
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

$_wp_wp_translit =& new wp_translit;

?>
