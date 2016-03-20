<?php

class WPAS_slider_meta_box {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_slider_custom_box' ) );
        add_action( 'save_post', array( $this, 'save_postdata' ) );
    }

    /**
     * Add slider meata box
     */
    function add_slider_custom_box() {
        add_meta_box(
            'wpas_slider_meta',
            __( 'Slides', 'wpas' ),
            array( $this, 'render_slider_content') ,
            'wpas_slider'
        );
        add_meta_box(
            'wpas_slider_shortcode',
            __( 'Slider Shortcode', 'wpas' ),
            array( $this, 'render_slider_shortcode') ,
            'wpas_slider'
        );
    }

    /**
     * Slider shortcode
     */
    function render_slider_shortcode( $slider_post ) {
        ?>
        <input type="text" readonly value="[animate_slider id=<?php echo $slider_post->ID; ?>]"/>
    <?php
    }

    /**
     * Render slider content
     * slider posts
     */
    function render_slider_content( $slider_post ) {

        $slides = get_posts( array(
            'post_type' => 'wpas_slide',
            'post_parent' => $slider_post->ID
        ) ); ?>
        <div class="slider-metabox">
            <a class="button button-success" href="<?php echo admin_url().'post-new.php?post_type=wpas_slide&parent_id='.$slider_post->ID;?> ">New Slide</a>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                <tr>
                    <th>Slide Name</th>
                </tr>
                </thead>

                <tbody id="the-list">
                <?php if( !empty( $slides ) ) :  ?>
                    <?php foreach ( $slides as $key => $slide ) : ?>
                        <tr>
                            <td>
                                <p><?php echo $slide->post_title; ?></p>
                                <p><a href="<?php echo get_edit_post_link($slide->ID); ?>">Edit</a> | <a href="<?php echo get_delete_post_link($slide->ID) ?>">Remove</a></p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <td>No Slide Created Yet ! Create <a href="<?php echo admin_url().'post-new.php?post_type=wpas_slide&parent_id='.$slider_post->ID;?> ">New Slide</a></td>
                <?php endif; ?>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>
    <?php
    }

    /**
     * Save the data
     */
    function save_postdata() {

    }


    public static function init() {
        new WPAS_slider_meta_box();
    }
}

WPAS_slider_meta_box::init();