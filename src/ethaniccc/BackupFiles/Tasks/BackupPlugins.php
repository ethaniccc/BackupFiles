<?php

namespace ethaniccc\BackupFiles\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class BackupPlugins extends AsyncTask{

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
        $destination = 'plugin_data/BackupFiles/PluginBackup';
        if(file_exists($destination)) $this->delete_old_backup($destination);
        sleep(2);
        mkdir($destination);
        $this->backup('plugins', $destination);
    }

    public function onCompletion(Server $server){
        Server::getInstance()->getLogger()->info(TextFormat::GREEN . "Your plugins have been saved!");
    }

}