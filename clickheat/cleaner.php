<?php
/**
 * ClickHeat : Fonctions diverses / Various functions
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 01/04/2007
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

if (CLICKHEAT_ADMIN === false)
{
	return false;
}
$deletedFiles = 0;
$deletedDirs = 0;
/**
 * Clean the logs' directory according to configuration data
**/
if ($clickheatConf['flush'] !== 0 && is_dir($clickheatConf['logPath']) === true)
{
	$logDir = dir($clickheatConf['logPath'].'/');
	while (($dir = $logDir->read()) !== false)
	{
		if ($dir === '.' || $dir === '..' || !is_dir($logDir->path.$dir))
		{
			continue;
		}

		$d = dir($logDir->path.$dir.'/');
		$deletedAll = true;
		$oldestDate = mktime(0, 0, 0, date('m'), date('d') - $clickheatConf['flush'], date('Y'));
		while (($file = $d->read()) !== false)
		{
			if ($file === '.' || $file === '..' || $file === 'url.txt')
			{
				continue;
			}
			$ext = explode('.', $file);
			if (count($ext) !== 2)
			{
				$deletedAll = false;
				continue;
			}
			$filemtime = filemtime($d->path.$file);
			if ($ext[1] === 'log' && $filemtime <= $oldestDate)
			{
				@unlink($d->path.$file);
				$deletedFiles++;
				continue;
			}
			$deletedAll = false;
		}
		/** If every log file (but the url.txt) has been deleted, then we should delete the directory too */
		if ($deletedAll === true)
		{
			@unlink($d->path.'/url.txt');
			$deletedFiles++;
			@rmdir($d->path);
			$deletedDirs++;
		}
		$d->close();
	}
	$logDir->close();
}

/**
 * Clean the cache directory for every file older than 2 minutes
**/
if (is_dir($clickheatConf['cachePath']) === true)
{
	$d = dir($clickheatConf['cachePath'].'/');
	while (($file = $d->read()) !== false)
	{
		if ($file === '.' || $file === '..')
		{
			continue;
		}
		$ext = explode('.', $file);
		if (count($ext) !== 2)
		{
			continue;
		}
		$filemtime = filemtime($d->path.$file);
		switch ($ext[1])
		{
			case 'html':
			case 'png':
			case 'png_temp':
			case 'png_log':
				{
					if ($filemtime + 86400 < time())
					{
						@unlink($d->path.$file);
						$deletedFiles++;
						continue;
					}
					break;
				}
		}
	}
	$d->close();
}

if ($deletedDirs + $deletedFiles === 0)
{
	echo 'OK';
	return true;
}
echo sprintf(LANG_CLEANER_RUN, $deletedFiles, $deletedDirs);
?>