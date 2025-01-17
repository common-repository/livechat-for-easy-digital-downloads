<?php

namespace LiveChatEdd;

require_once('Template.class.php');

class DeactivationFeedbackForm extends Template {
	public function render() {

		$LiveChatEdd = new LiveChatEdd;
		$wp_user = $LiveChatEdd->getUserDetails();
		$license_id = !$LiveChatEdd->license ? 0 : $LiveChatEdd->license;

		?>
        <div class="lc-design-system-typography lc-modal-base__overlay" id="lc-deactivation-feedback-modal-overlay" style="display: none">
            <div class="lc-modal-base"  id="lc-deactivation-feedback-modal-container">
                <button title="<?php _e('Cancel', 'livechat-for-easy-digital-downloads') ?>" class="lc-modal-base__close">
                    <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24px"
                            height="24px"
                            viewBox="0 0 24 24"
                            fill="#424D57"
                            class="material material-close-icon undefined"
                    >
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                    </svg>
                </button>
                <div class="lc-modal__header">
                    <div class="lc-modal__heading" id="lc-deactivation-feedback-modal-heading">
                        <img
                                id="lc-deactivation-feedback-logo"
                                alt="LiveChat logo"
                                src="<?php echo plugins_url('livechat-for-easy-digital-downloads').'/includes/media/livechat-logo.svg'; ?>"
                        />
                        <h2 id="lc-deactivation-feedback-modal-title">
                            <?php _e('Quick Feedback', 'livechat-for-easy-digital-downloads') ?>
                        </h2>
                    </div>
                </div>
                <div class="lc-modal__body">
                    <form
                            action="#"
                            method="post"
                            id="lc-deactivation-feedback-form"
                    >
                        <div role="group" class="lc-form-group">
                            <div class="lc-form-group__header">
                                <div class="lc-form-group__label">
                                    <?php _e('If you have a moment, please let us know why you are deactivating LiveChat:', 'livechat-for-easy-digital-downloads') ?>
                                </div>
                            </div>
                            <div class="lc-field-group">
                                <div class="lc-radio">
                                    <label class="lc-radio__label">
                                        <div class="lc-radio__circle">
                                            <span class="lc-radio__inner-circle"></span>
                                            <input
                                                    type="radio"
                                                    class="lc-radio__input"
                                                    value="I no longer need the plugin."
                                                    name="lc-deactivation-feedback-option"
                                            />
                                        </div>
                                        <div class="lc-radio__text">
                                            <?php _e('I no longer need the plugin.', 'livechat-for-easy-digital-downloads') ?>
                                        </div>
                                    </label>
                                </div>
                                <div class="lc-radio">
                                    <label class="lc-radio__label">
                                        <div class="lc-radio__circle">
                                            <span class="lc-radio__inner-circle"></span>
                                            <input
                                                    type="radio"
                                                    class="lc-radio__input"
                                                    value="I couldn't get the plugin to work."
                                                    name="lc-deactivation-feedback-option"
                                            />
                                        </div>
                                        <div class="lc-radio__text">
                                            <?php _e("I couldn't get the plugin to work.", 'livechat-for-easy-digital-downloads') ?>
                                        </div>
                                    </label>
                                </div>
                                <div class="lc-radio">
                                    <label class="lc-radio__label">
                                        <div class="lc-radio__circle">
                                            <span class="lc-radio__inner-circle"></span>
                                            <input
                                                    type="radio"
                                                    class="lc-radio__input"
                                                    value="I found a better plugin."
                                                    name="lc-deactivation-feedback-option"
                                            />
                                        </div>
                                        <div class="lc-radio__text">
                                            <?php _e('I found a better plugin.', 'livechat-for-easy-digital-downloads') ?>
                                        </div>
                                    </label>
                                </div>
                                <div class="lc-radio">
                                    <label class="lc-radio__label">
                                        <div class="lc-radio__circle">
                                            <span class="lc-radio__inner-circle"></span>
                                            <input
                                                    type="radio"
                                                    class="lc-radio__input"
                                                    value="It's a temporary deactivation."
                                                    name="lc-deactivation-feedback-option"
                                            />
                                        </div>
                                        <div class="lc-radio__text">
                                            <?php _e("It's a temporary deactivation.", 'livechat-for-easy-digital-downloads') ?>
                                        </div>
                                    </label>
                                </div>
                                <div class="lc-radio">
                                    <label class="lc-radio__label">
                                        <div class="lc-radio__circle">
                                            <span class="lc-radio__inner-circle"></span>
                                            <input
                                                    type="radio"
                                                    class="lc-radio__input"
                                                    value="Other"
                                                    name="lc-deactivation-feedback-option"
                                                    id="lc-deactivation-feedback-option-other"
                                            />
                                        </div>
                                        <div class="lc-radio__text">
                                            <?php _e('Other', 'livechat-for-easy-digital-downloads') ?>
                                        </div>
                                    </label>
                                </div>
                                <div class="lc-text-field" id="lc-deactivation-feedback-other-field">
                                    <div>
                                    <textarea
                                            class="lc-textarea"
                                            placeholder="<?php _e('Tell us more...', 'livechat-for-easy-digital-downloads') ?>"
                                    ></textarea>
                                    </div>
                                </div>
                                <span class="lc-field-error" id="lc-deactivation-feedback-form-option-error">
                                    <?php _e('Please choose one of available options.', 'livechat-for-easy-digital-downloads') ?>
                                </span>
                                <span class="lc-field-error" id="lc-deactivation-feedback-form-other-error">
                                    <?php _e('Please provide additional feedback.', 'livechat-for-easy-digital-downloads') ?>
                                </span>
                            </div>
                        </div>
                    </form>
                    <script>
                        window.deactivationDetails = window.deactivationDetails || {};
                        window.deactivationDetails = {
                            license: <?php echo htmlspecialchars($license_id); ?>,
                            name: '<?php echo htmlspecialchars($wp_user['name']); ?>',
                            wpEmail: '<?php echo htmlspecialchars($wp_user['email']); ?>'
                        };
                    </script>
                </div>
                <div class="lc-modal__footer">
                    <button class="lc-btn" id="lc-deactivation-feedback-modal-skip-btn">
                        <?php _e('Skip & continue', 'livechat-for-easy-digital-downloads') ?>
                    </button>
                    <button class="lc-btn lc-btn--primary" id="lc-deactivation-feedback-modal-submit-btn">
                        <?php _e('Send feedback', 'livechat-for-easy-digital-downloads') ?>
                    </button>
                </div>
            </div>
        </div>
		<?php
	}
}
