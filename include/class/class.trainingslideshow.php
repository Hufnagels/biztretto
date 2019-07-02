<?php
class TrainingSlideShow
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
            $sql='UPDATE training_slideshow SET 
ts_id="'.(int)MySQL::filter($this->dbFields['ts_id']).'",training_id="'.(int)MySQL::filter($this->dbFields['training_id']).'",slideshow_id="'.(int)MySQL::filter($this->dbFields['slideshow_id']).'",startDate='.MySQL::filterQ($this->dbFields['startDate'],true).',endDate='.MySQL::filterQ($this->dbFields['endDate'],true).',timeout1='.MySQL::filterQ($this->dbFields['timeout1'],true).',timeout2='.MySQL::filterQ($this->dbFields['timeout2'],true).',wtimeout1='.MySQL::filterQ($this->dbFields['wtimeout1'],true).',wtimeout2='.MySQL::filterQ($this->dbFields['wtimeout2'],true).',testlevel="'.(int)MySQL::filter($this->dbFields['testlevel']).'",type="'.(int)MySQL::filter($this->dbFields['type']).'",testtype="'.(int)MySQL::filter($this->dbFields['testtype']).'",repetable="'.(int)MySQL::filter($this->dbFields['repetable']).'",credit="'.(int)MySQL::filter($this->dbFields['credit']).'",parent_id="'.(int)MySQL::filter($this->dbFields['parent_id']).'",lft="'.(int)MySQL::filter($this->dbFields['lft']).'",rgt="'.(int)MySQL::filter($this->dbFields['rgt']).'",depth="'.(int)MySQL::filter($this->dbFields['depth']).'",createdDate="'.MySQL::filter($this->dbFields['createdDate']).'",updatedDate="'.MySQL::filter($this->dbFields['updatedDate']).'",office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'" WHERE ts_id='.$this->id;

            ActionLogger::addToActionLog('trainingslideshow.save.update',$this,'');
            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO training_slideshow (training_id,slideshow_id,startDate,endDate,timeout1,timeout2,wtimeout1,wtimeout2,testlevel,type,testtype,repetable,credit,parent_id,lft,rgt,depth,createdDate,updatedDate,office_id,office_nametag) VALUES(
"'.(int)MySQL::filter($this->dbFields['training_id']).'","'.(int)MySQL::filter($this->dbFields['slideshow_id']).'",'.MySQL::filterQ($this->dbFields['startDate'],true).','.MySQL::filterQ($this->dbFields['endDate'],true).','.MySQL::filterQ($this->dbFields['timeout1'],true).','.MySQL::filterQ($this->dbFields['timeout2'],true).','.MySQL::filterQ($this->dbFields['wtimeout1'],true).','.MySQL::filterQ($this->dbFields['wtimeout2'],true).',"'.(int)MySQL::filter($this->dbFields['testlevel']).'","'.(int)MySQL::filter($this->dbFields['type']).'","'.(int)MySQL::filter($this->dbFields['testtype']).'","'.(int)MySQL::filter($this->dbFields['repetable']).'","'.(int)MySQL::filter($this->dbFields['credit']).'","'.(int)MySQL::filter($this->dbFields['parent_id']).'","'.(int)MySQL::filter($this->dbFields['lft']).'","'.(int)MySQL::filter($this->dbFields['rgt']).'","'.(int)MySQL::filter($this->dbFields['depth']).'",NOW(),NOW(),"'.(int)MySQL::filter($this->dbFields['office_id']).'","'.MySQL::filter($this->dbFields['office_nametag']).'")';
            MySQL::runCommand($sql);
            $id = MySQL::getLastId();
            $ret = ($this->id = $id)>0;
            ActionLogger::addToActionLog('trainingslideshow.save.insert',$this,'');
            return $ret;
        }
    }

    /**
     * @return bool
     */
    public function remove()
    {
        ActionLogger::addToActionLog('trainingslideshow.remove',$this,'');
        /*
        $sql='';
        $rows = MySQL::runCommand($sql);
        return ($rows > 0)?true:false;
        */
    }

    /**
     * @param $id (ts_id,training_id,slideshow_id,startDate,endDate,timeout1,timeout2,wtimeout1,wtimeout2,testlevel,type,testtype,repetable,credit,parent_id,lft,rgt,depth,createdDate,updatedDate,office_id,office_nametag)
     * @param $value
     */
    public function setDBField($id,$value)
    {
        $this->dbFields[$id] = $value;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
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
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM training_slideshow WHERE ts_id='.(int)$this->id),MySQL::fmAssoc);
    }

    /**
     * @param $tId
     * @param $sId
     * @return bool|TrainingSlideShow
     */
    public static function getObjectByTrainingIdAndSlideShowId($tId,$sId)
    {
        $retVal = false;
        $sql = 'SELECT ts_id FROM training_slideshow WHERE training_id='.(int)$tId.' AND slideshow_id='.(int)$sId.' LIMIT 1';
        $row = MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc);
        if ($row['ts_id']>0)
            $retVal = new TrainingSlideShow($row['ts_id']);
        return $retVal;
    }

    /**
     * Get TrainingSlideShow records by Training ID
     *
     * @param int $tId
     * @return array
     */
    public static function getObjectsByTrainingId($tId)
    {
        $retVal = array();
        $sql = 'SELECT ts_id FROM training_slideshow WHERE training_id='.(int)$tId;
        foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $row)
            $retVal[] = new TrainingSlideShow($row['ts_id']);
        return $retVal;
    }

    /**
     * @param $tId
     * @return array
     */
    public static function getSlideShowIdsByTrainingId($tId)
    {
        $retVal = array();
        $sql = 'SELECT ts_id FROM training_slideshow WHERE training_id='.(int)$tId;
        foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $row)
            $retVal[] = $row['slideshow_id'];
        return $retVal;
    }

    /**
     * @param $tId
     * @return array
     */
    public static function getObjectIdsByTrainingId($tId)
    {
        $retVal = array();
        $sql = 'SELECT ts_id FROM training_slideshow WHERE training_id='.(int)$tId;
        foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $row)
            $retVal[] = $row['ts_id'];
        return $retVal;
    }

    /**
     * @param $trainingId
     * @return array
     */
    public static function toHierarchy($trainingId)
    {
        $retVal = array();

        $sql = "
        SELECT slideshow.*, orig.name
        FROM training_slideshow slideshow
        LEFT JOIN training_slideshow slideshow2
        ON slideshow2.parent_id = slideshow.slideshow_id AND slideshow2.training_id = slideshow.training_id
        LEFT JOIN training_slideshow slideshow3
        ON slideshow3.parent_id = slideshow2.slideshow_id AND slideshow3.training_id = slideshow2.training_id
        LEFT JOIN slide_slideshow orig
        ON orig.slideshow_id = slideshow.slideshow_id
        WHERE slideshow.training_id = " . (int)$trainingId . "
        ORDER BY lft ASC";

        if ($rows = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc))
            $retVal = toHierarchy($rows);

        return $retVal;
    }


    /**
     * @param $toArray
     * @param $trainingId
     * @param $officeId
     * @param $officeNameTag
     * @return bool
     */
    public static function toArray($toArray, $trainingId,$officeId,$officeNameTag)
    {
        $retVal = true;

		$trainingId = (int) $trainingId;
        $ids = array(-1);

        if (isset($toArray) && count($toArray) > 0) {
            foreach ($toArray as &$row) {
                if ($row['parent_id'] == 'none') continue;

                $ids[] = $row['slideshow_id'];

                $sql = 'INSERT IGNORE INTO training_slideshow (training_id,slideshow_id,lft,rgt,depth,parent_id,office_id,office_nametag,createdDate) VALUES(
                        "' . MySQL::filter($trainingId) . '",
                        "' . MySQL::filter($row['slideshow_id']) . '",
                        "' . MySQL::filter($row['lft']) . '",
                        "' . MySQL::filter($row['rgt']) . '",
                        "' . MySQL::filter($row['depth']) . '",
                        "' . MySQL::filter((int)$row['parent_id']) . '",
                        "' . MySQL::filter((int)$officeId) . '",
                        "' . MySQL::filter($officeNameTag) . '",
                        NOW()

                ) ON DUPLICATE KEY UPDATE
                        lft="' . MySQL::filter($row['lft']) . '",
                        rgt="' . MySQL::filter($row['rgt']) . '",
                        depth="' . MySQL::filter($row['depth']) . '",
                        updatedDate=NOW(),
                        parent_id="' . MySQL::filter((int)$row['parent_id']) . '"';

                $retVal = MySQL::execute($sql);
            }
        }

        if ($trainingId > 0 and $retVal === true)
            $retVal = MySQL::execute('DELETE FROM training_slideshow WHERE training_id = ' . (int)$trainingId . ' AND slideshow_id NOT IN (' . implode(',', $ids) . ')');

        return $retVal;
    }

}
?>