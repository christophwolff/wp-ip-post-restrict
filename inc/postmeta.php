<?php

/**
 * Adds a meta box (Checkbox) to the post editing screen
 */
function ipr_hide_post_meta() {
    add_meta_box( 'ipr_hide_post_meta', __( 'Hide Post', 'ipr-textdomain' ), 'ipr_meta_callback', array('post', 'corporateinsights'), 'side', 'high' );
}
add_action( 'add_meta_boxes', 'ipr_hide_post_meta' );
 
/**
 * Outputs the content of the meta box
 */
 
function ipr_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'ipr_nonce' );
    $ipr_stored_meta = get_post_meta( $post->ID );
    ?>
 
 <p>
    <span class="ipr-row-title"><?php _e( 'Restrict Post to specified IP IPs', 'ipr-textdomain' )?></span>
    <div class="ipr-row-content">
        <label for="hide-post-checkbox">
            <input
                type="checkbox" 
                name="hide-post-checkbox" 
                id="hide-post-checkbox" 
                value="yes" 
                <?php if ( isset ( $ipr_stored_meta['hide-post-checkbox'] ) ) checked( $ipr_stored_meta['hide-post-checkbox'][0], 'yes' ); ?> />

            <?php _e( 'Restrict', 'ipr-textdomain' )?>
        </label>
 
    </div>
</p>   
 
<?php

}
 
/**
 * Saves the custom meta input
 */
function ipr_meta_save( $post_id ) {
 
    // Checks save status - overcome autosave, etc.
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'ipr_nonce' ] ) && wp_verify_nonce( $_POST[ 'ipr_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
// Checks for input and saves - save checked as yes and unchecked at no
if( isset( $_POST[ 'hide-post-checkbox' ] ) ) {
    update_post_meta( $post_id, 'hide-post-checkbox', 'yes' );
} else {
    update_post_meta( $post_id, 'hide-post-checkbox', 'no' );
}
 
}
add_action( 'save_post', 'ipr_meta_save' );