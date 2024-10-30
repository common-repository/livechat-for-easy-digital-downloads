<?php

namespace LiveChatEdd;

require_once( 'Template.class.php' );

class Settings extends Template {

    public function render()
    {

        $LiveChatEdd = new LiveChatEdd;
        $email = $LiveChatEdd->email;
        $license = $LiveChatEdd->license;

        if (array_key_exists('actionType', $_POST) && $_POST['actionType'] === 'install') { ?>
            <div class="updated installed">
                <p>
                    <?php _e('LiveChat is now installed on your website!', 'livechat-for-easy-digital-downloads'); ?>
                </p>
                <span id="installed-close" class="tooltip tooltip-left tooltip-auto" data-tooltip="Close">x</span>
            </div>
        <?php }

        ?>
        <div class="lc-design-system-typography lc-table">
            <div class="lc-column">
                <p id="lc-plus-edd">
                    <img src="<?php echo $LiveChatEdd->pluginFilesURL; ?>media/lc-plus-edd.png" srcset="<?php echo $LiveChatEdd->pluginFilesURL; ?>media/lc-plus-edd.png, <?php echo $LiveChatEdd->pluginFilesURL; ?>media/lc-plus-edd@2x.png 2x" alt="LiveChat for Easy Digital Downloads">
                </p>
                <p>
                    <?php _e('Currently you are using your', 'livechat-for-easy-digital-downloads'); ?><br>
                    <strong><?php echo $email; ?></strong><br>
                    <?php _e('LiveChat account.', 'livechat-for-easy-digital-downloads'); ?>
                </p>
                <p id="lc-webapp">
                    <a href="https://my.livechatinc.com/?utm_source=easydigitaldownloads.com&utm_medium=integration&utm_campaign=easydigitaldownloads_integration" target="_blank" class="lc-link-button">
                        <button class="lc-btn lc-btn--primary">
                            <?php _e('Open web application', 'livechat-for-easy-digital-downloads'); ?>
                        </button>
                    </a>
                </p>
                <p class="lc-meta-text">
                    <?php _e('Something went wrong?', 'livechat-for-easy-digital-downloads'); ?>
                    <a id="resetAccount" href="#"><?php _e('Disconect your account.', 'livechat-for-easy-digital-downloads'); ?>
                    </a>
                </p>
                <iframe id="login-with-livechat" src="https://addons.livechatinc.com/sign-in-with-livechat" style="display: none"></iframe>
                <form id="livechatReset" action="" method="post">
                    <input type="hidden" name="actionType" value="reset">
                </form>
            </div>
            <div class="lc-column">
                <form id="lc-settings" action="" method="post">
                    <div id="cartDetails" class="lc-settings-option">
                        <div class="lc-css-tooltip" data-placement="top">
                            <?php _e('See the product\'s and total value of your customer\'s cart.', 'livechat-for-easy-digital-downloads'); ?>
                            <div class="lc-css-tooltip__arrow" data-placement="top"></div>
                        </div>
                        <p class="lc-settings-option-label">
                            <?php _e('Show cart details', 'livechat-for-easy-digital-downloads'); ?>:
                        </p>
                        <p class="lc-settings-option-switch">
                            <span><?php _e('No', 'livechat-for-easy-digital-downloads'); ?></span>
                            <label class="switch">
                                <input name="cartDetails" type="checkbox" <?php echo ($LiveChatEdd->cartDetails) ? 'checked' : ''; ?>><span class="slider round"></span>
                            </label>
                            <span><?php _e('Yes', 'livechat-for-easy-digital-downloads'); ?></span>
                        </p>
                    </div>
                    <div id="disableMobile" class="lc-settings-option">
                        <div class="lc-css-tooltip" data-placement="top">
                            <?php _e('Hide chat window for the mobile version of your website.', 'livechat-for-easy-digital-downloads'); ?>
                            <div class="lc-css-tooltip__arrow" data-placement="top"></div>
                        </div>
                        <p class="lc-settings-option-label">
                            <?php _e('Disable LiveChat on mobile', 'livechat-for-easy-digital-downloads'); ?>:
                        </p>
                        <p class="lc-settings-option-switch">
                            <span><?php _e('No', 'livechat-for-easy-digital-downloads'); ?></span>
                            <label class="switch">
                                <input name="disableMobile" type="checkbox" <?php echo ($LiveChatEdd->disableMobile) ? 'checked' : ''; ?>><span class="slider round"></span>
                            </label>
                            <span><?php _e('Yes', 'livechat-for-easy-digital-downloads'); ?></span>
                        </p>
                    </div>
                    <div id="disableGuests" class="lc-settings-option">
                        <div class="lc-css-tooltip" data-placement="top">
                            <?php _e('Hide chat window for not logged-in users.', 'livechat-for-easy-digital-downloads'); ?>
                            <div class="lc-css-tooltip__arrow" data-placement="top"></div>
                        </div>
                        <p class="lc-settings-option-label">
                            <?php _e('Disable LiveChat for Guest users', 'livechat-for-easy-digital-downloads'); ?>:
                        </p>
                        <p class="lc-settings-option-switch">
                            <span><?php _e('No', 'livechat-for-easy-digital-downloads'); ?></span>
                            <label class="switch">
                                <input name="disableGuests" type="checkbox" <?php echo ($LiveChatEdd->disableGuests) ? 'checked' : ''; ?>><span class="slider round"></span>
                            </label>
                            <span><?php _e('Yes', 'livechat-for-easy-digital-downloads'); ?></span>
                        </p>
                    </div>
                    <input type="hidden" name="actionType" value="update">
                </form>
            </div>
            <script>
                var lcDetails = {
                    license: <?php echo $license; ?>,
                    email: '<?php echo $email; ?>'
                }
            </script>
        </div>
        <?php

    }
}
