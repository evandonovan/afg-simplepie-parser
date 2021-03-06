<?php

// define the namespace for Footprint XML (All for Good's proprietary add-on), so that other elements can be parsed
define('FP_NS', 'http://www.allforgood.org/');

// define the query to be run
define('PARSER_QUERY', 'http://www.allforgood.org/api/volopps?key=christianvolunteering&output=rss&vol_loc=Kansas%20City,MO&vol_dist=500&num=10&q=-detailurl:volunteermatch%20AND%20-detailurl:christianvolunteering%20AND%20-detailurl:churchvolunteering%20AND%20%28christian%20OR%20jesus%20OR%20catholic%20OR%20ministry%29');

// define the feed file
define('PARSER_FEED', 'http://www.urbanministry.org/afg-feed-full.xml');

// define the Google Maps API key
// (site-specific; would have to be modified if others use this code)
define('GMAP_API_KEY', 'ABQIAAAADF2STd2FFyIZbSoiWXIbaxR7PiuzwriKPLyzR6zyLjSn6oZVURSUPbbY1cObAiEF0-t2-A1LNN8x1w');

// Initialize Simplepie.
function afg_parser_init($feed_url = '') {
  // Make sure SimplePie is included. 
  require_once('../sites/all/modules/feeds/libraries/simplepie.inc');
 
  // Fire up SimplePie.
  $feed = new SimplePie();

  // Disable the SimplePie cache. (We want to re-run this query each time.)
  $feed->enable_cache(FALSE);

  // Make the parser as fast as possible.
  $feed->set_stupidly_fast(TRUE);

  // Change the cache location to a writable one (@todo: required if cache is off?)
  $feed->set_cache_location('../tmp');
 
  // Set which feed to process.
  if($feed_url == '') {
    $feed->set_feed_url(PARSER_FEED); 
  }
  else {
    $feed->set_feed_url($feed_url);
  }
 
  // Run SimplePie.
  $feed->init();
 
  // This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
  $feed->handle_content_type();

  return $feed;
}

// Parse out a URL into a format with a query array - borrowed from http://api.drupal.org/api/drupal/includes--common.inc/function/drupal_parse_url/7
function drupal_parse_url($url) {
  $options = array(
    'path' => NULL, 
    'query' => array(), 
    'fragment' => '',
  );

  // External URLs: not using parse_url() here, so we do not have to rebuild
  // the scheme, host, and path without having any use for it.
  if (strpos($url, '://') !== FALSE) {
    // Split off everything before the query string into 'path'.
    $parts = explode('?', $url);
    $options['path'] = $parts[0];
    // If there is a query string, transform it into keyed query parameters.
    if (isset($parts[1])) {
      $query_parts = explode('#', $parts[1]);
      parse_str($query_parts[0], $options['query']);
      // Take over the fragment, if there is any.
      if (isset($query_parts[1])) {
        $options['fragment'] = $query_parts[1];
      }
    }
  }
  // Internal URLs.
  else {
    // parse_url() does not support relative URLs, so make it absolute. E.g. the
    // relative URL "foo/bar:1" isn't properly parsed.
    $parts = parse_url('http://example.com/' . $url);
    // Strip the leading slash that was just added.
    $options['path'] = substr($parts['path'], 1);
    if (isset($parts['query'])) {
      parse_str($parts['query'], $options['query']);
    }
    if (isset($parts['fragment'])) {
      $options['fragment'] = $parts['fragment'];
    }
  }

  // Removed the part of the original function that excluded the 'q'.

  return $options;
}

// Filter unwanted elements out of a query array - from http://api.drupal.org/api/drupal/includes--common.inc/function/drupal_get_query_parameters/7
function drupal_get_query_parameters(array $query = NULL, array $exclude, $parent = '') {
  // Set defaults, if none given.
  if (!isset($query)) {
    $query = $_GET;
  }
  // If $exclude is empty, there is nothing to filter.
  if (empty($exclude)) {
    return $query;
  }
  elseif (!$parent) {
    $exclude = array_flip($exclude);
  }

  $params = array();
  foreach ($query as $key => $value) {
    $string_key = ($parent ? $parent . '[' . $key . ']' : $key);
    if (isset($exclude[$string_key])) {
      continue;
    }

    if (is_array($value)) {
      $params[$key] = drupal_get_query_parameters($value, $exclude, $string_key);
    }
    else {
      $params[$key] = $value;
    }
  }

  return $params;
}

// Turns an array into a valid query string - http://api.drupal.org/api/drupal/includes--common.inc/function/drupal_http_build_query/7
function drupal_http_build_query(array $query, $parent = '') {
  $params = array();

  foreach ($query as $key => $value) {
    $key = ($parent ? $parent . '[' . rawurlencode($key) . ']' : rawurlencode($key));

    // Recurse into children.
    if (is_array($value)) {
      $params[] = drupal_http_build_query($value, $key);
    }
    // If a query parameter value is NULL, only append its key.
    elseif (!isset($value)) {
      $params[] = $key;
    }
    else {
      // For better readability of paths in query strings, we decode slashes.
      $params[] = $key . '=' . str_replace('%2F', '/', rawurlencode($value));
    }
  }

  return implode('&', $params);
}

