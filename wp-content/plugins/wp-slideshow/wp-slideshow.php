<?php
/*
Plugin Name: Slideshow
Description: A plugin that creates a shortcode to show images in a slideshow
Author: Shkurte Azemi
Version: 1.0.0
*/
/**
 *
 * @package Wp_Slideshow
 */


if (!defined('ABSPATH')) exit; // Exit if accessed directly

//set default values for settings options upon plugin activation
function plugin_activate()
{

    if (!get_option('slideshow_images_ids')) {
        add_option('slideshow_images_ids', '');
    }

}

register_activation_hook(__FILE__, 'plugin_activate');

class SlideshowSettings
{

    function __construct()
    {
        $this->setup();
    }

    //setup function for plugin action hooks
    public function setup()
    {

        //registered hooks and filters that are needed for the plugin
        add_action('admin_menu', array($this, 'adminPage'));
        add_action('admin_init', array($this, 'settings'));
        add_action('admin_enqueue_scripts', array($this, 'adminAssets'));
        add_action('wp_enqueue_scripts', array($this, 'frontendAssets'));
        add_action('init', array($this, 'slideshow_custom_shortcode'));
    }

    function slideshow_custom_shortcode()
    {
        add_shortcode('myslideshow', array($this, 'myslideshow_callback'));
    }

    //added setting options fields
    function settings()
    {
        //added page section
        add_settings_section('slideshow_images_section', 'Slideshow Images', null, 'slideshow-settings-page');

        //registered slideshow images option
        add_settings_field('slideshow_images_ids', 'Images', array($this, 'slideshowImagesInput'), 'slideshow-settings-page', 'slideshow_images_section');
        register_setting('slideshowimages', 'slideshow_images_ids', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));

    }

    //show the input field for media upload
    function slideshowImagesInput()
    {
        ?>
        <input type="hidden" name="slideshow_images_ids"
               value="<?php echo esc_attr(get_option('slideshow_images_ids')) ?>"
               style="width:600px" id="images">
        <button type="button" class="button button-primary upload_image_button"><?php _e('Select Images'); ?></button>

        <?php
        $images = $this->getSlideshowImages();
            ?>
            <div class="slideshow-block grid-gallery">
                <div class="container slideshow-container">
                    <div id="sortable" class="row">
                        <?php  if (!empty($images)): foreach ($images as $image):
                            ?>
                            <div class="ui-state-default col-md-6 col-lg-4 item" data-id="<?php echo $image['id'] ?>">
                                <img
                                        class="img-fluid image scale-on-hover"
                                        src="<?php echo $image['url'] ?>">
                                <span class="remove-image" href="#">&#215;</span>
                            </div>
                        <?php  endforeach;endif; ?>
                    </div>
                </div>
            </div>
        <?php

    }

    //register admin page in the dashboard menu
    public function adminPage()
    {
        add_options_page('Slideshow Settings', 'Slideshow Settings', 'manage_options', 'slideshow-settings-page', array($this, 'ourHtml'));
    }

    //render settings fields HTML
    public function ourHtml()
    {
        //check if current logged in user has administrator privileges
        if (!current_user_can('manage_options')) {
            return;
        }

        ?>
        <div class="wrap">
            <h1> <?php _e('Plugin Settings', 'slideshow-images') ?></h1>
            <div class="tab-content">
                <form method="post" action="options.php" enctype="multipart/form-data">
                    <?php
                    settings_fields('slideshowimages');
                    do_settings_sections('slideshow-settings-page');
                    submit_button();
                    ?>
                </form>

            </div>
        </div>
        <?php
    }

    //shortcode callback output
    function myslideshow_callback()
    {
        $images = $this->getSlideshowImages();

        $output = '';
        if (!empty($images)) {
            $output .= "<div class='swiper-container slider'>
                    <div class='swiper-wrapper'>";
            foreach ($images as $image):

                $output .= "<div class='swiper-slide'><img src='${image['url']}' alt=''></div>";

            endforeach;
            $output .= "</div><div class='swiper-button-next'></div><div class='swiper-button-prev'></div></div>";

            $output .= "<div class='swiper-container slider-thumbnail'><div class='swiper-wrapper'>";
            foreach ($images as $image):

                $output .= "<div class='swiper-slide'><img src='${image['url']}' alt=''></div>";

            endforeach;
            $output .= "</div></div>";
        }
        return $output;
    }

    //a function to get an array of selected images

    function getSlideshowImages()
    {
        $image_ids_string = get_option('slideshow_images_ids');
        $image_ids = explode(",", $image_ids_string);

        $images = [];
        if (!empty($image_ids)) {
            foreach ($image_ids as $image_id) {
                $image = wp_get_attachment_image_src($image_id, 'medium');
                if ($image) {
                    $images[] = array(
                        'id' => $image_id,
                        'url' => $image[0]
                    );
                }

            }
        }

        return $images;
    }

    //enqueue admin styles and scripts

    function adminAssets()
    {
        wp_enqueue_style('main-css', plugin_dir_url(__FILE__) . 'assets/css/main.css');
        wp_enqueue_style('bootstrap-style', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css');
        wp_enqueue_media();
        wp_enqueue_script('medialib', plugins_url('/assets/js/medialib-script.js', __FILE__), null, false, true);


    }

    //enqueue frontend styles and scripts
    function frontendAssets()
    {
        wp_enqueue_style('swiper-css', plugin_dir_url(__FILE__) . 'lib/swiper-bundle.min.css');
        wp_enqueue_style('main-css', plugin_dir_url(__FILE__) . 'assets/css/swiper.css');

        wp_enqueue_script('jquery-csn', '//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js', null, false, true);
        wp_enqueue_script('medialib', plugins_url('/assets/js/swiper.js', __FILE__), null, false, true);
        wp_enqueue_script('swiper-js', plugins_url('lib/swiper-bundle.min.js', __FILE__), null, false, true);

    }

}

$slideshow = new SlideshowSettings();
