# Benchmark

## start

    benchmark_start()

## end

    If html is enabled, return html array + die. Otherwise generate a file at the root of your project (same level as vendor directory)

    benchmark_end(bool $html = false)

## set point

    Set a point. Name is optional.
    Method name, line & file are retrieved from backtrace.

    benchmark_set_point(?string $name)
