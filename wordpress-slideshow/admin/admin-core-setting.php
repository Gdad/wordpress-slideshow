<?php

// create custom plugin settings menu
add_action('admin_menu', 'wordpress_slideshow_create_menu');
function wordpress_slideshow_create_menu() {
    //create new top-level menu
    add_options_page('slideshow Settings', 'slideshow Settings', 'administrator', __FILE__, 'wordpress_slideshow_settings_page');
}

/*Now lets do some setting stuff here*/
function wordpress_slideshow_settings_page(){

    //do some database stuff for images
    if(isset($_POST['create_sideshow']) && wp_verify_nonce( $_POST['slideshow_image_upload_nonce'], 'slideshow_images' )):
        
        $slideshow_images = $_FILES['slideshow_images'];
        
        //include depandancies 
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        foreach ($slideshow_images['name'] as $key => $value) {
            
        if ($slideshow_images['name'][$key]) {
          $file = array(
            'name'     => $slideshow_images['name'][$key],
            'type'     => $slideshow_images['type'][$key],
            'tmp_name' => $slideshow_images['tmp_name'][$key],
            'error'    => $slideshow_images['error'][$key],
            'size'     => $slideshow_images['size'][$key]
          );
          
        $_FILES = array ("slideshow_images_attachments" => $file); 
            $slideshow_images_array = array();
            $attach_id = media_handle_upload('slideshow_images_attachments', 0 );
            if ( !is_wp_error( $attach_id ) ) {
                // There was an error uploading the image.
                $slideshow_images_array = get_option("slideshow_images_array",true);
                if(!empty($slideshow_images_array) && is_array($slideshow_images_array)){
                    $slideshow_images_array[] = $attach_id;
                }else{
                    $slideshow_images_array = array($attach_id);
                }
                update_option("slideshow_images_array",$slideshow_images_array);

            } else {
                // The image was uploaded successfully!

            }
        }
      }        
    endif;

    /*Lets add some form designing code here...because design give life to our html code :D :) */
    ?>
    <style>
        .images_lists li{
            border: 2px solid #000 !important;
            padding: 10px !important;
            box-shadow: 10px 10px 5px #888888;
            width: 150px;
            height: 170px;
            text-align:center;
        }

        
        .container .slideshow_images {
            padding-top: 50px;
        }
        .slideshow_images .left_container{
            display: block;
            float: left;
            padding-right: 15px;
            
        }
        .slideshow_images .right_container{
            padding-left: 15px;
        }
        .right_container .save_slides_order{
            float: left;
        }
    </style>

    <div class='container wrap'>
        <h1>Slideshow Setting Page</h1>
        <form method="post" multipart="" style='border: 2px solid #ccc;width: 300px;padding: 10px;' enctype="multipart/form-data">
            <label>
                Add Images: 
                <input type="file" required name='slideshow_images[]' multiple/>
            </label>
            <?php wp_nonce_field( 'slideshow_images', 'slideshow_image_upload_nonce' ); ?>
            <label><input type='submit' class='button-primary' name='create_sideshow' value='Submit'/></label>
        </form>
        <div class='slideshow_images'>
            <div class='left_container'>
            <?php 
                $slideshow_images_array = get_option("slideshow_images_array",true);
                if(!empty($slideshow_images_array) && is_array($slideshow_images_array)):
                    echo "<p style='font-size: 11px;font-family: cursive;font-weight: bold;'>You can change order of slides by dragging up and down.</p>";
                    echo "<ul class='images_lists connectedSortable' id='sortable'>";    
                    foreach ($slideshow_images_array  as $slide_key => $slide_value) {
                        echo "<li id='item-".$slide_key."' data-img='".$slide_value."'>";
                            echo "<span style='border: 1px solid #ccc;  float:left;padding: 3px;background-color: black;color: white;font-size: 16px;'>".($slide_key+1)."</span>";
                            echo "<a href='#'  class='remove_current_item' data-id='".$slide_value."' style='float:right;color:red;'>Remove</a>";
                            echo wp_get_attachment_image( $slide_value, 'thumbnail', $icon, $attr );
                        echo "</li>";
                    }
                    echo "</ul>";
                endif;
            ?>
            </div>
            <?php if(!empty($slideshow_images_array) && is_array($slideshow_images_array)):?>
                <div class='right_container'>
                    <?php 
                        $slideshow_images_array = get_option("slideshow_images_array",true); 
                        if(!is_array($slideshow_images_array)):
                            $slideshow_images_array = array();
                        endif;
                    ?>
                    <input type='hidden' class='slideshow_image_order' value='<?php echo json_encode($slideshow_images_array);?>'>
                    <a href='#' class='save_slides_order btn button button-large button-primary'>Save Order</a>
                    <div class='loading_image_show' style='display:none;margin-left:5px;'>
                        <span style='margin: 10px;font-size: 12px;font-weight: bold;color: green;'>Reordering...</span>
                    </div>
                </div>
            <?php endif;?>
        </div>
    </div>
    <?php 
}

/*Function to handle remove slide Ajax work*/

function remove_current_slide_callback(){
    $slide_id = $_POST['slide_id'];
    $response = array();
    if(!empty($slide_id)):
        $slideshow_images_array = get_option("slideshow_images_array",true);
        
        if(($key = array_search($slide_id, $slideshow_images_array)) !== false) {
            unset($slideshow_images_array[$key]);
            $slideshow_images_array = array_values($slideshow_images_array);
            update_option("slideshow_images_array",$slideshow_images_array);

            $response['success'] = "removed";
        }
    else
        $response['error'] = "Something went wrong. please try again.";
    endif;
    die(json_encode($response));
}

/*Function to handle reorder slide Ajax work*/
function new_slide_order_ajax_callback(){
    $new_slide_order = $_POST['new_slide_order'];
    $response = array();
    if(!empty($new_slide_order)):
        update_option("slideshow_images_array",json_decode($new_slide_order));
        $response['success'] = "changed";
    else:
        $response['error'] = "Something went wrong. please try again.";
    endif;
    die(json_encode($response));
}

