<?php

class K3B {

    public $real_path = '';
    public $total = 0;
    public $limit = 4400000000;
    public $count = 1;
    public $files = array();
    public $project_name = '';

    public function __construct($name = '', $path = '.') {
        $this->project_name = $name;
        $this->real_path = realpath($path);
        $this->getDirContents($path);
        $this->prepareProjectFile();
    }

    public function humanFilesize($bytes, $decimals = 2) {
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
    
    protected function prepareProjectFile() {
        if($this->count > 1) {
            $project_name = $this->project_name."_".$this->count;
        } else {
            $project_name = $this->project_name;
        }
        $project = new K3B_project($project_name);
        $files = $project->document->addChild('files');
        $this->relPath();
        foreach ($this->files as $key => $result) {
            $rel_path = explode('/', $result['rel_path']);
            if (isset($result['size'])) {
                $files = $this->addXmlChild($files, $rel_path, $result['path']);
            } else {
                $files = $this->addXmlChild($files, $rel_path);
            }
        }
        echo "[$project_name]\t\t";
        echo "plików: ".count($this->files) . "\t";
        echo "rozmiar:".$this->humanFilesize($this->total) . "\n";        
        $project->saveProject();
        $this->files = array();
        $this->total = 0;
        $this->count++;
    }

    protected function addXmlChild($xml, $rel_path, $path = FALSE) {
        $xml_children = $xml->children();
        foreach ($xml_children as $ch) {
            foreach ($ch->attributes() as $attr => $value) {
                if ($value == $rel_path[0]) {
                    $child = $ch;
                }
            }
        }
        if (count($rel_path) > 1) {
            if (!isset($child)) {
				$child = $xml->addChild('directory');
                $child->addAttribute('name', htmlspecialchars($rel_path[0]));
            }
            unset($rel_path[0]);
            $rel_path = array_values($rel_path);
            $child = $this->addXmlChild($child, $rel_path, $path);
        } elseif ($path) {
            if (!isset($child)) {
                $child = $xml->addChild('file');
                $child->addAttribute('name', htmlspecialchars($rel_path[0]));
            }
            $url = $child->addChild('url', htmlspecialchars($path));
        }
        return $xml;
    }

    protected function relPath() {
        foreach ($this->files as $key => $result) {
            $this->files[$key]['rel_path'] = substr($result['path'], strlen($this->real_path) + 1);
        }
    }

    protected function calculateSize($size) {
        if ($this->total + $size > $this->limit) {
            $this->prepareProjectFile();
        }
        $this->total = $this->total + $size;
    }

    protected function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $size = filesize($path);
                $this->calculateSize($size);
                $this->files[] = array(
                    'path' => $path,
                    'size' => $size,
                );
            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $this->files[] = array(
                    'path' => $path,
                );
            }
        }
    }

}
