function wpgoplugins_admin_settings_fw_fn() {

  function move_welcome_page_tab($, plugin_data) {

    // @todo there is an issue with this method as the numbered icon doesn't move with tab
    return;

    // console.log('move-welcome-page-tab');

    // move welcome page tab to the last tab position
    var navTabWrapper = $('.nav-tab-wrapper');
    navTabWrapper.find('.nav-tab:contains("New Features")').appendTo(navTabWrapper);
  }

  function move_about_page_tab($, plugin_data) {

    // move about page tab to the last tab position
    var navTabWrapper = $('.nav-tab-wrapper');
    navTabWrapper.find('.nav-tab:contains("About")').appendTo(navTabWrapper);
  }

  function add_numbered_icon_to_tab_label($, plugin_data) {

    const {hook, new_features_number, nav_status, main_menu_label, menu_type, plugin_prefix } = plugin_data;

    if (new_features_number === '0') {
      return; // nothing to see here!
    }

    let new_features_number_html = '';

    // add numbered icon to tab label
    if (nav_status === 'tabs') {
      let new_features_number_html = ' <span class="new-features-count">' + new_features_number + '</span>';
      $('.nav-tab-wrapper .nav-tab:nth-child(2)').append(new_features_number_html);
    }
  }

  function collapsible_settings_section($, plugin_data) {

    // setup event listeners for expandable sections
    ['shortcodes', 'blocks'].map(function (section) {
      const btn = $('#' + section + '-btn');
      const wrap = $('#' + section + '-wrap');

      btn.on('click', function () {
        var isHidden = wrap.is(":hidden");
        wrap.toggle(function () {
          if (isHidden) {
            btn.html('Collapse <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-up-alt2"></span>');
          } else {
            btn.html('Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span>');
          }
        });
      });
    });
  }

  return {
    move_welcome_page_tab: move_welcome_page_tab,
    move_about_page_tab: move_about_page_tab,
    add_numbered_icon_to_tab_label: add_numbered_icon_to_tab_label,
    collapsible_settings_section: collapsible_settings_section
  }
}

const wpgoplugins_admin_settings_fw = wpgoplugins_admin_settings_fw_fn();
