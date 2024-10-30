<?php
/**
 * Plugin Name:  LiveChat for Easy Digital Downloads
 * Plugin URI:   https://livechatinc.com/
 * Description:  Live chat software for live help, online sales and customer support. This plugin allows to quickly install LiveChat on any Easy Digital Downloads website.
 * Version:      1.4.2
 * Copyright:    © 2018 LiveChat
 * Author:       LiveChat <support@livechatinc.com>
 * Author URI:   https://www.livechatinc.com/
 * License:      GNU General Public License v3.0
 * License URI:  http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:  livechat-for-easy-digital-downloads
 * Domain Path: /languages
*/

if ( in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	if (is_admin())
	{
		require_once(dirname(__FILE__).'/includes/LiveChatEddAdmin.class.php');
		new \LiveChatEdd\LiveChatEddAdmin();
	}
	else
	{
		require_once(dirname(__FILE__).'/includes/LiveChatEdd.class.php');
		$LiveChatEdd = new \LiveChatEdd\LiveChatEdd();
		add_action('wp_footer', array($LiveChatEdd, 'tracking_code'), 100000);
	}
}
