<?php

namespace LiveChatEdd;

require_once('LiveChatEdd.class.php');

final class LiveChatEddAdmin extends LiveChatEdd {

    protected $render = true;

    protected $review_notice_start_timestamp = null;

    protected $review_notice_start_timestamp_offset = null;

    protected $review_notice_dismissed = false;

    public function __construct() {
        parent::__construct();

        add_action('admin_init', array($this, 'load_translations'));
        add_action('admin_init', array($this, 'handlePost'));
        add_action('admin_init', array($this, 'loadAssets'));

        if($this->check_review_notice_conditions()) {
            add_action('admin_init', array($this, 'load_review_scripts_and_styles'));
            add_action('wp_ajax_lc_edd_review_dismiss', array($this, 'ajax_review_dismiss'));
            add_action('wp_ajax_lc_edd_review_postpone', array($this, 'ajax_review_postpone'));
            add_action('admin_notices', array($this, 'show_review_notice'));
        }

        if(!$this->license && !(array_key_exists('page', $_GET) && $_GET['page'] === 'livechat-easydigitaldownloads')) {
            add_action('admin_notices', array($this, 'show_connect_notice'));
        }

        add_action('admin_menu', array($this, 'adminMenu'));
        add_filter('plugin_action_links', array($this, 'plugin_add_settings_link'), 10, 2);
        add_action('in_admin_header', array($this, 'show_deactivation_feedback_form'));

        if (array_key_exists('SCRIPT_NAME', $_SERVER) && strpos($_SERVER['SCRIPT_NAME'], 'plugins.php')) {
            add_action('in_admin_header', array($this, 'show_deactivation_feedback_form'));
        }
    }

    public function handlePost() {
        if (array_key_exists('actionType', $_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!current_user_can('manage_options')) {
                wp_die();
            }
            $action = $_POST['actionType'];

            switch ($action) {
                case 'install':
                    $this->installSettings($_POST);
                    break;
                case 'update':
                    $this->updateSettings($_POST);
                    break;
                case 'reset':
                    $this->resetSettings();
                    break;
                default:
                    break;
            }
        }
    }

    public function load_translations()
    {
        load_plugin_textdomain(
            'livechat-for-easy-digital-downloads',
            false,
            'livechat-for-easy-digital-downloads/languages'
       );
    }

    public function plugin_add_settings_link($links, $file) {

        if (basename($file) !== 'livechat-easydigitaldownloads.php')
        {
            return $links;
        }

        $settings_link = sprintf('<a href="edit.php?post_type=download&page=livechat-easydigitaldownloads">%s</a>', __('Settings'));
        array_unshift ($links, $settings_link);
        return $links;
    }

    public function adminMenu()
    {
        add_submenu_page(
            'edit.php?post_type=download',
            'LiveChat',
            $this->license || (array_key_exists('page', $_GET) && $_GET['page'] === 'livechat-easydigitaldownloads')  ? 'LiveChat' : 'LiveChat <span class="awaiting-mod">!</span>',
            'administrator',
            'livechat-easydigitaldownloads',
            array($this, 'settings')
       );
    }

