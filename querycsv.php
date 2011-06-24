<?php
/*  @file querycsv.php - Mass testing of AllForGood queries.
    @todo: 1) Make a version of the function at the core of this page that uses output buffering, so that it could simply be called
             (with parameters) from another page. Question: Would the generated array be too large for memory.
          2) Make a version of the function that runs as batch operation, to parse arbitrarily large CSV's.
          3) (Possible) Make this class-based so that it would be easier to set up, and pass configuration options for the parse.
					4)  Make it so the returned data for each row is logged to a CSV file.
*/

// Include the code to setup the AFG Simplepie parser.
require_once('afg-simplepie.inc');

// define the defaults for the CSV file
define(FILES_DIR, 'files');
define(DEFAULT_CSV, 'US-radius-points.csv');

?><!DOCTYPE html>
<html>
<head>
<title>AllForGood CSV Query Parser</title>
<style type="text/css">
/* Adding some basic styles to make the page more readable */
table, table tr { margin: 0; padding: 0}
table tr td { margin: 0; padding: 5px }
tr.header { background-color: #555555; color: #FFFFFF; }
tr.footer { background-color: #CCCCCC; }
td { border: 1px solid #333333; }
</style>
</head>
<body>
<?php
/* Return the actual data from the CSV file */

// Initialize a counter for the while loop
$i = 0;
// Total items defaults to 0
$total_items = 0;
// open the CSV file
if (($handle = fopen(FILES_DIR . '/' . DEFAULT_CSV, "r")) !== FALSE) {
  echo '<table>';
  echo '<tr class="header"><td>Location</td><td>Address</td><td>Number of Items</td><td>Feed URL</td></tr>';
  // initialize the variable for saving the data about the feeds
  $results = array();
  // parse the csv file of latitude/longitudes, and then show result counts
  // set a maximum for $i so that this will not go crazy
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $i < 150) {
    // set the latitude/longitude (as "vol_loc" parameter)
    $results[$i]['vol_loc'] = implode(',', $data);
    // geocode the address
    $results[$i]['address'] = ($address = afg_google_geocode_latlon($data[0], $data[1])) ? 
    $address : '';
    // get the feed url
    $results[$i]['feed_url'] = afg_set_feed_url(array('vol_loc' => $results[$i]['vol_loc']));
    // load the feed
    $feed = afg_parser_init($results[$i]['feed_url']);
    // count the items in the feed (performance-intensive because it actually gets a full array)
    $results[$i]['num_items'] = $feed->get_item_quantity();
    // destruct the feed manually (cf. http://simplepie.org/wiki/faq/i_m_getting_memory_leaks)
    $feed->__destruct();
    unset($feed);
    echo '<tr class="data-row">';
    echo '<td>' . $results[$i]['vol_loc'] . '</td><td>' . $results[$i]['address'] . '</td><td>' . $results[$i]['num_items'] . '</td><td>' . $results[$i]['feed_url'] . '</td>';
    echo '</tr>';
    $total_items = $total_items + $results[$i]['num_items'];
    $i++;
  }
  echo '<tr class="footer"><td colspan="5">Total items with Christian terms on AllForGood: ' . $total_items . 
'</td></tr>';
  echo '</table>';
  // Close the CSV file from which the latitude/longitude data was parsed
  fclose($handle);
  // If there is a file set to write the results to, then do that
  if(isset($_GET['filename'])) {
    $fp = fopen(check_plain('parse-results/' . $_GET['filename']) . '.csv', 'w');
    foreach ($results as $fields) {
      fputcsv($fp, $fields);
    }
  }
}
else {
  echo '<p>The CSV file was not able to be loaded. Check it exists, and is readable by the web server.</p>';
}
?>
</body>
</html>
