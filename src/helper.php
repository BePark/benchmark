<?php

	if(!function_exists('benchmark_start'))
	{
		function benchmark_start()
		{
			$benchmark = \Bepark\Benchmark::getInstance();
		}
	}

	if(!function_exists('benchmark_set_point'))
	{
		function benchmark_set_point(?string $name)
		{
			$benchmark = \Bepark\Benchmark::getInstance();
			$benchmark->setPoint($name);
		}
	}

	if(!function_exists('benchmark_end'))
	{
		function benchmark_end(bool $html = false)
		{
			$benchmark = \Bepark\Benchmark::getInstance();
			$benchmark->render($html);
		}
	}