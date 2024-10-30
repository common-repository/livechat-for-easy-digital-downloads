<?php

namespace LiveChatEdd;

class LiveChatEdd {

	protected $pluginVersion = null;
	protected $pluginPath = null;
	public $pluginFilesURL = null;
	public $license = false,
		$email = null,
		$cartDetails= true,
		$cartContent = null,
		$disableMobile = false,
		$disableGuests = false,
		$isMobile = false,
		$isLogged = false,
		$userName = null,
		$userEmail = null,
		$eddSlug = 'downloads';

	function __construct() {
		$this->getPluginFiles();
		$this->getPluginVersion();
		$this->getSettings();
		$this->checkMobile();
		$this->checkLogged();
		$this->getCart();
		add_action('wp_ajax_livechat_edd_update_cart', array($this, 'updateCart'));
		add_action('wp_ajax_nopriv_livechat_edd_update_cart', array($this, 'updateCart'));
	}

	protected function getPluginVersion() {
		if(!function_exists('get_plugin_data'))
			require_once(ABSPATH.'wp-admin/includes/plugin.php');
		$version = get_plugin_data($this->pluginPath.'livechat-easydigitaldownloads.php');
		$this->pluginVersion = $version['Version'];
	}

	protected function getSettings() {
		$this->license = get_option('livechat_edd_license');
		$this->email = get_option('livechat_edd_email');
		$this->cartDetails= get_option('livechat_edd_cartDetails');
		$this->disableMobile = get_option('livechat_edd_disableMobile');
		$this->disableGuests = get_option('livechat_edd_disableGuests');
	}

	public function tracking_code() {
		$this->loadTemplate('TrackingCode');
	}

	protected function getPluginFiles() {
		if (is_null($this->pluginFilesURL)) {
			$this->pluginFilesURL = plugins_url('/', __FILE__);
			$this->pluginPath = WP_PLUGIN_DIR.'/livechat-for-easy-digital-downloads/';
		}
	}

	protected function loadTemplate( $template, $render=1 ) {

		if (class_exists($template) == false)
		{
			$templateClass = $this->pluginPath.'includes/templates/'.$template.'.class.php';
			if (file_exists($templateClass) !== true)
			{
				return false;
			}

			require_once($templateClass);
		}

		$template = '\LiveChatEdd\\'.$template;

		$class = new $template;

		if ($render)
		{
			echo $class->render();
			return true;
		}
		else
		{
			return $class->render();
		}

	}

	private function checkMobile() {
		$userAgent = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$regex = '/((Chrome).*(Mobile))|((Android).*)|((iPhone|iPod).*Apple.*Mobile)|((Android).*(Mobile))/i';
		$this->isMobile = preg_match($regex, $userAgent);
	}

	public function getUserDetails() {
		$currentUser = wp_get_current_user();
		$this->userEmail = $currentUser->user_email;
		if (!empty($currentUser->user_firstname) && !empty($currentUser->user_lastname))
			$this->userName = $currentUser->user_firstname.' '.$currentUser->user_lastname;
		else
			$this->userName = $currentUser->user_login;

		return array(
			'name' => $this->userName,
			'email' => $this->userEmail
		);
	}

	public function checkLogged() {
		if (get_current_user_id()) {
			$this->isLogged = true;
			$this->getUserDetails();
		} else
			$this->isLogged = false;

		return $this->isLogged;
	}

	public function getCart() {
		$cartContent = edd_get_cart_contents();

		if(defined('EDD_SLUG') && EDD_SLUG !== 'downloads' && EDD_SLUG !== 'EDD_SLUG')
			$this->eddSlug = EDD_SLUG;

		$cart = array();
		$cart['total'] = 0;

		foreach ($cartContent as $key => $id) {
			$itemDetails = edd_get_download($id['id']);
			if(!empty($id['options'])) {
				$optionName = ' ('.edd_get_price_option_name($id['id'], $id['options']['price_id']).')';
				$cart[ $id['id'] ][ $id['options']['price_id'] ]['name'] = addslashes($itemDetails->post_title.$optionName);
				$cart[ $id['id'] ][ $id['options']['price_id'] ]['link'] = get_site_url().'/'.$this->eddSlug.'/'.$itemDetails->post_name.'/';
				$cart[ $id['id'] ][ $id['options']['price_id'] ]['quantity'] += $id['quantity'];
				$price = edd_price($id['id'], false, $id['options']['price_id']);
				$price = preg_replace('/<[^>]*>/','',$price);
				if(empty($cart['currency'])) {
					preg_match('/&[^;]*;/', $price, $cart['currency']);
					$cart['currency'] = html_entity_decode($cart['currency'][0]);
				}
				$price = preg_replace('/&[^;]*;/', '', $price);
				$cart['total'] += $price;
				$cart[ $id['id'] ][ $id['options']['price_id'] ]['price'] = $price;
			} else {
				$cart[ $id['id'] ]['name'] = addslashes($itemDetails->post_title);
				$cart[ $id['id'] ]['link'] = get_site_url().'/'.$this->eddSlug.'/'.$itemDetails->post_name.'/';
				$cart[ $id['id'] ]['quantity'] += $id['quantity'];
				$price = edd_price($id['id'], false);
				$price = preg_replace('/<[^>]*>/','',$price);
				if(empty($cart['currency'])) {
					preg_match('/&[^;]*;/', $price, $cart['currency']);
					$cart['currency'] = html_entity_decode($cart['currency'][0]);
				}
				$price = preg_replace('/&[^;]*;/', '', $price);
				$cart['total'] += $price;
				$cart[ $id['id'] ]['price'] = $price;
			}
		}

		$cart['total'] = number_format((float)$cart['total'], 2, '.', '');

		$this->cartContent = $cart;
	}

	public function updateCart() {

		$result = array();

		if ($_REQUEST['action'] === 'livechat_edd_update_cart') {

			$this->getCart();

			$position = edd_get_option( 'currency_position', 'before' );

			$result['type'] = "success";

			$newCart = array();
			foreach ($this->cartContent as $key=>$item) {
				if($key !== 'total' && $key !== 'currency') {
					if(isset($item['name'])) {
						$quantity = '';

						if($item['quantity'] > 1)
							$quantity = $item['quantity'].'x ';

						$newCart[] = array(
							"name" => $quantity.stripslashes($item['name']),
							"value" => $item['link']
						);
					} else {
						foreach ($item as $option) {
							$quantity = '';

							if($option['quantity'] > 1)
								$quantity = $option['quantity'].'x ';

							$newCart[] = array(
								"name" => $quantity.stripslashes($option['name']),
								"value" => $option['link']
							);
						}
					}
				}
			}

			if ($position === 'before') {
				$total = $this->cartContent['currency'].$this->cartContent['total'];
			} else {
				$total = $this->cartContent['total'].$this->cartContent['currency'];
			}

			$newCart[] = array(
				"name" => "Total",
				"value" => $total
			);

			$result['cart'] = $newCart;


		} else {

			$result['type'] = "error";
			$result['message'] = 'Wrong action';

		}

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$result = json_encode($result);
			echo $result;
		}
		else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();
	}

}