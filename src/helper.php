<?php

	if(!function_exists('benchmark_start'))
	{
		function benchmark_start()
		{
			$benchmark = \Bepark\Benchmark\Benchmark::getInstance();
		}
	}

	if(!function_exists('benchmark_set_point'))
	{
		function benchmark_set_point(?string $name = null)
		{
			$benchmark = \Bepark\Benchmark\Benchmark::getInstance();
			$benchmark->setPoint($name);
		}
	}

	if(!function_exists('benchmark_end'))
	{
		function benchmark_end(bool $html = false)
		{
			$benchmark = \Bepark\Benchmark\Benchmark::getInstance();
			$benchmark->render($html);
		}
	}
