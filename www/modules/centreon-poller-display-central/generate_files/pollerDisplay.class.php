<?php
/*
 * Centreon
 *
 * Source Copyright 2005-2016 Centreon
 *
 * Unauthorized reproduction, copy and distribution
 * are not allowed.
 *
 * For more informations : contact@centreon.com
 *
 */

require_once dirname(__FILE__) . '/../centreon-poller-display-central.conf.php';

use \CentreonPollerDisplayCentral\ConfigGenerate\Bam\Ba;
use \CentreonPollerDisplayCentral\ConfigGenerate\Centreon\Host;
use \CentreonPollerDisplayCentral\ConfigGenerate\Centreon;

class PollerDisplay extends AbstractObject
{
    protected $engine = false;
    protected $broker = true;
    protected $generate_filename = 'poller-display.sql';

    public static function getInstance() {
        static $instances = array();

        $calledClass = get_called_class();

        if (!isset($instances[$calledClass])) {
            $instances[$calledClass] = new $calledClass();
        }

        return $instances[$calledClass];
    }

    public function reset() {

    }

    public function generateFromPollerId($poller_id, $localhost) {

        $pollerConfigPath = "/usr/share/centreon/filesGeneration/broker/$poller_id/";

        $baObj = new Ba($this->backend_instance->db);
        $sql = $baObj->generateSql();

        $obj = new Host($this->backend_instance->db);
        $sql = $obj->generateSql();

        var_dump($sql);

        $stmt = $this->backend_instance->db->prepare("SELECT id 
                                                    FROM mod_poller_display_server_relations 
                                                    WHERE nagios_server_id = :pollerId");
        $stmt->bindParam(':pollerId', $poller_id, PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->fetch()){

            $contentCentreon = new Centreon($this->backend_instance->db);
            $this->generateFile($pollerConfigPath,$this->generate_filename,$contentCentreon);

            $stmt = $this->backend_instance->db->prepare("SELECT ba_id 
                                                    FROM mod_bam_poller_relations 
                                                    WHERE poller_id = :pollerId");
            $stmt->bindParam(':pollerId', $poller_id, PDO::PARAM_INT);
            $stmt->execute();
            while ($data = $stmt->fetch()){
                $bamDisplays = $data['ba_id'];
            }

            if($bamDisplays){

                $baObj = new Ba($this->backend_instance->db);
                $baObj->generateSql();

            }
        }
    }


    public function generateFile($root, $name, $content) {

        $path = $root . $name;
        if (!file_exists($path)) {
            touch($path);
        }

        $handle=fopen($path, "rw");
        fwrite($handle, $content);
        fclose($handle);

    }









}


