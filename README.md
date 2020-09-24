# Benchmark

## start

    benchmark_start()

## end

    benchmark_end(string $renderWith = 'file')
    
   Render can be
    
   * File: it generate a file at the root of your project (csv)
   * Html: it print the result directly in place of the benchmark_end() call
   * Mail: it sent data by email, using laravel Mail facade.
    * From = the one defined in config/mail.php
    * TO need to be defined by adding a new key in config/mail.php 
        
            mail.default_to 

## set point

    Set a point. Name is optional.
    Method name, line & file are retrieved from backtrace.

    benchmark_set_point(?string $name)
