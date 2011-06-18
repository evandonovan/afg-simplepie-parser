<?php
// Include the code to setup the AFG Simplepie parser.
require_once('afg-simplepie.inc');

// define the defaults for the CSV file
define(FILES_DIR, 'files');
define(DEFAULT_CSV, 'US-radius-points.csv');

$row = 0;
// open the CSV file
if (($handle = fopen(FILES_DIR . '/' . DEFAULT_CSV, "r")) !== FALSE) {
  // parse the csv file of latitude/longitudes, and then show result counts
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    // set the latitude/longitude (as "vol_loc" parameter)
    $vol_loc = implode(',', $data);
    // get the feed url
    $feed_url = afg_set_feed_url(array('vol_loc' => $vol_loc));
    echo $feed_url . "<br/>";
    $row++;
  }
  fclose($handle);
}
