<?php

require_once("vendor/autoload.php");
if(count($argv)>1) {	
    $k3b = new K3B($argv[1], isset($argv[2])?$argv[2]:'.', isset($argv[3])?$argv[3]:FALSE);
} else {
    echo "K3B Backup Tool\n";
    echo "usage: php k3b.php <project_name> <path> <maximum_size>\n";
}
