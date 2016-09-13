<?php

/* 
 * @function Name: slideshow_front_view
 * @paramter: none
 * @Description: function is to create a simple sildeshow of all slides added in backend 
 * @return: image slider html
 */
add_shortcode("front_slide_show",'slideshow_front_view');
function slideshow_front_view(){
    ob_start();
    $slideshow_images_array = get_option("slideshow_images_array",true);
    if(!empty($slideshow_images_array)):
        echo "<div class='bjqs_slider' id='banner-slide'>";
            echo "<ul class='bjqs'>";
                foreach($slideshow_images_array as $key => $slide_item){
                    echo "<li>".wp_get_attachment_image( $slide_item, 'full', $icon, $attr )."</li>";
                }
            echo "</ul>";
        echo "</div>";
    endif;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}


/*include front page css and jquery*/
