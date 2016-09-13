<?php 
/**
 * Plugin Name: Wordpress Slideshow 
 * Description: plugin to make a slideshow module for worpdress website 
 * Author: Naveen Giri
 * Author Url: 
 * version:0.1
*/

/*include Admin setting core work page here*/
require_once 'admin/admin-core-setting.php';

/*include fontend core work  here*/
require_once 'front/front-core.php';

/*include all scripts and style*/
add_action( 'admin_enqueue_scripts', 'slider_backend_enqueue_script' );
add_action( 'wp_enqueue_scripts', 'slider_frontend_enqueue_script' );

/*remove ajax handler*/
add_action( 'wp_ajax_remove_current_slide', 'remove_current_slide_callback' );
/*reorder slides ajax handler*/
add_action( 'wp_ajax_new_slide_order_ajax', 'new_slide_order_ajax_callback' );


/*include jquery ui sortable in backend*/
function slider_backend_enqueue_script($hook) {
    wp_enqueue_script ('jquery-ui-sortable');
    wp_enqueue_script ('slideshow-admin-script', plugins_url( '_inc/admin-script.js', __FILE__ ),array("jquery"));    

    wp_localize_script( 'slideshow-admin-script', 'admin_ajax_object',
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' )
        )
    );
}

/*include script and style for frontend */
function slider_frontend_enqueue_script(){
	wp_enqueue_script ('slideshow-script', plugins_url( '_inc/script.js', __FILE__ ),array("jquery"));    
	wp_enqueue_style( 'slideshow-style',plugins_url( '_inc/style.css', __FILE__ ));

	wp_enqueue_script ('bjqs-script', plugins_url( 'front/lib/bjqs-1.3.min.js', __FILE__ ),array("jquery"));    
	wp_enqueue_style( 'bjqs-style',plugins_url( 'front/lib/bjqs.css', __FILE__ ));
}
