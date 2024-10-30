<?php

namespace LiveChatEdd;

require_once( 'Template.class.php' );

class Install extends Template {

    public function render()
    {

        $LiveChatEdd = new LiveChatEdd;
        $email = $LiveChatEdd->userEmail;
        $name = $LiveChatEdd->userName;
        $url = get_site_url();

        ?>
        <div class="lc-design-system-typography lc-table lc-install">
            <div class="lc-column">
                <p id="lc-plus-edd">
                    <img src="<?php echo $LiveChatEdd->pluginFilesURL; ?>media/lc-plus-edd.png" srcset="<?php echo $LiveChatEdd->pluginFilesURL; ?>media/lc-plus-edd.png, <?php echo $LiveChatEdd->pluginFilesURL; ?>media/lc-plus-edd@2x.png 2x" alt="LiveChat for Easy Digital Downloads">
                </p>
                <p>
                    <iframe id="login-with-livechat" src="https://addons.livechatinc.com/sign-in-with-livechat/edd/?designSystem=1&popupRoute=signup&utm_source=easydigitaldownloads.com&utm_medium=integration&utm_campaign=easydigitaldownloads_integration&name=<?php echo urlencode($name); ?>&email=<?php echo urlencode($email); ?>&url=<?php echo urlencode($url) ?>" > </iframe>
                </p>
                <form id="licenseForm" action="" method="post" style="display: none">
                    <input type="hidden" name="licenseEmail" id="licenseEmail">
                    <input type="hidden" name="licenseNumber" id="licenseNumber">
                    <input type="hidden" name="actionType" value="install">
                </form>
            </div>
            <div class="lc-column">
                <p>
                    <img src="<?php echo $LiveChatEdd->pluginFilesURL; ?>media/lc-app.png" alt="LiveChat apps" id="lc-app-img">
                </p>
                <p>
                    <?php _e('Check out our apps for', 'livechat-for-easy-digital-downloads'); ?>
                    <a href="https://www.livechatinc.com/applications/?utm_source=easydigitaldownloads.com&utm_medium=integration&utm_campaign=easydigitaldownloads_integration" target="_blank">
                        <?php _e('desktop or mobile!', 'livechat-for-easy-digital-downloads'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php
    }
}
