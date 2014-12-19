<?php

/* 
 * theme merge for SugarCRM CE module loader install scripts
 * Gábor Darvas <gabor.darvas@soulware.eu>
 * Soulware Ltd. <www.soulware.eu>
 * 
 * This class uses $GLOBALS['theme'], works inside SugarCRM
 * 
 */

namespace Soulware;

require_once(__DIR__ . '/theme_merge.config.php');

class mergeTheme {

    //screen | console | false
    protected $log_messages = "screen";

    //array of Soulware/themeMergeConfig objects
    protected $config;

    public function __construct(array $merge_configs) {
        $this->log("init mergeTheme class");
        $this->config = $merge_configs;
        
    }
    
    public function install() {

        if (isset($this->config) && is_array($this->config)) {

            $this->log("theme info found.");

            foreach ($this->config as $file_info) {

                if ($source_path = $this->getSourceFilePath($file_info->sourcefile)) {

                    $this->log("opening " . $source_path);

                    $paths = $this->getPaths($file_info->sourcefile);

                    $this->log("merging content with $source_path");

                    $tag = (isset($file_info->tag) && !empty($file_info->tag)) ? $file_info->tag : '';

                    $new_content = $this->generateNewContent($source_path, $file_info->content, $file_info->insert_method, $tag);

                    $this->createDirStructure($paths['custom_path']);

                    file_put_contents($paths['custom_path'], $new_content);

                    $this->log("well, this is goodbye :)");
                }
                else {

                    $this->log("no sourcefile found.");
                }
            }
        }
        else {

            $this->log("no merge config data found.");
        }
    }
    
    
    //log
    protected function log($message) {

        if ($this->log_messages == "console") {
            $this->logToConsole($message);
        } elseif ($this->log_messages == "screen") {
            $this->logToScreen($message);
        }
    }

    protected function logToConsole($message) {
        echo $message . "\n";
    }

    protected function logToScreen($message) {
        echo $message . "<br />";
    }
    
    //helpers
    protected function getSourceFilePath($filename) {

        $paths = $this->getPaths($filename);

        if (is_file($paths['custom_path'])) {
            return $paths['custom_path'];
        } elseif (is_file($paths['path'])) {
            return $paths['path'];
        } else {
            return false;
        }
    }

    protected function getPaths($filename) {

        $theme = $this->getTheme();

        $path = 'themes/' . $theme . '/tpls/' . $filename;
        $custom_path = 'custom/' . $path;

        return array('path' => $path, 'custom_path' => $custom_path);
    }

    protected function getTheme() {

        return $GLOBALS['theme'];
    }

    protected function generateNewContent($source_path, $content, $insert_method, $tag) {

        $original_content = file_get_contents($source_path);

        $pattern = '/' . str_replace('/', '\/', $tag) . '/i';

        $chunks = preg_split($pattern, $original_content);

        $output = "";
        if ($insert_method == 'prepend' && empty($tag)) {

            $output .= $content . "\n";
            $output .= $original_content;
        } elseif ($insert_method == 'append' && empty($tag)) {

            $output .= $original_content . "\n";
            $output .= $content;
        } elseif ($insert_method == 'prepend') {

            $output .= $chunks[0] . "\n";
            $output .= $content . "\n";
            $output .= $tag . "\n";
            $output .= $chunks[1];
        } else {

            $output .= $chunks[0] . "\n";
            $output .= $tag . "\n";
            $output .= $content . "\n";
            $output .= $chunks[1];
        }

        return $output;
    }

    protected function createDirStructure($path) {

        $current_path = "";

        $dir_array = $this->getDirArray($path);

        foreach ($dir_array as $dir) {

            $current_path .= $dir . "/";

            if (!is_dir($current_path)) {

                mkdir($current_path);
            }
        }

        return true;
    }

    protected function getDirArray($path) {

        $return_array = explode('/', $path);
        array_pop($return_array);

        return $return_array;
    }

}
