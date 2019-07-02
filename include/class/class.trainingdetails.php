<?php
    abstract class TrainingDetails
    {
        /**
         * Osszes slideshow listazasa, megjelolve azokat, melyeket mar indithat is
         *
         * @param $userId
         * @param $trainingId
         * @return array
         */
        /*
        public static function getAvailableSlideShowList($userId,$trainingId)
        {
            $rows = array();

            $sql = 'SELECT * FROM training_slideshow INNER JOIN slide_slideshow ON slide_slideshow.slideshow_id=training_slideshow.slideshow_id WHERE training_id='.(int)$trainingId.' ORDER BY lft ASC';
            foreach (MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc) as $tmp)
                $rows[$tmp['slideshow_id']] = $tmp;


            foreach ($rows as &$row)
            {
                $slideShowId = $row['slideshow_id'];

                $row['_allow_exec'] = 0;

                $slideShowResult = self::getTrainingSlideShowResult($userId,$trainingId,$slideShowId);

                $row['_results']=$slideShowResult;

                if ($row['depth']==1)
                {
                    //ide jöhetnek egyéb feltételek pl időpont-tol-ig stb

                    $row['_allow_exec']=1;
                }
                else
                {
                    //ide jöhetnek egyéb feltételek pl időpont-tol-ig

                    //a szuloje el lett vegezve? ha igen, akkor ez is indithato lesz
                    if ($rows[$row['parent_id']]['_results']['success'])
                    {
                        $row['_allow_exec']=1;
                    }
                }
            }

            return array('slideshows'=>$rows,'training'=>self::getTraningFields($trainingId));
        }
        */

        /**
         * A slideshow slidejaval ossszeszedheto pontok osszege (figyelembe veve a slideLevel erteket is - 100*slideLevel)
         * @param $slideShowId
         * @return int
         */
        private static function calculateSlideShowMaxPoints($slideShowId)
        {
            $retVal = 0;

            $res = MySQL::executeQuery('SELECT (SUM(`slideLevel`) * 100) AS maxpoint FROM slide_slides WHERE `type`="template" AND slideshow_id='.(int)$slideShowId);
            if ($row = MySQL::fetchRecord($res,MySQL::fmAssoc))
                $retVal = $row['maxpoint'];
            return $retVal;
        }


        /**
         * @param $trainingId
         * @param null $slideShowId
         * @param null|string|array $prop
         * @return array
         */
        public static function getTraningSlideShowFields($trainingId,$slideShowId,$prop=null)
        {
            $res = MySQL::executeQuery($sql = 'SELECT * FROM training_slideshow WHERE training_id='.(int)$trainingId.(($slideShowId>0)?' AND slideshow_id='.(int)$slideShowId:''));
            $row = MySQL::fetchRecord($res,MySQL::fmAssoc);
            return $prop===null?$row:$row[$prop];
        }

        /**
         * @param $trainingId
         * @param null $prop
         * @return array
         */
        public static function getTraningFields($trainingId,$prop=null)
        {
            $res = MySQL::executeQuery('SELECT * FROM training_training WHERE training_id='.(int)$trainingId.' LIMIT 1');
            $row = MySQL::fetchRecord($res,MySQL::fmAssoc);
            return $prop===null?$row:$row[$prop];
        }

        /**
         * @param $trainingId
         * @param $slideShowId
         * @param $userId
         * @return mixed
         */
        public static function getLastToken($trainingId,$slideShowId,$userId)
        {
            //legutolso training slideshow slide rekord, majd token megkeresese a results tablaban
            $sql = 'SELECT `token` FROM training_results WHERE training_id='.(int)$trainingId.' AND slideshow_id='.(int)$slideShowId.' AND u_id='.(int)$userId.' ORDER BY `date` DESC LIMIT 1';
            $res = MySQL::executeQuery($sql);
            $row = MySQL::fetchRecord($res,MySQL::fmAssoc);
            $token = $row['token'];
            unset($row);
            return $token;
        }

        /**
		 * Results tablaban levo osszes rekordal ter vissza a parameterben megadott szuro feltetelek szerint, SLIDE_ID-vel indexelve!
         *
         * @param $trainingId
         * @param $slideShowId
         * @param $tokenId
         * @param $userId
         * @return array
         */
        public static function getTrainingResultsTableRowsWithIndexSlideId($trainingId,$slideShowId,$tokenId,$userId)
        {
            $sql = 'SELECT * FROM training_results WHERE training_id='.(int)$trainingId.' AND slideshow_id='.(int)$slideShowId.' AND u_id='.(int)$userId.' AND token="'.MySQL::filter($tokenId).'" ';
            $res = MySQL::executeQuery($sql);
            return MySQL::resultArrayId($res,MySQL::fmAssoc,'slide_id');
        }


        /**
         * A slideshowban elert pontszamok osszesitese
         * -results tablaban idorendben az utolso datumot kivalasztani az adott training_id,slideshow_id,user_id alapjan
         * -elerpontok = szummazni a result pontszamokat,lsd fent
         * -maxpont = lekerdezni a slideshowban elerheto osszes pont erteket (100*level*slideokszama)
         * -pontok szama = elertpontok * elerhetokreditpontok / maxpontok
         *
         * @param $userId
         * @param $trainingId
         * @param $slideshowId
         *
         *              ELERT PONT , ELERHETO PONT, ELERT KREDIT, ELERHETO KREDIT, SIKERES, OSZTALYZAT
         * @return array[point=>int,max_point=>int,credit=>int,max_credit=>int,success=>boolean,rate=>int]
         */
        public static function getTrainingSlideShowResult($userId,$trainingId,$slideshowId)
        {
            $token = ''; //form token id kerul bele
            $trainingResultUserPoints = 0; //felhasznalo altal gyujtott pontok a results tablaban
            $trainingSlideShowCredit = (int) self::getTraningSlideShowFields($trainingId,$slideshowId,'credit'); //training_slideshow credit erteke
            $slideShowMaxPoints = self::calculateSlideShowMaxPoints($slideshowId);
            $slideShowType = (int) self::getTraningSlideShowFields($trainingId,$slideshowId,'testtype'); //training_slideshow testtype 0->2polus,1->5polus

            $retVal = array();

            $token = self::getLastToken($trainingId,$slideshowId,$userId);
            //ide csak akkor fut bele ha mar kitoltotte a tesztet
            if ($token)
            {
                //token alapjan SUM(result) -> elert pontok osszesitese
                $sql = 'SELECT SUM(`result`) as result FROM training_results WHERE token="'.MySQL::filter($token).'" AND training_id='.(int)$trainingId.' AND slideshow_id='.(int)$slideshowId.' AND u_id='.(int)$userId;
                $row = MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc);
                $trainingResultUserPoints = (int)$row['result'];
            }

            $retVal['point'] = $trainingResultUserPoints; //elert pontok szama
            $retVal['max_point'] = $slideShowMaxPoints;    //max elerheto pontok szama
            if ($slideShowMaxPoints>0)
                $retVal['credit'] = $trainingResultUserPoints * $trainingSlideShowCredit / $slideShowMaxPoints; //elert creditek szama
            else
                $retVal['credit'] = 0;

            $retVal['max_credit'] = $trainingSlideShowCredit; //elert maximalis creditek szama

            $success = false;
            $rate=0;
            switch($slideShowType)
            {
                //2-polus
                case 0:
                    $success = $retVal['credit'] > $retVal['max_credit'] * (60 / 100);
                break;
                //5-polus
                case 1:
                    foreach (array(90,80,70,60,0) as $key => $percent)
                    {
                        if ($retVal['credit']>=$retVal['max_credit'] * ($percent / 100))
                        {
                            $rate = abs($key-5);
                            break;
                        }
                    }
                    $success = ($rate>1);
                break;
                case 2:
                    $success = 1;
                    $retVal['max_point'] = 0;
                break;
            }

            //megfelelt?
            $retVal['success']  = $success;

            //osztalyzat ha 5polusu 1,2,3,4,5
            $retVal['rate']  = $rate;
            $retVal['token']  = $token;

            return $retVal;
        }

        /**
         * @param $trainingId
         * @param $officeId
         * @param $slideShowId
         * @param $token
         * @param $userId
         * @return bool
         */
        public static function addScore($trainingId,$officeId,$slideShowId,$token,$userId)
        {
            //kitoltom a score tablaba is a test testtype mezoket, ezert lekerem a training_slideshow tablabol oket!
            $typeResult = self::getTraningSlideShowFields($trainingId,$slideShowId);

            $retVal = false;

            //$trainingDetailsObject = TrainingDetails::new TrainingDetails($this->trainingId,$userId,$this->officeId);
            $result = TrainingDetails::getTrainingSlideShowResult((int)$userId,(int)$trainingId,(int)$slideShowId);
            if ($token == $result['token'])
            {
                //regi eredmenyek archivalasa (ami nem visited)
                MySQL::runCommand('UPDATE training_slideshow_score SET archive=1 WHERE visited=0 AND training_id='.(int)$trainingId.' AND slideshow_id='.(int)$slideShowId.' AND u_id='.(int)$userId.' AND office_id='.(int)$officeId);

                //uj eredmeny beszurada
                $retVal = MySQL::execute('INSERT INTO training_slideshow_score (training_id,slideshow_id,type,testtype,office_id,u_id,token_id,created,max_credit,credit,max_point,point,success,rate) VALUES(
                '.(int)$trainingId.',
                '.(int)$slideShowId.',
                '.$typeResult['type'].',
                '.$typeResult['testtype'].',
                '.(int)$officeId.',
                '.(int)$userId.',
                "'.MySQL::filter($token).'",
                NOW(),
                '.(int)$result['max_credit'].',
                '.(int)$result['credit'].',
                '.(int)$result['max_point'].',
                '.(int)$result['point'].',
                '.(int)$result['success'].',
                '.(int)$result['rate'].'
                )');
            }
            return $retVal;
        }

        /**
         * @param $trainingId
         * @param $showId
         * @param $officeId
         * @param $userId
         * @param $tokenId
         * @return bool
         */
        public static function addVisited($trainingId,$showId,$officeId,$userId,$tokenId)
        {
            if ($trainingSlideShowObject = TrainingSlideShow::getObjectByTrainingIdAndSlideShowId($trainingId,$showId))
            {
                return MySQL::execute('INSERT IGNORE INTO training_slideshow_score (training_id,slideshow_id,office_id,u_id,type,testtype,token_id,created,success,archive,visited) VALUES("' . (int)$trainingId . '","' . (int)$showId . '","' . (int)$officeId . '","' . (int)$userId . '","'.$trainingSlideShowObject->getDBField('type').'","'.$trainingSlideShowObject->getDBField('testtype').'","' . MySQL::filter($tokenId) . '",NOW(),0,0,1) ');
            }
        }

    }
?>