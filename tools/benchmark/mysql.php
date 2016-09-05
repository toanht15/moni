<?php
$handle = mysqli_connect('127.0.0.1', 'brandco', 'changeme', 'test');
mysqli_query($handle, "DELETE FROM demo_test");

// insert
echo "start insert " . microtime(true) . PHP_EOL;
for ($i = 0 ; $i < 10000 ; $i ++) {
   mysqli_query($handle, "INSERT INTO demo_test(c1, c2, c3, c4, c5) VALUES('" . $i . "', '" . $i . "', '" . $i . "', '". $i. "', '". $i ."')");
}
echo "end insert " . microtime(true) . PHP_EOL;

// select
echo "start select " . microtime(true) . PHP_EOL;
for ($i = 0 ; $i < 10000 ; $i ++) {
   $rs = mysqli_query($handle, "SELECT c1, c2, c3, c4, c5 FROM demo_test WHERE c1 = " . $i);
   mysqli_fetch_array($rs);
}
echo "end select " . microtime(true) . PHP_EOL;

// update
echo "start update " . microtime(true) . PHP_EOL;
for ($i = 0 ; $i < 10000 ; $i ++) {
   $j = $i + 1;
   $rs = mysqli_query($handle, "UPDATE demo_test SET c2 = " . $j . " WHERE c1 = " . $i);
}
echo "end update " . microtime(true) . PHP_EOL;

?>
