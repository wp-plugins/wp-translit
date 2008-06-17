<?php
/*
Plugin Name: WP Translit
Plugin URI: http://blog.urosevic.net/wp-translit/
Description: Transliterate text from Serbian Cyrillic to Latin script in posts and pages. Use wptextureize filter to do job.
Author: Aleksandar Urošević
Version: 0.3.1
Author URI: http://urosevic.net

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

Thanks to: http://www.emanueleferonato.com/2008/02/15/how-to-create-a-wordpress-widget/
*/

	add_action("plugins_loaded","init_wpt");
	add_action("translit", "wpt_translit");
	add_action("wpt_set_lang", "wpt_set_lang");

function init_wpt()
{
	register_sidebar_widget("WP Translit (links)", "wpt_translit_links_widget");
	register_sidebar_widget("WP Translit (list)",  "wpt_translit_list_widget");
}

function wpt_translit_list_widget()
{
$uri_adresa = $_SERVER['REQUEST_URI'];
if ( isset($_REQUEST['lng']) ) {
	$wpt_lang = $_REQUEST['lng'];
} else {
	$wpt_lang = $_COOKIE["wpt_lang"];
} 
print <<<EOF
	<!-- WP Translit Widget (list) -->
		<li class="widget widget_wpt">
			<h2 class="widgettitle">Писмо/Pismo</h2>
<form action="${uri_adresa}" method="post">

<select name="lng" id="lng" onchange="this.form.submit()">
EOF;
if ($wpt_lang != "lat" && $wpt_lang != "asc" || $wpt_lang == "cir") {
	echo '<option value="cir" selected="selected">изворно (ћирилица)</option>';
} else {
	echo '<option value="cir">изворно (ћирилица)</option>';
}
if ($wpt_lang == "lat") {
	echo '<option value="lat" selected="selected">latinica (sa kvačicama)</option>';
} else {
	echo '<option value="lat">latinica (sa kvačicama)</option>';
}
if ($wpt_lang == "asc") {
	echo '<option value="asc" selected="selected">latinica (bez kvacica)</option>';
} else {
	echo '<option value="asc">latinica (bez kvacica)</option>';
}
print <<<EOF
</select>

</form>
		</li>
	<!-- /WP Translit Widget (list) -->
EOF;
}

function wpt_translit_links_widget()
{
if ( isset($_REQUEST['lng']) ) {
	$wpt_lang = $_REQUEST['lng'];
} else {
	$wpt_lang = $_COOKIE["wpt_lang"];
}
	print <<<EOF
	<!-- WP Translit Widget (links) -->
		<li class="widget widget_links">
			<h2 class="widgettitle">Писмо/Pismo</h2>
			<ul>
EOF;
			if ( $wpt_lang != "cir" ) {
				print '			<li><a href="?lng=cir">изворно (ћирилица)</a></li>';
			} else {
				print '			<li><strong>изворно (ћирилица)</strong></li>';
			}
			if ( $wpt_lang != "lat" ) {
				print '			<li><a href="?lng=lat">latinica (sa kvačicama)</a></li>';
			} else {
				print '			<li><strong>latinica (sa kvačicama)</strong></li>';
			}
			if ( $wpt_lang != "asc" ) {
				print '			<li><a href="?lng=asc">latinica (bez kvacica)</a></li>';
			} else {
				print '			<li><strong>latinica (bez kvacica)</strong></li>';
			}
print <<<EOF
			</ul>
		</li>
	<!-- /WP Translit Widget (links) -->
EOF;
}

function wpt_set_lang() {
$lng = "";
$wpt_lang = "";
		if ( isset($_REQUEST['lng']) ) {
			$wpt_lang = $_REQUEST['lng'];
			if ( $wpt_lang == "cir" || $wpt_lang == "lat" | $wpt_lang == "asc" ) {
				setcookie("wpt_lang", $wpt_lang, strtotime("+3 months"), "/");
			}
		}
}

