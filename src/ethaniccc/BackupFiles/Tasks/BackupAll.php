<?php

namespace ethaniccc\BackupFiles\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class BackupAll extends AsyncTask{

    private function backup( $source, $target ) {
        if ( is_dir( $source ) ) {
            @mkdir( $target );
            $d = dir( $source );
            while ( FALSE !== ( $entry = $d->read() ) ) {
                if ( $entry == '.' || $entry == '..' ) {
                    continue;
                }
                $Entry = $source . '/' . $entry;
                if ( is_dir( $Entry ) ) {
                    $this->backup( $Entry, $target . '/' . $entry );
                    continue;
                }
                copy( $Entry, $target . '/' . $entry );
            }
   
            $d->close();
        } else {
            copy( $source, $target );
        }
    }

    private function delete_old_backup($dirname) {
        if (is_dir($dirname))
          $dir_handle = opendir($dirname);
        if (!$dir_handle)
         return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
               if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
               else{
                    $this->delete_old_backup($dirname.'/'.$file);
               }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    public function onRun(){
        $backup = 'plugin_data/BackupFiles/FullBackup';
        if(file_exists($backup)) $this->delete_old_backup($backup);
        mkdir($backup);
        $worlds = 'plugin_data/BackupFiles/FullBackup/worlds';
        $plugins = 'plugin_data/BackupFiles/FullBackup/plugins';
        $data = 'plugin_data/BackupFiles/FullBackup/plugindata';
        mkdir($worlds);
        mkdir($plugins);
        mkdir($data);
        $this->backup('worlds', $worlds);
        $this->backup('plugins', $plugins);
        $this->backup('plugin_data', $data);
    }

    public function onCompletion(Server $server){
        Server::getInstance()->getLogger()->info(TextFormat::GREEN . "All worlds, plugins, and plugin data have been saved!");
    }

}