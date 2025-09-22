<?php

namespace App\Extra\Logging;

class CustomizeFormatter
{
    public function __invoke($logger): void
    {
        foreach ($logger->getHandlers() as $handler) {

            $format = "[log]\n";
                $format .= "[date] %datetime% [/date]\n";
                $format .= "[channel] %channel% [/channel]\n";
                $format .= "[type] %level_name% [/type]\n";
                $format .= "[msg] %message% [/msg]\n";
                $format .= "[context] %context% [/context]\n";
                $format .= "[extra] %extra% [/extra]\n";
            $format .= "[/log]\n\n";


            $handler->setFormatter(new CustomLineFormatter(
                $format,
                'd.m.Y H:i:s',
                false,
                true,
                true
            ));

        }
    }
}
