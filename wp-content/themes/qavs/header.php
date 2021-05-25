<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package QAVS
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
  <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'qavs' ); ?></a>
  
  <div aria-live="polite">
    <div class="cookie-banner hidden">
      <div class="container">
        <div class="initial-content">
          <h2>Cookies on QAVS.UK</h2>
          <p>
            We use some essential cookies to make this website work.
          </p>
          <p>
            We’d like to set additional cookies to understand how you use QAVS.UK, remember your settings and improve our services.
          </p>

          <div class="cookie-banner__buttons">
            <button class="button button--inverse accept-additional-cookies">
              Accept additional cookies
            </button>
            <button class="button button--inverse reject-additional-cookies">
              Reject additional cookies
            </button>
            <a href="/cookie-policy">View cookie policy and settings</a>
          </div>
        </div>
        <div class="rejected-message hidden">
          <p>
            You have rejected additional cookies. You can <a href='/cookie-policy'>change your cookie settings</a> at any time.
          </p>
          <div class="cookie-banner__buttons">
            <button class="button button--inverse hide-cookie-banner">
              Hide this message
            </button>
          </div>
        </div>
        <div class="accepted-message hidden">
          <p>
            You have accepted additional cookies. You can <a href='/cookie-policy'>change your cookie settings</a> at any time.
          </p>
          <div class="cookie-banner__buttons">
            <button class="button button--inverse hide-cookie-banner">
              Hide this message
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if (!empty(carbon_get_theme_option( 'qavs_linkedin' )) || !empty(carbon_get_theme_option( 'qavs_twitter' )) || !empty(carbon_get_theme_option( 'qavs_facebook' )) || !empty(carbon_get_theme_option( 'qavs_login' ))): ?>
  <div class="pre-header">
    <div class="container">
      <?php if (!empty(carbon_get_theme_option( 'qavs_linkedin' ))): ?>
        <a href="<?php echo carbon_get_theme_option( 'qavs_linkedin' );?>" class="pre-header__social-link linkedin" aria-label="LinkedIn">
          <svg width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2.125 4.003c.647 0 1.169-.191 1.565-.574.397-.382.591-.858.583-1.428C4.266 1.424 4.07.946 3.684.568 3.3.189 2.787 0 2.148 0S.991.19.595.568C.198.946 0 1.424 0 2c0 .57.192 1.046.577 1.428.385.383.894.574 1.525.574h.023zm7.808 13.179v-6.473c0-.398.042-.714.127-.948.162-.398.406-.731.733-1 .327-.27.734-.404 1.219-.404.662 0 1.149.232 1.46.696.313.464.469 1.106.469 1.926v6.203h3.8v-6.648c0-1.709-.401-3.006-1.202-3.892-.8-.885-1.86-1.328-3.176-1.328-.485 0-.926.06-1.322.181-.397.121-.732.29-1.005.51-.274.218-.491.42-.653.608a5.022 5.022 0 00-.45.618V5.583h-3.8l.011.562c.007.315.01 1.184.012 2.606v1.612c-.002 1.822-.01 4.095-.023 6.819h3.8zM4.03 5.582v11.6H.219v-11.6h3.812z" fill="#fff"/></svg>
        </a>
      <?php endif; ?>
      <?php if (!empty(carbon_get_theme_option( 'qavs_twitter' ))): ?>
        <a href="<?php echo carbon_get_theme_option( 'qavs_twitter' );?>" class="pre-header__social-link twitter" aria-label="Twitter">
          <svg width="19" height="15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.605 14.979c1.31 0 2.541-.206 3.692-.618 1.15-.413 2.133-.965 2.949-1.657a11.108 11.108 0 002.109-2.389c.59-.9 1.03-1.84 1.32-2.82a10.34 10.34 0 00.423-3.42 7.706 7.706 0 001.852-1.896 7.4 7.4 0 01-2.126.568 3.563 3.563 0 001.623-2.02 7.271 7.271 0 01-2.343.885C15.372.84 14.473.455 13.405.455c-1.021 0-1.892.357-2.612 1.072a3.519 3.519 0 00-1.08 2.593c0 .272.03.552.091.84A10.347 10.347 0 015.56 3.83a10.452 10.452 0 01-3.367-2.706 3.571 3.571 0 00-.503 1.85c0 .628.15 1.21.446 1.747.297.537.698.972 1.2 1.305a3.683 3.683 0 01-1.668-.465v.045c0 .885.28 1.663.84 2.332a3.637 3.637 0 002.12 1.265c-.32.083-.644.125-.971.125-.214 0-.446-.019-.698-.057.237.734.67 1.337 1.303 1.81a3.617 3.617 0 002.15.732c-1.342 1.044-2.87 1.566-4.584 1.566a7.95 7.95 0 01-.892-.045c1.715 1.097 3.605 1.645 5.67 1.645z" fill="#fff"/></svg>
        </a>
      <?php endif; ?>
      <?php if (!empty(carbon_get_theme_option( 'qavs_facebook' ))): ?>
        <a href="<?php echo carbon_get_theme_option( 'qavs_facebook' );?>" class="pre-header__social-link facebook" aria-label="Facebook">
          <svg width="10" height="17" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.571.754v2.597H8.012c-.569 0-.953.118-1.151.354-.199.236-.298.59-.298 1.062v1.858h2.909l-.387 2.911H6.563V17H3.524V9.536H.993v-2.91h2.531V4.481c0-1.22.345-2.166 1.033-2.838C5.245.972 6.162.636 7.307.636c.973 0 1.728.04 2.264.118z" fill="#fff"/></svg>
        </a>
      <?php endif; ?>
      <?php if (!empty(carbon_get_theme_option( 'qavs_login' ))): ?>
        <a href="<?php echo carbon_get_theme_option( 'qavs_login' );?>" class="pre-header__link">
          Login
        </a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
	<header id="masthead" class="site-header">
    <div class="container">
      <a href='/' class="site-branding">
        <img src="/wp-content/themes/qavs/images/QAVS_logo_web_3x.png" alt="The Queen's Award for Voluntary Service" />  
      </a><!-- .site-branding -->

      <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Menu', 'qavs' ); ?></button>
      <nav id="site-navigation" class="main-navigation">
        <?php
        wp_nav_menu(
          array(
            'theme_location' => 'menu-1',
            'menu_id'        => 'primary-menu',
          )
        );
        ?>
      </nav><!-- #site-navigation -->
    </div>
  </header><!-- #masthead -->
  <div class="phase-banner">
    <div class="container">
      <p>
        <span>BETA</span>
        This is a new website – your <a href="">feedback</a> will help us to improve it.
      </p>
    </div>
  </div>
  <div id="primary" tabindex="-1">
