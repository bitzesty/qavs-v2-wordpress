<?php

namespace WPGO_Plugins\Plugin_Framework;

/*
*	Class will address issues appearing because of other plugins and themes, 
* and help our plugin to become compatible with them.
*/

class Compatibility_FW {

  protected $module_roots;

  /* Class constructor. */

  public function __construct($module_roots, $plugin_data, $custom_plugin_data, $utility) {
    $this->module_roots = $module_roots;
    $this->custom_plugin_data = $custom_plugin_data;
    //to remove the meta box for a specific CPT when GP theme is installed
    add_action( 'add_meta_boxes', array( &$this, 'remove_layout_meta_box'), 999 );
  }

  //remove the generatepress theme metabox
  public function remove_layout_meta_box() {
    remove_meta_box('generate_layout_options_meta_box', $this->custom_plugin_data->cpt_slug, 'side');
  }

}
?>