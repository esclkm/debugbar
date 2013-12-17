<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=input
  Order=1
  [END_COT_EXT]
  ==================== */

/**
 * Header notifications
 *
 * @package debugbar
 * @version 2.1.0
 * @author Cotonti Team
 * @copyright (c) Cotonti Team 2008-2013
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

use DebugBar\StandardDebugBar;
use DebugBar\JavascriptRenderer;


if (cot_auth('plug', 'debugbar', 'R'))
{
	$cfg['debug_mode'] = TRUE;

	spl_autoload_register(function($className) {
		global $cfg;
	    if (substr($className, 0, 8) === 'DebugBar') {
			$filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
			require_once $cfg['plugins_dir'].'/debugbar/src/'.$filename;
	    }
		if (substr($className, 0, 3) === 'Psr')
		{
			$filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
			require_once $cfg['plugins_dir'].'/debugbar/'.$filename;
		}	    
	});


	$debugbar = new StandardDebugBar();

	$debugbarRenderer = $debugbar->getJavascriptRenderer();
	$debugbarRenderer->setIncludeVendors();
	$debugbarRenderer->setEnableJqueryNoConflict(false);

	$debugbarRenderer->setBaseUrl($cfg['plugins_dir'].'/debugbar/src/DebugBar/Resources');

	list($cssFiles, $jsFiles) = $debugbarRenderer->getAssets();
	$cssFiles[] = $cfg['plugins_dir']."/debugbar/css/debugbar.css";
	$debugbarrc = '';
	foreach ($cssFiles as $jscssFile) 
	{
		$jscssFile = preg_replace("/(.*?)".$cfg['plugins_dir']."/", $cfg['plugins_dir'], $jscssFile);
		$debugbarrc .= '<link href="'.$jscssFile.'" type="text/css" rel="stylesheet" /> ';

	}
	foreach ($jsFiles as $jscssFile) 
	{
		$jscssFile = preg_replace("/(.+?)".$cfg['plugins_dir']."/", $cfg['plugins_dir'], $jscssFile);
		if (!preg_match('/(.+)jquery(.+)/', $jscssFile))
		{
			$debugbarrc .= '<script type="text/javascript" src="'.$jscssFile.'"></script>';
		}

	}


	function cot_debug()
	{
		global $debugbar;
		$vars = func_get_args();
		foreach ($vars as $name => $var)
		{
			$debugbar["messages"]->addMessage($var);
		}

	}
/*
	$pdo = new DebugBar\DataCollector\PDO\TraceablePDO($db);
	$debugbar->addCollector(new DebugBar\DataCollector\PDO\PDOCollector($pdo));*/
}