<!DOCTYPE html>
 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>AllForGood Parser Test - Query Builder</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <!-- add styles to make it nicer -->
  <link type="text/css" rel="stylesheet" href="afg-simplepie.css" />
  <!-- include jquery to make the afg information collapsable -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
  <!-- add code from roshanbh.com.np for making afg information collapsible -->
  <script type="text/javascript">
  $(document).ready(function(){
    //hide the all of the element with class item-afg-info-data
    $(".help-info").hide();
    //toggle the component with class item-afg-info-title
    $(".help-info-title").click(function(){
    $(this).next(".help-info").slideToggle(250);
    });
  });
  </script>
</head>
<body>
<h1>All For Good Query Builder</h1>
<p>This page uses the All For Good query parameters as documented on <a href="http://www.allforgood.org/api.html">their API page</a>. See below for more information.</p>
<div class="help-info-title" id="help-1"><h2><a href="#help-1">How to Use the Query (q) Parameter</h2></a></div>
<div class="help-info">
<ul>
      <li>When creating a query, list search terms separated by spaces, in the form <code>q=term1 term2 term3</code>. (As with all of the query parameter values, the spaces must be URL encoded.) The service returns all entries that match all of the search terms (like using <code>AND</code> between terms). Like Google's web search, a service searches on complete words (and related words with the same stem), not substrings.</li>
      <li>Boolean operations may also be used, such as AND and OR.</li>
      <li>You may use parentheses to group terms, such as (hunger OR food)</li>
      <li>To search for an exact phrase, enclose the phrase in quotation marks: <code>q="exact&nbsp;phrase".</code></li>
      <li>To exclude entries that match a given term, use the form <code>q=-term</code>.</li>
      <li>The search is case-insensitive.</li>
      <li>Example: to search for all entries that contain the exact phrase "Elizabeth Bennet" and the word "Darcy" but don't contain the word "Austen", use the following query: <code>?q="Elizabeth Bennet" Darcy -Austen</code></li>
      <li>You can filter on particular fields by specifying the field name. For example, <code>-detailurl: christianvolunteering</code> excludes results where the provider's source URL contains "christianvolunteering". (Note: This appears to be case-sensitive.)</li>
</ul>
<p><small>Source: <a href="http://code.google.com/apis/gdata/docs/2.0/reference.html#Queries">Google Data Protocol Reference</a></small>, with additions based on example provided by Dan Stryker of AllForGood</p>
</div>
<div class="help-info-title" id="help-2"><h2><a href="#help-2">Example Query</a></h2></div>
<div class="help-info"><p>The following parameters:</p>
<ul>
  <li><strong>q:</strong> -detailurl:volunteermatch AND -detailurl:christianvolunteering AND -detailurl:churchvolunteering AND (christian OR jesus OR catholic OR ministry) </li>
  <li><strong>num:</strong> 500</li>
  <li><strong>vol_dist:</strong> 500</li>
  <li><strong>vol_loc:</strong> Boston,MA</li>
</ul>
<p>would run <code>http://www.allforgood.org/api/volopps?key=christianvolunteering&output=rss&q=-detailurl%3Avolunteermatch%20AND%20-detailurl%3Achristianvolunteering%20AND%20-detailurl%3Achurchvolunteering%20AND%20%28christian%20OR%20jesus%20OR%20catholic%20OR%20ministry%29%20&num=500&vol_dist=500&vol_loc=Boston%2CMA</code></p></div>
<div id="all-for-good-query-form">
<form method="post" action="dynamic.php">
<div class="form-element"><label for="q">Query (q):</label> <input type="text" size="60" name="q" id="q" /></div>
<div class="form-element"><label for="num">Number of Results (num):</label> <input type="text" size="60" name="num" id="num" /></div>
<div class="form-element"><label for="start">Offset (start):</label> <input type="text" size="60" name="start" id="start" /></div>
<div class="form-element"><label for="provider">Provider (provider):</label> <input type="text" size="60" name="provider" id="provider" /></div>
<div class="form-element"><label for="timeperiod">Time Period (timeperiod):</label> <input type="text" size="60" name="timeperiod" id="timeperiod" /></div>
<div class="form-element"><label for="vol_dist">Maximum Distance from Center Point (vol_dist):</label> <input type="text" size="60" name="vol_dist" id="vol_dist" /></div>
<div class="form-element"><label for="vol_loc">Query Center Point (vol_loc):</label> <input type="text" size="60" name="vol_loc" id="vol_loc" /></div>
<div class="form-element"><label for="vol_startdate">Opportunity Start Date (vol_startdate):</label> <input type="text" size="60" name="start" id="start" /></div>
<div class="form-element"><label for="vol_enddate">Opportunity End Date (vol_enddate):</label> <input type="text" size="60" name="vol_enddate" id="vol_enddate" /></div>
<div class="form-element"><label for="overfetch">Ratio of Results from Backend (overfetch):</label> <input type="text" size="60" name="overfetch" id="overfetch" /></div>
<div class="form-element" id="form-submit"><label for="merge">Merge/Dedupe Results?</label><input type="checkbox" name="merge" value="1" checked /></div>
<div class="form-element" id="form-submit"><input type="submit" value="Search" name="submit"></div>
</form>
</div>
</body>
</html>