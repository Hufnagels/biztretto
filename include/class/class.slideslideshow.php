<?php
class SlideSlideShow
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
            $sql='UPDATE slide_slideshow SET 
slideshow_id="'.(int)MySQL::filter($this->dbFields['slideshow_id']).'",description="'.MySQL::filter($this->dbFields['description']).'",diskArea_id="'.(int)MySQL::filter($this->dbFields['diskArea_id']).'",mediabox_id="'.(int)MySQL::filter($this->dbFields['mediabox_id']).'",attachment="'.MySQL::filter($this->dbFields['attachment']).'",office_id="'.(int)MySQL::filter($this->dbFields['office_id']).'",office_nametag="'.MySQL::filter($this->dbFields['office_nametag']).'",name="'.MySQL::filter($this->dbFields['name']).'",cover="'.MySQL::filter($this->dbFields['cover']).'",owner="'.(int)MySQL::filter($this->dbFields['owner']).'",createdDate="'.MySQL::filter($this->dbFields['createdDate']).'",updatedDate="'.MySQL::filter($this->dbFields['updatedDate']).'",isEnabled="'.(int)MySQL::filter($this->dbFields['isEnabled']).'",templateSlideCount="'.(int)MySQL::filter($this->dbFields['templateSlideCount']).'" WHERE slideshow_id='.$this->id;

            ActionLogger::addToActionLog('slideslideshow.save.update',$this,'');

            return (MySQL::runCommand($sql)!==null);
        }
        else
        {
            //create
            $sql='INSERT INTO slide_slideshow (description,diskArea_id,mediabox_id,attachment,office_id,office_nametag,name,cover,owner,createdDate,updatedDate,isEnabled,templateSlideCount) VALUES(
"'.MySQL::filter($this->dbFields['description']).'","'.(int)MySQL::filter($this->dbFields['diskArea_id']).'","'.(int)MySQL::filter($this->dbFields['mediabox_id']).'","'.MySQL::filter($this->dbFields['attachment']).'","'.(int)MySQL::filter($this->dbFields['office_id']).'","'.MySQL::filter($this->dbFields['office_nametag']).'","'.MySQL::filter($this->dbFields['name']).'","'.MySQL::filter($this->dbFields['cover']).'","'.(int)MySQL::filter($this->dbFields['owner']).'",NOW(),NOW(),"'.(int)MySQL::filter($this->dbFields['isEnabled']).'","'.(int)MySQL::filter($this->dbFields['templateSlideCount']).'")';
            MySQL::runCommand($sql);
            $id = MySQL::getLastId();
            $ret = ($this->id = $id)>0;

            ActionLogger::addToActionLog('slideslideshow.save.insert',$this,'');

            return $ret;
        }
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $sql = 'DELETE FROM slide_slideshow WHERE slideshow_id="'.(int)MySQL::filter($this->dbFields['slideshow_id']).'"';

        if(MySQL::runCommand($sql)!==null) {
            ActionLogger::addToActionLog('slideslideshow.remove',$this,'');
            $sql = 'DELETE FROM slide_slides WHERE slideshow_id="'.(int)MySQL::filter($this->dbFields['slideshow_id']).'"';
            return (MySQL::runCommand($sql)!==null);
        } else
            return;





        /*
        $sql='';
        $rows = MySQL::runCommand($sql);
        return ($rows > 0)?true:false;
        */
    }

    /**
     * @param $id (slideshow_id,description,diskArea_id,mediabox_id,attachment,office_id,office_nametag,name,cover,owner,createdDate,updatedDate,isEnabled,templateSlideCount)
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
     * @return array
     */
    public function getSlideObjects()
    {
        $retVal = array();

        $sql = 'SELECT * FROM slide_slides WHERE slideshow_id='.(int)$this->getId().' ORDER BY badge ASC';

        foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $row)
        {
            $retVal[] = new SlideSlides($row['slides_id']);
        }

        return $retVal;
    }


    /**
     * @param SlideSlideShow $sourceSlideShowObject
     * @param $owner
     * @return bool|SlideSlideShow
     */
    public static function duplicate(SlideSlideShow $sourceSlideShowObject,$owner)
    {
        //slideshow objektum letrehozasa a forras alaojan
        //slideshow slides-ok lekerdezese majd letrehozasa ujbol

        $dbtr = new DBTransaction();

        $slideSlideShowObject = new SlideSlideShow(null);

        foreach ($sourceSlideShowObject->getDBFields() as $fieldName => $fieldValue)
            $slideSlideShowObject->setDBField($fieldName,$fieldValue);

        $slideSlideShowObject->setDBField('owner',(int)$owner);

        if ($slideSlideShowObject->save())
        {
            $name = $slideSlideShowObject->getDBField('name');
            if (($diskAreaId=$slideSlideShowObject->getDBField('diskArea_id'))>0)
            {
                $mediaBoxObject = new MediaMediaBox(null);
                $mediaBoxObject->setDBField('diskArea_id',$diskAreaId);
                $mediaBoxObject->setDBField('office_id',$slideSlideShowObject->getDBField('office_id'));
                $mediaBoxObject->setDBField('office_nametag',$slideSlideShowObject->getDBField('office_nametag'));
                $mediaBoxObject->setDBField('name',$name?$name:$slideSlideShowObject->getId().'. slideshow');
                $mediaBoxObject->setDBField('owner',$owner);
                $mediaBoxObject->save();

                if ($mediaBoxObject->getId()>0)
                {
                    $tmpMediaBoxObject = new MediaMediaBox($sourceSlideShowObject->getDBField('mediabox_id'));
                    foreach ($tmpMediaBoxObject->getMediaBoxFiles() as $tmpMediaBoxFileObject)
                    {
                        $mediaMediaBoxFilesObject = new MediaMediaBoxFiles(null);
                        /**
                         * @var $tmpMediaBoxFileObject MediaMediaBoxFiles
                         */
                        foreach ($tmpMediaBoxFileObject->getDBFields() as $fieldName=>$fieldValue)
                            $mediaMediaBoxFilesObject->setDBField($fieldName,$fieldValue);
                        $mediaMediaBoxFilesObject->setDBField('owner',$owner);
                        $mediaMediaBoxFilesObject->setDBField('mediabox_id',$mediaBoxObject->getId());
                        $mediaMediaBoxFilesObject->save();
                    }

                    MediaMediaBox::connectSlideShowToMediaBox($slideSlideShowObject->getId(),$mediaBoxObject->getId());
                }
            }

            $slideSlideShowObject = new SlideSlideShow($slideSlideShowObject->getId()); //refresh

            /**
             * @var $oldSlideObject SlideSlides
             */
            foreach ($sourceSlideShowObject->getSlideObjects() as $oldSlideObject)
            {
                $slideSlidesObject = new SlideSlides(null);

                foreach ($oldSlideObject->getDBFields() as $fieldName => $fieldValue)
                    $slideSlidesObject->setDBField($fieldName,$fieldValue);

                $slideSlidesObject->setDBField('slideshow_id',$slideSlideShowObject->getId());
                usleep(10);$slideSlidesObject->setDBField('id',round(microtime(true)*1000));
                $slideSlidesObject->setDBField('owner',(int)$owner);

                $slideSlidesObject->save();
            }
        }

        ActionLogger::addToActionLog('slideshow.duplicate',$slideSlideShowObject,'src:'.$sourceSlideShowObject->getId().';dest:'.$slideSlideShowObject->getId());

        $dbtr->destroy();

        return $slideSlideShowObject->getId()>0?$slideSlideShowObject:false;
    }

    /**
     * @param $newName
     * @return bool
     */
    public function rename($newName)
    {
        if (strlen($newName)>0 and $this->getId()>0)
        {
            $this->setDBField('name',$newName);
            if ($this->save())
            {
                if ($this->getDBField('mediabox_id')>0)
                {
                    $mediaBoxObject = new MediaMediaBox($this->getDBField('mediabox_id'));
                    $mediaBoxObject->setDBField('name',$newName);
                    if ($mediaBoxObject->save())
                        return true;
                }
                else
                    return true;
            }
            else
                return false;
        }
        else
            return false;
    }

    /**
     * Load object from db
     */
    private function load()
    {
        $this->dbFields = MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM slide_slideshow WHERE slideshow_id='.(int)$this->id),MySQL::fmAssoc);
    }
}
?>