// Checks if text is safe to display or use in an HTTP request - from Drupal (but not doing quotes)
function check_plain($text) {
  return htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
}

// Drupal's version of URL encode
function drupal_urlencode($text) {
 return str_replace('%2F', '/', rawurlencode($text));
}


function afg_get_query_parameters($feed_url = '') {
  if($feed_url == '') {
	  $parsed_url = drupal_parse_url(PARSER_QUERY);
	}
	else {
	  $parsed_url = drupal_parse_url($feed_url);
	}
	$filtered_query = drupal_get_query_parameters($parsed_url['query'], array('key', 'output'));
	return $filtered_query;
}

function afg_set_query_parameters($extra_params = array()) {
  // set default parameters for all queries
  $query = array('key' => 'christianvolunteering', 'output' => 'rss');	               
  
  // merge in extra parameters if they are being set programmatically (i.e., not in $_POST)
  if(isset($extra_params) && count($extra_params) >= 0) {
    if(!isset($extra_params['q'])) {
      $extra_params['q'] = '-detailurl:christianvolunteering AND -detailurl:churchvolunteering AND (christian OR jesus OR catholic OR ministry)';
    }
    if(!isset($extra_params['num'])) { 
      $extra_params['num'] = 100;
    }
    if(!isset($extra_params['vol_dist'])) {
      $extra_params['vol_dist'] = 100;
    }
    $query = array_merge($query, $extra_params);
  }

  if(isset($_POST)) {
    if(isset($_POST['q']) && !empty($_POST['q'])) {
      $query['q'] = check_plain($_POST['q']);
    }
    if(isset($_POST['num']) && !empty($_POST['num'])) {
      $query['num'] = check_plain($_POST['num']);
    }
    if(isset($_POST['start']) && !empty($_POST['start'])) {
      $query['start'] = check_plain($_POST['start']);
    }
    if(isset($_POST['provider']) && !empty($_POST['provider'])) {
      $query['provider'] = check_plain($_POST['provider']);
    }
    if(isset($_POST['timeperiod']) && !empty($_POST['timeperiod'])) {
      $query['timeperiod'] = check_plain($_POST['timeperiod']);
    }
    if(isset($_POST['vol_dist']) && !empty($_POST['vol_dist'])) {
      $query['vol_dist'] = check_plain($_POST['vol_dist']);
    }
    // Note: Can't call lat or long directly; have to pass a comma-delimited string into the vol_loc parameter
    if(isset($_POST['vol_loc']) && !empty($_POST['vol_loc'])) {
      $query['vol_loc'] = check_plain($_POST['vol_loc']);
    }
    if(isset($_POST['vol_startdate']) && !empty($_POST['vol_startdate'])) {
      $query['vol_startdate'] = check_plain($_POST['vol_dist']);
    }
    if(isset($_POST['vol_enddate']) && !empty($_POST['vol_enddate'])) {
      $query['vol_enddate'] = check_plain($_POST['vol_dist']);
    }
    if(isset($_POST['type']) && !empty($_POST['type'])) {
      $query['type'] = check_plain($_POST['type']);
    }
    if(isset($_POST['category']) && !empty($_POST['category'])) {
      $query['category'] = check_plain($_POST['category']);
    }
    if(isset($_POST['overfetch']) && !empty($_POST['overfetch'])) {
      $query['overfetch'] = check_plain($_POST['overfetch']);
    }
    if(isset($_POST['merge']) && $_POST['merge'] == 1) {
      $query['merge'] = 1;
    }
  }
  return $query;
}

function afg_set_feed_url($params = array()) {
  $query = afg_set_query_parameters($params);
  $query_string = drupal_http_build_query($query);
  $feed_url = 'http://www.allforgood.org/api/volopps?' . $query_string;
  return $feed_url;
}


