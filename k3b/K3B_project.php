<?php

class K3B_project {

    public $name, $document;

    public function __construct($name = '') {
        $this->project_name = $name;
        $this->getEmptyDocument();
    }

    protected function getEmptyDocument() {
        $document = new SimpleXMLElement('<k3b_data_project></k3b_data_project>');
        // General
        $general = $document->addChild('general');
        $general->addChild('writing_mode', 'auto');
        $general->addChild('dummy')->addAttribute('activated', 'no');
        $general->addChild('on_the_fly')->addAttribute('activated', 'yes');
        $general->addChild('only_create_images')->addAttribute('activated', 'no');
        $general->addChild('remove_images')->addAttribute('activated', 'yes');
        // Options
        $options = $document->addChild('options');
        $options->addChild('rock_ridge ')->addAttribute('activated', 'yes');
        $options->addChild('joliet ')->addAttribute('activated', 'yes');
        $options->addChild('udf ')->addAttribute('activated', 'no');
        $options->addChild('joliet_allow_103_characters ')->addAttribute('activated', 'yes');
        $options->addChild('iso_allow_lowercase ')->addAttribute('activated', 'no');
        $options->addChild('iso_allow_period_at_begin ')->addAttribute('activated', 'no');
        $options->addChild('iso_allow_31_char ')->addAttribute('activated', 'yes');
        $options->addChild('iso_omit_version_numbers ')->addAttribute('activated', 'no');
        $options->addChild('iso_omit_trailing_period ')->addAttribute('activated', 'no');
        $options->addChild('iso_max_filename_length ')->addAttribute('activated', 'no');
        $options->addChild('iso_relaxed_filenames ')->addAttribute('activated', 'no');
        $options->addChild('iso_no_iso_translate ')->addAttribute('activated', 'no');
        $options->addChild('iso_allow_multidot ')->addAttribute('activated', 'no');
        $options->addChild('iso_untranslated_filenames ')->addAttribute('activated', 'no');
        $options->addChild('follow_symbolic_links ')->addAttribute('activated', 'no');
        $options->addChild('create_trans_tbl ')->addAttribute('activated', 'no');
        $options->addChild('hide_trans_tbl ')->addAttribute('activated', 'no');
        $options->addChild('iso_level', 3);
        $options->addChild('discard_symlinks ')->addAttribute('activated', 'no');
        $options->addChild('discard_broken_symlinks ')->addAttribute('activated', 'no');
        $options->addChild('preserve_file_permissions ')->addAttribute('activated', 'no');
        $options->addChild('do_not_cache_inodes ')->addAttribute('activated', 'yes');
        $options->addChild('whitespace_treatment', 'noChange');
        $options->addChild('whitespace_replace_string', '_');
        $options->addChild('data_track_mode', 'auto');
        $options->addChild('multisession', 'auto');
        $options->addChild('verify_data ')->addAttribute('activated', 'no');
        // Header
        $header = $document->addChild('header');
        $header->addChild('volume_id', $this->project_name);
        $header->addChild('volume_set_id', '');
        $header->addChild('volume_set_size', '1');
        $header->addChild('volume_set_number', '1');
        $header->addChild('system_id', 'LINUX');
        $header->addChild('application_id', 'K3B THE CD KREATOR (C) 1998-2010 SEBASTIAN TRUEG AND MICHAL MALEK');
        $header->addChild('publisher', '');
        $header->addChild('preparer', '');
        $this->document = $document;
    }

    public function prettyPrint() {
        $dom = dom_import_simplexml($this->document)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    public function saveProject() {
        $xml = $this->document->asXML();
        $xml = str_replace("<?xml version=\"1.0\"?>\n", "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE k3b_data_project>\n", $xml);
        file_put_contents("maindata.xml", $xml);
        file_put_contents("mimetype", "application/x-k3b\n");
        $zip = new ZipArchive();
        $zip->open($this->project_name . '.k3b', ZipArchive::CREATE);
        $zip->addFile('maindata.xml', 'maindata.xml');
        $zip->addFile('mimetype', 'mimetype');
        $zip->close();
        unlink('maindata.xml');
        unlink('mimetype');
    }

}
