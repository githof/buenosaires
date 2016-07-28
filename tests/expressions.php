<?php

function test_cond($b)
{
  echo $b ? "ok" : "ko";
  echo "\n";
}

test_cond("A" == "B");
test_cond("A" === "B");
test_cond("A" == "A");
test_cond("A" === "A");
test_cond("A" == TRUE);
test_cond("A" === TRUE);
test_cond("true");
test_cond("false");
test_cond("");
?>
