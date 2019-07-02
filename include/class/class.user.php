<?php
class User
{
    const MIN_PASSWORD_LENGTH = 5;

    const IMG_MALE="data:image/jpeg;base64,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";

    const IMG_FEMALE = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD//gA8Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBxdWFsaXR5ID0gMTAwCv/bAEMAAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/bAEMBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/AABEIADIAMgMBIgACEQEDEQH/xAAfAAABBQEBAQEBAQAAAAAAAAAAAQIDBAUGBwgJCgv/xAC1EAACAQMDAgQDBQUEBAAAAX0BAgMABBEFEiExQQYTUWEHInEUMoGRoQgjQrHBFVLR8CQzYnKCCQoWFxgZGiUmJygpKjQ1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4eLj5OXm5+jp6vHy8/T19vf4+fr/xAAfAQADAQEBAQEBAQEBAAAAAAAAAQIDBAUGBwgJCgv/xAC1EQACAQIEBAMEBwUEBAABAncAAQIDEQQFITEGEkFRB2FxEyIygQgUQpGhscEJIzNS8BVictEKFiQ04SXxFxgZGiYnKCkqNTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqCg4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2dri4+Tl5ufo6ery8/T19vf4+fr/2gAMAwEAAhEDEQA/APpg/wDB7V+xcfFkVkP2PP2nB4INyI5fEja58LB4mjtC+Dcp4PXxG+mySqnz/Zj41jBPy+eCAa/qP/Yf/b2/Zd/4KIfBiy+On7K/xHs/HXhGS6XTPEGlzQvpPjDwN4iECXM3hnxv4YumN9oOsxQyLNGkolsdQtWS+0q9v7F47lv8Niv0M/4Js/t2ftvfsNfHu21v9hbxPqVp8Uvi3BZfC3/hA4/D9v400f4i3XiDUrW38PaPc+C79ZbDWNcg1uS2bw3dCIX9jezSRW0wgvLyC4AP9uL5efxz0x78e23sPz5peBz6cevA4/Lvgc/jmvwG/wCCNa/8F3/EF74q8f8A/BV/Xfgnofw317wsY/Afwo0zwz4bsfjbovi5tR0ye21fW734eIngzR/DQ0ddWtbrRdS1TXvEE+ozWbmDR47WU3nq/wDwVW+B3/BZ/wCM194Gtv8Agl1+1j+zr+zZ4R0/w7dr8Qbf4n+HLu48e+IvFUmo3TW8+ieJZ/hX8XNFstAi0g2MKWKaJo2pR6it7cy6ndwTW1vagH4nf8Hev/BU3xV+z18KPhz+wJ8CfGureEviV8d9Ob4g/GvXfDOoz6drOjfBS1utS0bRfB6ajYzw3mnSfEXxLZ3dxqPkSR3D+HfC1xYTAWPiAiX/AD5fgx+1l+1B+zpPdXHwF/aF+M/wde+Wdb1Phx8SPFvhC3vBcgidrq00TVbO1uJJATullheTkkOCTXsn/BSXXv2p9V/bU+O+g/tofGW3+PX7Rfw18X3Xwq+IPxH07Vf7Y0PUtQ8AAaB9g8O3a6B4Wi/sXSWtpLK2jg8O6TH5sVxJ9l8yR5ZfhmgD7Cl/4KFft5TyyTS/tn/tSvLNI8srn48/E7LySMXdjjxMBlmJJwAMmivj2igAr6S/Y3+LPhH4C/tZ/s1fGzx9o93r/gn4TfHP4XfETxZo1gzLfah4f8IeMtH1zVYLIq8Ra7+x2Ur20fmRiWZUjLoHLD5tr9Av+CU3wWH7Q/8AwUg/Yt+Dsk3heO28Y/tA+AU1CLxnawX3hvUdK0LVE8Tavomo2F14c8WWWoNr+maNd6JY6fqPh+/0y+1HULS01M2dhNc31sAf7Yvg/wAWeG/H3hTwx458HaxZeIfCXjLQNH8UeGdd02YTWGs6Br9hb6po+qWUwXElrfWF1b3ML4y0cq5AOQNbUdQ0/SNPv9W1W9ttO0zS7O51HUdQvZ47aysbCyhe5vLy7uZmSK3tbW2iknnnlZY4okeR2VVJo0vS9O0TTrDRtG06y0nSNKs7bTtL0vTLSKw07TdPs4UtrOwsLK0jhtrSztLeKKC2treKOCCGNIokRFChNV0rT9d0zUdF1mwtNV0jV7G70vVdL1C2S8sNS03UIJLW+sL60nSSC6s7u1lkt7m3mR4p4ZHjkV1YigD/AA//APgpN8afhf8AtF/t8/tc/HL4LaK+g/Cz4ofHfx/4t8FWUj3DyXWj6lrU7LrrrcyyzW7+KLlLjxK1kXCWB1Y2UMcMNvHEnxHX9Jn/AAdL/sgfA/8AY8/4KR6Z4W/Z98HfDn4ZfD3x78CPAfjm3+Gfw502y0Ow8KamdS8R+HdTnv8ARNP0qxt7GbxBdaI+rQSyXup3N3HI8jtZWyWlqP5s6ACiiigD+33/AIM2/wDgn58NPjX49/aW/bM+MfgHwt8QdH+E0OhfBj4V6V4z0HTvEWi6f478TQW/ivxj4nttN1a1urBtd0PwxDoGlafdvG0lja+LNReJRNNDND/fN4c/ZB/ZR8H/ABGsvi/4R/Zp+A3hX4qabFcxaf8AEPw38JfAmheM7JbyJ7e7Np4i0vQbTVLeW4tpZbWeaK6E0ltLJbPKYXZW/ny/4IZ/sl/Eb/gj1+1N+1H/AMEyPiNd3vjT4WfGtX/a3/ZJ+OCWC2OleP8ARvDMHhn4b/FHwP4hgjU2+k/E7wxYz+BNS1PQ7e4uLa60i3udfsDHZXUcK/1L5x+PHGTzgYzken48AgckUALnoOf0BGO556HB5HHbvRxjqevXjOePQc8cjjp7YpOc4GQAQOvbA9eOw79+hyaXPoDj+fPJHc9fTB7e4B+fn7V//BK7/gnx+3J4/wDD/wAUv2rv2XvAfxn+IHhbw3F4Q0TxV4gu/Fen6hb+GbfUb/VbbRrj/hGvEWiW2qWVpqGqajdWkeqwXrWr3tytu0aSuh/iY/4Owf8AglZ+xD+xB8Ev2U/i/wDsi/ADw/8ABDWvGPxZ8V/DXxvD4P1HxRPpHiKwbwe3iTQDdaVrOuavY22pWNzpGpLBd2EFpdXkN1PHePcLb24i/wBGjIAySQOpY8DA656kY9zg8/wgCvwN8Afsgt/wUy/bVtf+Cgn7VWktrn7Ln7O+qal4S/4J3/ADXonk8O+I7rSdSig8S/tf+PtFkAttWu/G2vafcD4QaTqCXtgfAmneG/F08AnvdOFAH+bN4a/4IY/8Fa/F3hzQPFegfsJfHa80LxPouleIdFu5PDtvZSXWk61Ywalp1zJZX19bX1o89ncwyva3lvb3VuzGK4himR41K/2ic7flAwF4A+YYA4AwCAMDsAMelFAHmHjzTNNufG3wX1O50+xuNS0vxp4iXTNQntLeW+05b74ZeOIb0WF3JG09mLyFEiuhbyRi4iRY5t6qAPUl6j6f0WiigBx6fUjP5gfy4owPQflRRQBDKiSwSxyIskciMkkbqHSRHQK6OrAq6upKurAhgSCCCaq6bYWOlaZpumaZZWmnabp9nZ2Nhp9hbQ2djY2VpbpBa2dnaW6RwW1rbQRxw29vDGkUMSJHGioqqCigC+AMDgdB2FFFFAH/2Q==";

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
            $sql='UPDATE
                    user_u
                  SET
                    office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",
                    office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",
                    elotag="'.MySQL::filter($this->dbFields['elotag']).'",
                    vezeteknev="'.MySQL::filter($this->dbFields['vezeteknev']).'",
                    keresztnev="'.MySQL::filter($this->dbFields['keresztnev']).'",
                    full_name="'.MySQL::filter($this->dbFields['full_name']).'",
                    user_name="'.MySQL::filter($this->dbFields['user_name']).'",
                    user_email="'.MySQL::filter($this->dbFields['user_email']).'",
                    userlevel="'.(int)MySQL::filter($this->dbFields['userlevel']).'",
                    parent_id="'.(int)MySQL::filter($this->dbFields['parent_id']).'",
                    pwd="'.MySQL::filter($this->dbFields['pwd']).'",
                    office_name="'.MySQL::filter($this->dbFields['office_name']).'",
                    user_tel="'.MySQL::filter($this->dbFields['user_tel']).'",
                    department="'.(int)MySQL::filter($this->dbFields['department']).'",
                    position="'.MySQL::filter($this->dbFields['position']).'",
                    birthDate='.MySQL::filterQ($this->dbFields['birthDate'],true).',
                    gender="'.MySQL::filter($this->dbFields['gender']).'",
                    language="'.MySQL::filter($this->dbFields['language']).'",
                    schools="'.MySQL::filter($this->dbFields['schools']).'",
                    description="'.MySQL::filter($this->dbFields['description']).'",
                    cv="'.MySQL::filter($this->dbFields['cv']).'",
                    skills="'.MySQL::filter($this->dbFields['skills']).'",
                    profilePicture="'.MySQL::filter($this->dbFields['profilePicture']).'",
                    pemail="'.MySQL::filter($this->dbFields['pemail']).'",
                    users_ip="'.MySQL::filter($this->dbFields['users_ip']).'",
                    approved="'.(int)MySQL::filter($this->dbFields['approved']).'",
                    activation_code="'.MySQL::filter($this->dbFields['activation_code']).'",
                    activation_time='.MySQL::filterQ($this->dbFields['activation_time'],true).',
                    activatedBy="'.(int)MySQL::filter($this->dbFields['activatedBy']).'",
                    activeState="'.(int)MySQL::filter($this->dbFields['activeState']).'",
                    banned="'.(int)MySQL::filter($this->dbFields['banned']).'",
                    ckey="'.MySQL::filter($this->dbFields['ckey']).'",
                    ctime="'.MySQL::filter($this->dbFields['ctime']).'",
                    depth="'.(int)MySQL::filter($this->dbFields['depth']).'",
                    lft="'.(int)MySQL::filter($this->dbFields['lft']).'",
                    rgt="'.(int)MySQL::filter($this->dbFields['rgt']).'",
                    loginattempt="'.(int)MySQL::filter($this->dbFields['loginattempt']).'",
                    createdDate="'.MySQL::filter($this->dbFields['createdDate']).'",
                    updatedDate="'.MySQL::filter($this->dbFields['updatedDate']).'",
                    cryptedText="'.MySQL::filter($this->dbFields['cryptedText']).'",
                    deleted="'.(int)MySQL::filter($this->dbFields['deleted']).'"
                  WHERE
                    u_id='.$this->id;

