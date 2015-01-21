<?php
/*
Plugin Name: WP Translit
Plugin URI: http://urosevic.net/wordpress/plugins/wp-translit/
Description: Transliterate text from Serbian Cyrillic to Latin script in posts, pages and feeds.
Author: Aleksandar Urošević
Version: 0.4.1.1
Author URI: http://urosevic.net

Thanks to:
	http://www.emanueleferonato.com/2008/02/15/how-to-create-a-wordpress-widget/
	http://kimmo.suominen.com/sw/srlatin/
	http://us3.php.net/ob_start
	http://css-tricks.com/snippets/php/get-current-page-url/
*/
/*
	WP Translit transliterate Serbian Cyrillic to Latin script in WordPress blog's
	Copyright (C) 2008-2015 Aleksandar Urošević <urke.kg@gmail.com>

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
 */

// define some constants
define( 'WPT_VER', "0.4.1.1" );
define( 'WPT_SLUG', "wp-translit" );

if ( !class_exists('WP_TRANSLIT') )
{

/**
 * Master Class WP_TRANSLIT
 */
class WP_TRANSLIT
{

	private $slug;
	private $defaults;

	function __construct()
	{

		$this->slug = WPT_SLUG;
		$this->defaults = self::defaults();

		add_action( 'init', array($this, 'init') );

		// Initialize Settings
		require_once('inc/settings.php');

		// Initialize Widget
		require_once('inc/widget.php');

		if ( is_admin() )
			add_filter("plugin_action_links_".plugin_basename(__FILE__), array($this, 'add_action_links') );

		add_filter( 'plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2 );

	} // eom __construct

	function init()
	{

		load_plugin_textdomain( 'wpt', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

		// get default language from HTTP headers
		$this->header_lang();

		// determine output script
		$this->set_cookie();

		// setup translit hooks
		$this->wp_translit();

	} // eom init

	/**
	 * Defaults
	 */
	public static function defaults()
	{

		$defaults = array(
			'default_lng'      => 'cir',
			'widget_title'     => __('Select script','wpt'),
			'widget_style'     => 'list',
			'gt_show'          => false,
			'gt_lang'          => 'en',
			'gt_text'          => __('Read in bad English','wpt'),
			'inline_delimiter' => ' | ',
			'inline_prefix'    => '',
			'inline_suffix'    => '',
			'inline_class'     => 'wpt_inline',
			'inline_ashow'     => false
		);

		$options = wp_parse_args(get_option('wptranslit'), $defaults);
		return $options;

	} // eom defaults

	/* ============================== LINKS ============================== */
	function add_action_links( $links )
	{

		$new_links = '<a href="options-general.php?page='.$this->slug.'">'.__('Settings').'</a>';
		array_unshift( $links, $new_links );

		return $links;
	}

	function plugin_row_meta( $links, $file )
	{

		if ( strpos( $file, basename(__FILE__) ) !== false ) {
			$new_links = array(
				'<a href="http://urosevic.net/wordpress/plugins/wp-translit/" target="_blank">' . __('More info','wpt') . '</a>',
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RQZS3U57E7F5U" target="_blank">' . __('Donate','wpt') . '</a>'
			);

			$links = array_merge( $links, $new_links );
		}

		return $links;

	} // eom plugin_row_meta

	/* ============================== WP Translit Master Kung-Fu ============================== */
	/**
	 * Prepare hooks for transliteration
	 */
	function wp_translit()
	{

		add_action('wp_head', array(&$this,'buffer_start'));
		add_action('wp_footer', array(&$this,'buffer_end'));

		// add transliteration to feed
		add_action('feed_head', array(&$this,'buffer_start'), 1);
		add_action('feed_footer', array(&$this,'buffer_end'), 1);
		add_action('rss_head', array(&$this,'buffer_start'), 1);
		add_action('rss_footer', array(&$this,'buffer_end'), 1);
		add_action('rss2_head', array(&$this,'buffer_start'), 1);
		add_action('rss2_footer', array(&$this,'buffer_end'), 1);

	} // eom wp_translit

	function buffer_start() {
		ob_start( array(&$this,"do_wptranslit") );
	}

	function buffer_end() {
		ob_end_flush();
	}

	/**
	 * Actual test transliteration goes here
	 * @param  [string] $text Input original content
	 * @return [string]       Transliterated content
	 */
	function do_wptranslit($text) {

		// set default script from default settings
		$wpt_lang = $this->defaults['default_lng'];

		// get language from REQUEST (if 'lng' exists)
		if ( isset($_REQUEST['lng']) ) {
			$wpt_lang = $_REQUEST['lng'];
		}
		// if no language in REQUEST, get language from cookies (if cookie 'wpt_lang' exists)
		elseif ( isset($_COOKIE['wpt_lang']) ) {
			$wpt_lang = $_COOKIE['wpt_lang'];
		}
		// if no wpt_lang in cookies, get Accept Language from headers
		elseif ( isset($GLOBALS['hdr_lang']) ) {
			$wpt_lang = $GLOBALS['hdr_lang'];
		}

		// Do we even need to do transliteration (wpt_lang is 'lat')?
		if ( $wpt_lang == "lat" ) {
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

	} // eom do_wptranslit

	/* ============================== HELPERS ============================== */

	/**
	 * Check is there Cyrillic language set in Browser and set $GLOBALS for reuse
	 */
	function header_lang() {

		// if we have request or coookie, then do nothing
		if ( !empty($_REQUEST['lng']) || !empty($_COOKIE['wpt_lang']) )
			return;

		// Get default language from browser
		if ( empty($GLOBALS['hdr_lang']) && !empty($_SERVER["HTTP_ACCEPT_LANGUAGE"]) )
		{

			// look for Cyrillic language in Accept Language header
			if ( preg_match("/(sr|mk|bg|ru)+/", $_SERVER["HTTP_ACCEPT_LANGUAGE"]) )
			{ // for Serbian, Macedonian, Bulgarian and Russian set 'cir'
				$hdr_lang = "cir";
			} else { // for all other set to 'lat'
				$hdr_lang = "lat";
			}

		}
		else if ( empty($GLOBALS['hdr_lang']) )
		{

			// if no language set in browser, then use plugin's default
			$hdr_lang = $this->defaults['default_lng'];

		}

		// set global variable
		$GLOBALS['hdr_lang'] = $hdr_lang;

	} // eom header_lang

	/**
	 * Set website target language to cookie wpt_lang
	 * @return [type] [description]
	 */
	function set_cookie()
	{

		if ( isset($_REQUEST['lng']) && ( $_REQUEST['lng'] == "cir" || $_REQUEST['lng'] == "lat" ) )
		{
			// set cookie from request
			setcookie("wpt_lang", $_REQUEST['lng'], strtotime("+3 months"), "/");
		}
		elseif ( empty($_COOKIE['wpt_lang']) && !empty($this->defaults['default_lng']) )
		{
			// get default target script from settings
			setcookie("wpt_lang", $this->defaults['default_lng'], strtotime("+3 months"), "/");
		}

	} // eom set_cookie

	/**
	 * Get the current Url taking into account Https and Port
	 * @link    http://css-tricks.com/snippets/php/get-current-page-url/
	 * @version Refactored by @AlexParraSilva
	 * @param   none
	 * @return  URL of current page
	 */
	public static function getCurrentUrl()
	{

		$url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
		$url .= '://' . $_SERVER['SERVER_NAME'];
		$url .= in_array( $_SERVER['SERVER_PORT'], array('80', '443') ) ? '' : ':' . $_SERVER['SERVER_PORT'];
		$url .= $_SERVER['REQUEST_URI'];

		return $url;

	}
} // eo class WP_TRANSLIT

} // eo class exists

new WP_TRANSLIT;
