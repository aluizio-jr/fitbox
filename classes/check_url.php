<?php
//$url = "http://www.youtube.com/w=2kdhir";
$url = $_GET['url_check'];

echo $url . "<br>";

// Validate url
if (filter_var($url, FILTER_VALIDATE_URL)) {
  echo("Valid URL");
} else {
  echo("Not a valid URL");
}
?>