<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<!-- ============================================
SECURITY STEP
============================================== -->
<?php
$sectionId   = 'section-security';
$expandClass = $sectionId == $open_section ? 'open' : 'close';
?>

<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
        <h2 class="header expand-header">Installer Security</h2>
        <div class="content" >
            <div id="dup-help-installer" class="help-page">
                The installer security screen will allow for basic password protection on the installer. The password is set at package creation time.  The password
                input on this screen must be entered before proceeding with an install.   This setting is optional and can be turned on/off via the package creation screens.
                <br/><br/>

                If you do not recall the password then login to the site where the package was created and click the details of the package to view the original password.
                To validate the password just typed you can toggle the view by clicking on the lock icon.   For detail on how to override this setting visit the online FAQ for
                <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-030-q" target="_blankopen_section">more details</a>.

                <table class="help-opt">
                    <?php
                    dupxTplRender('pages-parts/help/widgets/option-heading');
                    dupxTplRender('pages-parts/help/options/security/locked');
                    dupxTplRender('pages-parts/help/options/security/unlocked');
                    ?>
                </table>
                <br/>
                Note: Even though the installer has a password protection feature, it should only be used for the short term while the installer is being used. All installer files should and
                must be removed after the install is completed.  Files should not to be left on the server for any long duration of time to prevent any security related issues.
            </div>
        </div>
    </section>