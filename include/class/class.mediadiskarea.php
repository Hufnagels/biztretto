<?php
class MediaDiskArea
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
            $sql='UPDATE media_diskarea SET
office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",name="'.MySQL::filter($this->dbFields['name']).'",sortname="'.MySQL::filter($this->dbFields['sortname']).'",owner="'.(int)MySQL::filter($this->dbFields['owner']).'",createdDate="'.MySQL::filter($this->dbFields['createdDate']).'",size="'.(int)MySQL::filter($this->dbFields['size']).'" WHERE diskArea_id='.$this->id;

            ActionLogger::addToActionLog('mediadiskarea.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO media_diskarea (office_id,office_nametag,name,sortname,owner,createdDate,size) VALUES(
"'.(int)MySQL::filter($this->dbFields['office_id']).'","'.MySQL::filter($this->dbFields['office_nametag']).'","'.MySQL::filter($this->dbFields['name']).'","'.MySQL::filter($this->dbFields['sortname']).'","'.(int)MySQL::filter($this->dbFields['owner']).'",NOW(),"'.(int)MySQL::filter($this->dbFields['size']).'")';
            MySQL::runCommand($sql);
            $id = MySQL::getLastId();

            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('mediadiskarea.save.insert',$this,'');

            return $ret;
        }
    }

    /**
     * @return bool
     */
    public function remove()
    {
        /*
        $sql='';
        $rows = MySQL::runCommand($sql);
        return ($rows > 0)?true:false;
        */
    }

    /**
     * @param $id (diskArea_id,office_id,office_nametag,name,sortname,owner,createdDate,size)
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
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM media_diskarea WHERE diskArea_id='.(int)$this->id),MySQL::fmAssoc);
    }
}
?>