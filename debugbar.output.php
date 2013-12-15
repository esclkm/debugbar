<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=output
  Order=99
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

if (cot_auth('plug', 'debugbar', 'R'))
{
	global $debugbarRenderer, $out, $debugbar, $cfg, $debugbarrcfooter, $debugbarrc;


	$output = str_replace('</head>', $debugbarrc.'</head>', $output);
	$output = str_replace('</body>', $debugbarrcfooter.'</body>', $output);


}