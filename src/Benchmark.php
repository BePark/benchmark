<?php

namespace Bepark\Benchmark;

class Benchmark
{
	const METHOD_TO_NOT_TRACE = ['benchmark_set_point', 'benchmark_start', 'benchmark_end', 'getInstance'];

	const RENDER_FILE = 'file';
	const RENDER_HTML = 'html';
	const RENDER_MAIL = 'mail';

	/** @var Benchmark|null */
	protected static $_instance;

	/** @var array */
	protected $_points = [];

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
		$pointTime =  microtime(true);

		$n = 1;
		do {
			$previousBacktrace = $backTrace[$n];
			$n++;
		} while(
			in_array($previousBacktrace['function'], self::METHOD_TO_NOT_TRACE) && $n < 4
		);

		$lastPoint = empty($this->_points) ? false : end($this->_points);
		$firsPoint = (isset($this->_points[0]) ? $this->_points[0] : false);

		$file = isset($previousBacktrace['file']) ? $previousBacktrace['file'] : null;

		if(is_null($file) && isset($previousBacktrace['class']))
		{
			$file = $previousBacktrace['class'];
		}

		$this->_points[] = [
			'name' => 'step ' . count($this->_points) . ' - ' . $name,
			'file' => $file,
			'line' => $previousBacktrace['line'] ?? '',
			'method' => $previousBacktrace['function'],
			'time' => $pointTime,
			'time_from_start' => round(($firsPoint ? $pointTime - $firsPoint['time'] : 0),3),
			'time_from_last_point' => round(($lastPoint ? $pointTime - $lastPoint['time'] : 0), 3),
		];

		return $this;
	}

	/**
	 * Render benchmark result.
	 * RenderWith available are file, html or mail.
	 * Other it write a file at the root directory.
	 *
	 * @param string $renderWith
	 */
	public function render(string $renderWith = 'file')
	{
		$this->setPoint('END');

		if(empty($this->_points))
		{
			die('NOT POINT ARE SET');
		}

		$headers = ($renderWith === self::RENDER_HTML ? '' : []);

		foreach($this->_points[0] as $key => $value)
		{
			if($renderWith === self::RENDER_HTML)
			{
				$headers .= '<td>' . $key . '</td>';
			}
			else
			{
				$headers[] = $key;
			}

		}

		if($renderWith === self::RENDER_HTML)
		{
			echo '<table border="1px">
			<thead>
				<tr>
				'. $headers .'
				</tr>
			</thead>
	
		';
		}

		if($renderWith !== self::RENDER_HTML)
		{
			$fileName = __DIR__ . '/../../../../benchmark_' . md5(microtime()) . '.csv';
			$fp = fopen($fileName, 'w+');
			fputcsv($fp, $headers);
		}

		foreach($this->_points as $point)
		{
			if($renderWith === self::RENDER_HTML)
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

		if($renderWith === self::RENDER_HTML)
		{
			echo '</table>';
			die;
		}

		if($renderWith === self::RENDER_MAIL)
		{
			\Mail::raw('here are benchmark result', function ($message) use ($fileName) {
				$message->from(
					config('mail.from.address'),
					config('mail.from.name')
				);

				$message->to(config('mail.default_to'), 'Benchmark guys');

				$message->subject('Benchmark result on ' . config('app.name'));

				$message->attach($fileName);
			});

			unlink($fileName);
		}
	}
}
