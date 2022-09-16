<?php

function fopen_create_or_truncate($filename, $mode = 'wa+') {
  if (file_exists($filename)) {
    $fh = fopen($filename, 'wa+');

    ftruncate($fh, 0);

    fclose($fh);
  }

  return fopen($filename, $mode);
}

function write_line($fh, $string, $level = 0, $spacing = 2, $tab_spacing = 2) {
  for ($i = 0; $i < $spacing; $i += 1) {
    fwrite($fh, "\n");
  }

  fwrite($fh, str_repeat(' ', $level * $tab_spacing));

  fwrite($fh, $string);
}
