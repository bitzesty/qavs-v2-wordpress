<?php

function qavs_custom_post_types() {
  register_post_type('awardee',
    array(
      'labels'      => array(
        'name'          => __('Awardees', 'textdomain'),
        'singular_name' => __('Awardee', 'textdomain'),
      ),
      'public'      => false,
      'has_archive' => false,
    )
  );
}
add_action('init', 'qavs_custom_post_types');
