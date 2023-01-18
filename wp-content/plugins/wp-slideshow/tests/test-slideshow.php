<?php
/**
 * Class SlideshowSettingsTest
 *
 * @package Wp_Slideshow
 */


class SlideshowSettingsTest extends WP_UnitTestCase
{

    private SlideshowSettings $class_instance;

    public function setUp(): void
    {
        parent::setUp();
        $this->class_instance = new SlideshowSettings();
    }

    /**
     * @return void
     *
     * Test actions and filters
     */
    public function test_setup()
    {
        $this->assertNotTrue(has_action('admin_menu', array($this->class_instance, 'adminPage')));
        $this->assertNotTrue(has_action('admin_init', array($this->class_instance, 'settings')));
        $this->assertNotTrue(has_action('admin_enqueue_scripts', array($this->class_instance, 'adminAssets')));
        $this->assertNotTrue(has_action('wp_enqueue_scripts', array($this->class_instance, 'frontendAssets')));
        $this->assertNotTrue(has_action('init', array($this->class_instance, 'slideshow_custom_shortcode')));

        $this->class_instance->setup();

        $this->assertNotFalse(has_action('admin_menu', array($this->class_instance, 'adminPage')));
        $this->assertNotFalse(has_action('admin_init', array($this->class_instance, 'settings')));
        $this->assertNotFalse(has_action('admin_enqueue_scripts', array($this->class_instance, 'adminAssets')));
        $this->assertNotFalse(has_action('wp_enqueue_scripts', array($this->class_instance, 'frontendAssets')));
        $this->assertNotFalse(has_action('init', array($this->class_instance, 'slideshow_custom_shortcode')));
    }

    /**
     * @return void
     * Test admin enqueued scripts
     */

    public function test_admin_assets()
    {
        $this->class_instance->adminAssets();

        $this->assertTrue(wp_style_is('main-css', 'enqueued'));
        $this->assertTrue(wp_style_is('bootstrap-style', 'enqueued'));
        $this->assertTrue(wp_style_is('mediaelement', 'enqueued'));
        $this->assertTrue(wp_style_is('wp-mediaelement', 'enqueued'));
        $this->assertTrue(wp_style_is('imgareaselect', 'enqueued'));

        $this->assertTrue(wp_script_is('medialib', 'enqueued'));

    }

    /**
     * @return void
     * Test frontend enqueued scripts
     */

    public function test_frontend_assets()
    {
        $this->class_instance->frontendAssets();

        $this->assertTrue(wp_style_is('swiper-css', 'enqueued'));
        $this->assertTrue(wp_style_is('main-css', 'enqueued'));

        $this->assertTrue(wp_script_is('jquery-csn', 'enqueued'));
        $this->assertTrue(wp_script_is('medialib', 'enqueued'));
        $this->assertTrue(wp_script_is('swiper-js', 'enqueued'));

    }

    /**
     * @return void
     *
     * Test registered options page submenu
     */

    public function test_register_settings_page()
    {

        $current_user = get_current_user_id();
        wp_set_current_user(self::factory()->user->create(array('role' => 'administrator')));
        update_option('siteurl', 'http://example.com');

        // Add settings page.
        $this->class_instance->adminPage();

        $expected['slideshow-settings-page'] = 'http://example.com/wp-admin/options-general.php?page=slideshow-settings-page';

        foreach ($expected as $name => $value) {
            $this->assertSame($value, menu_page_url($name, false));
        }

        wp_set_current_user($current_user);

    }

    /**
     * @return void
     *
     * Test slideshow shortcode output
     */
    public function test_myslideshow_callback()
    {
        $filename_1 = ('./images/test-img.png');
        $contents_1 = file_get_contents($filename_1);
        $upload_1 = wp_upload_bits(basename($filename_1), null, $contents_1);
        $this->assertTrue(empty($upload_1['error']));

        $filename_2 = ('./images/test-img.png');
        $contents_2 = file_get_contents($filename_2);
        $upload_2 = wp_upload_bits(basename($filename_2), null, $contents_2);
        $this->assertTrue(empty($upload_2['error']));

        $filename_3 = ('./images/test-img.png');
        $contents_3 = file_get_contents($filename_3);
        $upload_3 = wp_upload_bits(basename($filename_3), null, $contents_3);
        $this->assertTrue(empty($upload_3['error']));

        add_option('slideshow_images_ids', '5,6,7');
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

        $actual = $this->class_instance->myslideshow_callback();

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

        $this->assertEquals($actual, $output);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
