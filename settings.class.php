<?php
class WPHTSettings {
	public function __construct(){
		add_action( 'admin_menu', array( &$this, 'menu' ) );
		add_action( 'admin_init', array( &$this, 'admin' ) );
		add_action( 'admin_init', array( &$this, 'admin_register_styles' ) );
	}

	function admin_register_styles(){
		wp_register_style( 'wpht-style', plugins_url('css/style.css', __FILE__), array(), '1.0', 'screen' );
		wp_register_script( 'wpht-script', plugins_url( 'js/handtalk.js', __FILE__ ), array('jquery'), '1.0', true );
	}

	public function admin_enqueue_styles() {
		wp_enqueue_style( 'wpht-style' );
	}
	
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'wpht-script' );
	}

	public function menu() {
		$page = add_options_page( __( 'Hand Talk', 'wpht' ), __( 'Hand Talk', 'wpht' ), 'manage_options', 'wpht', array( &$this, 'page' ) );
		add_action( 'admin_print_styles-' . $page, array( &$this, 'admin_enqueue_styles' ) );
		add_action( 'admin_print_scripts-' . $page, array( &$this, 'admin_enqueue_scripts' ) );
	}

	public static function getSettings() {
		$defaults = array(
			'token' => '',
			'exceptions' => '',
		);
		return wp_parse_args( get_option( 'wpht' ), $defaults );
	}

	public function admin() {
		$settings = WPHTSettings::getSettings();
		register_setting( 'wpht-group', 'wpht', array( &$this, 'wpht_validate' ) );
		add_settings_section( 'wpht_section', __( 'Hand Talk Configuration', 'wpht' ), array( &$this, 'wpht_section' ), 'wpht' );
		add_settings_field( 'wpht_token', __( 'Security Token', 'wpht' ), array( &$this, 'text_input' ), 'wpht', 'wpht_section', array(
				'name' => 'token',
				'value' => $settings['token'],
			)
		);
		add_settings_field( 'wpht_latest', '', array( &$this, 'text_checkbox' ), 'wpht', 'wpht_section', array(
				'name' => 'latest',
				'value' => $settings['latest'],
				'text' => __( 'Use latest version', 'wpht' ),
			)
		);
		add_settings_field( 'wpht_versions', __( 'Version', 'wpht' ), array( &$this, 'text_input' ), 'wpht', 'wpht_section', array(
				'name' => 'version',
				'value' => $settings['version'],
				'helper' => __( 'Available versions: 1.0, 1.1. Please use just one.', 'wpht' ),
			)
		);
		add_settings_field( 'wpht_exceptions', __( 'Exceptions', 'wpht' ), array( &$this, 'text_input' ), 'wpht', 'wpht_section', array(
				'name' => 'exceptions',
				'value' => $settings['exceptions'],
				'helper' => __( 'Comma separated options', 'wpht' ),
			)
		);
	}

	public function wpht_section() {
		?>
		<p><?php echo sprintf( __( 'To subscribe and get your token, go to <a href="%1s" target="_blank">Hand Talk</a> website', 'wpht' ), 'http://www.handtalk.me/sites' ) ?></p> 
		<?php
	}

	public function page() {
		global $title;
		?>
		<div class="wrap handtalk">
			<div class="icon32" id="icon-wpht"><br></div>
			<h2><?php echo $title; ?></h2>
			<form action="options.php" method="POST">
				<div class="metabox-holder" id="poststuff">
					<div id="post-body-content">
						<div class="meta-box-sortables">
							<div class="postbox">
								<div class="settings">
									<img src="<?php echo plugins_url('images/computer.png', __FILE__)?>" />
									<?php settings_fields( 'wpht-group' ); ?>
									<?php do_settings_sections( 'wpht' ); ?>
								</div>
								<div style="clear:both"></div>
								<p><?php echo sprintf( __('For further technical info, go to <a href="%1s">api.handtalk.me/info</a>', 'wpht'), 'http://api.handtalk.me/info')?></p>
							</div>
						</div>
					</div>
					<?php submit_button(); ?>
				</div>
			</form>
		</div>
		<?php
	}

	public function wpht_validate( $input ) {
		$output = get_option( 'wpht' );
		if ( !empty( $input['token'] ) ) {
			$output['token'] = $input['token'];
		} else {
			add_settings_error( 'wpht', 'invalid-token', __( 'Invalid security token', 'wpht' ) );
		}
		if( $input['latest'] == "" ){
			$output['latest'] = "0";
			if ( !empty( $input['version'] ) ) {
				$output['version'] = $input['version'];
			} else {
				add_settings_error( 'wpht', 'invalid-version', __( 'Version must be filled', 'wpht' ) );
			}
		}else{
			$output['latest'] = "1";
			$output['version'] = '';
		}
		$output['exceptions'] = $input['exceptions'];
		return $output;
	}

	public function text_input( $args ) {
		$defaults = array(
			'type' => 'text',
			'helper' => '',
		);
		$args = wp_parse_args( $args, $defaults );
		$name = esc_attr( $args['name'] );
		$value = esc_attr( $args['value'] );
		$type = esc_attr( $args['type'] );
		$helper = esc_attr( $args['helper'] );
		echo '<input type="' . $type . '" class="widefat" name="wpht[' . $name . ']" value="' . $value . '" />';
		if ($helper != ''){
			echo '<p class="description">' . $helper . '</p>';
		}
	}

	public function text_checkbox( $args ) {
		$defaults = array(
			'type' => 'text',
			'helper' => '',
		);
		$args = wp_parse_args( $args, $defaults );
		$name = esc_attr( $args['name'] );
		$value = esc_attr( $args['value'] );
		$text = esc_attr( $args['text'] );
		$helper = esc_attr( $args['helper'] );
		echo '<label><input type="checkbox" name="wpht[' . $name . ']" value="1"' . ($value == "1" || $value == "" ? ' checked="checked"' : '' )  . ' />' . ' ' . $text . '</label>';
		if ($helper != ''){
			echo '<p class="description">' . $helper . '</p>';
		}
	}
}
