<?php namespace EarnAway;

use Exception;

class FailingTooHardException extends Exception {}

/**
 * @param $retries
 * @param callable $fn
 * @return mixed
 * @throws Exception
 * @throws FailingTooHardException
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
                throw new FailingTooHardException(sprintf('Failed after %d retries', $attempts), 0, $e);
            }
            $retries--;
            goto beginning;
        } else {
            throw $e;
        }
    }
}
