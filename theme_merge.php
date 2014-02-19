<?php

function merge_theme(){

	$console_messages = true;

	require_once(__DIR__ . '/theme_merge.config.php');

	if(isset($merge_config) && is_array($merge_config)){

		if($console_messages) echo "theme info found.\n--------------------------\n";

		foreach($merge_config as $file_info){

			if($source_path = getSourceFilePath($file_info['sourcefile'])){

				if($console_messages) echo "opening " . $source_path . " \n";

				$paths = getPaths($file_info['sourcefile']);

				if($console_messages) echo "merging content with $source_path \n";

				$tag = (isset($file_info['tag']) && !empty($file_info['tag'])) ? $file_info['tag'] : '';

				$new_content = generateNewContent($source_path, $file_info['content'], $file_info['insert_method'], $tag);			

				file_put_contents($paths['custom_path'], $new_content);
		
				if($console_messages) echo ":)\n";

			}
			else{
			
				if($console_messages) echo "no sourcefile found.\n";

			}

		}

	}
	else{

		if($console_messages) echo "no merge config data found.\n";

	}

}

function getSourceFilePath($filename){

	$paths = getPaths($filename);

	if(is_file($paths['custom_path'])){
		return $paths['custom_path'];
	}
	elseif(is_file($paths['path'])){
		return $paths['path'];
	}
	else{
		return false;
	}

}

function getPaths($filename){

	$theme = getTheme();

	$path = 'themes/' . $theme . '/tpls/' . $filename;
	$custom_path = 'custom/' . $path;

	return array('path' => $path, 'custom_path' => $custom_path);

}

function getTheme(){

	return $GLOBALS['theme'];

}

function generateNewContent($source_path, $content, $insert_method, $tag){

	$original_content = file_get_contents($source_path);
	
	$pattern = '/' . str_replace('/', '\/', $tag) . '/i';

	$chunks = preg_split($pattern, $original_content);

	$output = "";
	if($insert_method == 'prepend' && empty($tag)){

		$output .= $content . "\n";
		$output .= $original_content;

	}
	elseif($insert_method == 'append' && empty($tag)){

		$output .= $original_content . "\n";
		$output .= $content;

	}
	elseif($insert_method == 'prepend'){

		$output .= $chunks[0] . "\n";
		$output .= $content . "\n";
		$output .= $tag . "\n";
		$output .= $chunks[1];

	}
	else{

		$output .= $chunks[0] . "\n";
		$output .= $tag . "\n";
		$output .= $content . "\n";
		$output .= $chunks[1];

	}

	return $output;

}

?>
