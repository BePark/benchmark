<?php

	namespace Bepark\Benchmark;

	class Benchmark
	{
		const METHOD_TO_NOT_TRACE = ['benchmark_set_point', 'benchmark_start', 'benchmark_end', 'getInstance'];

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

			foreach($this->_points[0] as $key => $value)
			{
				if($html)
				{
					$headers = '<td>' . $key . '</td>';
				}
				else
				{
					$headers[] = $key;
				}

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
			fputcsv($fp, $headers);

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
