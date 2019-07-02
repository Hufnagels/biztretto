<?php
class UserTrainingGroup
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
            $sql='UPDATE user_traininggroup SET 
                traininggroup_id="'.(int)MySQL::filter($this->dbFields['traininggroup_id']).'",
                office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",
                office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",
                name="'.MySQL::filter($this->dbFields['name']).'",
                doname="'.MySQL::filter($this->dbFields['doname']).'",
                owner="'.(int)MySQL::filter($this->dbFields['owner']).'"
                WHERE traininggroup_id='.$this->id;

            ActionLogger::addToActionLog('usertranininggroup.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO user_traininggroup (office_id,office_nametag,name,doname,owner)
            VALUES(
                "'.(int)MySQL::filter($this->dbFields['office_id']).'",
                "'.MySQL::filter($this->dbFields['office_nametag']).'",
                "'.MySQL::filter($this->dbFields['name']).'",
                "'.MySQL::filter($this->dbFields['doname']).'",
                "'.(int)MySQL::filter($this->dbFields['owner']).'")';

            MySQL::runCommand($sql);

            $id = MySQL::getLastId();

            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('usertranininggroup.save.insert',$this,'');

            return $ret;
        }
    }

    /**
     * Remove group
     *
     * @return bool
     */
    public function remove()
    {
        $sql = "DELETE FROM user_traininggroup, user_traininggroupusers USING user_traininggroup LEFT JOIN user_traininggroupusers on (user_traininggroupusers.traininggroup_id = user_traininggroup.traininggroup_id ) WHERE user_traininggroup.traininggroup_id = " . $this->getId();
        $res1 = MySQL::runCommand($sql);

        $sql = "DELETE FROM user_traininggroup WHERE user_traininggroup.traininggroup_id = " . $this->getId();
        $res2 = MySQL::runCommand($sql);

        return $res1!==null and $res2!==null;
    }

    /**
     * @param $id (traininggroup_id,office_id,office_nametag,name,doname,owner)
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
     * @param $groupId
     * @param $userId
     * @param $ownerId
     * @param $officeId
     * @param $officeNameTag
     * @return bool (only if sql error)
     */
    public static function addUserToGroup($groupId,$userId,$ownerId,$officeId,$officeNameTag)
    {
        ActionLogger::addToActionLog('usermanager.addusertogroup',null,'grpid:'.$groupId.',userid:'.$userId);

        $sql='INSERT IGNORE INTO user_traininggroupusers (u_id,traininggroup_id,office_id,office_nametag,owner) VALUES("'.(int)$userId.'","'.(int)$groupId.'","'.(int)$officeId.'","'.MySQL::filter($officeNameTag).'","'.(int)$ownerId.'")';
        return is_numeric(MySQL::runCommand($sql));
    }


    /**
     * @param $groupId
     * @param $userId
     * @param $officeId
     * @param $officeNameTag
     * @return bool
     */
    public static function removeUserFromGroup($groupId,$userId,$officeId,$officeNameTag)
    {
        ActionLogger::addToActionLog('usermanager.removeuserfromgroup',null,'grpid:'.$groupId.',userid:'.$userId);

        $sql='DELETE FROM user_traininggroupusers WHERE office_id='.(int)$officeId.' AND office_nametag="'.MySQL::filter($officeNameTag).'" AND traininggroup_id = '.(int)$groupId.' AND u_id='.(int)$userId.' LIMIT 1';
        return is_numeric(MySQL::runCommand($sql));
    }

    /**
     * Get traininggroups
     * array(array(groupid,name,doname,members,badge),...)
     *
     * @param $officeId
     * @param $officeNameTag
     * @return array
     */
    public static function getGroupsWithMembers($officeId,$officeNameTag)
    {
        $sql='SELECT
          box.traininggroup_id AS groupid, box.name AS name, box.doname, GROUP_CONCAT(users.u_id) AS members, COUNT(users.u_id) AS badge
      FROM user_traininggroup box
      LEFT JOIN user_traininggroupusers users
      ON box.traininggroup_id = users.traininggroup_id
      WHERE box.office_id = ' . (int)$officeId . ' AND box.office_nametag = "' . MySQL::filter($officeNameTag) . '" GROUP BY groupid ORDER BY box.name ASC';

        return MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc);
    }

    /**
     * @param array $groupIds
     * @return array
     */
    public static function getGroupUserObjects(array $groupIds)
    {
        $retVal = array();$groupIds[]=-1;
        foreach (MySQL::resultArray(MySQL::executeQuery('SELECT u_id FROM user_traininggroupusers WHERE traininggroup_id IN ('.implode(',',$groupIds).')'),MySQL::fmIndex) as $row)
            $retVal[] = new User($row[0]);
        return $retVal;
    }

    /**
     * @param array $groupIds
     * @return mixed
     */
    public static function getGroupUserCount(array $groupIds)
    {
        $groupIds[]=-1;
        $row = MySQL::fetchRecord(MySQL::executeQuery('SELECT COUNT(u_id) FROM user_traininggroupusers WHERE traininggroup_id IN ('.implode(',',$groupIds).')'),MySQL::fmIndex);
        return $row[0];
    }

    /**
     * @param $groupName *required
     * @param $ownerUserId
     * @param $officeId
     * @param $officeNameTag
     * @return bool|UserTrainingGroup
     */
    public static function helperCreateNewUserTrainingGroup($groupName,$ownerUserId,$officeId,$officeNameTag)
    {
        $retVal = false;

        if (strlen($groupName=trim($groupName))>0)
        {
            $obj = new UserTrainingGroup(null);
            $obj->setDBField('office_id',(int)$officeId);
            $obj->setDBField('office_nametag',$officeNameTag);
            $obj->setDBField('name',$groupName);
            $obj->setDBField('doname',normalize_special_characters(str_replace(' ', '', strtolower($groupName))));
            $obj->setDBField('owner',(int)$ownerUserId);
            if ($obj->save() and $obj->getId()>0)
                $retVal = new UserTrainingGroup($obj->getId());
        }

        return $retVal;
    }

    /**
     * @param $groupId
     * @param $groupName
     * @return bool|UserTrainingGroup
     */
    public static function helperRenameUserTrainingGroup($groupId,$groupName)
    {
        $retVal = false;

        if (strlen($groupName=trim($groupName))>0 and (int)$groupId>0)
        {
            $obj = new UserTrainingGroup((int)$groupId);
            $obj->setDBField('name',$groupName);
            $obj->setDBField('doname',normalize_special_characters(str_replace(' ', '', strtolower($groupName))));
            if ($obj->save() and $obj->getId()>0)
                $retVal = new UserTrainingGroup($obj->getId());
        }

        return $retVal;

    }


    /**
     * Load object from db
     */
    private function load()
    {
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM user_traininggroup WHERE traininggroup_id='.(int)$this->id),MySQL::fmAssoc);
    }
}
?>