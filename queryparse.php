<?php require_once('afg-simplepie.inc');
?><!DOCTYPE html>
 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>AllForGood Parser Test - Raw Solr URL Parser</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <!-- add styles to make it nicer -->
  <link type="text/css" rel="stylesheet" href="afg-simplepie.css" />
</head>
<body>
<h1>Raw Solr URL Parser</h1>
<?php
if(isset($_POST) && isset($_POST['raw_url']) && !empty($_POST['raw_url'])) {
  $url = check_plain($_POST['raw_url']);
	echo "<h2>URL Provided</h2>";
	echo "<code>" . $url . "</code>";
	$parsed_url = parse_url($url); 
  $query = afg_get_query_parameters($url);
	echo "<p>The parsed URL is:</p>";
	echo "<h2>Solr Backend</h2>";
	echo "<p>" . $parsed_url['host'] . ":" . $parsed_url['port'] . "</p>";
	echo "<h2>Query String Parameters</h2><ul>";
  foreach($query as $key => $value) {
	  $param = str_replace('amp;', '', $key);
    echo "<li><strong>" . $param . ":</strong> " . $value;
  }
	echo "</ul>";
?>
<form method="post" action="queryparse.php">
<div class="form-element" id="form-submit"><input type="submit" value="Try Another?" name="submit"></div>
</form>
<?php 
}
else {
?>
<p>Enter a raw Solr URL from AllForGood below, and the page will parse it into its component parts (backend URL and Solr query string parameters).</p>
<div id="afg-raw-url-parser">
<form method="post" action="queryparse.php">
<div class="form-element"><label for="raw_url">URL:</label> <input type="text" size="100" name="raw_url" id="raw_url" /></div>
<div class="form-element" id="form-submit"><input type="submit" value="Parse" name="submit"></div>
</form>
</div>
<?php } ?>
</body>
</html>