<?php
class SlideSlides
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

        if ((int)$this->dbFields['slideLevel']<1)   $this->dbFields['slideLevel'] = 1;

        if ($this->id>0)
        {
            $sql='UPDATE slide_slides SET 
slides_id="'.(int)MySQL::filter($this->dbFields['slides_id']).'",
slideshow_id="'.(int)MySQL::filter($this->dbFields['slideshow_id']).'",
office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",
office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",
owner="'.(int)MySQL::filter($this->dbFields['owner']).'",
id="'.MySQL::filter($this->dbFields['id']).'",
type="'.MySQL::filter($this->dbFields['type']).'",
templateType="'.MySQL::filter($this->dbFields['templateType']).'",
slideItems="'.MySQL::filter($this->dbFields['slideItems']).'",
html="'.MySQL::filter($this->dbFields['html']).'",
htmlForSlideshow="'.MySQL::filter($this->dbFields['htmlForSlideshow']).'",
tag="'.MySQL::filter($this->dbFields['tag']).'",
badge="'.(int)MySQL::filter($this->dbFields['badge']).'",
slideLevel="'.(int)MySQL::filter($this->dbFields['slideLevel']).'",
templateOption="'.MySQL::filter($this->dbFields['templateOption']).'",
answare="'.MySQL::filter($this->dbFields['answare']).'",
description="'.MySQL::filter($this->dbFields['description']).'",
parent_id="'.(int)MySQL::filter($this->dbFields['parent_id']).'",
lft="'.(int)MySQL::filter($this->dbFields['lft']).'",
rgt="'.(int)MySQL::filter($this->dbFields['rgt']).'",
depth="'.(int)MySQL::filter($this->dbFields['depth']).'",
transform="'.MySQL::filter($this->dbFields['transform']).'",
createdDate="'.MySQL::filter($this->dbFields['createdDate']).'",
updatedDate="'.MySQL::filter($this->dbFields['updatedDate']).'",
missingContent="'.MySQL::filter($this->dbFields['missingContent']).'",
background="'.MySQL::filter($this->dbFields['background']).'"
WHERE slides_id='.$this->id;

            ActionLogger::addToActionLog('slideslides.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO slide_slides
            (slideshow_id,office_id,office_nametag,owner,id,type,templateType,slideItems,html,htmlForSlideshow,
            tag,badge,slideLevel,templateOption,answare,description,parent_id,lft,rgt,depth,
            transform,createdDate,updatedDate,missingContent,background)
            VALUES(
"'.(int)MySQL::filter($this->dbFields['slideshow_id']).'",
"'.(int)MySQL::filter($this->dbFields['office_id']).'",
"'.MySQL::filter($this->dbFields['office_nametag']).'",
"'.(int)MySQL::filter($this->dbFields['owner']).'",
"'.MySQL::filter($this->dbFields['id']).'",
"'.MySQL::filter($this->dbFields['type']).'",
"'.MySQL::filter($this->dbFields['templateType']).'",
"'.MySQL::filter($this->dbFields['slideItems']).'",
"'.MySQL::filter($this->dbFields['html']).'",
"'.MySQL::filter($this->dbFields['htmlForSlideshow']).'",
"'.MySQL::filter($this->dbFields['tag']).'",
"'.(int)MySQL::filter($this->dbFields['badge']).'",
"'.(int)MySQL::filter($this->dbFields['slideLevel']).'",
"'.MySQL::filter($this->dbFields['templateOption']).'",
"'.MySQL::filter($this->dbFields['answare']).'",
"'.MySQL::filter($this->dbFields['description']).'",
"'.(int)MySQL::filter($this->dbFields['parent_id']).'",
"'.(int)MySQL::filter($this->dbFields['lft']).'",
"'.(int)MySQL::filter($this->dbFields['rgt']).'",
"'.(int)MySQL::filter($this->dbFields['depth']).'",
"'.MySQL::filter($this->dbFields['transform']).'",NOW(),NOW(),
"'.MySQL::filter($this->dbFields['missingContent']).'",
"'.MySQL::filter($this->dbFields['background']).'")';
            MySQL::runCommand($sql);
            $id = MySQL::getLastId();

            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('slideslides.save.insert',$this,'');

            return $ret;
        }
    }

    /**
     * @param $id (slides_id,slideshow_id,office_id,office_nametag,owner,id,type,templateType,slideItems,html,htmlForSlideshow,tag,badge,slideLevel,templateOption,answare,description,parent_id,lft,rgt,depth,transform,createdDate,updatedDate,missingContent)
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
     * Remove object
     *
     * @return bool
     */
    public function remove()
    {
        ActionLogger::addToActionLog('slideslides.remove',$this,'');

        $sqlDelete = "DELETE FROM slide_slides
                                WHERE slides_id = " . (int)$this->getId();
        $rows = MySQL::runCommand($sqlDelete);

        return ($rows > 0)?true:false;
    }


    /**
     * Load object from db
     */
    private function load()
    {
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM slide_slides WHERE slides_id='.(int)$this->id),MySQL::fmAssoc);
    }

    /**
     * @param $toArray
     * @return bool
     */
    public static function toArray($toArray)
    {
        $updateResult = false;
        $leftRow=$rightRow=$depthRow=$parentRow=$badgeRow=$idRow=array();

        if (isset($toArray) && count($toArray) > 0) {
            foreach ($toArray as $row) {
                if ($row['parent_id'] == 'none') continue;
                $leftRow[] = " WHEN {$row['slides_id']} THEN {$row['lft']}";
                $rightRow[] = " WHEN {$row['slides_id']} THEN {$row['rgt']}";
                $depthRow[] = " WHEN {$row['slides_id']} THEN {$row['depth']}";
                $parentRow[] = " WHEN {$row['slides_id']} THEN " . ($row['parent_id'] ? $row['parent_id'] : 0);
                $badgeRow[] = " WHEN {$row['slides_id']} THEN {$row['badge']}";
                $idRow[] = $row['slides_id'];
            }

            $sqlRow[] = "lft = CASE slides_id " . implode(' ', $leftRow) . " END ";
            $sqlRow[] = "rgt = CASE slides_id " . implode(' ', $rightRow) . " END ";
            $sqlRow[] = "depth = CASE slides_id " . implode(' ', $depthRow) . " END ";
            $sqlRow[] = "parent_id = CASE slides_id " . implode(' ', $parentRow) . " END ";
            $sqlRow[] = "badge = CASE slides_id " . implode(' ', $badgeRow) . " END ";
            $sqlToArray = "UPDATE slide_slides SET " . implode(',', $sqlRow) . " WHERE slides_id IN (" . implode(',', $idRow) . ")";

            $updateResult = MySQL::execute($sqlToArray);
        }
        return $updateResult;
    }
}
?>