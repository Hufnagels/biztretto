<?php
    class Office
    {
        private $id;
        private $dbFields=array();

        /**
         * @param $id
         */
        public function __construct($id)
        {
            $this->id = (int)$id;

            //load object
            if ($id>0)  $this->load();
        }

        /**
         * Save object
         */
        public function save()
        {
            //update
            if ($this->id>0)
            {
                $sql = 'UPDATE office SET
                            office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",
                            office_type="'.MySQL::filter($this->dbFields['office_type']).'",
                            office_name_hu="'.MySQL::filter($this->dbFields['office_name_hu']).'",
                            office_email="'.MySQL::filter($this->dbFields['office_email']).'",
                            office_tel="'.MySQL::filter($this->dbFields['office_tel']).'",
                            office_postcode="'.MySQL::filter($this->dbFields['office_postcode']).'",
                            office_city="'.MySQL::filter($this->dbFields['office_city']).'",
                            office_street="'.MySQL::filter($this->dbFields['office_street']).'",
                            office_name_en="'.MySQL::filter($this->dbFields['office_name_en']).'",
                            contact_name="'.MySQL::filter($this->dbFields['contact_name']).'",
                            contact_title="'.MySQL::filter($this->dbFields['contact_title']).'",
                            updatedDate=NOW(),
                            fileSystemQuota="'.(int)MySQL::filter($this->dbFields['fileSystemQuota']).'"
                             WHERE office_id='.(int)$this->id;

                ActionLogger::addToActionLog('office.save.update',$this,'');

                return (MySQL::runCommand($sql)!==NULL);
            }
            else
            {
                //new
                $sql='INSERT INTO office (
                office_nametag,
                office_type,
                office_name_hu,
                office_email,
                office_tel,
                office_postcode,
                office_city,
                office_street,
                office_name_en,
                contact_name,
                contact_title,
                updatedDate,
                createdDate,
                fileSystemQuota)
                 VALUES(
                  "'.MySQL::filter($this->dbFields['office_nametag']).'",
                  "'.MySQL::filter($this->dbFields['office_type']).'",
                  "'.MySQL::filter($this->dbFields['office_name_hu']).'",
                  "'.MySQL::filter($this->dbFields['office_email']).'",
                  "'.MySQL::filter($this->dbFields['office_tel']).'",
                  "'.MySQL::filter($this->dbFields['office_postcode']).'",
                  "'.MySQL::filter($this->dbFields['office_city']).'",
                  "'.MySQL::filter($this->dbFields['office_street']).'",
                  "'.MySQL::filter($this->dbFields['office_name_en']).'",
                  "'.MySQL::filter($this->dbFields['contact_name']).'",
                  "'.MySQL::filter($this->dbFields['contact_title']).'",
                  NOW(),
                  NOW(),
                  "'.(int)MySQL::filter($this->dbFields['fileSystemQuota']).'"
                 )';

                MySQL::runCommand($sql);
                $id = MySQL::getLastId();

                $ret = ($this->id = $id)>0;

                ActionLogger::addToActionLog('office.save.insert',$this,'');

                return $ret;
            }
        }

        /**
         * @return int
         */
        public function getId()
        {
            return (int)$this->id;
        }

        /**
         * @param $id (elotag,vezeteknev,keresztnev,full_name,user_name,user_email,userlevel,pwd,position,gender,language,schools,skills,user_kep)
         * @param $value
         */
        public function setDBField($id,$value)
        {
            $this->dbFields[$id] = $value;
        }

        /**
         * @return array
         */
        public function getDBFields()
        {
            return $this->dbFields;
        }

        /**
         * @param $id
         * @return mixed
         */
        public function getDBField($id)
        {
            return $this->dbFields[$id];
        }

        /**
         * Load object from db
         */
        private function load()
        {
            $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM office WHERE office_id='.(int)$this->id),MySQL::fmAssoc);
        }

        /**
         * @param $a_bytes
         * @return array
         */
        public function _formatBytes($a_bytes, $type = TRUE)
        {
            if ($a_bytes < 1024) {
                $result = array("size" => $a_bytes, "type" => " B");
            } elseif ($a_bytes < 1048576) {
                $result = array("size" => round($a_bytes / 1024, 2), "type" => " KB");
                //return round($a_bytes / 1024, 2) .' KiB';
            } elseif ($a_bytes < 1073741824) {
                $result = array("size" => round($a_bytes / 1048576, 2), "type" => " MB");
                //return round($a_bytes / 1048576, 2) . ' MiB';
            } elseif ($a_bytes < 1099511627776) {
                $result = array("size" => round($a_bytes / 1073741824, 2), "type" => " GB");
                //return round($a_bytes / 1073741824, 2) . ' GiB';
            } elseif ($a_bytes < 1125899906842624) {
                $result = array("size" => round($a_bytes / 1099511627776, 2), "type" => " TB");
                //return round($a_bytes / 1099511627776, 2) .' TiB';
            } elseif ($a_bytes < 1152921504606846976) {
                $result = array("size" => round($a_bytes / 1125899906842624, 2), "type" => " PB");
                //return round($a_bytes / 1125899906842624, 2) .' PiB';
            } elseif ($a_bytes < 1180591620717411303424) {
                $result = array("size" => round($a_bytes / 1152921504606846976, 2), "type" => " EB");
                //return round($a_bytes / 1152921504606846976, 2) .' EiB';
            } elseif ($a_bytes < 1208925819614629174706176) {
                $result = array("size" => round($a_bytes / 1180591620717411303424, 2), "type" => " ZB");
                //return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
            } else {
                $result = array("size" => round($a_bytes / 1208925819614629174706176, 2), "type" => " YB");
                //return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
            }

            return $result['size'].$result['type'];//$type?$result['size'].$result['type']:json_encode($result, TRUE);
        }
        /**
         * @return array|bool
         */
        public function calculateDiskUsage()
        {
            $quota = (int)$this->getDBField('fileSystemQuota');
            if (!$quota) return FALSE;
            $quota = $quota * 1024 * 1024;

            $kb=0;

            //upload dir
            foreach (getAllFilesRecursive(IMGPATH.$this->getDBField('office_nametag').'/') as $fileName)
                $kb+=filesize($fileName);

            $freeh = ($quota - $kb) / 1024 / 1024;

            return array(
                'quota'=>$quota,
                'free'=>$quota - $kb,
                'quotaMB'=>$this->_formatBytes($quota,FALSE),
                'freeMB'=>$this->_formatBytes($quota - $kb),
            );
        }

        /**
         * @param $filesizeInBytes
         * @return bool
         */
        public function checkQuotaBeforeUpload($filesizeInBytes)
        {
            $quota = $this->calculateDiskUsage();
            if (!$quota) return TRUE;

            $test = $quota['free'] - $filesizeInBytes >= 0;

            return $test;
        }

        /**
         * @param $officeId
         * @return array|bool
         */
        public static function helperCalculateDiskUsage($officeId)
        {
            $o = new Office((int)$officeId);
            return $o->calculateDiskUsage();
        }

        /**
         * @param $officeId
         * @param $filesizeInBytes
         * @return bool
         */
        public static function helperCheckQuotaBeforeUpload($officeId,$filesizeInBytes)
        {
            $officeObject = new Office((int)$officeId);
            return $officeObject->checkQuotaBeforeUpload($filesizeInBytes);
        }
    }
?>