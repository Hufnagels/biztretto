<?php
class MediaMediaBox
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
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
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Save object
     *
     * @return bool
     */
    public function save()
    {
        //update
        if ($this->id>0)
        {
            $sql='UPDATE media_mediabox SET 
mediabox_id="'.(int)MySQL::filter($this->dbFields['mediabox_id']).'",diskArea_id="'.(int)MySQL::filter($this->dbFields['diskArea_id']).'",office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",name="'.MySQL::filter($this->dbFields['name']).'",owner="'.(int)MySQL::filter($this->dbFields['owner']).'",createdDate="'.MySQL::filter($this->dbFields['createdDate']).'" WHERE mediabox_id='.$this->id;

            ActionLogger::addToActionLog('mediamediabox.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO media_mediabox (diskArea_id,office_id,office_nametag,name,owner,createdDate) VALUES(
"'.(int)MySQL::filter($this->dbFields['diskArea_id']).'","'.(int)MySQL::filter($this->dbFields['office_id']).'","'.MySQL::filter($this->dbFields['office_nametag']).'","'.MySQL::filter($this->dbFields['name']).'","'.(int)MySQL::filter($this->dbFields['owner']).'",NOW())';
            MySQL::runCommand($sql);
            $id = MySQL::getLastId();
            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('mediamediabox.save.insert',$this,'');

            return $ret;
        }
    }

    /**
     * @return bool
     */
    public function remove()
    {
        ActionLogger::addToActionLog('mediamediabox.remove',$this,'');

        //$sql = "DELETE FROM media_mediabox, media_mediaboxfiles USING media_mediabox LEFT JOIN media_mediaboxfiles on (media_mediaboxfiles.mediabox_id = media_mediabox.mediabox_id ) WHERE media_mediabox.mediabox_id = ".(int)$this->getId();
        //$rows = MySQL::runCommand($sql);

        $sql = "DELETE FROM media_mediabox WHERE media_mediabox.mediabox_id = ".$this->getId();
        $rows = MySQL::runCommand($sql);

        return true;
    }

    /**
     * @param $id (mediabox_id,diskArea_id,office_id,office_nametag,name,owner,createdDate)
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
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM media_mediabox WHERE mediabox_id='.(int)$this->id),MySQL::fmAssoc);
    }
	
    /**
     * @param $officeId
     * @param $name
     * @return bool|int
     */
    public static function getDiskAreaIdByOfficeIdAndName($officeId,$name)
    {
        $retVal = false;
        $sql = 'SELECT diskArea_id FROM media_diskarea WHERE office_id='.(int)$officeId.' AND name="'.MySQL::filter($name).'" LIMIT 1';
        if ($row = MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc))
        {
            $retVal = (int)$row['diskArea_id'];
        }
        return $retVal;
    }

    //ALTER TABLE `slide_slideshow` ADD `mediabox_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `diskArea_id` ;
    /**
     * @param $slideShowId
     * @param $mediaBoxId
     */
    public static function connectSlideShowToMediaBox($slideShowId,$mediaBoxId)
    {
        ActionLogger::addToActionLog('mediamediabox.connectSlideShowToMediaBox',null,'slideshowid:'.$slideShowId.',mediaboxid:'.$mediaBoxId);

        $sql=' UPDATE slide_slideshow SET mediabox_id='.(int)$mediaBoxId.' WHERE slideshow_id='.(int)$slideShowId;
        MySQL::runCommand($sql);
    }

    /**
     * @return array
     */
    public function getMediaBoxFiles()
    {
        $retVal = array();
        $sql = 'SELECT mediaboxFiles_id FROM media_mediaboxfiles WHERE mediabox_id='.$this->getId();
        $rows = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc);
        foreach ($rows as $row)
        {
            $retVal[] = new MediaMediaBoxFiles($row['mediaboxFiles_id']);
        }
        return $retVal;
    }
}
?>