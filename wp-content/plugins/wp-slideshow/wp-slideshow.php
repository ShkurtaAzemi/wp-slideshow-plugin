<?php
/*
Plugin Name: Slideshow
Description: A plugin that creates a shortcode to show images in a slideshow
Author: Shkurte Azemi
Version: 1.0.0
*/

use JetBrains\PhpStorm\NoReturn;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class SlideshowSettings
{

    function __construct()
    {
        //registered hooks and filters that are needed for the plugin
        add_action('admin_menu', array($this, 'adminPage'));
        add_action('admin_init', array($this, 'settings'));;
        add_action('admin_enqueue_scripts', array($this, 'pluginAssets'));

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
        $image_ids_string = get_option('slideshow_images_ids');
        $image_ids = explode(",", $image_ids_string);

        $images = [];
        foreach ($image_ids as $image_id) {
            $image = wp_get_attachment_image_src($image_id, 'medium');
            $images[$image_id] = $image[0];
        }


        if (!empty($images)):
            ?>
            <div class="slideshow-block grid-gallery">
                <div class="container slideshow-container">

                    <div id="sortable" class="row">
                        <?php foreach ($images as $id=>$url): ?>
                            <div class="ui-state-default col-md-6 col-lg-4 item" data-id="<?php echo $id?>"><img
                                        class="img-fluid image scale-on-hover"
                                        src="<?php echo $url?>">
                                <a class="remove-image" href="#" >&#215;</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php
        endif;
    }

    //register admin page in the dashboard menu
    function adminPage()
    {
        add_options_page('Slideshow Settings', 'Slideshow Settings', 'manage_options', 'slideshow-settings-page', array($this, 'ourHtml'));
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

    //enqueue js scripts and styles
    function pluginAssets()
    {
        wp_enqueue_style('main-css', plugin_dir_url(__FILE__) . 'assets/css/main.css');
        wp_enqueue_style('bootstrap-style', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css');
//        wp_enqueue_script( 'test-script', plugins_url( '/assets/js/test.js' ,__FILE__),null, false, true );
//        wp_enqueue_script('jquery-csn', '//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js', null, false, true);
        wp_enqueue_media();
        wp_enqueue_script('medialib', plugins_url('/assets/js/medialib-script.js', __FILE__), null, false, true);


    }

}

$slideshow = new SlideshowSettings();