class wp_translit
{
  function wp_translit()
  {
			global $wp_filter;
			// go through all filters and add our style-fixer after wptexturize
			foreach ($wp_filter as $tag => $filter) {
				$found_wptxt = 0;
				foreach ($wp_filter[$tag] as $priority => $functions) {
					if (!is_null($functions)) {
						foreach ($functions as $function) {
							if ("wptexturize" == $function['function']) {
								add_filter($tag, array(&$this, 'wptranslit'), $priority+1);
								$found_wptxt = 1;
								break;
							}
						}
					}
				}
				if ($found_wptxt)
					continue;
			}
  } //function z_tanslit()

	function wptranslit($text) {
		$wpt_lang = "cir";

		// prvo pokupim postavku iz cookies
		if ( isset($_COOKIE['wpt_lang']) ) {
			$wpt_lang = $_COOKIE['wpt_lang'];
		}
		// ako nema cookies, proverim da li ima u REQUEST-u
		if ( isset($_REQUEST['lng']) ) { 
			$wpt_lang = $_REQUEST['lng'];
		}

		// ako wpt_lang nije "cir", onda će se odraditi transliteracija
		if ( $wpt_lang == "lat" || $wpt_lang == "asc" ) {
			$wpt_izlaz = "";
			// cirilica
			$str_from = array ("а","б","в","г","д","ђ","е","ж","з","и","ј","к","л","љ","м","н","њ","о","п","р","с","т","ћ","у","ф","х","ц","ч","џ","ш","А","Б","В","Г","Д","Ђ","Е","Ж","З","И","Ј","К","Л","Љ","М","Н","Њ","О","П","Р","С","Т","Ћ","У","Ф","Х","Ц","Ч","Џ","Ш","č","Č","ć","Ć","ž","Ž","đ","Đ","š","Š");

			if ( $wpt_lang == "lat" ) {
				// fake UTF-8 latinica
				$str_to = array ("a","b","v","g","d","đ","e","ž","z","i","j","k","l","lj","m","n","nj","o","p","r","s","t","ć","u","f","h","c","č","dž","š","A","B","V","G","D","Đ","E","Ž","Z","I","J","K","L","Lj","M","N","Nj","O","P","R","S","T","Ć","U","F","H","C","Č","Dž","Š","č","Č","ć","Ć","ž","Ž","đ","Đ","š","Š");
			} else if ( $wpt_lang == "asc" ) {
				// ascii
				$str_to = array ("a","b","v","g","d","dj","e","z","z","i","j","k","l","lj","m","n","nj","o","p","r","s","t","c","u","f","h","c","c","dz","s","A","B","V","G","D","Dj","E","Z","Z","I","J","K","L","Lj","M","N","Nj","O","P","R","S","T","C","U","F","H","C","C","Dz","S","c","C","c","C","z","Z","dj","Dj","s","S");
			}

			// Capture tags and everything inside them
			$tarr = preg_split("/(<.*>)/Us", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
	
			// loop stuff
			$stop = count($tarr); $next = true;
			for ($i = 0; $i < $stop; $i++) {
		    $curl = $tarr[$i];			
		    if (isset($curl{0}) && '<' != $curl{0} && $next) {
				$curl = str_replace($str_from, $str_to, $curl);
		    } elseif (strstr($curl, '<code') || strstr($curl, '<pre')
					|| strstr($curl, '<kbd' || strstr($curl, '<style')
					|| strstr($curl, '<script'))) {
				// strstr is fast
				$next = false;
		    } else {
				$next = true;
		    }
		    $wpt_izlaz .= $curl;
			}
//		$output = str_replace('<q>', '&#8222;<q>', $output);
//		$output = str_replace('</q>', '</q>&#8220;', $output);
			return $wpt_izlaz;
  	} else {
			return $text;
		} // wpt_lang == lat|asc
	} // function ztranslit()
} // class wp_translit

$_wp_wp_translit =& new wp_translit;

?>