            ActionLogger::addToActionLog('user.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO user_u (user_type,office_id,office_nametag,elotag,vezeteknev,keresztnev,full_name,user_name,user_email,userlevel,parent_id,pwd,office_name,user_tel,department,position,birthDate,gender,language,schools,description,cv,skills,profilePicture,pemail,users_ip,approved,activation_code,activation_time,activatedBy,activeState,banned,ckey,ctime,depth,lft,rgt,loginattempt,createdDate,updatedDate,cryptedText,deleted) VALUES(
"'.MySQL::filter($this->dbFields['user_type']).'","'.(int)MySQL::filter($this->dbFields['office_id']).'","'.MySQL::filter($this->dbFields['office_nametag']).'","'.MySQL::filter($this->dbFields['elotag']).'","'.MySQL::filter($this->dbFields['vezeteknev']).'","'.MySQL::filter($this->dbFields['keresztnev']).'","'.MySQL::filter($this->dbFields['full_name']).'","'.MySQL::filter($this->dbFields['user_name']).'","'.MySQL::filter($this->dbFields['user_email']).'","'.(int)MySQL::filter($this->dbFields['userlevel']).'","'.(int)MySQL::filter($this->dbFields['parent_id']).'","'.MySQL::filter($this->dbFields['pwd']).'","'.MySQL::filter($this->dbFields['office_name']).'","'.MySQL::filter($this->dbFields['user_tel']).'","'.(int)MySQL::filter($this->dbFields['department']).'","'.MySQL::filter($this->dbFields['position']).'",'.MySQL::filterQ($this->dbFields['birthDate'],true).',"'.MySQL::filter($this->dbFields['gender']).'","'.MySQL::filter($this->dbFields['language']).'","'.MySQL::filter($this->dbFields['schools']).'","'.MySQL::filter($this->dbFields['description']).'","'.MySQL::filter($this->dbFields['cv']).'","'.MySQL::filter($this->dbFields['skills']).'","'.MySQL::filter($this->dbFields['profilePicture']).'","'.MySQL::filter($this->dbFields['pemail']).'","'.MySQL::filter($this->dbFields['users_ip']).'","'.(int)MySQL::filter($this->dbFields['approved']).'","'.MySQL::filter($this->dbFields['activation_code']).'",'.MySQL::filterQ($this->dbFields['activation_time'],true).',"'.(int)MySQL::filter($this->dbFields['activatedBy']).'","'.(int)MySQL::filter($this->dbFields['activeState']).'","'.(int)MySQL::filter($this->dbFields['banned']).'","'.MySQL::filter($this->dbFields['ckey']).'","'.MySQL::filter($this->dbFields['ctime']).'","'.(int)MySQL::filter($this->dbFields['depth']).'","'.(int)MySQL::filter($this->dbFields['lft']).'","'.(int)MySQL::filter($this->dbFields['rgt']).'","'.(int)MySQL::filter($this->dbFields['loginattempt']).'",NOW(),NOW(),"'.MySQL::filter($this->dbFields['cryptedText']).'","'.(int)MySQL::filter($this->dbFields['deleted']).'")';

