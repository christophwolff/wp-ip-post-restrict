<?php

add_filter('network_admin_menu', 'ipr_network_admin_menu');
function ipr_network_admin_menu() {
    // Create our options page.
    add_submenu_page(
      'settings.php',
      __('IP Post Restrict Options', 'wp-ip-post-restrict'),
      __('IP Post Restrict', 'wp-ip-post-restrict'),
      'manage_network_options',
      'ipr_network_options_page',
      'ipr_network_options_page_callback'
    );

    // Create a section (we won't need a section header).
    add_settings_section(
      'default',
      '', // no title
      false,
      'ipr_network_options_page'
    );

    // Create and register our option (we make the option id very explicit because
    // this is the key that will be used to store the options.
    register_setting('ipr_network_options_page', 'ipr_network_option_ipranges');
    add_settings_field(
      'ipr_network_option_ipranges',
      __('IP Ranges', 'wp-ip-post-restrict'),
      'ipr_network_iprange_callback',
      'ipr_network_options_page',
      'default'
    );
}

/**
 * Displays our only option. Nothing special here.
 */
function ipr_network_iprange_callback() { ?>
  <textarea name="ipr_network_option_ipranges" style="width: 100%" rows="6"><?php echo get_site_option('ipr_network_option_ipranges'); ?></textarea>
  <span class="description">Whitelist IPs or IP ranges. Separate by newline.<br>Example IP: <code>12.34.56.78</code>.<br>Example IP Range: <code>12.34.56.*</code> or <code>12.34.*.*</code> or <code>12.34.56.[33-62]</code> </span>
  <?php
}

/**
 * Displays the options page. The big difference here is where you post the data
 * because, unlike for normal option pages, there is nowhere to process it by
 * default so we have to create our own hook to process the saving of our options.
 */
function ipr_network_options_page_callback() {
  if (isset($_GET['updated'])): ?>
<div id="message" class="updated notice is-dismissible"><p><?php _e('Options saved.') ?></p></div>
  <?php endif; ?>
<div class="wrap">
  <h1><?php _e('IP Post Restrict Options', 'wp-ip-post-restrict'); ?></h1>
  
  <div class="card">
    <form method="POST" action="edit.php?action=ipr_update_network_options"><?php
      settings_fields('ipr_network_options_page');
      do_settings_sections('ipr_network_options_page');
      submit_button(); ?>
    </form>
  </div>

  <div class="card">
    Your current IP <code><?php echo ipr_get_client_ip(); ?></code> <em>is <?php if (!ipr_client_ip_is_allowed()): ?><strong>not</strong><?php endif ?> part of the configured ranges</em>.
  </div>
</div>
<?php
}


/**
 * This function here is hooked up to a special action and necessary to process
 * the saving of the options. This is the big difference with a normal options
 * page.
 */
add_action('network_admin_edit_ipr_update_network_options',  'ipr_update_network_options');
function ipr_update_network_options() {
  // Make sure we are posting from our options page. There's a little surprise
  // here, on the options page we used the 'ipr_network_options_page'
  // slug when calling 'settings_fields' but we must add the '-options' postfix
  // when we check the referer.
  check_admin_referer('ipr_network_options_page-options');

  // This is the list of registered options.
  global $new_whitelist_options;
  $options = $new_whitelist_options['ipr_network_options_page'];

  // Go through the posted data and save only our options. This is a generic
  // way to do this, but you may want to address the saving of each option
  // individually.
  foreach ($options as $option) {
    if (isset($_POST[$option])) {
      // If we registered a callback function to sanitizes the option's
      // value it is where we call it (see register_setting).
      $option_value = apply_filters('sanitize_option_' . $option_name, $_POST[$option]);
      // And finally we save our option with the site's options.
      update_site_option($option, $option_value);
    } else {
      // If the option is not here then delete it. It depends on how you
      // want to manage your defaults however.
      delete_site_option($option);
    }
  }

  // At last we redirect back to our options page.
  wp_redirect(add_query_arg(['page' => 'ipr_network_options_page', 'updated' => 'true'], network_admin_url('settings.php')));
  exit;
}
