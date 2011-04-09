=== WP Translit ===
Contributors: urkekg
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Q6Q762MQ97XJ6
Tags: language, automatic, transliteration, translation, translate, translit, sidebar, widget, plugin, serbian, cyrillic, latin, script, multilanguage
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.3.8

Transliterate text in posts and pages from Serbian Cyrillic to Latin script. Visitor can select output script from widget.

== Description ==

When visitor enter some site written in Serbian Cyrillic script, WP Translit can automatically or on demand transliterate that site to Latin script.

This is performed by checking `Accept Language HTTP headers`. If visitor don't have Serbian, Macedonian, Bulgarian or Russian language set as preferred in web browser, WP Translit will display blog in Latin script.

If visitor wish Cyrillic or Latin script on demand, then he can make choose from widget (unordered list or dropdown list) or inline links placed on some other place.

= Features =

* All transliteation is in one way: Cyrillic → Latin.
* Transliterate all text on pages, including widget titles, header and footer, posts, pages, comments and feeds.
* Automatically display original (Cyrillic) script to visitors with Serbian, Macedonian, Bulgarian and Russian as preferred language set in web browser.
* Automatically display Latin script for all other languages (including Croatian, Slovenian, English and others).
* Choose style of widget - dropdown list or unordered list.
* Place inline links by adding `do_action("wptranslit")` anywhere in template.
* Set custom widget title.
* Can display link to translate page to English, German, French or Russian language with Google Translate.
* WP Translit support `gettext` localisation.

= Српски =

Када посетилац отвори веб страницу написану српским ћириличним писмом, додатак WP Translit може аутоматски или на захтев посетиоца да преслови текст у латинично писмо.

Ово је омогућено провером HTTP заглавља `Accept Language`. Ако посетилац нема постављен српски, македонски, бугарски или руски језик као подразумевани језик у веб прегледачу, WP Translit ће приказати блог латиничним писмом.

Ако посетилац жели да чита ћирилични или латинични текст, то једноставно може да изабере у виџету или у једнолинијским везама на другом месту на страници.

= Могућности =

* Пресловљавање се обавља само у из латинице у ћирилицу. Обрнуто није подржано.
* Пресловљава се сав текст на странама, укључујући и виџете, наслове, заглавље, подножје, чланке, странице, коментари и доводи.
* Аутоматски се приказује ћирилични (изворни) текст посетиоцима којима је у веб прегледачу постављен српски, македински, бугарски или руски језик.
* Изаберите весту виџета - падајућа листа или набрајање.
* Поставља једнолинијске везе додавањем кода `do_action("wptranslit")` било где у шаблону.
* Кориснички дефинисан наслов виџета
* Може да прикаже везу за превод странице на енглески, немачки, француски или руски језик помоћу сервиса Google Translate.
* WP Translit може да се локализује помоћу система `gettext`.

== Installation ==

1. Login to admin panel of your WP blog and go to `Plugins` → `Add New`
2. Enter `wp-translit` and click on `Search` button
3. Click on link `Install` bellow plugin name `WP Translit`
4. Activate plugin by clicking on link `Activate`
5. Go to `Settings` → `WP Translit` to configure plugin
6. Add widget `WP Translit` to sidebar or `<?php do_action("wptranslit"); ?>` in template

= Српски =
1. Улогујте се на свој Вордпрес блог и отворите страницу `Додаци` → `Додај нови`
2. Унесите `wp translit` у поље и кликните на дугме `Претражи додатке`
3. Кликните на везу `Поставите сада` испод назива додатка `WP Translit`
4. Укључите додатак кликом на везу `Укључи додатак`
5. Подесите додатак на страници `Подешавања` → `WP Translit`
6. Додајте виџет `WP Translit` или уметните код `<?php do_action("wptranslit"); ?>` у шаблон

== Frequently Asked Questions ==

= Which text is tansliterated? =
WP Translit threat only Serbian Cyrillic characters in text and transliterate it to Serbian Latin characters.

= Do this plugin transliterate in opposite way? =
No. WP Translit don't made transliteration from Latin to Cyrillic script. That is not even planned to be implemented.

= Where is help in Serbian language? =
WP Translit page in Serbian Cyrillic is placed on my blog [Zapisi](http://blog.urosevic.net/wordpress/wp-translit/ "WP Translit plugin page").

== Changelog ==

= 0.3.9 (2011-04-09) =
* Fixed all debug errors
* Added option to hide active script link in inline links
* Added custom class, prefix and suffix to inline links

= 0.3.8 (2011-03-05) =
* Added support for inline links
* Added custom delimiter for inline list
* Added custom Google Translate target language with custom link text (list and inline, dropdown has not supoprted)
* Moved settings from widget to plugin page

= 0.3.7.1 (2009-03-04) =
* Fixed code that broke title rewrite for All-in-One-SEO
	
= 0.3.7 (2009-02-28) =
* Fixed handling of default permalink broken in 0.3.6

= 0.3.6 (2009-02-28) =
* Fixed 'headers already sent' problem (I think so?)
* Fixed wrong handling of default permalink
* Dropped floating widget part (do we really need this? I think no.)

= 0.3.5a (2008-09-15) =
* Added transliteation to feeds

= 0.3.5 (2008-09-14) =
* New public release 0.3.5
* Floating widget CSS fixed

= 0.3.4 internal (2008-09-13) =
* Added Floating widget settings (on/off on: home, single, page, archive and search)
* Unified function and variable names
* Added gettext localisation

= 0.3.3 internal (2008-09-10) =
* Added Floating widget for non-index (w/o sidebar) pages
* Removed YUSCII script (now only Cyrillic and Latin)

= 0.3.2 internal (2008-09-08) =
* Added configurable widget - manual set title and select widget style (list or links) (thanks to http://lonewolf-online.net/computers/wordpress/create-widgets-control-panels/)
* Added transliteration of gettextable objects (thanks to http://kimmo.suominen.com/sw/srlatin/)
* Added transliteration based on Accept Language HTTP header if there not cookie or request language (thanks to Danilo Šegan for pointing on this)
* Simplified function for generating widhets
* Rewritten buffer transliteration code
* No more need for manual code inserting in header.php template
* Added specified two character transliteration pairs (Dž*, Nj* and Lj*)

= 0.3.1 (2008-06-17) =
* Adding first version to WordPress SVN
* Changed first option title to „изворно (ћирилица)“
* Enabled YUSCII to Latin script conversion

= 0.3 (2008-06-16) =
* Initial public release 0.3

== Screenshots ==

1. WP Translit on Plugins page
2. WP Translit Settings page
3. WP Translit Widget panel
4. WP Translit in action
