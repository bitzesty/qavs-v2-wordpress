<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package QAVS
 */

$pageLanguage = carbon_get_post_meta(get_the_ID(), "page_language");

?>
	<footer id="colophon" class="site-footer">
		<div class="container">
      <div class="site-footer__inner">
        <div class="site-footer__links">
          <nav aria-label="Footer navigation">
            <a href='/privacy-policy'>Privacy Statement</a>
            <a href='/accessibility-statement'>Accessibility Statement</a>
            <a href='/cookie-policy'>Cookie Policy</a>
            <a href='/cookie-settings'>Cookie Settings</a>
            <a href='/sitemap'>Sitemap</a>
          </nav>

          <?php if($pageLanguage != 'cy'): ?>
            <p>
              Information on how to make a nomination is also <a href="/gwneud-enwebiad/ynglyn-ag-enwebu/">available in Welsh (Cymraeg)</a>.
            </p>
          <?php else: ?>
            <p aria-hidden="true">&nbsp;</p>
          <?php endif; ?>
          <nav aria-label="Contact information">
            <a href="mailto:kingsaward@dcms.gov.uk" class="site-footer__email">
              <svg width="19" height="13" viewBox="0 0 19 13" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Email: " focusable="false">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M0.872596 0.105525C1.03106 0.039822 1.20324 0 1.38461 0H17.5385C17.7166 0 17.8871 0.0344674 18.0433 0.0979752L10.0024 7.25106C9.64996 7.56433 9.25786 7.56411 8.90625 7.25106L0.872596 0.105525ZM0 1.44719C0 1.37608 0.00503538 1.30445 0.0144231 1.23614L8.0048 8.35153C8.83456 9.09025 10.074 9.08907 10.9038 8.35153L18.9086 1.23614C18.918 1.30445 18.9231 1.37608 18.9231 1.44719V11.0952C18.9231 11.8969 18.3055 12.5424 17.5385 12.5424H1.38461C0.617552 12.5424 0 11.8969 0 11.0952V1.44719Z" fill="#1F1F1F"/>
              </svg>kingsaward@dcms.gov.uk
            </a>
          </nav>
        </div>

        <div class="site-footer__copyright">
          <a href="https://www.nationalarchives.gov.uk/information-management/re-using-public-sector-information/uk-government-licensing-framework/crown-copyright/" target="_blank" rel="noopener nofollow">
            <img src="/wp-content/themes/qavs/images/crown-footer.png" alt="GOV.UK crown symbol">
            <span>&copy; Crown copyright</span>
          </a>
        </div>
      </div>
    </div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>
<?php wp_footer(); ?>

</body>
</html>
