<?php
if ( !class_exists('WPTranslit_Widget') )
{

class WPTranslit_Widget extends WP_Widget
{

	private $defaults;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		$this->defaults = WP_TRANSLIT::defaults();

		// add widget parameters to defaults
		$this->defaults['cir_str']  = "&#x045b;&#x0438;&#x0440;&#x0438;&#x043b;&#x0438;&#x0446;&#x0430;";
		$this->defaults['lat_str']  = "latinica";
		$this->defaults['page_url'] = WP_TRANSLIT::getCurrentUrl();
		$this->defaults['wpt_lang'] = $this->wpt_lang();

		$_GET['lng'] = 'cir';
		$this->defaults['cir_url'] = http_build_query($_GET);

		$_GET['lng'] = 'lat';
		$this->defaults['lat_url'] = http_build_query($_GET);

		add_action( 'wptranslit', array($this, 'wpt_inline') );
		add_shortcode( 'wptranslit_inline', array($this, 'widget_inline') );

		parent::__construct(
			'wpt', // Base ID
			__( 'WP Translit', 'wpt' ), // Name
			array( 'description' => __( 'Display WP Translit widget so your visitors can select Serbian Cyrillic or Latin script.', 'wpt' ), ) // Args
		);

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance )
	{

		$out = '';
		$out .= $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			$out .= $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		// Which wiget style to print out?
		if ( $instance['style'] == "inline" ) {
			$out .= $this->widget_inline();
		} elseif ( $instance['style'] == "list" ) {
			$out .= $this->widget_list();
		} else {
			$out .= $this->widget_dropdown();
		}

		$out .= $args['after_widget'];

		echo $out;

	} // eom widget

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : $this->defaults['widget_title'];
		$style = ! empty( $instance['style'] ) ? $instance['style'] : $this->defaults['widget_style'];

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Style' ); ?>:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
		<option value="inline" <?php selected($style, "inline"); ?>><?php _e('Inline', 'wpt'); ?></option>
		<option value="list" <?php selected($style, "list"); ?>><?php _e('List', 'wpt'); ?></option>
		<option value="dropdown" <?php selected($style, "dropdown"); ?>><?php _e('Dropdown', 'wpt'); ?></option>
		</select>
		</p>
		<?php
	} // eom form

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['style'] = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : 'list';

		return $instance;
	} // eom update

	/* ====================== HELPERS ====================== */
	function wpt_lang()
	{
		$wpt_lang = '';
		if ( isset($_REQUEST['lng']) ) {
			$wpt_lang = $_REQUEST['lng'];
		} elseif ( isset($_COOKIE['wpt_lang']) ) {
			$wpt_lang = $_COOKIE['wpt_lang'];
		} elseif ( isset($GLOBALS['hdr_lang']) ) {
			$wpt_lang = $GLOBALS['hdr_lang'];
		} else {
			$wpt_lang = $this->defaults['default_lng'];
		}

		return $wpt_lang;
	} // eom wpt_lang

	/* ====================== OUTPUTS ====================== */
	function wpt_inline()
	{
		echo $this->widget_inline();
	} // eom wpt_inline

	function widget_inline()
	{

		// preset empty vars
		$i = $links = Array();

		// get class
		$i['class']  = strip_tags( stripslashes( $this->defaults['inline_class'] ) );

		// get delimiter
		$i['delim']  = stripslashes($this->defaults['inline_delimiter']);

		// get prefix & suffix
		$i['prefix'] = htmlspecialchars_decode( stripslashes( $this->defaults['inline_prefix'] ) );
		$i['suffix'] = htmlspecialchars_decode( stripslashes( $this->defaults['inline_suffix'] ) );

		// Smart Compose Links
		$cir = '<a href="?'.$this->defaults['cir_url'].'">'.$this->defaults['cir_str'].'</a>';
		$lat = '<a href="?'.$this->defaults['lat_url'].'">'.$this->defaults['lat_str'].'</a>';

		// for cir
		if ( $this->defaults['wpt_lang'] == 'cir' && $this->defaults['inline_ashow'] )
		{
			$links[] = "<strong>$cir</strong>";
		} elseif ( $this->defaults['wpt_lang'] == 'lat' ) {
			$links[] = "$cir";
		}

		// for lat
		if ( $this->defaults['wpt_lang'] == 'lat' && $this->defaults['inline_ashow'] )
		{
			$links[] = "<strong>$lat</strong>";
		} elseif ( $this->defaults['wpt_lang'] == 'cir' ) {
			$links[] = "$lat";
		}

		if ( $this->defaults['gt_show'] )
			$links[] = '<a href="http://translate.google.com/translate?prev=_t&amp;ie=UTF-8&amp;sl=sr&amp;tl='.$this->defaults['gt_lang'].'&amp;u='.$this->defaults['page_url'].'">'.$this->defaults['gt_text'].'</a>';

		$i['links'] = implode( $i['delim'], $links );

		// compose output string
		$out = '<div class="wpt_widget ' . $i['class'] . '">' . $i['prefix'] . $i['links'] . $i['suffix'] . '</div>';

		// empty some memory
		unset( $links, $i );

		// return widget content
		return $out;

	} // eom widget_inline

	function widget_list() {

		// Smart Compose Links
		$cir = '<li><a href="?'.$this->defaults['cir_url'].'">';
		$lat = '<li><a href="?'.$this->defaults['lat_url'].'">';

		// Text In Link (bold current script)
		// cir
		if ( $this->defaults['wpt_lang'] == 'cir' )
		{
			$cir .= '<strong>' . $this->defaults['cir_str'] . '</strong>';
		} else {
			$cir .= $this->defaults['cir_str'];
		}
		$cir .= '</a></li>';

		// lat
		if ( $this->defaults['wpt_lang'] == 'lat' )
		{
			$lat .= '<strong>' . $this->defaults['lat_str'] . '</strong>';
		} else {
			$lat .= $this->defaults['lat_str'];
		}
		$lat .= '</a></li>';

		$gtlink = '';
		if ( $this->defaults['gt_show'] )
			$gtlink = '<li><a href="http://translate.google.com/translate?prev=_t&amp;ie=UTF-8&amp;sl=sr&amp;tl=' . $this->defaults['gt_lang'] . '&amp;u=' . $this->defaults['page_url'] . '">'.$this->defaults['gt_text'].'</a></li>';

		$out = <<<EOF
	<ul>
	$cir$lat$gtlink
	</ul>
EOF;

		return $out;

	} // eom widget_list()

	function widget_dropdown()
	{

		$lc = $cc = false;
		switch($this->defaults['wpt_lang']) {
			case "lat": $lc = 'selected="selected"'; break;
			default:    $cc = 'selected="selected"';
		}

		$out = <<<EOF
	<form action="" method="post" class="wpt_widget wpt_dropdown"><fieldset>
	<select name="lng" onchange="this.form.submit()">
	<option value="cir" $cc>{$this->defaults['cir_str']}</option>
	<option value="lat" $lc>{$this->defaults['lat_str']}</option>
	</select>
	</fieldset></form>
EOF;

		return $out;

	} // eom widget_dropdown

} // eo class WPTranslit_Widget

} // eo class exists

add_action( 'widgets_init', function(){
	register_widget( 'WPTranslit_Widget' );
});

?>