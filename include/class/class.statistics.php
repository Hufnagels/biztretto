<?php
    abstract class Statistics
    {
        /**
         * User IDhez tartozo statisztikai adatokkal ter vissza ($filters-el lehetoseg van egy adott treningre vonatkozo adatokat is elkerni)
         *
         * Mukodese:
         * 1. megkeresi azokat a treningeket, melyekhez a user hozzaferhet
         * 2. az osszegyujott trening listan vegiglepked, megkeresi a slideshowkat benne.
         * 3. slideshowkon vegiglepkedve megzeni hogy a score tablaban van e rekord, es kitolti a folyamatban, es elvegzett ertekeket
         * 4. kiszedi a score tablabol az utolso sort az adott slideshowhoz, ebben van benne a pontszam stb.
         * 5. a vegen osszesit, azaz ha a slideshowk mindegyike elvegzett, akkor a traininget is elvegzettre allitja
         * 6. meta adatokat is visszakuld
         *
         * @param $userId
         * @param array $filters
         * @return array
         */
        public static function getStatByUser($userId,array $filters=null)
        {
            $userId = (int)$userId;
            $trainingId = 0;

            if (is_array($filters))
            {
                if ($filters['training_id'])    $trainingId = (int)$filters['training_id'];
            }

            //kepzesek, melyekhez minimum egy slideshow miatt hozza kell ferjen

            $sql = 'SELECT * FROM training_training WHERE activeState="ready" AND deleted=0 AND parent_id>0 '.(($trainingId>0)?' AND training_id='.$trainingId:'').
			' AND '.self::getUserTrainingGroupIdsPartialSQL($userId);

            //treningek listaja, amihez hozzaferhet a user a groupja miatt
            $trainingListMetaData = MySQL::resultArrayId(
                MySQL::executeQuery($sql),
                MySQL::fmAssoc,'training_id',
                array('title','training_id','description','activeState','startDate','cover','endDate','authors','attachment')
            );

            $sql = "SELECT u_id, full_name FROM user_u WHERE userlevel IN (5,7)";
            $temp = MySQL::query($sql, false, false);

            $trainers = array();
            foreach($temp as $row)
            {
                $trainers[$row['u_id']] = $row['full_name'];
            }

            $_trainingIds = array(-1);
            foreach ($trainingListMetaData as $_tmpMetaData)
                $_trainingIds[] = $_tmpMetaData['training_id'];

            $sql = 'SELECT training_id,type,testtype,credit,slideshow_id,parent_id,depth,lft,rgt,type as vizsga,credit,startDate,endDate,timeout1,timeout2,wtimeout1,wtimeout2,repetable FROM training_slideshow WHERE TRUE AND training_id IN ('.implode(',',$_trainingIds).') '.(($trainingId>0)?' AND training_id='.$trainingId:'').' ORDER BY training_id ASC,lft ASC';

            $res = MySQL::executeQuery($sql);

            $trainingList=array();
            $_slideShowIds=array(-1);
            foreach (MySQL::resultArray($res,MySQL::fmAssoc) as $_slideShowList)
            {
                $trainingList[$_slideShowList['training_id']]['slideshows'][$_slideShowList['slideshow_id']] = $_slideShowList;
                $_slideShowIds[$_slideShowList['slideshow_id']]=$_slideShowList['slideshow_id'];
            }

            $slideShowListMetaData = MySQL::resultArrayId(
                MySQL::executeQuery('SELECT * FROM slide_slideshow WHERE slideshow_id IN ('.implode(',',$_slideShowIds).')'),
                MySQL::fmAssoc,'slideshow_id',array('name','training_id','description', 'authors'));

            foreach ($trainingList as $trainingId=>$slideShowList)
            {
                //treningenkent fut le 1x

                $sql = 'SELECT slideshow_id,MAX(success) as elvegzett,MAX(visited) as folyamatban FROM training_slideshow_score WHERE training_id = '.$trainingId.' AND u_id='.$userId.' GROUP BY slideshow_id';

                foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $scoreRow)
                {
                    if (isset($scoreRow['slideshow_id']))
                    {
                        $trainingList[$trainingId]['slideshows'][$scoreRow['slideshow_id']]['elvegzett']=$scoreRow['elvegzett'];
                        $trainingList[$trainingId]['slideshows'][$scoreRow['slideshow_id']]['folyamatban']=$scoreRow['folyamatban'];
                    }
                }

                $elvegzett = true;$folyamatban = false;

                $sumlastScore = 0;

                foreach ($trainingList[$trainingId]['slideshows'] as $slideShowId=>$_slideShowList)
                {
                    if (!$_slideShowList['elvegzett'])
                    {
                        $trainingList[$trainingId]['slideshows'][$slideShowId]['elvegzett'] = 0;
                        $elvegzett = false;
                    }

                    if ($_slideShowList['folyamatban'])
                        $folyamatban = true;
                    else
                        $trainingList[$trainingId]['slideshows'][$slideShowId]['folyamatban'] = 0;

                    $trainingList[$trainingId]['slideshows'][$slideShowId]['scores'] = $lastScore = self::getUserLastScore($userId,$trainingId,$slideShowId);
					//print $userId.','.$trainingId.','.$slideShowId;
					//print_r($trainingList[$trainingId]['slideshows']);
					//die;
                    $sumlastScore += $lastScore['credit'];
                    $trainingList[$trainingId]['slideshows'][$slideShowId]['meta'] = $slideShowListMetaData[$slideShowId];

                }

                $trainingList[$trainingId]['slideshows'] = self::setAllowExec($trainingList[$trainingId]['slideshows'],$userId);

                //training elvegezve?
                if (!$elvegzett)
                {
                    $trainingList[$trainingId]['data']['elvegzett'] = (int)$elvegzett;
                    $trainingList[$trainingId]['data']['folyamatban'] = (int)$folyamatban;
                }
                else
                {

                    $trainingList[$trainingId]['data']['elvegzett'] = 1;
                    $trainingList[$trainingId]['data']['folyamatban'] = 0;
                }

                //meta
                $trainingList[$trainingId]['data']['meta'] = $trainingListMetaData[$trainingId];
                $trainingList[$trainingId]['data']['meta']['sumcredit'] = $sumlastScore;

                //change authors data from user_id to full_name
                $trainingList[$trainingId]['data']['meta']["authors"] = $trainers[$trainingList[$trainingId]['data']['meta']["authors"]];

            }

            return ($trainingList);
        }

        /**
         * Csillagozasos ertekeleshez hasznalhato. Parameterben az $id lehet training_xx,trainger_xx,trainingslideshow_xx is!
         *
         * @param $officeId
         * @param $userId
         * @param $id [traning,trainingslideshow,trainer]_%d
         * @param $rate
         * @return bool
         */
        public static function addStarRating($officeId,$userId,$id,$rate)
        {
            $avPrefix=array('training','trainingslideshow','trainer');
            $tmp = explode('_',$id);
            $idPrefix = $tmp[0];
            $idSuffix = (int)$tmp[1];

            if (in_array($idPrefix,$avPrefix) and $rate<6 and $rate>0)
            {
                $sql='INSERT INTO starrating (office_id,u_id,id,rate,ts) VALUES("'.(int)$officeId.'","'.(int)$userId.'","'.MySQL::filter($idPrefix.'_'.$idSuffix).'","'.(int)$rate.'",NOW()) ON DUPLICATE KEY UPDATE ts=NOW(), rate='.(int)$rate;
                return MySQL::execute($sql);
            }

            return false;

        }


        /**
         * @param array $row
         * @param $userId
         * @return array
         */
        private static function _setAllowExec(array $row,$userId)
        {
            $allowExec = false;
            $allowExecMessage = '';

            $dateObjectNow = new DateTime('now');

            if ($row['startDate'] and $row['endDate'])
            {
                $dateObjectStart = new DateTime($row['startDate'].' 00:00:00');
                $dateObjectEnd = new DateTime($row['endDate']. '23:59:59');

                //indithato a datum alapjan
                if ($dateObjectNow>$dateObjectStart and $dateObjectNow<$dateObjectEnd)
                {
                    //ha van ora perc beallitva es hetkozben / hetvege van, akkor ez fut le, egyebkent indithato lesz
                    if (($row['timeout1'] and $row['timeout2']) or ($row['wtimeout1'] and $row['wtimeout2']))
                    {
                        //hetkoznap
                        if ($row['timeout1'] and $row['timeout2'])
                        {
                            if ($dateObjectNow->format('N')>=1 and $dateObjectNow->format('N')<=5)
                            {
                                $dateObjectStart1 = new DateTime(date('Y-m-d').' '.$row['timeout1']);
                                $dateObjectEnd1 = new DateTime(date('Y-m-d').' '.$row['timeout2']);

                                if ($dateObjectNow>$dateObjectStart1 and $dateObjectNow<$dateObjectEnd1)
                                    $allowExec = true;
                            }
                        }

                        //hetvege
                        if ($row['wtimeout1'] and $row['wtimeout2'])
                        {
                            if ($dateObjectNow->format('N')>=6 and $dateObjectNow->format('N')<=7)
                            {
                                $dateObjectStart1 = new DateTime(date('Y-m-d').' '.$row['timeout1']);
                                $dateObjectEnd1 = new DateTime(date('Y-m-d').' '.$row['timeout2']);

                                if ($dateObjectNow>$dateObjectStart1 and $dateObjectNow<$dateObjectEnd1)
                                    $allowExec = true;
                            }
                        }

                        if (!$allowExec)
                        {
                            if ((!$row['timeout1'] or !$row['timeout2']) and (($dateObjectNow->format('N')>=1 and $dateObjectNow->format('N')<=5)))
                            {
                                $allowExec = true;
                            }
                            if ((!$row['wtimeout1'] or !$row['wtimeout2']) and (($dateObjectNow->format('N')>=6 and $dateObjectNow->format('N')<=7)))
                            {
                                $allowExec = true;
                            }
                        }


                    }
                    else
                        $allowExec = true;

                    if (!$allowExec) $allowExecMessage = 'Time exceeded';
                }
                //nem indithato a datum alapjan
                else
                {
                    $allowExec = false;
                    $allowExecMessage = 'Date time exceeded';
                }
            }
            else
                $allowExec = true;

            if ($allowExec)
            {
                //ha teszt, akkor megnezni repetable e
                if ($row['type'] == 1 and !$row['repetable'])
                {
                    //ha van mar a score tablaban rekordja ami nem visited rekord, akkor false!
                    if (MySQL::fetchRecord(MySQL::executeQuery('SELECT * FROM training_slideshow_score WHERE training_id='.$row['training_id'].' AND slideshow_id='.$row['slideshow_id'].' AND u_id='.(int)$userId. ' AND visited=0 LIMIT 1')))
                    {
                        $allowExec = false;
                        $allowExecMessage = 'Test not repeatable';

                    }
                }
            }


            return array('_allow_exec'=>$allowExec,'_allow_exec_message'=>$allowExecMessage);
        }

        /**
         * Kivulrol nem hivhato, ez rendezi be az _allow_exec flaget attol fuggoen hogy indithato e a slideshow a usernek!
         *
         * @param $trainingSlideShowRows
         * @param $userId
         * @return mixed
         */
        private static function setAllowExec($trainingSlideShowRows,$userId)
        {
            foreach ($trainingSlideShowRows as &$row)
            {
                $slideShowId = $row['slideshow_id'];

                $row['_allow_exec'] = 0;

                if ($row['depth']==1)
                {
                    //ide jöhetnek egyéb feltételek pl időpont-tol-ig stb


                    $ae = self::_setAllowExec($row,$userId);
                    $row['_allow_exec']=$ae['_allow_exec'];
                    $row['_allow_exec_message']=$ae['_allow_exec_message'];
                }
                else
                {
                    //ide jöhetnek egyéb feltételek pl időpont-tol-ig

                    //a szuloje el lett vegezve? ha igen, akkor ez is indithato lesz
					//print_r($trainingSlideShowRows); 
					//print_r($trainingSlideShowRows[$row['parent_id']]['results']['success']);
                    //if ($trainingSlideShowRows[$row['parent_id']]['results']['success'])
                    if ($trainingSlideShowRows[$row['parent_id']]['elvegzett'])
                    {
                        $ae = self::_setAllowExec($row,$userId);
                        $row['_allow_exec']=$ae['_allow_exec'];
                        $row['_allow_exec_message']=$ae['_allow_exec_message'];
                    }
                    else
                    {
                        $row['_allow_exec']=false;
                        $row['_allow_exec_message']='depending on previous';
                    }
                }
            }

            return $trainingSlideShowRows;
        }


        /**
         * @param $userId
         * @return array
         */
        public static function getSumCreditByUserId($userId)
        {
            $credit=$maxCredit=0;

            foreach (Statistics::getStatByUser($userId) as $training)
                foreach ($training['slideshows'] as $slideShowData)
                {
                    $maxCredit += (int) $slideShowData['credit'];
                    $credit += (int) $slideShowData['scores']['credit'];
                }

            return array('max_credit'=>$maxCredit,'credit'=>$credit);
        }


        /**
         * Csillagozasos pontozas atlag eredmenyevel ter vissza
         *
         * @param $officeId
         * @param $id
         * @return array [cnt=>x, avg=xx.x]
         */
        public static function getStarRating($officeId,$id)
        {
            $sql='SELECT AVG(rate),COUNT(*) FROM starrating WHERE office_id="'.(int)$officeId.'" AND id="'.MySQL::filter($id).'"';
            $res = MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmIndex);
            return array('cnt'=>$res[1],'avg'=>round($res[0],1));
        }

        public static function getAllStars($officeId,$id)
        {
            $retval = array('1'=>0,'2'=>0,'3'=>0,'4' =>0,'5' =>0);
            $sql='SELECT rate, COUNT(rate) FROM starrating
                WHERE office_id="'.(int)$officeId.'"
                    AND id="'.MySQL::filter($id).'"
                    GROUP BY rate';
            $res = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmIndex);
            foreach ($res as $row){

                $retval[$row[0]] = $row[1];
            }
            return $retval;//array('cnt'=>$res[1],'avg'=>round($res[0],1));
        }
        /**
         *
         * Egy userhez kapcsolodo training statisztikaval ter vissza, lsd getStatByUser fv.
         *
         * @param $officeId
         * @param $userId
         * @param $trainingId
         * @return array
         */
        public static function getTrainingDetailsHomePage($officeId,$userId,$trainingId)
        {
            $res = self::getStatByUser($userId,array('training_id'=>(int)$trainingId));
            return $res;
        }

        /**
         * Home oldalra a mult,jelen,jovo trainingjeinek listajat adja vissza egy adott userre.
         * Gyakorlatilag ez semmi mast nem csinal, csak a getStatByUser() fv eredmenyet datum szerint elhelyezi
         * egy kategoriaban, aszerint hogy past,present,future a training idopontja.
         *
         * @param $userId
         * @return array
         *
         * elmult trainingek (mar nem indithatoak)
         * jelenlegi traningek (most indithatoak)
         * jovobeli trainingek (meg nem indithatoak)
         */
        public static function getTrainingListHomePage($userId)
        {
            $retVal = array('past'=>array(),'present'=>array(),'future'=>array());

            $res = self::getStatByUser($userId);

            foreach ($res as $trainingId=>$trainingDetails)
            {
                $meta = $trainingDetails['data']['meta'];

                if (strlen($meta['startDate'])>0 and strlen($meta['endDate'])>0)
                {
                    $dateObjectStart = new DateTime($meta['startDate'].' 00:00:00');
                    $dateObjectEnd = new DateTime($meta['endDate'].' 23:59:59');
                    $dateObjectNow = new DateTime('now');

                    if ($dateObjectStart<=$dateObjectNow and $dateObjectEnd>=$dateObjectNow)
                    {
                        $retVal['present'][$trainingId] = $trainingDetails;
                        $retVal['present'][$trainingId]['data']['days_expire'] = $dateObjectEnd->diff($dateObjectNow)->format('%d');
                    }
                    elseif ($dateObjectStart>$dateObjectNow)
                    {
                        $retVal['future'][$trainingId] = $trainingDetails;
                        $retVal['future'][$trainingId]['data']['days_start'] = $dateObjectStart->diff($dateObjectNow)->format('%d');
                    }
                    else
                    {
                        $retVal['past'][$trainingId] = $trainingDetails;
                    }
                }
                else
                {
                    $retVal['present'][$trainingId]=$trainingDetails;
                }

            }

            return $retVal;
        }

        /**
         * @param TrainingTraining $masterTraining
         * @param TrainingTraining $trainingInstance (opcionalis, ilyenkor egy csak a megadott instance adatokkal dolgozik)
         * @return array|bool
         */
        public static function getStatByMasterTraining(TrainingTraining $masterTraining,TrainingTraining $trainingInstance=null)
        {
            $retVal = array();

            if ($masterTraining->getDBField('parent_id') == 0)
            {
                $instanceStat = array();

                //osszevonhato adatok
                $totaluser=$inprogress=$finished=$trainingratesperuser=$finisheduser=$inprogressuser=$traininguser=array();

                //egyeb
                $attachments = 0; $created = ''; $trainingrates = array();

                $created     = $masterTraining->getDBField('createdDate');
                $updated     = $masterTraining->getDBField('updatedDate');

                if ($trainingInstance instanceof TrainingTraining)
                    $trainingInstanceList = array($trainingInstance);
                else
                    $trainingInstanceList = TrainingTraining::getInstances($masterTraining);

                foreach ($trainingInstanceList as $instance)
                {
                    /**
                     * @var $instance TrainingTraining
                     */
                    $instanceStatResult = Statistics::getStatByTraining($instance->getId());

//                    if ($instance->getDBField('title')=='teszt zol') logToFile('instanceresult',$instanceStatResult);

                    foreach ($instanceStatResult['totaluser'] as $value)              $totaluser[]=$value;
                    foreach ($instanceStatResult['inprogress'] as $value)             $inprogress[]=$value;
                    foreach ($instanceStatResult['finished'] as $value)               $finished[]=$value;
                    foreach ($instanceStatResult['trainingratesperuser'] as $uId=>$value)   $trainingratesperuser[$uId][]=$value;

                    $attachments = $instanceStatResult['attachments'];
                }

                //ha egy treningen belul tobb instanceban is szerepel akkor atlagolom az eredmenyeit
                foreach ($trainingratesperuser as $uId=>$rates) $trainingratesperuser[$uId]=array_sum($rates) / count($rates);

                //userdetails adatok lekerdezese
                $usersDetailsData = MySQL::resultArrayId(MySQL::executeQuery('SELECT * FROM user_u WHERE u_id IN ('.implode(',',$totaluser).')'),MySQL::fmAssoc,'u_id');
                $userGroupData = MySQL::resultArrayId(MySQL::executeQuery('SELECT * FROM user_usergroup'),MySQL::fmAssoc,'usergroup_id');


                foreach ($usersDetailsData as $uId=>$tmp)
                {
                    $traininguser[]= array('u_id'=>$uId,'full_name'=>$usersDetailsData[$uId]['full_name'],'img'=>$usersDetailsData[$uId]['profilePicture'],'department'=>($d=$userGroupData[$usersDetailsData[$uId]['department']]['name'])?$d:'');
                }

                foreach ($trainingratesperuser as $uId=>$avgRate)
                {
                    $avgRate = round($avgRate);

                    $trainingrates[(int)$avgRate][] = array('u_id'=>$uId,'full_name'=>$usersDetailsData[$uId]['full_name'],'img'=>$usersDetailsData[$uId]['profilePicture'],'department'=>($d=$userGroupData[$usersDetailsData[$uId]['department']]['name'])?$d:'');
                }

                foreach ($inprogress as $uId)
                {
                    $inprogressuser[]= array('u_id'=>$uId,'full_name'=>$usersDetailsData[$uId]['full_name'],'img'=>$usersDetailsData[$uId]['profilePicture'],'department'=>($d=$userGroupData[$usersDetailsData[$uId]['department']]['name'])?$d:'');
                }

                foreach ($finished as $uId)
                {
                    $finisheduser[]= array('u_id'=>$uId,'full_name'=>$usersDetailsData[$uId]['full_name'],'img'=>$usersDetailsData[$uId]['profilePicture'],'department'=>($d=$userGroupData[$usersDetailsData[$uId]['department']]['name'])?$d:'');
                }

                return array(
                    'totaluser'=>$totaluser,
                    'traininguser'=>$traininguser,
                    'inprogress'=>$inprogress,
                    'inprogressuser'=>$inprogressuser,
                    'finished'=>$finished,
                    'finisheduser'=>$finisheduser,
                    'trainingratesperuser'=>$trainingratesperuser,
                    'trainingrates'=>$trainingrates,
                    'attachments'=>$attachments,
                    'created'=>$created,
                    'updated'=>$updated,
                    'avarageexamresult'=>count($trainingratesperuser)>0?array_sum($trainingratesperuser) / count($trainingratesperuser):0
                );
            }
            else
                return false;
        }


        /*
         * Training statisztikaval ter vissza
         * a kovetkezoket tudja:
         * - hozzárendelt felhasználók, ebből hány kezdte el a képzést, hányan fejezték be
         * - treninghez rendelt felhasznalok szama
         * - mennyi attachment van hozzárendelve
         * - tartozik-e hozzá vizsga, és akkor azt hányan és kik tették le
         * - átlagos vizsgaeredmény treningenkent
         * - training létrehozás dátuma
         * - vizsgaidopontok (azok a slideshow melyek vizsgak js oldalon kinyerheto az adat)
         * - trening ertekelese
        */
        public static function getStatByTraining($trainingId)
        {

            $retVal=$rateAvgList=$userSuccessList=$trainingSuccessUserIds=array();
            $trainingDetails = TrainingDetails::getTraningFields($trainingId);

            $sql='SELECT * FROM training_slideshow WHERE deleted=0 AND training_id='.(int)$trainingId;

            foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $_slideShowRow)
            {
                $retVal['slideshows'][$_slideShowRow['slideshow_id']] = self::getStatByTrainingSlideShow($trainingId,$_slideShowRow['slideshow_id']);

                /*

                if (isset($retVal['slideshows'][$_slideShowRow['slideshow_id']]['rate_avg']))
                {
                    $rateAvgList[]=$retVal['slideshows'][$_slideShowRow['slideshow_id']]['rate_avg'];
                }


                foreach ($retVal['slideshows'][$_slideShowRow['slideshow_id']]['rates'] as $rate=>$userId)
                {
                    if ((int)$rate>1)
                    {
                        $userSuccessList[$userId] = $userId;
                    }

                }
                */

            }

            $totaluser=$inprogress=$finished=$_finished=$_inprogress=$_userrates=array();$trainingRatesPerUser=array();$attachments=0;

            foreach ($retVal['slideshows'] as $slideShowId=>$_slideShowRow)
            {
                if (is_array($_slideShowRow['totaluser']))
                    foreach ($_slideShowRow['totaluser'] as $id=>$value)    $totaluser[$id]=$value;
                if (is_array($_slideShowRow['inprogress']))
                    foreach ($_slideShowRow['inprogress'] as $id=>$value)   $inprogress[$id]=$value;

                //ha finished a user, es az inprogress counter = a slideshowk szamaval, akkor kiveszem az inprogress listabol a usert
                foreach ($_slideShowRow['inprogress'] as $id=>$value)
                    if ((isset($_slideShowRow['finished'][$id])) and (++$_inprogress[$id] == count($retVal['slideshows'])))
                        unset($inprogress[$id]);

                if (is_array($_slideShowRow['finished']))
                    foreach ($_slideShowRow['finished'] as $id=>$value)     if (++$_finished[$id] == count($retVal['slideshows'])) $finished[$id]=$value;
                if (is_array($_slideShowRow['rates']))
                    foreach ($_slideShowRow['rates'] as $rate=>$uIds)
                        foreach ($uIds as $uId) $_userrates[$uId][]=$rate;

                $attachments+=$_slideShowRow['attachments'];
            }

            foreach ($_userrates as $uId=>$rates) $trainingRatesPerUser[$uId]=array_sum($rates) / count($rates);


            $retVal['totaluser']=$totaluser;
            $retVal['inprogress']=$inprogress;
            $retVal['finished']=$finished;
            $retVal['trainingratesperuser']=$trainingRatesPerUser;
            $retVal['attachments'] = $attachments;
            $retVal['created'] = $trainingDetails['createdDate'];

            return $retVal;

        }

        /**
         *
         * Treninghez tartozo slideshow alapjan visszaadja a kovetkezoket:
         * - sikeresre vizsgazo felhasznaloinak szama / azonosítoik
         * - hozzarendelt felhasznalok szama / azonositoik
         * - vizsgai e a slideshow
         * - a slideshow kezdete vege datuma
         *
         * @param $trainingId
         * @param $slideShowId
         * @return array|bool
         */
        public static function getStatByTrainingSlideShow($trainingId,$slideShowId)
        {

            $retVal = array();

            $trainingId = (int)$trainingId;
            $slideShowId = (int)$slideShowId;
            $inProgress=array();$tmpRate=array();$totalUser=array();

            $trainingObject = new TrainingTraining($trainingId);
            $trainingSlideShow = TrainingSlideShow::getObjectByTrainingIdAndSlideShowId($trainingId,$slideShowId);

            foreach (UserTrainingGroup::getGroupUserObjects(@explode(',',$trainingObject->getDBField('traininggroups'))) as $tmp)
                $totalUser[$tmp->getId()]=$tmp->getId();

            $attachmentCount = $trainingSlideShow->getDBField('attachment')?count(explode(',',$trainingSlideShow->getDBField('attachment'))):0;


            //ha teszt
            if ($trainingSlideShow->getDBField('type') == 1)
            {
                //ha teszt AMI NEM VISITED=1 AZ ERTEKELES, HA SUCCESS=1 AKKOR MEGFELELT, HA SUCCESS=0 AKKOR NEM!
                //-total user = (osszes user szama a csoportban)
                //-in progressd =  (azon userek szama akiknel csak visited=1 sor van nincs mas)
                //-finished = total user - inprogress
                //-successfull exam = ahol a rate>1
                //-failed exam = ahol a rate=1
                //-attachment

				//visited
                foreach (MySQL::resultArray(MySQL::executeQuery('SELECT u_id FROM training_slideshow_score WHERE visited = 1 AND archive = 0 AND training_id='.$trainingId.' AND slideshow_id='.$slideShowId),MySQL::fmAssoc) as $tmp)
                {
                    $tmpVisited[$tmp['u_id']] = $tmp['u_id'];
                    $inProgress[$tmp['u_id']] = $tmp['u_id'];
                }
                //nem visited hanem eredmeny
                foreach (MySQL::resultArray(MySQL::executeQuery('SELECT u_id FROM training_slideshow_score WHERE visited = 0 AND archive = 0 AND training_id='.$trainingId.' AND slideshow_id='.$slideShowId),MySQL::fmAssoc) as $tmp)
                {
                    $tmpRate[$tmp['u_id']] = $tmp['u_id'];
                    //unset($inProgress[$tmp['u_id']]);
                }

                //$inProgress = count($inProgress);

                //$finished = $totalUserCount - $inProgress;
                $finished = $tmpRate;

                $rateAvgList = array(); $ra = false;

                $successExam = $failedExam = 0; $rates = array();

                //2polus/5polus 0->2polus,1->5polus
                if ($trainingSlideShow->getDBField('testtype') == 0 or $trainingSlideShow->getDBField('testtype') == 1)
                {
                    foreach (MySQL::resultArray(MySQL::executeQuery('SELECT u_id ,success,rate FROM training_slideshow_score WHERE archive = 0 AND visited = 0 AND training_id='.$trainingId.' AND slideshow_id='.$slideShowId),MySQL::fmAssoc) as $tmp)
                    {
                        //2polus
                        if ($trainingSlideShow->getDBFields('testtype') == 0)
                        {
                            if ($tmp['success'])
                            {
                                $rates[5][]=array('u_id'=>$tmp['u_id']);
                                $userSuccess[$tmp['u_id']] = $tmp['u_id'];
                                $successExam++;
                            }
                            else
                            {
                                $rates[1][$tmp['u_id']]=$tmp['u_id'];
                                $failedExam++;
                            }

                        }
                        //5polus
                        else
                        {
                            if ($tmp['rate']>1)
                            {
                                $successExam++;
                                $userSuccess[$tmp['u_id']] = $tmp['u_id'];
                            }
                            else    $failedExam++;

                            $rates[$tmp['rate']][$tmp['u_id']]=$tmp['u_id'];

                            $rateAvgList[] = (int) $tmp['rate'];

                            $ra = true;
                        }
                    }

                    $retVal = array(
                            'totaluser'=>$totalUser,
                            'inprogress'=>$inProgress,
                            'finished'=>$finished,
                            'successexam'=>$successExam,
                            'failedexam'=>$failedExam,
                            'attachments'=>$attachmentCount,
                            'rates'=>$rates
                    );

                    if ($ra)
                    {
                        $retVal['rate_avg'] = array_sum($rateAvgList) / count($rateAvgList);
                    }

                }
                else
                {
                    //eval?
                    $retVal = array(
                        'totaluser'=>$totalUser,
                        'inprogress'=>$inProgress,
                        'finished'=>$finished,
                        'attachments'=>$attachmentCount,
                        'user_success'=>$tmpRate
                    );

                }

            }
            else
            {
                $tmpVisited = array();

                //ha nem teszt
                //-total user
                //-finished
                //-attachment
                foreach (MySQL::resultArray(MySQL::executeQuery('SELECT u_id FROM training_slideshow_score WHERE visited = 1 AND archive = 0 AND training_id='.$trainingId.' AND slideshow_id='.$slideShowId),MySQL::fmAssoc) as $tmp)
                {
                    $tmpVisited[$tmp['u_id']] = $tmp['u_id'];
                }

                $retVal = array(
                    'totaluser'=>$totalUser,
                    'inprogress'=>$tmpVisited, //aki megnezte a prezit az inprogress is es finished is!
                    'finished'=>$tmpVisited,
                    'user_success'=>$tmpRate,
                    'attachments'=>$attachmentCount
                );
            }


            return $retVal;
        }

        /**
         * Trainer userId alapjan a kovetkezoket adja vissza:
         *
         * @param $trainerUserId
         * -tréningek listája és értékelése*
         * -tanár értékelés (a tréning értékelés átlaga)*
         * -tanári profiloldal*
         */
        public static function getStatByTrainer($trainerUserId)
        {
            $retVal = array();$rateAvgList=array();$trainingRows=array();

            //training lista author szerint szuressel
            $sql='SELECT * FROM training_training WHERE deleted=1 AND authors='.(int)$trainerUserId;
            $trainingList = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc);
            foreach ($trainingList as $trainingRow)
            {
                $trainingRow['starrating']=self::getStarRating($_SESSION['office_id'],'training_'.$trainingRow['training_id']);
                $rateAvgList[]=$trainingRow['starrating'];

                $trainingRows[] = $trainingRow;
            }

            //trainer adatai
            $sql='SELECT elotag,vezeteknev,keresztnev,full_name,user_email,office_name,gender,language,school,skills,user_kep,cv,description FROM user_u WHERE u_id='.(int)$trainerUserId;
            $userDetails = MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc);


            $retVal['trainings']=$retVal;
            $retVal['trainer']=$userDetails;
            $retVal['trainer']['training_rate_avg']=array_sum($rateAvgList) / count($rateAvgList);
        }

        /**
         * @param $userId
         * @return array
         */
        private static function getUserTrainingGroupIds($userId)
        {
            $grpIds=array('-1');
            foreach (MySQL::resultArray(MySQL::executeQuery('SELECT traininggroup_id FROM user_traininggroupusers WHERE u_id = '.(int)$userId),MySQL::fmAssoc) as $grpId)
                $grpIds[] = $grpId['traininggroup_id'];

            return $grpIds;
        }

        /**
         * @param $userId
         * @return string
         */
        private static function getUserTrainingGroupIdsPartialSQL($userId)
        {
            $partialTrainingGroupFilter = array('FIND_IN_SET(-1,`traininggroups`)');

            foreach (self::getUserTrainingGroupIds($userId) as $tmpGrpId)
                $partialTrainingGroupFilter[]='FIND_IN_SET('.$tmpGrpId.',`traininggroups`)';

            return ' ('.implode(' OR ',$partialTrainingGroupFilter).') ';
        }

        /**
         * @param $userId
         * @param $trainingId
         * @param $slideShowId
         * @return array
         */
        private static function getUserLastScore($userId,$trainingId,$slideShowId)
        {
            $sql = 'SELECT * FROM training_slideshow_score WHERE visited = 0 AND u_id='.(int)$userId.' AND slideshow_id='.(int)$slideShowId.' AND training_id='.(int)$trainingId.' ORDER BY created DESC LIMIT 1';
            $res = MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc);
            return $res;
        }

        //scores tablaban uj rekord s megjelolni visitednek
        //scores tablaban ha nem teszt, akkor regieket archive=1 es egy uj sor beszurasa success=1 rate=5
        //scores tablaban ha teszt, es vegzett, akkor a regieket archive=1 es uj sor beszurasa a kalkultlt adatokkal
    }
?>