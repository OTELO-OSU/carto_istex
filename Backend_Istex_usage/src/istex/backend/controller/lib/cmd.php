<?php

	function getCmdOpt($aArg, $aSpec, &$aResult, $bExitOnError = false, $bExitOnUnknown = false)
	{
		$aQuick = array();
		$aCounts = array();

		foreach($aSpec as $aLine)
		{
			if (is_array($aLine))
			{
				if ($aLine[0]) $aQuick['--'.$aLine[0]] = $aLine;
				if ($aLine[1]) $aQuick['-'.$aLine[1]] = $aLine;
				$aCounts[$aLine[0]] = 0;
			}
		}

		$aResult = array();
		$bUnknown = false;
		$iSize = sizeof($aArg);
		for ($i = 1; $i < $iSize; $i++)
		{
			if (isset($aQuick[$aArg[$i]]))
			{
				$aLine = $aQuick[$aArg[$i]];
				$aCounts[$aLine[0]]++;
				$xVal = null;
				if ($aLine[4] == $aLine[5])
				{
					if ($aLine[4])
					{
						$xVal = array();
						for($n = $aLine[4]; $i < $iSize && $n; $n--)
						{
							$i++;
							if ($i >= $iSize || $aArg[$i][0] == '-') showUsage($aSpec, $bExitOnError, 'Parameter of  \''.$aLine[0].'\' is missing');

							switch ($aLine[6])
							{
							case 'realpath':
								$xVal[] = realpath($aArg[$i]);
								break;
							case 'realdir':
								$sPath = realpath(dirname($aArg[$i]));
								if ($sPath)
									$xVal[] = $sPath . '/' . basename($aArg[$i]);
								else
									$xVal[] = $sPath;
								break;
							case 'bool':
								$xVal[] = (bool)$aArg[$i];
								break;
							case 'int':
								$xVal[] = (int)$aArg[$i];
								break;
							case 'float':
								$xVal[] = (float)$aArg[$i];
								break;
							default:
								$xVal[] = $aArg[$i];
								break;
							}
						}
						if ($aLine[4] == 1) $xVal = $xVal[0];
					}
					else
					{
						$xVal = true;
					}
				}
				else
				{
					fail('Variable numbers of params not yet supported');
				}

				if ($aLine[3] > 1)
				{
					if (!array_key_exists($aLine[0], $aResult)) $aResult[$aLine[0]] = array();
					$aResult[$aLine[0]][] = $xVal;
				}
				else
				{
					$aResult[$aLine[0]] = $xVal;
				}
			}
			else
			{
				$bUnknown = $aArg[$i];
			}
		}

		if (array_key_exists('help', $aResult)) showUsage($aSpec);
		if ($bUnknown && $bExitOnUnknown) showUsage($aSpec, $bExitOnError, 'Unknown option \''.$bUnknown.'\'');

		foreach($aSpec as $aLine)
		{
			if (is_array($aLine))
			{
				if ($aCounts[$aLine[0]] < $aLine[2]) showUsage($aSpec, $bExitOnError, 'Option \''.$aLine[0].'\' is missing');
				if ($aCounts[$aLine[0]] > $aLine[3]) showUsage($aSpec, $bExitOnError, 'Option \''.$aLine[0].'\' is pressent too many times');
				switch ($aLine[6])
				{
				case 'bool':
					if (!array_key_exists($aLine[0], $aResult))
						$aResult[$aLine[0]] = false;
					break;
				}
			}
		}
		return $bUnknown;
	}

	function showUsage($aSpec, $bExit = false, $sError = false)
	{
		if ($sError)
		{
			echo basename($_SERVER['argv'][0]).': '.$sError."\n";
			echo 'Try `'.basename($_SERVER['argv'][0]).' --help` for more information.'."\n";
			exit;
		}
		echo "Usage: ".basename($_SERVER['argv'][0])."\n";
		$bFirst = true;
		foreach($aSpec as $aLine)
		{
			if (is_array($aLine))
			{
				if ($bFirst)
				{
					$bFirst = false;
					echo "\n";
				}
				$aNames = array();
				if ($aLine[1]) $aNames[] = '-'.$aLine[1];
				if ($aLine[0]) $aNames[] = '--'.$aLine[0];
				$sName = join(', ',$aNames);
				echo '  '.$sName.str_repeat(' ',30-strlen($sName)).$aLine[7]."\n";
			}
			else
			{
				echo $aLine."\n";
			}
		}
		echo "\n";
		exit;
	}