// Parse the Footprint XML tags for a feed item.
function afg_parse_fp_tags($item) {
    /* Tags that every AFG feed item has */
    $fp['id'] = $item->get_item_tags(FP_NS, 'id');													// hash appended to GUID; DON'T MAP
    $fp['groupid'] = $item->get_item_tags(FP_NS, 'groupid');								// machine name of provider?; DON'T MAP
    $fp['provider'] = $item->get_item_tags(FP_NS, 'provider');							// human name of provider; POSSIBLE MAP
    $fp['startDate'] = $item->get_item_tags(FP_NS, 'startDate');						// start date of opp; POSSIBLE MAP - seems inaccurate
    $fp['endDate'] = $item->get_item_tags(FP_NS, 'endDate');								// end date of opp; POSSIBLE MAP - seems inaccurate
    $fp['base_url'] = $item->get_item_tags(FP_NS, 'base_url');							// also same as hash appended to GUID; DON'T MAP
    $fp['xml_url'] = $item->get_item_tags(FP_NS, 'xml_url');								// same as full link & GUID; DON'T MAP
    $fp['url_short'] = $item->get_item_tags(FP_NS, 'url_short');						// domain name of provider; DON'T MAP
    $fp['latlong'] = $item->get_item_tags(FP_NS, 'latlong');								// latitude/longitude of opportunity; MAP to location (reverse geocode)
    $fp['location_name'] = $item->get_item_tags(FP_NS, 'location_name');		// city, state zip; DON'T MAP
    $fp['interest_count'] = $item->get_item_tags(FP_NS, 'interest_count');	// unsure; DON'T MAP
    $fp['impressions'] = $item->get_item_tags(FP_NS, 'impressions');				// number of clicks, provided by provider?; DON'T MAP
    $fp['quality_score'] = $item->get_item_tags(FP_NS, 'quality_score');		// internal AFG rating; POSSIBLE LATER MAP
    $fp['virtual'] = $item->get_item_tags(FP_NS, 'virtual');								// whether opportunity is virtual; POSSIBLE MAP
    $fp['sponsoringOrganizationName'] = 
      $item->get_item_tags(FP_NS, 'sponsoringOrganizationName');						// name of organization; POSSIBLE LATER MAP
    $fp['openEnded'] = $item->get_item_tags(FP_NS, 'openEnded');						// opportunity has no specific end date; DON'T MAP
    $fp['startTime'] = $item->get_item_tags(FP_NS, 'startTime');						// opportunity start time; DON'T MAP
    $fp['endTime'] = $item->get_item_tags(FP_NS, 'endTime');								// opportunity end time; DON'T MAP

    /* Tags that only some AFG items have */
    $fp['skills'] = $item->get_item_tags(FP_NS, 'skills');									// volunteer skills description; POSSIBLE MAP (to additional info field)
    $fp['contactEmail'] = $item->get_item_tags(FP_NS, 'contactEmail');			// contact email; POSSIBLE MAP
    $fp['contactPhone'] = $item->get_item_tags(FP_NS, 'contactPhone');			// contact phone; POSSIBLE MAP

    /* Tags that none (so far) have */
    $fp['categories'] = $item->get_item_tags(FP_NS, 'categories');						// provider-submitted category tags; if present could be used for reverse mapping categories
    $fp['s'] = $item->get_item_tags(FP_NS, 's');															// ?
    $fp['m'] = $item->get_item_tags(FP_NS, 'm');															// ?
    $fp['v'] = $item->get_item_tags(FP_NS, 'v');															// ?
    $fp['addr1'] = $item->get_item_tags(FP_NS, 'addr1');											// first line of address? - was warned this may change
    $fp['addrname1'] = $item->get_item_tags(FP_NS, 'addrname1');							// name of address? - was warned this may change
    $fp['contactNoneNeeded'] = 
      $item->get_item_tags(FP_NS, 'contactNoneNeeded');												// ?
    $fp['contactName'] = $item->get_item_tags(FP_NS, 'contactName');					// contact name
    $fp['detailUrl'] = $item->get_item_tags(FP_NS, 'detailUrl');							// link to page on provider's site with additional opportunity info; same as link/GUID if present?
    $fp['audienceAll'] = $item->get_item_tags(FP_NS, 'audienceAll');					// ?
    $fp['audienceAge'] = $item->get_item_tags(FP_NS, 'audienceAge');					// age for which opportunity is suited?
    $fp['minAge'] = $item->get_item_tags(FP_NS, 'minAge');										// minimum age?
    $fp['audienceSexRestricted'] = 
      $item->get_item_tags(FP_NS, 'audienceSexRestricted');										// what gender volunteers should be?
    $fp['street1'] = $item->get_item_tags(FP_NS, 'street1');									// 1st line of street?
    $fp['street2'] = $item->get_item_tags(FP_NS, 'street2');									// 2nd line of street?
    $fp['city'] = $item->get_item_tags(FP_NS, 'city');												// city?
    $fp['region'] = $item->get_item_tags(FP_NS, 'region');										// region?
    $fp['postalCode'] = $item->get_item_tags(FP_NS, 'postalCode');						// postalCode?
    $fp['country'] = $item->get_item_tags(FP_NS, 'country');									// country?
    
    return $fp;
}

// Google geocoding (based on http://tips4php.net/2010/04/geocoding-with-google-maps-and-php/)
// @todo: Switch to JSON, and parse out the individual elements of the address (cf. google_geocode_location())
function afg_google_geocode_latlon($lat, $lon) {
  // assume by default that the address could not be parsed
  $ret_val = FALSE;
  $q = urlencode($lat . ',' . $lon);
  $url = 'http://maps.google.com/maps/geo?q=' . $q . '&output=xml&key=' . GMAP_API_KEY;
  $xml = simplexml_load_file($url);
  $status = $xml->Response->Status->code;
  // Initialize a variable for addresses
  $addresses = array();
  if ($status='200') { // Google responded with data; show results
    // parse the XML
    foreach ($xml->Response->Placemark as $node) {
      $addresses[] = $node->address;
      $quality = $node->AddressDetails['Accuracy'];
    }
  }
  // if there are addresses in the response 
  if(count($addresses)) {
    // for now, just return the first address
    $ret_val = $addresses[0];
  }
  return $ret_val;
}