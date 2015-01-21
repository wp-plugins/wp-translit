<div class="wrap" id="wp_translit_settings">
	<h2><?php _e( 'WP Translit Settings', 'wpt' ); ?></h2>
	<form method="post" action="options.php">
		<?php

		@settings_fields('general_settings');
		@settings_fields('widget_settings');

		@do_settings_sections(WPT_SLUG);

		@submit_button();

		?>
	</form>
    <h3><?php _e( 'Help', 'wpt' ); ?></h3>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="float:right">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="RQZS3U57E7F5U">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<p><?php _e('WP Translit plugin displays links to transliterate text from Serbian Cyrillic to Serbian Latin script, or translate it with Google Translate to selected language.', 'wpt'); ?></p>

<p><?php _e('If you wish to display inline links programatically, embed following code (outside of Loop) to tempalte file.', 'wpt' ); ?></p>
<code>&lt;?php do_action("wptranslit"); ?&gt;</code>

<p><?php _e('You can also use shortcode to insert inline links in page content (but, avoid that if you already use widgets).', 'wpt'); ?></p>
<code>[wptranslit_inline]</code>

<p><?php
	_e(
	sprintf(
		"For all questions, feature requests and communication with author and users of this plugin, use %s.",
		sprintf(
			'<a href="https://wordpress.org/support/plugin/wp-translit" target="_blank">%s</a>',
			__('community forum','wpt')
		)
	),
	'wpt');
	_e( ' '.sprintf('Learn more about plugin on %s.', '<a href="http://urosevic.net/wordpress/plugins/wp-translit/" target="_blank">Urosevic DevYard</a>'), 'wpt');
	?></p>


</div>