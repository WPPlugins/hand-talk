<?php

class WPHT {
	public function __construct() {
		load_plugin_textdomain( 'wpht', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		add_action( 'wp_footer', array( &$this, 'wp_footer' ) );
		add_filter( 'plugin_action_links', array( &$this, 'wpht_plugin_action_links' ), 10, 2 );
	}

	public function wpht_plugin_action_links( $links, $file ) {
		if ( untrailingslashit( plugins_url( '', $file ) ) != untrailingslashit( plugins_url( '', __FILE__ ) ) )
			return $links;
		$settings_link = '<a href="' . menu_page_url( 'wpht', false ) . '">'
			. esc_html( __( 'Settings', 'wpht' ) ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

	public function wp_footer() {
		$settings = WPHTSettings::getSettings();
		if( $settings['token'] != '' ) {
			if($settings['latest'] == "1" || $settings['latest'] == ""){
				$url = "http://api.handtalk.me/handtalk_init.js";
			}else{
				$url = "http://api.handtalk.me/libs/" . $settings['version'] . "/handtalk.min.js ";
			}
			?>
			<script type="text/javascript">
			var _hta = {'_setToken': '<?php echo $settings['token']?>','_htException': '<?php echo $settings['exceptions'] == "" ? "HandTalk_EXCECAO" : $settings['exceptions']?>'};
			(function() {
			var ht = document.createElement('script'); ht.type = 'text/javascript'; ht.async = true;
			ht.src = '<?php echo $url; ?>';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.appendChild(ht);
			})();
			</script>
			<?php
		}
	}
}