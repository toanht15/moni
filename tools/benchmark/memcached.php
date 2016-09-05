<?php
$handle = mysqli_connect('127.0.0.1', 'brandco', 'changeme', 'test');
mysqli_query($handle, "DELETE FROM demo_test");
$pool = memcache_connect('localhost',11211);

// insert
echo "start insert " . microtime(true) . PHP_EOL;
for ($i = 0 ; $i < 10000 ; $i++) {
  memcache_add($pool, $i, $i);
}
echo "end insert " . microtime(true) . PHP_EOL;

// select
echo "start select " . microtime(true) . PHP_EOL;
for ($i = 0 ; $i < 10000 ; $i++) {
  $result = memcache_get($pool, $i);
}
echo "end select " . microtime(true) . PHP_EOL;

// update
echo "start update " . microtime(true) . PHP_EOL;
for ($i = 0 ; $i < 10000 ; $i++) {
  $j = $i + 1;
  $result = memcache_set($pool, $i, $j);
}
echo "end update " . microtime(true) . PHP_EOL;
?>
