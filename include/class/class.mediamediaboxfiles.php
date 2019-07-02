<?php
class MediaMediaBoxFiles
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
            $sql='UPDATE media_mediaboxfiles SET 
mediaboxFiles_id="'.(int)MySQL::filter($this->dbFields['mediaboxFiles_id']).'",diskArea_id="'.(int)MySQL::filter($this->dbFields['diskArea_id']).'",mediabox_id="'.(int)MySQL::filter($this->dbFields['mediabox_id']).'",mymedia_id="'.(int)MySQL::filter($this->dbFields['mymedia_id']).'",office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",owner="'.(int)MySQL::filter($this->dbFields['owner']).'",createdDate="'.MySQL::filter($this->dbFields['createdDate']).'" WHERE mediaboxFiles_id='.$this->id;

            ActionLogger::addToActionLog('mediamediaboxfiles.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO media_mediaboxfiles (diskArea_id,mediabox_id,mymedia_id,office_id,office_nametag,owner,createdDate) VALUES(
"'.(int)MySQL::filter($this->dbFields['diskArea_id']).'","'.(int)MySQL::filter($this->dbFields['mediabox_id']).'","'.(int)MySQL::filter($this->dbFields['mymedia_id']).'","'.(int)MySQL::filter($this->dbFields['office_id']).'","'.MySQL::filter($this->dbFields['office_nametag']).'","'.(int)MySQL::filter($this->dbFields['owner']).'",NOW())';
            MySQL::runCommand($sql);
            $id = MySQL::getLastId();
            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('mediamediaboxfiles.save.insert',$this,'');

            return $ret;
        }
    }

    /**
     * @param $myMediaId
     * @param $mediaBoxId
     * @return bool
     */
    public function removeByMyMediaIdAndMediaBoxId($myMediaId,$mediaBoxId)
    {
        ActionLogger::addToActionLog('mediamediaboxfiles.removebymediaidandmediaboxid',$this,'mymediaid:'.$myMediaId.'mediaboxid:,'.$mediaBoxId);
        $sql = "DELETE from media_mediaboxfiles WHERE mymedia_id = ".(int)$myMediaId." AND mediabox_id = ".(int)$mediaBoxId;
        $rows = MySQL::runCommand($sql);
        return ($rows > 0)?true:false;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public static function removeFileByIds(array $ids)
    {
        //foreach ($ids as &$id)  $id=(int)$id;

        ActionLogger::addToActionLog('mediafiles.removefilebyids',null,'ids:'.implode(',',$ids));

        //$sql = "DELETE from media_mymedia, media_mediaboxfiles USING media_mymedia LEFT JOIN media_mediaboxfiles on (media_mymedia.mymedia_id = media_mediaboxfiles.mymedia_id) WHERE media_mymedia.mymedia_id IN (".implode(',',$ids).") ";
        $sql = "DELETE from media_mymedia WHERE mymedia_id IN (".implode(',',$ids).") ";
        return MySQL::runCommand($sql)>0;
    }

    /**
     * @param $id (mediaboxFiles_id,diskArea_id,mediabox_id,mymedia_id,office_id,office_nametag,owner,createdDate)
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
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM media_mediaboxfiles WHERE mediaboxFiles_id='.(int)$this->id),MySQL::fmAssoc);
    }
}
?>