    /**
     * Loads LC Design System related files.
     */
    protected function load_design_system_styles() {
        wp_register_style('livechat-source-sans-pro-font', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600');
        wp_register_style('livechat-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
        wp_register_style('livechat-design-system', 'https://cdn.livechat-static.com/design-system/styles.css');
        wp_enqueue_style('livechat-source-sans-pro-font', false, $this->pluginVersion);
        wp_enqueue_style('livechat-material-icons', false, $this->pluginVersion);
        wp_enqueue_style('livechat-design-system', false, $this->pluginVersion);
    }

    public function loadAssets() {
        $this->load_design_system_styles();
        wp_enqueue_script('livechat-easydigitaldownloads-admin-script', $this->pluginFilesURL.'js/lc-edd-admin.js', 'jquery', $this->pluginVersion, false);
        wp_enqueue_style('livechat-easydigitaldownloads-style', $this->pluginFilesURL.'css/lc-edd-general.css', false, $this->pluginVersion);
    }

    public function load_review_scripts_and_styles()
    {
        wp_enqueue_script('livechat-easydigitaldownloads-review-script', $this->pluginFilesURL.'js/lc-edd-review.js', 'jquery', $this->pluginVersion, true);
        wp_enqueue_style('livechat-easydigitaldownloads-review-style', $this->pluginFilesURL.'css/lc-edd-review.css', false, $this->pluginVersion);
    }

    public function settings() {

        $this->getSettings();

        if ($this->license) {
            $this->loadTemplate('Settings');
        } else {
            $this->loadTemplate('Install');
        }
    }

    protected function installSettings() {
        update_option('livechat_edd_license', $_POST['licenseNumber']);
        update_option('livechat_edd_email', $_POST['licenseEmail']);
        update_option('livechat_edd_cartDetails', true);
        update_option('livechat_edd_disableMobile', false);
        update_option('livechat_edd_disableGuests', false);

        update_option('livechat_edd_review_notice_start_timestamp', time());
        update_option('livechat_edd_review_notice_start_timestamp_offset', 16);
    }

    protected function updateSettings($data) {
        foreach ($data as $key => $value) {
            if ($value === 'on') {
                $data[ $key ] = true;
            } else if ($key !== 'actionType') {
                $data[ $key ] = false;
            }
        }

        update_option('livechat_edd_cartDetails', $data['cartDetails']);
        update_option('livechat_edd_disableMobile', $data['disableMobile']);
        update_option('livechat_edd_disableGuests', $data['disableGuests']);

        $this->sendResponse($data);
    }

    protected function resetSettings() {
        delete_option('livechat_edd_license');
        delete_option('livechat_edd_email');
        delete_option('livechat_edd_cartDetails');
        delete_option('livechat_edd_disableMobile');
        delete_option('livechat_edd_disableGuests');

        delete_option('livechat_edd_review_notice_start_timestamp');
        delete_option('livechat_edd_review_notice_start_timestamp_offset');
    }

    private function sendResponse($message) {
        $callback = isset($_GET['jsoncallback']) ? $_GET['jsoncallback'] : '';
        $response = array(
            'response' => $message,
       );
        $ctype = 'application/json';
// Sanitize callback
        $callback = preg_replace("/[^][.\\'\\\"_A-Za-z0-9]/", '', $_GET['jsoncallback']);

        $prefix = $callback . '(';
        $suffix = ')';
        header('Content-type: ' . $ctype, true);
        die($prefix . json_encode($response) . $suffix);
    }

    protected function check_if_review_notice_was_dismissed()
    {
        if (!$this->review_notice_dismissed)
        {
            $this->review_notice_dismissed = get_option('livechat_edd_review_notice_dismissed');
        }

        return $this->review_notice_dismissed;
    }

    protected function check_if_license_is_active($license_number)
    {
        if ($license_number > 0) {
            $url = 'https://api.livechatinc.com/v2/license/' . $license_number;
            try {
                if (function_exists('curl_init')) {
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($curl);
                    $code     = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    if ($code === 200) {
                        return json_decode($response)->license_active;
                    } else {
                        throw new \Exception($code);
                    }
                } else if (ini_get('allow_url_fopen') === '1' || strtolower(ini_get('allow_url_fopen')) === 'on') {
                    $options = array(
                        'http' => array(
                            'method' => 'GET'
                       ),
                   );
                    $context = stream_context_create($options);
                    $result  = file_get_contents($url, false, $context);

                    return json_decode($result)->license_active;
                }
            } catch (\Exception $exception) {
                error_log(
                    'check_if_license_is_active() error ' .
                    $exception->getCode() .
                    ': ' .
                    $exception->getMessage()
               );
            }
        }

        return false;
    }

    protected function get_review_notice_start_timestamp()
    {
        if (is_null($this->review_notice_start_timestamp))
        {
            $timestamp = get_option('livechat_edd_review_notice_start_timestamp');
            // if timestamp was not set on install
            if (!$timestamp) {
                $timestamp = time();
                update_option('livechat_edd_review_notice_start_timestamp', $timestamp); // set timestamp if not set on install
            }

            $this->review_notice_start_timestamp = $timestamp;
        }

        return $this->review_notice_start_timestamp;
    }

    protected function get_review_notice_start_timestamp_offset()
    {
        if (is_null($this->review_notice_start_timestamp_offset))
        {
            $offset = get_option('livechat_edd_review_notice_start_timestamp_offset');
            // if offset was not set on install
            if (!$offset) {
                $offset = 16;
                update_option('livechat_edd_review_notice_start_timestamp_offset', $offset); // set shorter offset
            }

            $this->review_notice_start_timestamp_offset = $offset;
        }

        return $this->review_notice_start_timestamp_offset;
    }

    protected function check_review_notice_conditions()
    {
        $this->getSettings();
        if(!$this->check_if_review_notice_was_dismissed() && $this->check_if_license_is_active($this->license)) {
            $secondsInDay = 60 * 60 * 24;
            $noticeTimeout = time() - $this->get_review_notice_start_timestamp();
            $timestampOffset = $this->get_review_notice_start_timestamp_offset();
            if ($noticeTimeout >= $secondsInDay * $timestampOffset) {
                return true;
            }
        }

        return false;
    }

    public function ajax_review_dismiss()
    {
        update_option('livechat_edd_review_notice_dismissed', true);
        echo 'OK';
        wp_die();
    }

    public function ajax_review_postpone()
    {
        update_option('livechat_edd_review_notice_start_timestamp', time());
        update_option('livechat_edd_review_notice_start_timestamp_offset', 7);
        echo 'OK';
        wp_die();
    }

    public function show_review_notice()
    {
        $this->loadTemplate('ReviewNotice');
    }

    public function show_connect_notice()
    {
        $this->loadTemplate('ConnectNotice');
    }

    public function show_deactivation_feedback_form()
    {
        $this->loadTemplate('DeactivationFeedbackForm');
    }
}
