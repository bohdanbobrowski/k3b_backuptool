<?php

require_once("vendor/autoload.php");
if(count($argv)>1) {
    if(isset($argv[2])) {
        $k3b = new K3B($argv[1], $argv[2]);
    } else {
        $k3b = new K3B($argv[1]);
    }
} else {
    echo "K3B Backup Tool\n";
    echo "usage: php k3b.php <project_name> <path>\n";
}
