<?php
 
// Include the code to setup the AFG Simplepie parser.
require_once('afg-simplepie.inc');

// Initialize the feed URL from the posted values
$feed_url = afg_set_feed_url();
 
// Initialize the parser (selects the feed, options, etc.)
$feed = afg_parser_init($feed_url);
 
// Begin the HTML5 webpage code.  The DOCTYPE is supposed to be the very first thing, so we'll keep it on the same line as the closing PHP tag.
?><!DOCTYPE html>
 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>AllForGood Parser Test - Changeable URL</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<!-- add styles to make it nicer -->
	<link type="text/css" rel="stylesheet" href="afg-simplepie.css" />
	<!-- include jquery to make the afg information collapsable -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
	<!-- add code from roshanbh.com.np for making afg information collapsible -->
	<script type="text/javascript">
  $(document).ready(function(){
	  //hide the all of the element with class item-afg-info-data
	  $(".item-afg-info-data").hide();
	  //toggle the component with class item-afg-info-title
	  $(".item-afg-info-title").click(function(){
		  $(this).next(".item-afg-info-data").slideToggle(250);
	  });
		$("#toggle-afg-info").click(function(){
		  $(".item-afg-info-data").toggle();
			return false;
		});
  });
  </script>
</head>
<body>
 
	<div class="header">
		<h1><a href="<?php echo $feed->get_permalink(); ?>"><?php echo $feed->get_title(); ?></a></h1>
		<p><?php echo $feed->get_description(); ?></p>
		<div class="query-information">
		  <h2><strong>Query run:</strong></h2>
		  <p><code><?php echo $feed_url; ?></code></p>
			<h2>Query parameters</h2>
		  <ul>
			<?php $query = afg_get_query_parameters($feed_url); 
			foreach($query as $key => $value) {
			  echo '<li><strong>' . $key . ':</strong> ' . $value;
			}?>
			</ul>
			<h2>Number of items returned:</h2>
			<?php echo $feed->get_item_quantity(); ?>
	  </div>
		<div class="afg-info-display-control">
		  <p><a href="#" id="toggle-afg-info">Show/Hide AFG Info?</a></p>
		</div>
	</div>
 
	<?php
	/**
	  * Loop through all of the items in the feed.
	  * $item represents the current item in the loop.
	*/
	$i = 0;
	foreach ($feed->get_items() as $item):
	  // Parse the Footprint XML items.
	  $fp = afg_parse_fp_tags($item);
		$i++;
	?>
		
		<div class="item" id="item-<?php echo $i; ?>">
			<h2><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h2>
			<p><?php echo $item->get_description(); ?></p>
      <div class="item-afg-info">
			  <div class="item-afg-info-title">
			  <h3><a href="#item-<?php echo $i; ?>">AllForGood Information</a></h3>
				</div>
				<div class="item-afg-info-data">
			  <?php foreach($fp as $tagname => $tag): ?>
			    <p><strong><?php echo $tagname; ?>:</strong> <?php echo $tag[0]['data']; ?></p>
			  <?php endforeach; ?>
				</div>
			</div>
			<!-- note that the AFG feed doesn't have a "posted on" date -->
			<p><small>Posted on <?php echo $item->get_date('j F Y | g:i a'); ?></small></p>
		</div>
 
	<?php endforeach; ?>
 
</body>
</html>