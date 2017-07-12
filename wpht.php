<?php
/*
* Plugin Name: Hand Talk - WordPress
* Plugin URI: http://www.handtalk.me/sites
* Description: Hand Talk - Tradutor automático do português para Libras. Conheça os outros produtos de acessibilidade para surdos no site <a href="http://handtalk.me" target="_blank">http://handtalk.me</a>
* Author: Hand Talk
* Version: 1.0
* Author URI: http://www.handtalk.me
* Text Domain: wpht
* Domain Path: /languages/
*/

include_once('settings.class.php');
include_once('wpht.class.php');

function wphtInit(){
	new WPHTSettings();
	new WPHT();
}
add_action("init", "wphtInit");
