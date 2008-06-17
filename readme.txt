=== WP Translit ===
Contributors: urkekg
Donate link: http://blog.urosevic.net/wp-translit
Tags: transliteration, serbian, cyrillic, latin
Requires at least: 2.1
Tested up to: 2.5.1
Stable tag: trunk

Transliterate text in posts and pages from Serbian Cyrillic to Latin script. Visitor can select output script from widget.

== Description ==

When visitor enter Serbian Cyrillic site, and don't understand Cyrillic script, they can select different output script (Latin or YUSCII) from widget (dropdown list or links list), to get transliterated post and/or page in chosen script.

== Installation ==

1. Extract package and upload `wp-translit` directory to `/wp-content/plugins/` directory
2. Place `<?php if (function_exists('wpt_set_lang')) { wpt_set_lang(); } ?>` in front of `header.php` file in template
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Add widget `WP Translit (links)` or `WP Translit (list)` (recommended)

== Frequently Asked Questions ==

= Is there explanation for this plugin in Serbian Cyrillic? =

Yes. Full WP Translit page in Serbian Cyrillic script is placed on my blog [Zapisi](http://blog.urosevic.net/wp-translit/ "WP Translit plugin page")

== Screenshots ==

1. This is `WP Translit (list)` widget
2. This is `WP Translit (links)` widget
