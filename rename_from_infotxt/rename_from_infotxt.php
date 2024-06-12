<?php

if( $argc < 2 ){
    echo "need args files.";
    sleep(5);
    exit(0);
}
 
$filelist = $argv;
$res = array_shift($filelist);
if ($res == null) {
    echo "error.";
    sleep(5);
    exit(0);
}

if (count($filelist) !== 1) {
    echo "need 1 rename_info file.";
    sleep(5);
    exit(0);
}

$info_path = $filelist[0];
$base_path = dirname($info_path);
$rename_info_file = file_get_contents($info_path);
$rename_info_list = explode("\n", $rename_info_file);

// load opt
$opts = explode(',', array_shift($rename_info_list));
$is_index_only = false;
$num_override = false;
$prefix = "";
$is_fullpath = false;
foreach ($opts as $opt) {
    $setting = (explode(":", $opt));
    if ($setting[0] == "index_only") {
        $is_index_only = $setting[1] == "true" ? true : false;
    }
    if ($setting[0] == "num_override") {
        $num_override = $setting[1] == "true" ? true : false;
    }
    if ($setting[0] == "prefix") {
        $prefix = $setting[1];
    }
    if ($setting[0] == "is_fullpath") {
        $is_fullpath = $setting[1] == "true" ? true : false;
    }
}


// precheck grouping
$is_grouping = false;
if (preg_match('/g[1-9]_/', $rename_info_file)) {
    $is_grouping = true;
    echo "grouping true.";
}


foreach($rename_info_list as $rename_info) {
    if (! preg_match('/:/', $rename_info)) {
        continue;
    }
    $kv = explode(":", $rename_info);
    $status = $kv[0];
    $org_filename = $kv[1];

    $org_filepath = $base_path . "\\" . $org_filename;
    if ($is_fullpath) {
        $org_filepath = $org_filename;
        $org_filename = basename($org_filename);
    }

    if ($status == "delete") {
        $delete_path = $to_filepath = $base_path . "\\" . "delete";
        if (!file_exists($delete_path)) {
            mkdir($delete_path);
        }
        $to_filepath = $delete_path . "\\" . $org_filename;
    } else {
        $group_and_index = explode("_", $status);
        $group = $group_and_index[0];
        $to_dir_path = $base_path;
        if ($is_grouping) {
            $to_dir_path = $base_path . "\\" . $group;
            if (!file_exists($to_dir_path)) {
                mkdir($to_dir_path);
            }
        }

        $index = $group_and_index[1];
        $number = str_pad($index, 5, "0", STR_PAD_LEFT);
        if ($is_index_only) {
            $to_filepath = $to_dir_path . "\\" . $prefix . $number . "." . explode(".", $org_filename)[1];
        } else {
            if ($num_override && preg_match('/^[0-9]{5}-/', $org_filename)) {
                $to_filepath = $to_dir_path . "\\" . $prefix . preg_replace('/^[0-9]{5}-/', $number . '-', $org_filename);
            } else {
                $to_filepath = $to_dir_path . "\\" . $org_filename;
            }
            
        }
    }

    if ($org_filepath == $to_filepath) {
        continue;
    }
    
    if (file_exists($org_filepath)) {
        echo $org_filepath . " -> " . $to_filepath . PHP_EOL;
        rename($org_filepath, $to_filepath);
    } else if (file_exists($to_filepath)) {
        echo $to_filepath . " -> " . $org_filepath . PHP_EOL;
        rename($to_filepath, $org_filepath);
    }
}

echo "script end." . PHP_EOL;
sleep(1);
exit(0);

