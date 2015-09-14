<?php
if (count($argv) < 4) {
  echo "Usage:\r\n";
  echo "\tphp PharUsage.php create pharfile source_dir\r\n";
  echo "\tphp PharUsage.php extract pharfile output_dir";
} else {
  require_once("Bit0_Phar.php");
  $phar = new \Bit0\Phar($argv[2], $argv[3]);
  
  switch ($argv[1]) {
    case 'create':
    case 'c':
      $phar->Create();
      break;
    case 'extract':
    case 'e':
      $phar->Extract();
      break;
  }
}
