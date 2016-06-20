<?php namespace EarnAway;

use Exception;

class TooManyTimeoutsException extends Exception {}

/**
 * @param $retries
 * @param callable $fn
 * @return mixed
 * @throws Exception
 * @throws TooManyTimeoutsException
 */
function retry($retries, callable $fn)
{
    // keep the intial number of retries
    $attempts = $retries;

    beginning:
    try {
        return $fn();
    } catch (Exception $e) {
        // operation has timed out
        if (strpos($e->getMessage(), 'timed out') !== false) {
            if (!$retries) {
                throw new TooManyTimeoutsException(sprintf('Failed after %d timeouts', $attempts), 0, $e);
            }
            $retries--;
            unset($e);
            goto beginning;
        } else {
            throw $e;
        }
    }
}
