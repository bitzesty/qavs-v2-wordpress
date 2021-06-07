<?php

use Carbon_Fields\Container;
use Carbon_Fields\Block;
use Carbon_Fields\Field;
use League\Csv\Reader;


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

  $performedImport = false;

  if (isset($_FILES['csv_file']) && !$_FILES['csv_file']['error']) {
    $csv = Reader::createFromPath($_FILES['csv_file']['tmp_name'], 'r');
    $csv->setHeaderOffset(0);

    $performedImport = true;

    $headers = $csv->getHeader();
    $records = $csv->getRecords(); //returns all the CSV records as an Iterator object

    $yearHeader = $headers[0];
    $lieutenancyHeader = $headers[1];
    $nameHeader = $headers[2];
    $citationHeader = $headers[4];
    $firstTypeHeader = $headers[5];
    $secondTypeHeader = $headers[6];
    $websiteHeader = $headers[7];

    $successful = [];
    $errors = [];

    $lietenanciesMapping = array_flip(QavsWebsite::lieutenanciesMapping());
    $groupTypesMapping = array_flip(QavsWebsite::groupTypeMapping());

    foreach ($records as $record) {
      $awardee = get_page_by_title($record[$nameHeader], OBJECT, 'awardee');

      $id = wp_insert_post([
        'post_title' => $record[$nameHeader],
        'post_type' => 'awardee',
        'post_status' => 'publish',
        'ID' => $awardee ? $awardee->ID : 0
      ]);

      if (!$id) {
        $errors[] = [$id, $record[$nameHeader]];
        continue;
      }
      
      carbon_set_post_meta( $id, 'awardee_award_year', $record[$yearHeader] );
      if (!empty($record[$lieutenancyHeader])) {
        carbon_set_post_meta( $id, 'awardee_ceremonial_county', $lietenanciesMapping[$record[$lieutenancyHeader]] );
      }
      carbon_set_post_meta( $id, 'awardee_short_citation', $record[$citationHeader] );
      if (!empty($record[$firstTypeHeader])) {
        carbon_set_post_meta( $id, 'awardee_group_type_1', $groupTypesMapping[$record[$firstTypeHeader]] );
      }
      if (!empty($record[$secondTypeHeader])) {
        carbon_set_post_meta( $id, 'awardee_group_type_2', $groupTypesMapping[$record[$secondTypeHeader]] );
      }
      carbon_set_post_meta( $id, 'awardee_website', $record[$websiteHeader] );
      carbon_set_post_meta( $id, 'awardee_has_news_article', '0' );
      
      $successful[] = [$id, $record[$nameHeader]];
    }
  }

  echo '<h2> Import Awardees</h2>';
?>

  <form action="" method="post" enctype="multipart/form-data" id="poststuff">
    <div class="postbox">
      <div class="postbox-header">
        <h2>Import CSV</h2>
      </div>
      <div class="inside">
        <div class="cf-container__fields">
          <div class="cf-field cf-text">
            <div class="cf-field__head">
              <label class="cf-field__label" for="file">File</label>
            </div>
            <div class="cf-field__body">
              <input type="file" id="file" name="csv_file" />
            </div>
          </div>
          <p>
            <button type="submit" class="button button-primary">Import CSV</button>
          </p>
        </div>
      </div>
    </div>
  </form>

  <?php if($performedImport): ?>
    <h3>CSV import results</h3>

    <p>Successfully imported records: <?php echo count($successful); ?></p>
    <p>Records with error: <?php echo count($errors); ?></p>

    <?php if (count($errors) > 0): ?>
      <p>Here's a breakdown of the records that failed:</p>
      <ul>
        <?php foreach($errors as $error): ?>
          <li>
            <?php echo $error[1]; ?>
          </li>
        <?php endforeach; ?>
      </ul>
      <p>Please fix these rows in the CSV and reupload it. Existing records will not be duplicated.</p>
    <?php endif; ?>
  <?php endif; ?>

<?php
}
