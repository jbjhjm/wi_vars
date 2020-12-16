<?php
___phpbanner___;

// this file won't be overridden if plugin is updated.
// use it for custom vars.
class plgSystemWi_varsHelper
{

	// manually search & replace content
    // static function replaceVars($content)
    // {
	// 	return $content;
    // }

	static $vars = array (
		'my_var' => 'value',
	);

	// return a key=>value array of to be replaced data.
    static function getVars()
    {
		return self::$vars;
    }

    static function processVar_my_function($arg)
    {
		return 'test: '.$arg;
    }

}
?>
