=== WP Translit ===
Contributors: urkekg
Donate link: http://blog.urosevic.net/wp-translit/
Tags: language, automatic, transliteration, translation, translit, sidebar, widget, plugin, posts, page, serbian, cyrillic, latin
Requires at least: 2.6
Tested up to: 2.6.2
Stable tag: trunk

Transliterate text in posts and pages from Serbian Cyrillic to Latin script. Visitor can select output script from widget.

== Description ==

When visitor enter some site written in Serbian Cyrillic script, blog will be transliterated to Latin script.

This is performed by checking `Accept Language HTTP headers`. If visitor don't have Serbian, Macedonian, Bulgarian or Russian language set as preferred in web browser, WP Translit displays blog in Latin script.

If visitor wish Cyrillic or Latin script on demand, then he can make choose from widget/floating widget (dropdown list or links list).

== Features ==

* All transliteation is in one way: Cyrillic -> Latin
* Transliterate all text on pages, including widget titles, header and footer, posts, pages and comments
* Automaticaly display original (Cyrillic) script for Serbian, Macedonian, Bulgarian and Russian visitors
* Automaticaly display Latin script for all other languages (including Croatian, Slovenian, English and others)
* Choose style of widget - dropdown list or unordered list
* Set custom widget and floating widget title
* Display floating widget on home, single, page, archives or search pages
* WP Translit has now fully localised with GNU gettext (English, Serbian Cyrillic and Serbian Latin included)

== Installation ==

WARNING: If you update from earlier version 0.3.2, first remove `<?php if (function_exists('wpt_set_lang')) { wpt_set_lang(); } ?>` from beginning of template file `header.php`

1. Extract package and upload `wp-translit` directory to `/wp-content/plugins/` directory
2. Activate the plugin on 'Plugins' page in `Admin panel`
3. Add widget `WP Translit` to sidebar
4. Configure widget `title`, `style` and where to display floating widget

== Frequently Asked Questions ==

= Which text is tansliterated? =
WP Translit threat only Serbian Cyrillic characters in text and transliterate it to Serbian Latin characters.

= Do this plugin transliterate in opposite way? =
No. WP Translit don't made transliteration from Latin to Cyrillic script. That is not even planned to be implemented.

= What is `Floating widget`? =
When used theme have page styles without sidebar (for example single posts, pages, archives and search results in default theme), then WP Translit displays floating widget in upper right corner of page, so visitor can change displayed scripts.

= Where is help in Serbian language? =
WP Translit page in Serbian Cyrillic is placed on my blog [Zapisi](http://blog.urosevic.net/wp-translit/ "WP Translit plugin page").

== Screenshots ==

1. Widget configuration panel
2. Widget `WP Translit` in action
3. Floating widget in action