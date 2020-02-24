<?php

namespace Bepark\Benchmark;

class Benchmark
{
	/** @var Benchmark|null */
	protected static $_instance;

	/** @var array */
	protected $_points;

	public static function getInstance(): Benchmark
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new self;

			self::$_instance->setPoint('START');
		}

		return self::$_instance;
	}

	/**
	 * @param string|null $name
	 * @return $this
	 */
	public function setPoint(?string $name = ''): Benchmark
	{
		$backTrace = debug_backtrace();
		$previousBacktrace = $backTrace[1];
		$pointTime =  microtime(true);

		//If we are coming from the helper method.
		if($previousBacktrace['function'] == 'benchmark_set_point')
		{
			$previousBacktrace = $backTrace[2];
		}

		$lastPoint = end($this->_points);
		$firsPoint = (isset($this->_points[0]) ? $this->_points[0] : false);

		$this->_points[] = [
			'file' => $previousBacktrace['file'],
			'line' => $previousBacktrace['line'],
			'method' => $previousBacktrace['function'],
			'time' => $pointTime,
			'time_from_start' => ($lastPoint ? $pointTime - $firsPoint['time'] : 0),
			'time_from_last_point' => ($lastPoint ? $pointTime - $lastPoint['time'] : 0),
		];

		return $this;
	}

	/**
	 * Render benchmark result. If html is true, it's displayed directly.
	 * Other it write a file at the root directory.
	 *
	 * @param bool $html
	 */
	public function render(bool $html = false)
	{
		$this->setPoint('END');

		if(empty($this->_points))
		{
			die('NOT POINT ARE SET');
		}

		$headers = '';

		foreach($this->_points[0] as $key => $value)
		{
		    $headers = ($html ? '<td>' . $key . '</td>' : $key);
		}

		if($html)
		{
			echo '<table>
				<thead>
					<tr>
					'. $headers .'
					</tr>
				</thead>
		
			';
		}

		$fp = fopen(__DIR__ . '/../../../../benchmark_' . md5(microtime() . '.csv'), 'w+');
		foreach($this->_points as $point)
		{
			if($html)
			{
				echo '<tr>';
				foreach($point as $key => $value)
				{
					echo '<td>' . $value . '</td>';
				}
				echo '</tr>';
			}
			else
			{
				fputcsv($fp, $point);
			}
		}

		if($html)
		{
			die;
		}
	}
}
