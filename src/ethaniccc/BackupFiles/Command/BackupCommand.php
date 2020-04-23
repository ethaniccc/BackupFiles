<?php

namespace ethaniccc\BackupFiles\Command;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use ethaniccc\BackupFiles\Loader;

/* Internal */
use ethaniccc\BackupFiles\Tasks\BackupWorlds;
use ethaniccc\BackupFiles\Tasks\BackupPlugins;
use ethaniccc\BackupFiles\Tasks\BackupPluginData;
use ethaniccc\BackupFiles\Tasks\BackupAll;

class BackupCommand extends Command implements PluginIdentifiableCommand{

    private $plugin;

    public function __construct(string $name, Plugin $plugin, string $description = "", ?string $usageMessage = null, array $aliases = []){
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->setDescription("Backup your server files!");
    }

    public function getPlugin() : Plugin{
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("backup.command")){
            $sender->sendMessage(TextFormat::BOLD . TextFormat::RED . "You don't have the permission to use this command!");
        } else {
            if($sender instanceof Player){
                if(empty($args[0])){
                    $sender->sendMessage(TextFormat::BOLD . TextFormat::RED . "You need to chose what to backup! /backup <argument>");
                    $sender->sendMessage(TextFormat::BOLD . TextFormat::YELLOW . "List of supported arguments: worlds, plugins, plugindata, all (worlds, plugins, plugindata)");
                } else {
                    if($args[0] !== "worlds" and $args[0] !== "plugins" and $args[0] !== "plugindata" and $args[0] !== "all"){
                        $sender->sendMessage(TextFormat::RED . "Invalid arguments!");
                        return;
                    }
                    switch($args[0]){
                        case "worlds":
                            $this->getPlugin()->getServer()->getAsyncPool()->submitTask(new BackupWorlds());
                            $sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "Your worlds are being backed up!");
                        break;
                        case "plugins":
                            $this->getPlugin()->getServer()->getAsyncPool()->submitTask(new BackupPlugins());
                            $sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "Your plugins are now being backed up!");
                        break;
                        case "plugindata":
                            $this->getPlugin()->getServer()->getAsyncPool()->submitTask(new BackupPluginData());
                            $sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "Your plugin data is now being backed up!");
                        break;
                        case "all":
                            $this->getPlugin()->getServer()->getAsyncPool()->submitTask(new BackupAll());
                            $sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "Your worlds, plugins, and plugin data is now being backed up!");
                        break;
                    }
                }
            } else {
                if(empty($args[0])){
                    $this->getPlugin()->getServer()->getLogger()->info(TextFormat::BOLD . TextFormat::RED . "You need to chose what to backup! /backup <argument>");
                    $this->getPlugin()->getServer()->getLogger()->info(TextFormat::BOLD . TextFormat::YELLOW . "List of supported arguments: worlds, plugins, plugindata, all (worlds, plugins, plugindata)");
                } else {
                    if($args[0] !== "worlds" and $args[0] !== "plugins" and $args[0] !== "plugindata" and $args[0] !== "all"){
                        $this->getPlugin()->getServer()->getLogger()->info(TextFormat::RED . "Invalid arguments!");
                        return;
                    }
                    switch($args[0]){
                        case "worlds":
                            $this->getPlugin()->getServer()->getAsyncPool()->submitTask(new BackupWorlds($this->getPlugin()));
                            $this->getPlugin()->getServer()->getLogger()->info(TextFormat::BOLD . TextFormat::GREEN . "Your worlds are being backed up!");
                        break;
                        case "plugins":
                            $this->getPlugin()->getServer()->getAsyncPool()->submitTask(new BackupPlugins());
                            $this->getPlugin()->getServer()->getLogger()->info(TextFormat::BOLD . TextFormat::GREEN . "Your plugins are now being backed up!");
                        break;
                        case "plugindata":
                            $this->getPlugin()->getServer()->getAsyncPool()->submitTask(new BackupPluginData());
                            $this->getPlugin()->getServer()->getLogger()->info(TextFormat::BOLD . TextFormat::GREEN . "Your plugin data is now being backed up!");
                        break;
                        case "all":
                            $this->getPlugin()->getServer()->getAsyncPool()->submitTask(new BackupAll());
                            $this->getPlugin()->getServer()->getLogger()->info(TextFormat::BOLD . TextFormat::GREEN . "Your worlds, plugins, and plugin data is now being backed up!");
                        break;
                    }
                }
            }
        }
    }

}