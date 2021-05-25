<?php

use Carbon_Fields\Container;
use Carbon_Fields\Block;
use Carbon_Fields\Field;


function qavs_custom_post_types() {
  register_post_type('awardee',
    array(
      'labels'      => array(
        'name'          => __('Awardees', 'textdomain'),
        'singular_name' => __('Awardee', 'textdomain'),
      ),
      'public'      => false,
      'has_archive' => false,
      'rewrite'     => array( 'slug' => 'products' ),
      'supports' => array('title'),
      'show_ui' => true,
      'menu_icon' => 'dashicons-awards'
    )
  );
}
add_action('init', 'qavs_custom_post_types');

add_action('admin_menu', 'qavs_add_import_csv_submenu');

//admin_menu callback function

function qavs_add_import_csv_submenu(){

  add_submenu_page(
                  'edit.php?post_type=awardee', //$parent_slug
                  'Import CSV',  //$page_title
                  'Import CSV',        //$menu_title
                  'publish_posts',           //$capability
                  'import-awardees-csv',//$menu_slug
                  'qavs_import_csv_awardees_page'//$function
  );

}

//add_submenu_page callback function

function qavs_import_csv_awardees_page() {

  echo '<h2> Import Awardees from CSV </h2>';
?>

  <form action="" method="post" enctype="multipart/form-data">
    <?php Field::make( 'file', 'crb_file', __( 'File' ) ) ?>
  </form>

<?php
}
