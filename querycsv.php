<?php
// Include the code to setup the AFG Simplepie parser.
require_once('afg-simplepie.inc');

define(FILES_DIR, 'files');
define(DEFAULT_CSV, 'US-radius-points.csv');

$row = 0;
if (($handle = fopen(FILES_DIR . '/' . DEFAULT_CSV, "r")) !== FALSE) {
  echo '<ul>';
	// parse the csv file of latitude/longitudes
	// TODO: make it so that it logs these to a file - find out also if there is a way to count the elements without causing it to get out of memory errors
	// (currently this is just showing the first 10 since that after about 30 it runs out of memory, since they all get stored in an array)
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	  // set the latitude/longitude (as "vol_loc" parameter)
		$vol_loc = implode(',', $data);
		// get the feed url
		$feed_url = afg_set_feed_url(array('vol_loc' => $vol_loc));
		// load the feed
		$feed = afg_parser_init($feed_url);
		// count the items in the feed (performance-intensive because it actually gets a full array)
		$num_items = $feed->get_item_quantity();
		// TODO: Google geocode the location (possibly in a separate batch - that way, I would know where all my center points are)
		
		// destruct the feed manually (cf. http://simplepie.org/wiki/faq/i_m_getting_memory_leaks)
		$feed->__destruct();
		unset($feed);
		echo '<li>' . $vol_loc . ': ' . $num_items . ' results.</li>';
		$row++;
  }
	echo '</ul>';
  fclose($handle);
}