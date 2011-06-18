<?php
// Include the code to setup the AFG Simplepie parser.
require_once('afg-simplepie.inc');

// define the defaults for the CSV file
define(FILES_DIR, 'files');
define(DEFAULT_CSV, 'US-radius-points.csv');

$row = 0;
$total_items = 0;
// open the CSV file
if (($handle = fopen(FILES_DIR . '/' . DEFAULT_CSV, "r")) !== FALSE) {
  echo '<ul>';
  // parse the csv file of latitude/longitudes, and then show result counts
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    // set the latitude/longitude (as "vol_loc" parameter)
    $vol_loc = implode(',', $data);
    // get the feed url
    $feed_url = afg_set_feed_url(array('vol_loc' => $vol_loc));
    // load the feed
    $feed = afg_parser_init($feed_url);
    // count the items in the feed (performance-intensive because it actually gets a full array)
    $num_items = $feed->get_item_quantity();
    // destruct the feed manually (cf. http://simplepie.org/wiki/faq/i_m_getting_memory_leaks)
    $feed->__destruct();
    unset($feed);
    // @todo: make it so the feed location + number of results is logged to file
    echo '<li>' . $vol_loc . ': ' . $num_items . ' results.</li>';
    $row++;
    $total_items = $total_items + $num_items;
  }
  echo '</ul>';
  fclose($handle);
}
echo '<p>Total items with Christian terms on AFG: ' . $total_items . 
'</p>';
