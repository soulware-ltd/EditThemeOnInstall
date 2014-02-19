<?php

if(!isset($merge_config)) $merge_config = array();

$merge_config[] = array(
	'sourcefile' => '_head.tpl',
	'type' => 'template',
	//not required, but exact match and only one result is expected
	'tag' => '</head>',
	//[append, prepend]
	'insert_method' => 'prepend',
	'content' => '<!--html code to insert-->',
);

?>
