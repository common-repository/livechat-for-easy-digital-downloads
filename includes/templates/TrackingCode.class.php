<?php

namespace LiveChatEdd;

require_once( 'Template.class.php' );

class TrackingCode extends Template
{

	public $cart = '';

	public function __construct() {

	    add_action('wp_ajax_edd_add_to_cart', array($this, 'addCartDetails'));
	    add_action('wp_ajax_nopriv_edd_add_to_cart', array($this, 'addCartDetails'));
    }

	private function addCartDetails() {

		$LiveChatEdd = new LiveChatEdd;

		$LiveChatEdd->getCart();

		$position = edd_get_option( 'currency_position', 'before' );

		$this->cart = 'window.__lc.params = [';

		foreach ($LiveChatEdd->cartContent as $key=>$item) {
			if($key !== 'total' && $key !== 'currency') {
				if(isset($item['name'])) {

					$quantity = '';
					if($item['quantity'] > 1)
						$quantity = $item['quantity'].'x ';

					$this->cart .= "
        { name: '".$quantity.$item['name']."', value: '".$item['link']."' },";
				} else {
					foreach ($item as $option) {
						$quantity = '';
						if($option['quantity'] > 1)
							$quantity = $option['quantity'].'x ';

						$this->cart .= "
        { name: '".$quantity.$option['name']."', value: '".$option['link']."' },";
					}
				}
			}
		}
		$this->cart .= "
        { name: 'Total', value: '";
		if (is_array($LiveChatEdd->cartContent['currency'])) $LiveChatEdd->cartContent['currency'] = '';
		if ($position === 'before') {
			$this->cart .= $LiveChatEdd->cartContent['currency'].$LiveChatEdd->cartContent['total'];
		} else {
			$this->cart .= $LiveChatEdd->cartContent['total'].$LiveChatEdd->cartContent['currency'];
		}
		$this->cart .= "' }
    ];
    ";
    }

	public function render()
	{

		$LiveChatEdd = new LiveChatEdd;

		if ($LiveChatEdd->license )
		{

		    if (!$LiveChatEdd->disableMobile || ($LiveChatEdd->disableMobile && !$LiveChatEdd->isMobile) ) {

			    if (!$LiveChatEdd->disableGuests || ($LiveChatEdd->disableGuests && $LiveChatEdd->isLogged) ) {

				    $userDetails = '';

			        if(isset($LiveChatEdd->userName) && isset($LiveChatEdd->userEmail)) {
				        $userDetails = <<<DETAILS
window.__lc.visitor = {
        name: '{$LiveChatEdd->userName}',
        email: '{$LiveChatEdd->userEmail}'
    };

DETAILS;
			        }

			        $this->cart ='';

                    if ($LiveChatEdd->cartDetails ) {
                        $this->addCartDetails();
                    }

?>

<!-- Start of LiveChat (www.livechatinc.com) code -->
<script type="text/javascript">
    window.__lc = window.__lc || {};
    window.__lc.license = <?php echo $LiveChatEdd->license; ?>;
    <?php echo $this->cart; ?>
<?php echo $userDetails?>
    (function () {
        var lc = document.createElement('script');
        lc.type = 'text/javascript';
        lc.async = true;
        lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(lc, s);
    })();
<?php if ($LiveChatEdd->cartDetails) { ?>
    var LC_API = LC_API || {};

    (function ($) {
        var s_ajaxListener = new Object();
        s_ajaxListener.tempOpen = XMLHttpRequest.prototype.open;
        s_ajaxListener.tempSend = XMLHttpRequest.prototype.send;
        s_ajaxListener.callback = function () {
            this.data = JSON.parse('{"' + decodeURI(this.data).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
            if (this.data.action === 'edd_add_to_cart') {
                var ajaxURL = this.url;
                $.ajax({
                    type: "post",
                    dataType : "json",
                    url : ajaxURL,
                    data : {
                        action: "livechat_edd_update_cart"
                    },
                    success: function (data) {
                        if(data.type === 'success') {
                            var cart = data.cart;
                            LC_API.set_custom_variables(cart);
                        }
                    }
                })
            }
        }

        XMLHttpRequest.prototype.open = function(a,b) {
            if (!a) var a='';
            if (!b) var b='';
            s_ajaxListener.tempOpen.apply(this, arguments);
            s_ajaxListener.method = a;
            s_ajaxListener.url = b;
            if (a.toLowerCase() == 'get') {
                s_ajaxListener.data = b.split('?');
                s_ajaxListener.data = s_ajaxListener.data[1];
            }
        }

        XMLHttpRequest.prototype.send = function(a,b) {
            if (!a) var a='';
            if (!b) var b='';
            s_ajaxListener.tempSend.apply(this, arguments);
            if(s_ajaxListener.method.toLowerCase() == 'post')s_ajaxListener.data = a;
            s_ajaxListener.callback();
        }
    })(jQuery);
<?php } ?>
</script>
<!-- End of LiveChat code -->

<?php
			    }
		    }
		}
	}
}