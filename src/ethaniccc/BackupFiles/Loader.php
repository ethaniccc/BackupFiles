<?php

declare(strict_types=1);

namespace ethaniccc\BackupFiles;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\permission\Permission;

/* Internal */
use ethaniccc\BackupFiles\Command\BackupCommand;

class Loader extends PluginBase{

    public function onEnable(){
        Server::getInstance()->getLogger()->info(TextFormat::GREEN . "BackupFiles by ethaniccc has been enabled!");
        $this->loadCommands();
    }

    private function loadCommands(){
        $commandMap = Server::getInstance()->getCommandMap();
        $commandMap->register($this->getName(), new BackupCommand("backup", $this));
        $this->addPerms([
            /* I will add permissions as an array just in case I plan on adding
            multiple permissions later */
            new Permission('backup.command', 'Access to use the backup command', Permission::DEFAULT_OP)
        ]);
    }

    protected function addPerms(array $permissions){
        foreach($permissions as $permission){
            Server::getInstance()->getPluginManager()->addPermission($permission);
        }
    }

    public function onDisable(){
        Server::getInstance()->getLogger()->info(TextFormat::RED . "BackupFiles by ethaniccc has been disabled!");
    }

}