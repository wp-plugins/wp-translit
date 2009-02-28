=== WP Translit ===
Contributors: urkekg
Donate link: http://urosevic.net/kontakt
Tags: language, automatic, transliteration, translation, translit, sidebar, widget, plugin, serbian, cyrillic, latin, multilanguage
Requires at least: 2.6
Tested up to: 2.7.1
Stable tag: 0.3.6

Transliterate text in posts and pages from Serbian Cyrillic to Latin script. Visitor can select output script from widget.

== Description ==

When visitor enter some site written in Serbian Cyrillic script, blog will be transliterated to Latin script.

This is performed by checking `Accept Language HTTP headers`. If visitor don't have Serbian, Macedonian, Bulgarian or Russian language set as preferred in web browser, WP Translit displays blog in Latin script.

If visitor wish Cyrillic or Latin script on demand, then he can make choose from widget/floating widget (dropdown list or links list).

= Features =
* All transliteation is in one way: Cyrillic -> Latin
* Transliterate all text on pages, including widget titles, header and footer, posts, pages, comments and feeds
* Automaticaly display original (Cyrillic) script for Serbian, Macedonian, Bulgarian and Russian visitors
* Automaticaly display Latin script for all other languages (including Croatian, Slovenian, English and others)
* Choose style of widget - dropdown list or unordered list
* Set custom widget and floating widget title
* Display floating widget on home, single, page, archives or search pages
* WP Translit has now fully localised with GNU gettext (English, Serbian Cyrillic and Serbian Latin included)

== Installation ==

= Old way =
1. Download package to local computer
2. Extract package to temporary directory
3. Upload `wp-translit` directory to `/wp-content/plugins/` directory
4. Activate the plugin on `Plugins` page in `Admin panel`
5. Add widget `WP Translit` to sidebar
6. Configure widget `title` and `style` (dropdown list, or links in unordered list)

= WordPress 2.7 way =
1. Login to admin panel of your WP blog and go to `Plugins` -> `Add New`
2. For Search term type `wp-translit` and click on `Search` button
3. Click on link `Install` (column `Actions`)
4. On new opened dialog click on `Install Now`
5. Activate plugin by clicking on link `Activate`
6. Add widget `WP Translit` to sidebar
7. Configure widget `title` and `style` (dropdown list, or links in unordered list)


== Frequently Asked Questions ==

= Which text is tansliterated? =
WP Translit threat only Serbian Cyrillic characters in text and transliterate it to Serbian Latin characters.

= Do this plugin transliterate in opposite way? =
No. WP Translit don't made transliteration from Latin to Cyrillic script. That is not even planned to be implemented.

= Where is help in Serbian language? =
WP Translit page in Serbian Cyrillic is placed on my blog [Zapisi](http://blog.urosevic.net/wordpress/wp-translit/ "WP Translit plugin page").

== Screenshots ==

1. Widget configuration panel
2. Widget `WP Translit` in action
