// The numbered icon is rendered via PHP now so this JS file is no longer needed?
function wpgoplugins_all_admin_pages_fw_fn() {

  function update_menu($, plugin_data) {

    const {hook, new_features_number, nav_status, main_menu_label, menu_type, plugin_prefix } = plugin_data;
  
    // Add numbered icon to menu/tab label.
    if (new_features_number === '0') {
      return; // nothing to see here!
    }
  
    const new_features_number_html = ' <span class="update-plugins count-' + new_features_number + '"><span class="plugin-count">' + new_features_number + '</span></span>';
  
    // Add numbered icon to menu items.
    if (nav_status === 'menu') {
      if (menu_type === 'sub') { // Normal sub menu item.
        // @todo Add numbered counters to top level menu item.
        $('.fs-submenu-item.wpgo-plugins:contains("New Features")').append(add_features_number);
  
      } else { // cpt sub menu item
        $('.wp-menu-name:contains(' + main_menu_label + ')').append(new_features_number_html);
        $('.wp-submenu li > a:contains("New Features")').append(add_features_number);
      }
    } else { // tabs
      $('ul#adminmenu li > a:contains(' + main_menu_label + ')').append(new_features_number_html);
    }

    function add_features_number(index, currentValue) {
      //alert(currentValue);
      //return "I am new";
      const href = this.getAttribute("href");
      const contains_prefix = href.includes(plugin_prefix);
  
      if(contains_prefix) {
        // console.log('TRUE', href, contains_prefix);
        return new_features_number_html;
      }
    }
  }

  return {
    update_menu: update_menu
  }
}

const wpgoplugins_all_admin_pages_fw = wpgoplugins_all_admin_pages_fw_fn();