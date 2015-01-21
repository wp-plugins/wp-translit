<?php
if(!class_exists('WPAU_WP_TRANSLIT_SETTINGS'))
{
	class WPAU_WP_TRANSLIT_SETTINGS extends WP_TRANSLIT
	{
		private $slug;

		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{

			$this->slug = WPT_SLUG;

			// register actions
			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_menu'));

		} // END public function __construct

		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{
			// get default values
			$defaults = WP_TRANSLIT::defaults();

			// register plugin's settings
			// TODO: validate and sanitize inputs for symbols, error_message and cache_timeout
			register_setting('general_settings', "wptranslit");
			register_setting('widget_settings', "wptranslit");

			// add general settings section
			add_settings_section(
				'general_settings',
				__('General Options','wpt'),
				array(&$this, 'settings_section_general'),
				$this->slug
			);

			// add setting's fields
			add_settings_field(
				'wptranslit-default_lng',
				__('Default Language','wpt'),
				array(&$this, 'settings_field_select'),
				$this->slug,
				'general_settings',
				array(
					'field'       => "wptranslit[default_lng]",
					'description' => __('What script to use as fallback when no language is set in browser','wpt'),
					'items'       => array(
						"cir"   => __("Cyrillic",'wpt'),
						"lat" => __("Latin",'wpt')
					),
					'value' => $defaults['default_lng'],
				)
			);

			// Show GT link
			add_settings_field(
				'wptranslit-gt_show',
				__('Include Google Translate Link','wpt'),
				array(&$this, 'settings_field_checkbox'),
				$this->slug,
				'general_settings',
				array(
					'field'       => "wptranslit[gt_show]",
					'class'       => 'widefat',
					'value'       => $defaults['gt_show'],
					'description' => __('Enable this option to display Google Translate link. Does not work with Dropdown box widget style.','wpt')
				)
			);
			// GT link title
			add_settings_field(
				'wptranslit-gt_text',
				__('Google Translate Link Title','wpt'),
				array(&$this, 'settings_field_input_text'),
				$this->slug,
				'general_settings',
				array(
					'field'       => "wptranslit[gt_text]",
					'description' => __('Set text for Google Translate link.','wpt'),
					'class'       => 'wide-text',
					'value'       => $defaults['gt_text'],
				)
			);
			// GT target language
			add_settings_field(
				'wptranslit-gt_lang',
				__('Google Translate Target Language','wpt'),
				array(&$this, 'settings_field_select'),
				$this->slug,
				'general_settings',
				array(
					'field'       => "wptranslit[gt_lang]",
					'description' => __('Which language you wish to offer as target language to website visitors?','wpt'),
					'items'       => array(
						'zh-CN' => __('Chinese (Simplified)','wpt'),
						'zh-TW' => __('Chinese (Traditional)','wpt'),
						'en' => __('English','wpt'),
						'fr' => __('French','wpt'),
						'de' => __('German','wpt'),
						'el' => __('Greek','wpt'),
						'hu' => __('Hungarian','wpt'),
						'it' => __('Italian','wpt'),
						'ko' => __('Korean','wpt'),
						'ru' => __('Russian','wpt'),
						'es' => __('Spanish','wpt'),
						'tu' => __('Turkish','wpt'),
					),
					'value' => $defaults['gt_lang'],
				)
			);

			// add widget settings section
			add_settings_section(
				'widget_settings',
				__('Widget Defaults','wpt'),
				array(&$this, 'settings_section_widget'),
				$this->slug
			);

			// Widget Title
			add_settings_field(
				'wptranslit-widget_title',
				__('Default Widget Title','wpt'),
				array(&$this, 'settings_field_input_text'),
				$this->slug,
				'widget_settings',
				array(
					'field'       => "wptranslit[widget_title]",
					'description' => __('Set default widget title for new widgets','wpt'),
					'class'       => 'regular-text',
					'value'       => $defaults['widget_title'],
				)
			);
			// Widget Style
			add_settings_field(
				'wptranslit-widget_style',
				__('Widget Style','wpt'),
				array(&$this, 'settings_field_select'),
				$this->slug,
				'widget_settings',
				array(
					'field'       => "wptranslit[widget_style]",
					'description' => __("What to show in widget? You'll be able to override this global setting per widget.",'wpt'),
					'items'       => array(
						'inline' => __('Inline','wpt'),
						'list'   => __('Unordered list','wpt'),
						'drop'   => __('Dropdown box','wpt')
					),
					'value' => $defaults['widget_style'],
				)
			);

			// Inline DIV class
			add_settings_field(
				'wptranslit-inline_class',
				__('Inline Style DIV class','wpt'),
				array(&$this, 'settings_field_input_text'),
				$this->slug,
				'widget_settings',
				array(
					'field'       => "wptranslit[inline_class]",
					'description' => __('CSS class can be used in style.css to style inline links box.','wpt'),
					'class'       => 'regular-text',
					'value'       => $defaults['inline_class'],
				)
			);
			// Inline prefix
			add_settings_field(
				'wptranslit-inline_prefix',
				__('Inline Style prefix','wpt'),
				array(&$this, 'settings_field_input_text'),
				$this->slug,
				'widget_settings',
				array(
					'field'       => "wptranslit[inline_prefix]",
					'description' => __('Text to put in front of inline links. HTML allowed.','wpt'),
					'class'       => 'regular-text',
					'value'       => $defaults['inline_prefix'],
				)
			);
			// Inline suffix
			add_settings_field(
				'wptranslit-inline_suffix',
				__('Inline Style suffix','wpt'),
				array(&$this, 'settings_field_input_text'),
				$this->slug,
				'widget_settings',
				array(
					'field'       => "wptranslit[inline_suffix]",
					'description' => __('Additional text or links to append behind inline links. HTML allowed.','wpt'),
					'class'       => 'regular-text',
					'value'       => $defaults['inline_suffix'],
				)
			);
			// Inline delimiter
			add_settings_field(
				'wptranslit-inline_delimiter',
				__('Inline Style delimiter','wpt'),
				array(&$this, 'settings_field_input_text'),
				$this->slug,
				'widget_settings',
				array(
					'field'       => "wptranslit[inline_delimiter]",
					'description' => __('Set of characters to put in the middle of links. HTML not allowed.','wpt'),
					'class'       => 'wide-text',
					'value'       => $defaults['inline_delimiter'],
				)
			);
			// Show Active
			add_settings_field(
				'wptranslit-inline_ashow',
				__('Show Active Link','wpt'),
				array(&$this, 'settings_field_checkbox'),
				$this->slug,
				'widget_settings',
				array(
					'field'       => "wptranslit[inline_ashow]",
					'class'       => 'widefat',
					'value'       => $defaults['inline_ashow'],
					'description' => __('Enable this option to display current script link. <em>(Applicable only to Inline Style)</em>','wpt')
				)
			);

		} // eom public admin_init

		public function settings_section_general()
		{
			// Think of this as help text for the section.
			echo __('In this section you can set general plugin settings, used in all WP Translit widgets.','wpt');
		}
		public function settings_section_widget()
		{
			// Think of this as help text for the section.
			echo __('Here you can configure interface of WP Translit widgets. This will be used as defaults for new widgets.','wpt');
		}

		/* ============================ CALLBACKS ============================ */
		/**
		 * This function provides text inputs for settings fields
		 */
		public function settings_field_input_text($args)
		{
			extract( $args );
			echo sprintf('<input type="text" name="%s" id="%s" value="%s" class="%s" /><p class="description">%s</p>', $field, $field, $value, $class, $description);
		} // END public function settings_field_input_text($args)

		/**
		 * This function provides checkbox for settings fields
		 */
		public function settings_field_checkbox($args)
		{
			extract( $args );
			$checked = ( !empty($args['value']) ) ? 'checked="checked"' : '';
			echo sprintf('<label for="%s"><input type="checkbox" name="%s" id="%s" value="1" class="%s" %s />%s</label>', $field, $field, $field, $class, $checked, $description);
		} // END public function settings_field_checkbox($args)

		/**
		 * This function provides radio buttons for settings fields
		 */
		public function settings_field_radio($args)
		{
			extract( $args );
			$html = '';
			foreach ($items as $key=>$val)
			{
				$checked = ($value==$key) ? 'checked="checked"' : '';
				$html .= sprintf(
					'<label for="%s_%s"><input type="radio" name="%s" id="%s_%s" value="%s" %s>%s</label><br />',
					$field, $key,
					$field,
					$field, $key,
					$key,
					$checked,
					$val
				);
			}
			// $html .= sprintf('</select><p class="description">%s</p>',$description);
			$html .= sprintf('<p class="description">%s</p>',$description);
			echo $html;

		} // END public function settings_field_checkbox($args)

		/**
		 * This function provides textarea for settings fields
		 */
		public function settings_field_textarea($args)
		{
			extract( $args );
			if (empty($rows)) $rows = 7;
			echo sprintf('<textarea name="%s" id="%s" rows="%s" class="%s">%s</textarea><p class="description">%s</p>', $field, $field, $rows, $class, $value, $description);
		} // END public function settings_field_textarea($args)

		/**
		 * This function provides select for settings fields
		 */
		public function settings_field_select($args)
		{
			extract( $args );
			$html = sprintf('<select id="%s" name="%s">',$field,$field);
			foreach ($items as $key=>$val)
			{
				$selected = ($value==$key) ? 'selected="selected"' : '';
				$html .= sprintf('<option %s value="%s">%s</option>',$selected,$key,$val);
			}
			$html .= sprintf('</select><p class="description">%s</p>',$description);
			echo $html;
		} // END public function settings_field_select($args)

		public function settings_field_colour_picker($args)
		{
			extract( $args );
			$html = sprintf('<input type="text" name="%s" id="%s" value="%s" class="wpau-color-field" />',$field, $field, $value);
			$html .= (!empty($description)) ? ' <p class="description">'.$description.'</p>' : '';
			echo $html;
		} // END public function settings_field_colour_picker($args)

		/**
		 * add a menu
		 */
		public function add_menu()
		{
			// Add a page to manage this plugin's settings
			add_options_page(
				__('WP Translit Options','wpaust'),
				__('WP Translit','wpaust'),
				'manage_options',
				$this->slug,
				array(&$this, 'plugin_settings_page')
			);
		} // END public function add_menu()

		/**
		 * Menu Callback
		 */
		public function plugin_settings_page()
		{

			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			// Render the settings template
			include('settings_template.php');

		} // END public function plugin_settings_page()
	} // END class WPAU_WP_TRANSLIT_SETTINGS
} // END if(!class_exists('WPAU_WP_TRANSLIT_SETTINGS'))

new WPAU_WP_TRANSLIT_SETTINGS;
