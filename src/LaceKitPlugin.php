<?php

namespace MemiBeltrame\LaceKit;

use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\Event;
use Composer\Plugin\Capable;
use Composer\Plugin\Capability\CommandProvider;

class LaceKitPlugin implements PluginInterface, EventSubscriberInterface, Capable
{
    public function activate(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
        // No need to do much in activation; events handle the actions
    }

    public function deactivate(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
        // Clean up if necessary
    }

    public function uninstall(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
        // Clean up if necessary
    }

    public static function getSubscribedEvents()
    {
        return [
            'post-install-cmd' => 'onPostInstall',
            'post-update-cmd' => 'onPostUpdate'
        ];
    }

    public function getCapabilities()
    {
        return [
            CommandProvider::class => LaceKitCommandProvider::class,
        ];
    }

    public function onPostInstall(Event $event)
    {
        // Execute post-install script
        $this->runPostInstallScript();
    }

    public function onPostUpdate(Event $event)
    {
        // Execute post-update script
        $this->runPostUpdateScript();
    }

    private function runPostInstallScript()
    {
        $scriptPath = __DIR__ . '/../scripts/postInstall.php';
        if (file_exists($scriptPath)) {
            system("php " . escapeshellarg($scriptPath));
        } else {
            echo "Script not found: $scriptPath\n";
        }
    }
    private function runPostUpdateScript()
    {
        $scriptPath = __DIR__ . '/../scripts/postUpdate.php';
        if (file_exists($scriptPath)) {
            system("php " . escapeshellarg($scriptPath));
        } else {
            echo "Script not found: $scriptPath\n";
        }
    }
} 