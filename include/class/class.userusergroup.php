<?php
class UserUserGroup
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
            $sql='UPDATE user_usergroup SET 
usergroup_id="'.(int)MySQL::filter($this->dbFields['usergroup_id']).'",office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",name="'.MySQL::filter($this->dbFields['name']).'",doname="'.MySQL::filter($this->dbFields['doname']).'",owner="'.(int)MySQL::filter($this->dbFields['owner']).'" WHERE usergroup_id='.$this->id;

            ActionLogger::addToActionLog('usergroup.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO user_usergroup (office_id,office_nametag,name,doname,owner) VALUES(
"'.(int)MySQL::filter($this->dbFields['office_id']).'","'.MySQL::filter($this->dbFields['office_nametag']).'","'.MySQL::filter($this->dbFields['name']).'","'.MySQL::filter($this->dbFields['doname']).'","'.(int)MySQL::filter($this->dbFields['owner']).'")';

            MySQL::runCommand($sql);

            $id = MySQL::getLastId();

            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('usergroup.save.insert',$this,'');

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
        $dbtr = new DBTransaction();

        $sql = 'UPDATE user_u SET department=0 WHERE department = '. (int)$this->getId();
        $res1 = MySQL::runCommand($sql);

        $sql = 'DELETE FROM user_usergroup WHERE user_usergroup.usergroup_id = ' . (int)$this->getId();
        $res2 = MySQL::runCommand($sql);

        if ($res1 !== null and $res2 !== null)
        {
            $dbtr->destroy();
            return true;
        }
        else
            return false;
    }



    /**
     * @param $id (usergroup_id,office_id,office_nametag,name,doname,owner)
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
     * @param $groupId (department id)
     * @param $userId
     * @return bool
     */
    public static function setUserGroup($groupId,$userId)
    {
        ActionLogger::addToActionLog('usermanager.setusergroup',null,'grpid:'.$groupId.',userid:'.$userId);
        $user = new User($userId);
        $user->setDBField('department',$groupId);
        return $user->save();
    }

    /**
     * @param $officeId
     * @param $officeNameTag
     * @return array
     */
    public static function getGroupsWithMembersWeb($officeId,$officeNameTag)
    {
        $returnData = array();

        $sql = " SELECT COUNT(u_id) AS badge FROM user_u WHERE deleted=0 AND isvisible =1 AND office_id = ".MySQL::filter($officeId)." AND office_nametag = '".MySQL::filter($officeNameTag)."'";

        $query = MySQL::query($sql, false, false);

        $organized = 0;

        $mediaBoxesArray[0] = array(
            "id" => 'all',
            "name" => 'View all',
            "doname" => 'viewall',
            "badge" => $query[0]['badge']
        );

        if(!empty($query))
        {
            //collect usergroups and membercount
            $sql = 'SELECT usergroup_id,name,doname,COUNT(u_id) AS badge FROM user_usergroup LEFT JOIN user_u ON user_u.department = user_usergroup.usergroup_id AND user_u.deleted=0 GROUP BY usergroup_id,name,doname ORDER BY name';

            foreach ($result2=MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $row)
                $organized = $organized + intval($row['badge']);

            //ha van usergroup
            if(!empty($result2))
            {
                //not in list count
                $mediaBoxesArray[1] = array(
                    "id" => 'notInList',
                    "name" => 'Unorganized',
                    "doname" => 'unorganized',
                    "badge" => $query[0]['badge']-$organized
                );

                //collect return list
                foreach ($result2 as $row)
                {
                    $mediaBoxesArray[] = array(
                        'id' => $row['usergroup_id'],
                        'name' => $row['name'],
                        'doname' => $row['usergroup_id'],
                        'badge' => $row['badge']
                    );
                }

            }
            /*
            else
            {
                $mediaBoxesArray[1] = array(
                    "id" => 'notInList',
                    "name" => 'Unorganized',
                    "doname" => 'unorganized',
                    "badge" => $query[0]['badge']
                );
                $returnData['result'] = $mediaBoxesArray;
            }
            */
            $returnData['result'] = $mediaBoxesArray;
        }
        else
        {
            $returnData = array('error' => 'No users!');
        }

        return $returnData;
    }

    /**
     * @param $groupName *required
     * @param $ownerUserId
     * @param $officeId
     * @param $officeNameTag
     * @return bool|UserUserGroup
     */
    public static function helperCreateNewUserGroup($groupName,$ownerUserId,$officeId,$officeNameTag)
    {
        $retVal = false;

        if (strlen($groupName=trim($groupName))>0)
        {
            $obj = new UserUserGroup(null);
            $obj->setDBField('office_id',(int)$officeId);
            $obj->setDBField('office_nametag',$officeNameTag);
            $obj->setDBField('name',$groupName);
            $obj->setDBField('doname',normalize_special_characters(str_replace(' ', '', strtolower($groupName))));
            $obj->setDBField('owner',(int)$ownerUserId);
            if ($obj->save() and $obj->getId()>0)
                $retVal = new UserUserGroup($obj->getId());
        }

        return $retVal;
    }

    /**
     * @param $groupId
     * @param $groupName
     * @return bool|UserUserGroup
     */
    public static function helperRenameUserGroup($groupId,$groupName)
    {
        $retVal = false;

        if (strlen($groupName=trim($groupName))>0 and (int)$groupId>0)
        {
            $obj = new UserUserGroup((int)$groupId);
            $obj->setDBField('name',$groupName);
            $obj->setDBField('doname',normalize_special_characters(str_replace(' ', '', strtolower($groupName))));
            if ($obj->save() and $obj->getId()>0)
                $retVal = new UserUserGroup($obj->getId());
        }

        return $retVal;

    }

    /**
     * @param $groupName
     * @param $officeId
     * @param $officeNameTag
     * @return bool|UserUserGroup
     */
    public static function getGroupByName($groupName,$officeId,$officeNameTag)
    {
        $sql = 'SELECT usergroup_id FROM user_usergroup WHERE name="'.MySQL::filter($groupName).'" AND office_id='.(int)$officeId.' AND office_nametag="'.MySQL::filter($officeNameTag).'" LIMIT 1';

        if ($row=MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc))
        {
            return new UserUserGroup($row['usergroup_id']);
        }
        else
            return false;

    }

    /**
     * @param $groupName
     * @param $officeId
     * @param $officeNameTag
     * @param $owner
     * @return bool|UserUserGroup
     */
    public static function createNewOrLoadExistingGroupByName($groupName,$officeId,$officeNameTag,$owner)
    {
        $res = self::getGroupByName($groupName,$officeId,$officeNameTag);

        if (!($res instanceof UserUserGroup))
        {
            $res1 = self::helperCreateNewUserGroup($groupName,$owner,$officeId,$officeNameTag);

            if ($res1 instanceof UserUserGroup)
            {
                return $res1;
            }
            else
                return false;
        }
        else
            return $res;
    }

    /**
     * @param $groupId
     * @return mixed|string
     */
    public static function helperGetGroupNameByGroupId($groupId)
    {
        $retVal = '';

        if ($groupId>0)
        {
            $obj = new UserUserGroup((int)$groupId);
            $retVal=$obj->getDBField('name');
        }

        return $retVal;
    }


    /**
     * Load object from db
     */
    private function load()
    {
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM user_usergroup WHERE usergroup_id='.(int)$this->id),MySQL::fmAssoc);
    }
}
?>