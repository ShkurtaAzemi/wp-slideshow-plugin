<?php
/**
 * Class SlideshowSettingsTest
 *
 * @package Wp_Slideshow
 */


class SlideshowSettingsTest extends WP_UnitTestCase
{


//    const PLUGIN_BASENAME = 'C:\xampp\htdocs\wp-slideshow-plugin\wp-content\plugins\wp-slideshow\wp-slideshow.php';
    private SlideshowSettings $class_instance;
    private $t_submenu;
    protected $backupGlobals = false;
//    protected $backupGlobalsBlacklist = ['globalVariable'];
    public function setUp(): void
    {
        parent::setUp();
//        do_action( 'activate_' . static::PLUGIN_BASENAME );
        $this->class_instance = new SlideshowSettings();
//                global $submenu;
//        $this->submenu = $submenu;
//        print_r($this->submenu);
//        $GLOBALS['submenu'] = $submenu;
//        $this->test_submenu = $GLOBALS['submenu'];
//        global $submenu;
//        $this->t_submenu = $submenu;

    }

    /**
     * @return void
     *
     * Test actions and filters
     */
    public function test_setup()
    {
        $this->assertNotTrue(has_action('admin_menu',  array($this->class_instance, 'adminPage')));
        $this->assertNotTrue(has_action('admin_init', array($this->class_instance, 'settings')));
        $this->assertNotTrue(has_action('admin_enqueue_scripts',array($this->class_instance, 'adminAssets')));
        $this->assertNotTrue(has_action('wp_enqueue_scripts',  array($this->class_instance, 'frontendAssets')));
        $this->assertNotTrue(has_action('init', array($this->class_instance, 'slideshow_custom_shortcode')));

//        $this->class_instance->setup();
        $this->class_instance->setup();

        $this->assertNotFalse(has_action('admin_menu',  array($this->class_instance, 'adminPage')));
        $this->assertNotFalse(has_action('admin_init', array($this->class_instance, 'settings')));
        $this->assertNotFalse(has_action('admin_enqueue_scripts',array($this->class_instance, 'adminAssets')));
        $this->assertNotFalse(has_action('wp_enqueue_scripts',  array($this->class_instance, 'frontendAssets')));
        $this->assertNotFalse(has_action('init', array($this->class_instance, 'slideshow_custom_shortcode')));
    }

    /**
     * @return void
     * Test admin enqueued scripts
     */

    public function test_admin_assets () {
        // if no shortcode
        $this->class_instance->adminAssets();

        $this->assertTrue( wp_style_is( 'main-css', 'enqueued' ) );
        $this->assertTrue( wp_style_is( 'bootstrap-style', 'enqueued' ) );
        $this->assertTrue( wp_style_is( 'mediaelement', 'enqueued' ) );
        $this->assertTrue( wp_style_is( 'wp-mediaelement', 'enqueued' ) );
        $this->assertTrue( wp_style_is( 'imgareaselect', 'enqueued' ) );

        $this->assertTrue( wp_script_is( 'medialib', 'enqueued' ) );

    }

    /**
     * @return void
     * Test frontend enqueued scripts
     */

    public function test_frontend_assets () {
        // if no shortcode
        $this->class_instance->frontendAssets();

        $this->assertTrue( wp_style_is( 'swiper-css', 'enqueued' ) );
        $this->assertTrue( wp_style_is( 'main-css', 'enqueued' ) );

        $this->assertTrue( wp_script_is( 'jquery-csn', 'enqueued' ) );
        $this->assertTrue( wp_script_is( 'medialib', 'enqueued' ) );
        $this->assertTrue( wp_script_is( 'swiper-js', 'enqueued' ) );

    }

    public function test_register_settings_page () {
//                global $submenu;
////        $this->submenu = $submenu;
//
//
//        $this->assertFalse(isset($GLOBALS['submenu']['options-general.php']));
//        $this->assertNotTrue(has_action('admin_menu', array($this->class_instance, 'adminPage')));
//
//        $this->class_instance->adminPage();
//        print_r($submenu);
//
//        $this->assertTrue( isset($GLOBALS['submenu']['options-general.php'] ) );
//        $settings =$this->class_instance->submenu['options-general.php'];
//
//        $this->assertSame( 'Slideshow Settings', $settings[ 46 ][ 0 ] );
//        $this->assertSame( 'manage_options', $settings[ 46 ][ 1 ] );
//        $this->assertSame( 'slideshow-settings-page', $settings[ 46 ][ 2 ] );
//        $this->assertSame( 'Slideshow Settings', $settings[ 46 ][ 3 ] );
//        $this->assertNotFalse(has_action('admin_menu',  array($this->class_instance, 'adminPage')));

        $current_user = get_current_user_id();
        wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
        update_option( 'siteurl', 'http://example.com' );

        // Add some pages.
                $this->class_instance->adminPage();
//        add_options_page('Slideshow Settings', 'Slideshow Settings', 'manage_options', 'slideshow-settings-page', 'mt_settings_page');
//        add_options_page( 'Test Settings', 'Test Settings', 'manage_options', 'testsettings', 'mt_settings_page' );

        $expected['slideshow-settings-page']        = 'http://example.com/wp-admin/options-general.php?page=slideshow-settings-page';

//        $expected['testsettings']        = 'http://example.com/wp-admin/options-general.php?page=testsettings';

        foreach ( $expected as $name => $value ) {
            $this->assertSame( $value, menu_page_url( $name, false ) );
        }

        wp_set_current_user( $current_user );

//        $this->assertFalse( isset( $submenu[ 'edit.php?post_type=sos' ] ) );
//        $this->assertFalse(
//            Util::has_action( 'admin_page_sos_settings_page',
//                $this->sos_options, 'render_settings_page' ) );
//
//        $this->sos_options->register_settings_page();
//
//        $this->assertTrue( isset( $submenu[ 'edit.php?post_type=sos' ] ) );
//        $settings = $submenu[ 'edit.php?post_type=sos' ];
//        $this->assertSame( 'Settings', $settings[ 0 ][ 0 ] );
//        $this->assertSame( 'administrator', $settings[ 0 ][ 1 ] );
//        $this->assertSame( 'sos_settings_page', $settings[ 0 ][ 2 ] );
//        $this->assertSame( 'Common Options', $settings[ 0 ][ 3 ] );
//        $this->assertTrue(
//            Util::has_action( 'admin_page_sos_settings_page',
//                $this->sos_options, 'render_settings_page' ) );
    }

//    /**
//     * A single example test.
//     */
//    public function test_sample()
//    {
//        // Replace this with some actual testing code.
//        $this->factory->post->create();
//        $this->assertTrue(true);
//    }

//    public function test_slideshow_admin_menu()
//    {
////        $slideshow_object = new SlideshowSettings();
////        $this->class_instance->adminPage();
//        add_option('')
//        echo "<pre>";
//
//        $this->assertNotEmpty(menu_page_url('slideshow-settings-page'), false);
//    }
    public function test_myslideshow_callback()
    {
//        $slideshow_object = new SlideshowSettings();
//        $this->class_instance->adminPage();


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

//        $id_1 = $this->_make_attachment($upload_1);
//        $id_2 = $this->_make_attachment($upload_2);
//        $id_3 = $this->_make_attachment($upload_3);

//        print_r($id_1);
//        print_r($id_2);
//        print_r($id_3);


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
//        echo "output";
//        print_r($output);
        $this->assertEquals($actual, $output);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
