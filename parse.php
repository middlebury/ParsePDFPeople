<?php

// Include the configuration.
include 'config.php';

// Include Composer autoloader if not already done.
include 'vendor/autoload.php';

// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf    = $parser->parseFile($file);

// Retrieve all pages from the pdf file.
$pages  = $pdf->getPages();

// Store the matched ID numbers in this array.
$ids = [];

// Loop over each page to extract text.
foreach ($pages as $page) {
  $text = $page->getText();

  $matches = [];

  preg_match_all($pattern, $text, $matches);

  if (!empty($matches[1])) {
    $ids = array_merge($ids, $matches[1]);
  }
}

if ($match == "MiddleburyCollege6DigitIdNumber") {
  // Normalize ID numbers at six digits.
  foreach ($ids as &$id) {
    // Handle ID numbers less than six digits.
    $id = str_pad($id, 6, "0", STR_PAD_LEFT);

    // Handle ID numbers more than six digits.
    $id = substr($id, -6);
  }
}

$conn = ldap_connect($host) or die("Could not connect to LDAP server.");

if ($conn) {
  ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
  ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

  $bind = ldap_bind($conn, $user, $pass) or die ("Error trying to bind: ".ldap_error($conn));
  if ($bind) {

    foreach ($ids as $number) {
      $filter = '(' . $match . '=' . $number . ')';

      $data = ldap_search($conn, $tree, $filter, $attr);

      $info = ldap_get_entries($conn, $data);

      if ($info['count'] > 0) {
        unset($info['count']);
        foreach ($info as $user) {
          $vals = [];
          foreach ($attr as $key) {
            $vals[] = $user[strtolower($key)][0];
          }

          print implode(',', $vals) . "\n";
        }
      }
    }
  }

}
