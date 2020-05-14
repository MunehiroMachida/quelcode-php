<?php
$array = explode(',', $_GET['array']);

// 修正はここから
for ($i = 0; $i < count($array); $i++) {
    for ($j = (count($array)- 1); $j > $i; $j--) {
        if($array[$j] < $array[$j-1]){
            $tmp = $array[$j];
            $array[$j] = $array[$j-1];
            $array[$j-1] = $tmp;
        }
        
    }
}
// 修正はここまで

echo "<pre>";
print_r($array);
echo "</pre>";
