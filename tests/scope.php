<?php

function scope()
{
  foreach(array(1,2,3) as $val)
  {
    $last = $val;
  }
  echo "$last\n";
}

scope();

?>
