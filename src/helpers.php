<?php

if (! function_exists('rethrow_if')) {
    function rethrow_if($e, $exceptionIsTypeOf, $newType, ...$message)
    {
        if ($e instanceof $exceptionIsTypeOf) {
            throw new $newType(...$message);
        }
    }
}
if (! function_exists('get_first_event_response')) {
    function get_first_event_response($array)
    {
        $r = $array;

        if (is_array($array)) {
            foreach ($array as $elem) {
                $r = get_first_event_response($elem);
                if ($r != null) {
                    return $r;
                }
            }
        }

        return $r;
    }
}

if (! function_exists('expect_event_response')) {
    function expect_event_response($eventResponses, $exception = 'RuntimeException', ...$parameters)
    {
        $firstResponse = get_first_event_response($eventResponses);
        throw_if($firstResponse == null, $exception, ...$parameters);

        return $firstResponse;
    }
}
