<?php

namespace Soulware\EditThemeOnInstall;

class themeMergeConfig{
    
    public $sourcefile;
    public $type = 'template';
    //not required, but exact match and only one result is expected
    public $tag;
    //[append, prepend]
    public $insert_method = 'prepend';
    public $content;

    public function __construct($sourcefile, $content) {
        $this->sourcefile = $sourcefile;
        $this->content = $content;
    }
    
}
