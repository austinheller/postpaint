<?php
/**
 * Plugin Name: Postpaint
 * Plugin URI: https://github.com/austinheller/postpaint
 * Description: Add custom CSS to posts, pages, and other custom post types.
 * Version: 0.21
 * Author: Austin Heller
 * Author URI: http://aheller.me
 * Text Domain: postpaint
 * License: GPLv2 or later
**/


/*
Set up meta boxes.
*/

$postpaint_post_types = array();

add_action( 'admin_init', 'postpaint_get_post_types' );

function postpaint_get_post_types() {
  global $postpaint_post_types;
  $postpaint_post_types = get_post_types();
}

function postpaint_add_meta_boxes() {
  global $postpaint_post_types;
  foreach($postpaint_post_types as $post_type) {
    if($post_type == 'nav_menu_item' || $post_type == 'attachment' || $post_type == 'revision') {
      continue;
    }
    add_meta_box( 'postpaint_css', __( 'Custom CSS', 'postpaint' ), 'postpaint_meta_callback', $post_type );
  }
}

add_action( 'add_meta_boxes', 'postpaint_add_meta_boxes' );

function postpaint_meta_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'postpaint_nonce' );
  $postpaint_stored_meta = get_post_meta( $post->ID );
  ?>
  <p>
    <textarea name="<?php echo 'postpaint_styles'; ?>" id="postpaint-css-code"><?php if ( isset ( $postpaint_stored_meta['postpaint_styles'] ) ) echo $postpaint_stored_meta['postpaint_styles'][0]; ?></textarea>
  </p>
  <?php
}

function postpaint_meta_save( $post_id ) {
  $is_autosave = wp_is_post_autosave( $post_id );
  $is_revision = wp_is_post_revision( $post_id );
  $is_valid_nonce = ( isset( $_POST[ 'postpaint_nonce' ] ) && wp_verify_nonce( $_POST[ 'postpaint_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
  if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
      return;
  }
  if( isset( $_POST[ 'postpaint_styles' ] ) ) {
      update_post_meta( $post_id, 'postpaint_styles', wp_filter_nohtml_kses( $_POST[ 'postpaint_styles' ] ) );
  }
}

add_action( 'save_post', 'postpaint_meta_save' );

/*
Enqueue scripts
*/
function postpaint_admin_enqueue_scripts() {
  wp_enqueue_style( 'codemirror-css', plugin_dir_url( __FILE__ ) . 'scripts/codemirror/codemirror.css' );
  wp_enqueue_script( 'codemirror', plugin_dir_url( __FILE__ ) . 'scripts/codemirror/codemirror.js' );
  wp_enqueue_script( 'codemirror-mode-css', plugin_dir_url( __FILE__ ) . 'scripts/codemirror/mode/css/css.js' );
  wp_enqueue_style( 'postpaint-editor-css', plugin_dir_url( __FILE__ ) . 'styles/postpaint-editor.css' );
  wp_enqueue_script( 'postpaint-js', plugin_dir_url( __FILE__ ) . 'scripts/postpaint-editor.js' );
}

add_action( 'admin_enqueue_scripts', 'postpaint_admin_enqueue_scripts' );

/*
Helper methods
*/
function postpaint_has_css($id) {
  if(! $id) $id = get_the_id();
  $existing_css = get_post_meta( $id, 'postpaint_styles' );
  if( $existing_css ) return true;
  return false;
}

function postpaint_render_style_tag($styles) { ?>
  <?php if($styles): ?>
  <style type="text/css" id="postpaint">
    <?php echo $styles; ?>
  </style>
  <?php endif; ?>
<?php }

function postpaint_get_styles($id) {
  if(! $id) $id = get_the_id();
  $styles = get_post_meta( $id, 'postpaint_styles', true );
  return $styles;
}

/*
Site actions
*/
function postpaint_wp_head() {
  $id = get_the_id();
  if( is_singular() && postpaint_has_css($id) ) {
    $styles = postpaint_get_styles($id);
    postpaint_render_style_tag($styles);
  }
}

add_filter('wp_head', 'postpaint_wp_head');

function postpaint_body_class($classes) {
  $this_theme = wp_get_theme();
  $theme_id = esc_html( $this_theme->get( 'TextDomain' ) );
  $classes[] = 'theme-' . $theme_id;
  return $classes;
}

add_filter('body_class', 'postpaint_body_class');


?>
