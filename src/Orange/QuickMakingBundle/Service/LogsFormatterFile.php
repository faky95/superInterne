<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orange\QuickMakingBundle\Service;

use Monolog\Formatter\FormatterInterface;

class LogsFormatterFile implements FormatterInterface
{
	public function format(array $record)
	{
		if(isset($record['context']['request_uri'])){
		   $line  = "";
		}else 
			$line = sprintf('[ %s ]', $record['message'] );
		return $line.PHP_EOL;
	}

	public function formatBatch(array $records)
	{
		foreach ($records as $key => $record) {
			$records[$key] = $this->format($record);
		}

		return $records;
	}
}