            MySQL::runCommand($sql);
            $id = MySQL::getLastId();

            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('user.save.insert',$this,'');

            return $ret;
        }
    }

    /**
     * @param $newPassword
     * @return bool
     */
    public function changePassword($newPassword)
    {
        $retVal = false;
        if (strlen($newPassword)>=User::MIN_PASSWORD_LENGTH)
        {
            ActionLogger::addToActionLog('user.changepassword',$this,'',array());

            $this->setDBField('pwd',HashPassword($newPassword));
            $retVal = true;
        }
        return $retVal;
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $dbtr = new DBTransaction();

        ActionLogger::addToActionLog('user.remove',$this,'userid:'.$this->getId().',email:'.$this->getDbField('user_email'),array());

        MySQL::runCommand('DELETE FROM training_results WHERE u_id='.$this->getId());
        MySQL::runCommand('DELETE FROM user_u WHERE u_id='.$this->getId());
        MySQL::runCommand('DELETE FROM training_slideshow_score WHERE u_id='.$this->getId());
        MySQL::runCommand('DELETE FROM user_traininggroupusers WHERE u_id='.$this->getId());

        User::globalSkillUserRemove($this->getDBField('user_email'));

        $dbtr->destroy();

        return true;
    }

    /**
     * @param $id (u_id,user_type,office_id,office_nametag,elotag,vezeteknev,keresztnev,full_name,user_name,user_email,userlevel,parent_id,pwd,office_name,user_tel,department,position,birthDate,gender,language,schools,description,cv,skills,profilePicture,users_ip,approved,activation_code,activation_time,activatedBy,activeState,banned,ckey,ctime,depth,lft,rgt,loginattempt,createdDate,updatedDate,cryptedText,deleted)
     * @param $value
     */
    public function setDBField($id,$value)
    {
        $this->dbFields[$id] = $value;
    }

    /**
     * @param $email
     * @return bool|User
     */
    public static function getUserObjectByEmail($email)
    {
        if ($row = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM user_u WHERE user_email="'.MySQL::filter($email).'" LIMIT 1'),MySQL::fmAssoc))
        {
            return new User($row['u_id']);
        }
        return false;
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
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM user_u WHERE u_id='.(int)$this->id),MySQL::fmAssoc);
    }

    /**
     * @param $email
     * @param $database
     */
    public static function globalSkillUserAdd($email,$database)
    {
        if (strlen($email)>0 and strlen($database)>0)
        {
            $sql='INSERT IGNORE INTO '.substr(DB_PREFIX,0,-1).'glb.users (email,database_name) VALUES("'.MySQL::filter($email).'","'.MySQL::filter($database).'")';
            MySQL::runCommand($sql);
        }
    }

    /**
     * @param $email
     * @return string|null
     */
    public static function globalSkillDatabaseNameByEmail($email)
    {
        $retVal = null;
        $sql='SELECT database_name FROM '.substr(DB_PREFIX,0,-1).'glb.users WHERE email="'.MySQL::filter($email).'" LIMIT 1';
        if ($row = MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc))
            $retVal = $row['database_name'];
        return $retVal;
    }

    /**
     * @param $email
     */
    public static function globalSkillUserRemove($email)
    {
        $sql='DELETE FROM '.substr(DB_PREFIX,0,-1).'glb.users WHERE email="'.MySQL::filter($email).'" LIMIT 1';
        MySQL::runCommand($sql);
    }
}
?>