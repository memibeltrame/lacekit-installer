<?php

namespace MemiBeltrame\LaceKit;

use Composer\Plugin\Capability\CommandProvider;

class LaceKitCommandProvider implements CommandProvider
{
    public function getCommands()
    {
        return [
            new LaceKitCommand()
        ];
    }
} 