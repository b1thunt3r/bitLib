<?php
if (!isset($_SERVER['REQUEST_METHOD'])) {
  echo "CLI";
  exit();
}

Phar::interceptFileFuncs();
require_once('phar://' . __FILE__ . '/Bit0/Core/Context.php');
__HALT_COMPILER();
?>
