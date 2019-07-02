<?php
class TrainingTraining
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
     * Remove object
     *
     * @return bool
     */
    public function remove()
    {

        $dbtr = new DBTransaction();

        //mastertraining?
        if ($this->getDBField('parent_id') == 0)
        {
            ActionLogger::addToActionLog('trainingtraining.remove.master',$this,'');

            foreach (self::getInstances($this) as $instanceObject)
            {
                $instanceObject->remove();
            }
        }

        ActionLogger::addToActionLog('trainingtraining.remove',$this,'');

        MySQL::runCommand("DELETE FROM training_training, training_slideshow
                USING training_training
                    LEFT JOIN training_slideshow
                    ON (training_slideshow.training_id = training_training.training_id )
                        WHERE training_training.training_id = " . $this->getId());

        MySQL::runCommand('DELETE FROM training_slideshow_score WHERE training_id='.$this->getId());

        $dbtr->destroy();

        return true;
    }

    /**
     * @param $groupId
     * @return array of TrainingTraining
     */
    public static function getInstanceObjectsByTrainingGroupId($groupId)
    {
        $retVal = array();

        $sql = 'SELECT * FROM training_training WHERE activeState = "ready" AND FIND_IN_SET('.(int)$groupId.',traininggroups)';
        foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $row)
        {
            $retVal[] = new TrainingTraining($row['training_id']);
        }

        return $retVal;
    }
	
	/**
     * @param $groupId
     * @return array of TrainingTraining
	 * inserted 2013. 11. 20.
     */
    public static function getInstanceObjectsByTrainingId($trainingId)
    {
        $retVal = array();

        $sql = 'SELECT * FROM training_training WHERE activeState = "ready" AND training_id = '.$trainingId;
        foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $row)
        {
            $retVal[] = new TrainingTraining($row['training_id']);
        }

        return $retVal;
    }
	
    /**
     * @param TrainingTraining $parentObject
     * @return array
     */
    public static function getInstances(TrainingTraining $parentObject)
    {
        $retVal = array();

        $sql = 'SELECT training_id FROM training_training WHERE parent_id = '.$parentObject->getId();
        $rows = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmIndex);

        foreach ($rows as $row)
            $retVal[] = new TrainingTraining($row[0]);

        return $retVal;
    }
    /**
     * @param TrainingTraining $parentObject
     * @return bool|TrainingTraining
     */
    public static function createNewInstance(TrainingTraining $parentObject)
    {
        $retVal = false;

        $dbtr = new DBTransaction();

        if ($parentObject->getId()>0 and $parentObject->getDBField('parent_id')==0)
        {
            $newTrainingObject = new TrainingTraining($parentObject->getId());
            $newTrainingObject->setDBField('parent_id',$parentObject->getId());
            $newTrainingObject->setId(0);
            $newTrainingObject->save();//create clone and set parent id

            if ($newTrainingObject->getId()>0)
            {
                $trainingSlideShowObjects = TrainingSlideShow::getObjectsByTrainingId($parentObject->getId());
                /**
                 * @var $trainingSlideShowObjects TrainingSlideShow[]
                 */
                foreach ($trainingSlideShowObjects as $trainingSlideShowObject)
                {
                    $newTrainingSlideShowObject = new TrainingSlideShow($trainingSlideShowObject->getId());
                    $newTrainingSlideShowObject->setId(0);
                    $newTrainingSlideShowObject->setDBField('training_id',$newTrainingObject->getId());
                    $newTrainingSlideShowObject->save();//create clone and modify training id
                }
            }

            $retVal = new TrainingTraining($newTrainingObject->getId()); //refresh object
        }

        $dbtr->destroy();

        return $retVal;
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
            $sql='
                UPDATE training_training
                SET training_id="'.(int)MySQL::filter($this->dbFields['training_id']).'",
                    traininggroups='.MySQL::filterQ($this->dbFields['traininggroups'],true).',
                    title="'.MySQL::filter($this->dbFields['title']).'",
                    description="'.MySQL::filter($this->dbFields['description']).'",
                    cover="'.MySQL::filter($this->dbFields['cover']).'",
                    authors="'.(int)MySQL::filter($this->dbFields['authors']).'",
                    attachment="'.MySQL::filter($this->dbFields['attachment']).'",
                    parent_id="'.(int)MySQL::filter($this->dbFields['parent_id']).'",
                    owner="'.(int)MySQL::filter($this->dbFields['owner']).'",
                    diskArea_id="'.(int)MySQL::filter($this->dbFields['diskArea_id']).'",
                    office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",
                    office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",
                    createdDate="'.MySQL::filter($this->dbFields['createdDate']).'",
                    updatedDate="'.MySQL::filter($this->dbFields['updatedDate']).'",
                    activeState="'.MySQL::filter($this->dbFields['activeState']).'",
                    credit="'.(int)MySQL::filter($this->dbFields['credit']).'",
                    startDate='.MySQL::filterQ($this->dbFields['startDate'],true).',
                    endDate='.MySQL::filterQ($this->dbFields['endDate'],true).'
                    WHERE training_id='.$this->id;

            ActionLogger::addToActionLog('trainingtraining.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='
                INSERT INTO training_training (
                    title,
                    description,
                    cover,
                    authors,
                    attachment,
                    owner,
                    parent_id,
                    diskArea_id,
                    office_id,
                    office_nametag,
                    createdDate,
                    updatedDate,
                    traininggroups,
                    activeState,
                    credit,
                    startDate,
                    endDate
                )
                VALUES(
                    "'.MySQL::filter    ($this->dbFields['title']).'",
                    "'.MySQL::filter($this->dbFields['description']).'",
                    "'.MySQL::filter($this->dbFields['cover']).'",
                    "'.(int)MySQL::filter($this->dbFields['authors']).'",
                    "'.MySQL::filter($this->dbFields['attachment']).'",
                    "'.(int)MySQL::filter($this->dbFields['owner']).'",
                    "'.(int)MySQL::filter($this->dbFields['parent_id']).'",
                    "'.(int)MySQL::filter($this->dbFields['diskArea_id']).'",
                    "'.(int)MySQL::filter($this->dbFields['office_id']).'",
                    "'.MySQL::filter($this->dbFields['office_nametag']).'",
                    NOW(),
                    NOW(),
                    '.MySQL::filterQ($this->dbFields['traininggroups'],true).',
                    "'.MySQL::filter($this->dbFields['activeState']).'",
                    "'.(int)MySQL::filter($this->dbFields['credit']).'",
                    '.MySQL::filterQ($this->dbFields['startDate'],true).',
                    '.MySQL::filterQ($this->dbFields['endDate'],true).'
                )';

            MySQL::runCommand($sql);
            $id = MySQL::getLastId();

            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('trainingtraining.save.insert',$this,'');

            return $ret;
        }
    }

    /**
     * @param $id (training_id,title,description,cover,authors,attachment,owner,diskArea_id,office_id,office_nametag,createdDate,updatedDate,traininggroups,activeState,credit,startDate,endDate)
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
     * @return int
     */
    public function getSlideShowCount()
    {
        if ($row = MySQL::fetchRecord(MySQL::executeQuery('SELECT COUNT(*) FROM training_slideshow WHERE training_id='.$this->getId()),MySQL::fmIndex))
            return (int)$row[0];
        return 0;
    }

    /**
     * Load object from db
     */
    private function load()
    {
        if ($row = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM training_training WHERE training_id='.(int)$this->id),MySQL::fmAssoc)) $this->dbFields = $row;
        else    throw new Exception("Entry not found!");
    }
}
?>