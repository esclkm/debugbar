<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=footer.last
  Order=99
  [END_COT_EXT]
  ==================== */

/**
 * Header notifications
 *
 * @package contactphone
 * @version 2.1.0
 * @author Cotonti Team
 * @copyright (c) Cotonti Team 2008-2013
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

if (cot_auth('plug', 'debugbar', 'R'))
{
	$debugbar->addCollector(new DebugBar\DataCollector\MessagesCollector('Hooks'));
	// hooks
	$hooksss = $cot_hooks_fired;
	unset($hooksss[count($hooksss)-1]);
	foreach ($hooksss as $hook) 
	{
		$debugbar["Hooks"]->addMessage($hook);
	}
	$debugbar["Hooks"]->addMessage('');

	// Creation time statistics
	$i = explode(' ', microtime());
	$sys['endtime'] = $i[1] + $i[0];
	$sys['creationtime'] = round(($sys['endtime'] - $sys['starttime']), 3);

	$out['creationtime'] = (!$cfg['disablesysinfos']) ? $L['foo_created'].' '.cot_declension($sys['creationtime'], $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
	$out['sqlstatistics'] = ($cfg['showsqlstats']) ? $L['foo_sqltotal'].': '.cot_declension(round($db->timeCount, 3), $Ls['Seconds'], $onlyword = false, $canfrac = true).' - '.$L['foo_sqlqueries'].': '.$db->count. ' - '.$L['foo_sqlaverage'].': '.cot_declension(round(($db->timeCount / $db->count), 5), $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
	$out['bottomline'] = $cfg['bottomline'];
	$out['bottomline'] .= ($cfg['keepcrbottom']) ? $out['copyright'] : '';

	$debugbar->addCollector(new DebugBar\DataCollector\MessagesCollector('MySQL'));
	$debugbar['MySQL']->info('MySQL queries | Begin: 0.000 ms - End: '.sprintf("%.3f", $sys['creationtime']).' ms | Total: '.round($db->timeCount, 4) ." | Queries: ".$db->count. " | Average: ".round(($db->timeCount / $db->count), 5)."s/q");

	if(is_array($sys['devmode']['queries']))
	{
		foreach ($sys['devmode']['queries'] as $k => $i)
		{
			$path = str_replace("\n → ", ' \ ', $i[3]);
			preg_match('/(.+)->(.+?)\(\);$/', $path, $mt);

			$debugbar["MySQL"]->addMessage($i[0].". ".htmlspecialchars($i[2])."\n → Duration: ".sprintf("%.3f", round($i[1] * 1000, 3)).' ms | Timeline: '
				.sprintf("%.3f", round($sys['devmode']['timeline'][$k] * 1000, 3))." ms \n → Stack: ". $path, $mt[2]);
		}
	}

	$debugbar->addCollector(new DebugBar\DataCollector\MessagesCollector('System'));
	$debugbar['System']->info('System info');

	$i = explode(' ', microtime());
	$endtime = $i[1] + $i[0];
	$creationtime = round(($endtime - $sys['starttime']), 3);

	$debugbar["System"]->addMessage($L['foo_created'].' '.cot_declension($creationtime, $Ls['Seconds'], false, true));
	$debugbar["System"]->addMessage($L['foo_sqltotal'].': '.cot_declension(round($db->timeCount, 3), $Ls['Seconds'], false, true).' - '.$L['foo_sqlqueries'].': '.$db->count. ' - '.$L['foo_sqlaverage'].': '.cot_declension(round(($db->timeCount / $db->count), 5), $Ls['Seconds'], false, true));

// Эксперементальная опция получить все темплейты
	$pagehttp = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$tpl_debug = 'tpl_debug=1';
	$newhttp = (mb_strpos($pagehttp, '?') === false) ? $pagehttp . '?' . $tpl_debug : $pagehttp . '&' . $tpl_debug;
	

	if ($tpl_tags = file_get_contents($newhttp))
	{
		$debugbar->addCollector(new DebugBar\DataCollector\MessagesCollector('tpl Tags'));
		//$tpl_tags = str_replace(array('</h2>', '<ul>', '</ul>'), '', $tpl_tags);
//cot_print($tpl_tags);
		$tpl_tags = explode('<h2>', $tpl_tags);
		$tpls = array();
		unset($tpl_tags[0]);
		foreach($tpl_tags as $tpl_tag)
		{
			preg_match('/^(.+)\.tpl(.+?)<\/h2>(.*)/', $tpl_tag, $name);

			preg_match_all('/<li>(.+?)<\/li>/', $tpl_tag, $matches);

			$arr_out = array();
			$arr_out[] = $name[1].'.tpl'.$name[2];
			$debugbar["tpl Tags"]->addMessage($name[1].'.tpl'.$name[2], $name[1].'.tpl');

			foreach ($matches[0] as $match) 
			{
				preg_match('/<li>\{(.+?)\} =\&gt; <em>\&quot;(.*?)\&quot;<\/em><\/li>/', $match, $tpl_t);
				if(!empty($tpl_t[1]))
					$debugbar["tpl Tags"]->addMessage(' {'.$tpl_t[1].'}  → '.htmlspecialchars_decode($tpl_t[2]), $name[1].'.tpl');
			}

			//cot_print($matches);
			//cot_print($name,$tpl_tag);
			//$debugbar["tpl Tags"]->addMessage($arr_out, $name[1].'.tpl');
		}

	}


	$debugbarrcfooter = $debugbarRenderer->render();
}