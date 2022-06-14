<?php

namespace App\Service;

use DateTime;
use DateTimeInterface;

use function sprintf;

class DurationHumanizer
{
    private DateTimeInterface $from;

    public function __construct()
    {
        $this->from = new DateTime('@0');
    }

    public function humanize(int $duration): string
    {
        $to = new DateTime(sprintf('@%d', $duration));

        if ($duration < 60) {
            $format = '%ss';
        } elseif ($duration < 60*60) {
            $format = '%im %ss';
        } else {
            $format = '%hh %im %ss';
        }

        return $to->diff($this->from)->format($format);
    }
}