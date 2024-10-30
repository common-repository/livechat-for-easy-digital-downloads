<?php

namespace LiveChatEdd;

require_once( 'Template.class.php' );

class ReviewNotice extends Template {
    public function render() { ?>
        <div class="lc-design-system-typography lc-notice notice notice-info is-dismissible" id="lc-edd-review-notice">
            <div class="lc-notice-column">
                <img class="lc-notice-logo" src="<?php echo plugins_url('livechat-for-easy-digital-downloads').'/includes/media/livechat-logo.svg'; ?>" alt="LiveChat logo" />
            </div>
            <div class="lc-notice-column">
                <p><?php _e('Hey, you’ve been using <strong>LiveChat</strong> for more than 14 days - that’s awesome! Could you please do us a BIG favour and <strong>give LiveChat a 5-star rating on WordPress</strong>? Just to help us spread the word and boost our motivation.', 'livechat-for-easy-digital-downloads'); ?></p>
                <p><?php _e('<strong>&ndash; The LiveChat Team</strong>'); ?></p>
                <div id="lc-review-notice-actions">
                    <a href="https://wordpress.org/support/plugin/livechat-for-easy-digital-downloads/reviews/#new-post" target="_blank" class="lc-review-notice-action lc-btn lc-btn--compact lc-btn--primary" id="lc-edd-review-now">
                        <i class="material-icons">thumb_up</i> <span><?php _e('Ok, you deserve it', 'livechat-for-easy-digital-downloads'); ?></span>
                    </a>
                    <a href="#" class="lc-review-notice-action lc-btn lc-btn--compact" id="lc-edd-review-postpone">
                        <i class="material-icons">schedule</i> <span><?php _e('Maybe later', 'livechat-for-easy-digital-downloads'); ?></span>
                    </a>
                    <a href="#" class="lc-review-notice-action lc-btn lc-btn--compact" id="lc-edd-review-dismiss">
                        <i class="material-icons">not_interested</i> <span><?php _e('No, thanks', 'livechat-for-easy-digital-downloads'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    <?php }
}
