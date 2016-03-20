<?php
class WPAS_Shortcode {

    function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script_style' ) );
        add_shortcode( 'animate_slider', array( $this, 'shortcode_handler' ) );
    }

    /**
     * Shortcode
     */
    function shortcode_handler( $atts ) {
        $atts = shortcode_atts( array(
            'id' => ''
        ), $atts, 'animate_slider' );

        $slides = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => 'wpas_slide',
            'post_parent' => 'wpas_slider',
            'post_status' => 'publish'
        ));
         ?>
        <div id="wp-animate-slider-<?php echo $atts['id']?>">
            <ul class="anim-slider">

                <?php $slides_array = array();
                $slideshow_style = '<style>'; ?>

                <?php foreach( $slides as $key => $slide ) : ?>

                    <li class="anim-slide">
                    <?php $slide_meta = get_post_meta( $slide->ID, 'slide_meta', true ); //echo '<pre>';print_r($slide_meta);echo '</pre>';

                    if( isset( $slide_meta['layers'] ) && is_object( $slide_meta['layers'] ) ) :
                        array_push( $slides_array , $slide_meta['layers']) ;

                        foreach( $slide_meta['layers'] as $layer_id => $layer_array ): ?>
                            <?php
                            //check if there anything related to style
                            /**style stuff**/
                            if( isset( $layer_array->settings ) && is_object( $layer_array->settings ) ) {

                                $slideshow_style .= '#'.$layer_id.'{';
                                foreach( $layer_array->settings as $property => $value ) {
                                    $slideshow_style .= $property. ' : ' .$value. ( in_array(  $property, array( 'font-size','font-style' ) ) ? 'px' : '' ) .';' ;
                                }
                                $slideshow_style .= '}';
                            }

                            if( isset( $layer_array->final_pos ) && is_object( $layer_array->final_pos ) ) {
                                $slideshow_style .= '#'.$layer_id.'{';
                                foreach( $layer_array->final_pos as $property => $value ) {
                                    $slideshow_style .= ( $property == 'x' ? 'top' : 'left' ) . ' : ' .$value. 'px;' ;
                                }
                                $slideshow_style .= '}';
                            }
                            /**style stuff ends**/
                            switch( $layer_array->type ) {
                                case 'image' :
                                    ?>
                                    <img id="<?php echo $layer_id; ?>" src="<?php echo isset( $layer_array->imgurl ) ? $layer_array->imgurl : '' ; ?>" alt="image"/>
                                    <?php
                                    break;
                                case 'text' :
                                    ?>
                                    <p id="<?php echo $layer_id; ?>"><?php echo $layer_array->content; ?></p>
                                    <?php
                                    break;
                            }
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </li>
                <?php endforeach;  ?>
                <?php $slideshow_style .= '</style>';?>
                <?php echo $slideshow_style;?>
                <!-- Arrows -->
                <nav class="anim-arrows">
                    <span class="anim-arrows-prev"></span>
                    <span class="anim-arrows-next"></span>
                </nav>
                <!-- Dynamically created dots -->
            </ul>
            <?php $slides_array = json_encode($slides_array); ?>
        </div>
        <script>
            (function($){
                $(document).ready(function(){
                    //console.log(JSON.parse('<?php echo $slides_array; ?>'));
                    var anim_data = new Object({
                        autoplay	:true,
                        interval	:5500,
                        animations : {}
                    });
                    var slides_array = JSON.parse('<?php echo $slides_array; ?>');
                    //console.log(slides_array);
                    if ( typeof slides_array != "string") {
                        for( k in slides_array ) {
                            var tmp_array = {};
                            for( key in slides_array[k] ) {
                                tmp_array['#'+key] = slides_array[k][key];
                            }
                            anim_data.animations[k] = tmp_array;
                            //console.log(anim_data.animations[k]);
                        }
                    }
                    console.log(anim_data);
                    $('.anim-slider').animateSlider(anim_data);
                })
            }(jQuery))
        </script>
        <?php
    }

    public static function init() {
        new WPAS_Shortcode();
    }

    /**
     * Enqueue scripts and styles
     */
    function enqueue_script_style() {
        wp_enqueue_style( 'wpas-animate-css', plugins_url( '../assets/css/jquery.animateSlider.css', __FILE__) );
        wp_enqueue_style( 'wpas-normalize-css', plugins_url( '../assets/css/normalize.css', __FILE__) );
        wp_enqueue_style( 'wpas-slidermain-css', plugins_url( '../assets/css/slider-main.css', __FILE__) );

        wp_enqueue_script( 'wpas-modernizr', plugins_url( '../assets/js/modernizr.js', __FILE__), array( 'jquery') );
        wp_enqueue_script( 'wpas-js', plugins_url( '../assets/js/jquery.animateSlider.js', __FILE__), array( 'jquery', 'wpas-modernizr' ) );
    }
}

WPAS_Shortcode::init();

