<?php
    abstract class Access
    {
        /**
         * @var int
         */
        private static $officeId;

        /**
         * @var string
         */
        private static $officeName;

        /**
         * @var int
         */
        private static $userId;

        /**
         * @var
         * 5 > admin, de nincs usermanagere
         * 7 = admin
         * 3 = user
         */
        private static $userLevel;

        /**
         * @var bool
         */
        private static $init = false;

        /**
         * @var string
         */
        private static $officeType;

        /**
         * @param $session
         */
        private static function initBySession($session)
        {
            self::$officeId = (int)$session['office_id'];
            self::$officeName = $session['office_nametag'];
            self::$userId = (int)$session['u_id'];
            self::$userLevel = (int)$session['userlevel'];
            self::$officeType = (int)$session['officeType'];
            self::$init = true;
        }

        private static function tryInit()
        {
            if (!self::$init)
            {
                self::initBySession($_SESSION);
            }
        }


        /**
         * @param $trainingId
         * @param $slideShowId
         * @return int
         */
        public static function checkAccessTrainingSlideShow($trainingId,$slideShowId)
        {
            self::tryInit();

            $retVal = 0;

            if (self::$init)
            {
                if (self::$userLevel >= 3) $retVal = 2;
                else
                {
                    $sql='SELECT traininggroups FROM training_slideshow WHERE training_id='.(int)$trainingId.' AND slideshow_id='.(int)$slideShowId;
                    $row = MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc);
                    if (strlen($row['traininggroups'])>0)
                    {
                        $sql='SELECT u_id FROM `user_traininggroupusers` WHERE u_id='.self::$userId.' AND traininggroup_id in ('.$row['traininggroups'].') LIMIT 1';
                        $row=MySQL::fetchRecord(MySQL::executeQuery($sql),MySQL::fmAssoc);
                        if ($row['u_id']>0) $retVal=1;
                    }
                }
            }

            return $retVal;
        }

        /**
         * @param $id
         * @return bool
         */
        public static function checkMenuAccessByIdString($id)
        {
            self::tryInit();

            $retVal = false;
            switch ($id)
            {
                case 'superadmin':
                case 'users':
                case 'usermanager':
                    if (self::$userLevel>5)
                        $retVal = true;
                break;
                case 'editor':
                case 'mymedia':
                case 'publication':
                case 'statistic':
                    if (self::$userLevel>=5)
                        $retVal = true;
                break;
            }
            return $retVal;
        }

        /**
         * @return int
         */
        public static function getAccessLevel()
        {
            self::tryInit();

            return (int)self::$userLevel;
        }

        /**
         * @return true
         */
        public static function checkIsTrainerOffice()
        {
            self::tryInit();

            $retVal = false;

            if (self::$officeType == 'trainer') $retVal = true;
            return $retVal;
        }
    }
?>