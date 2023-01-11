<?php
/*
Plugin Name: Slideshow
Description: A plugin that creates a shortcode to show images in a slideshow
Author: Shkurte Azemi
Version: 1.0.0
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class SlideshowSettings
{



    function __construct()
    {
        //registered hooks and filters that are needed for the plugin
        add_action('admin_menu', array($this, 'adminPage'));
        add_action('admin_init', array($this, 'settings'));;
        add_action('wp_enqueue_scripts', array($this, 'pluginAssets'));
        add_filter('the_content', array($this, 'showEventsTable'));
        add_action('phpmailer_init', array($this, 'setupSMTP'));
        remove_filter('the_content', 'wpautop');
    }


    //added setting options fields
    function settings()
    {
        //added page section
        add_settings_section('event_data_fields', 'Event Data Fields', null, 'events-settings-page');

        //registered email receiver option
        add_settings_field('events_email_receiver', 'The recipient email', array($this, 'emailInput'), 'events-settings-page', 'event_data_fields');
        register_setting('eventsplugin', 'events_email_receiver', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));

    }

    //show the input field receiver email
    function emailInput()
    {
        ?>
        <input type="email" name="events_email_receiver"
               value="<?php echo esc_attr(get_option('events_email_receiver')) ?>"
               style="width:600px">
        <?php
    }

    //register admin page in the dashboard menu
    function adminPage()
    {
        add_options_page('Event Settings', 'Events Settings', 'manage_options', 'events-settings-page', array($this, 'ourHtml'));
    }

    //render settings fields HTML
    function ourHtml()
    {
        //check if current logged in user has administrator privileges
        if (!current_user_can('manage_options')) {
            return;
        }

        ?>
        <div class="wrap">
            <h1> <?php _e('Plugin Settings', 'events-importer') ?></h1>
            <div class="tab-content">
                <form method="post" action="options.php" enctype="multipart/form-data">
                    <?php
                    settings_fields('eventsplugin');
                    do_settings_sections('events-settings-page');
                    submit_button();
                    ?>
                </form>
            </div>
        </div>
        <?php
    }

    //enqueue js scripts and styles
    function pluginAssets()
    {
        wp_enqueue_style('bootstrap-style', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css');
        wp_enqueue_style('bootstrap-datatable-style', '//cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css');
        wp_enqueue_style('bootstrap-datatable-styles', '//cdn.datatables.net/v/bs4/jq-3.6.0/dt-1.13.1/r-2.4.0/sb-1.4.0/datatables.min.css');
        wp_enqueue_style('main-css', plugin_dir_url(__FILE__) . 'assets/css/main.css');

        wp_enqueue_script('jquery-csn', '//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js', null, false, true);
        wp_enqueue_script('bootstrap-script', plugins_url('assets/js/datatable-init.js', __FILE__), null, false, true);
        wp_enqueue_script('bootstrap-scripts', '//cdn.datatables.net/v/bs4/jq-3.6.0/dt-1.13.1/r-2.4.0/sb-1.4.0/datatables.min.js', null, false, true);

    }

}

$events = new SlideshowSettings();
