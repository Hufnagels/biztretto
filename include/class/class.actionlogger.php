<?php
    abstract class ActionLogger
    {
        const TYPE_NORMAL=0;
        const TYPE_NOTIFY=1;
        const TYPE_WARNING=2;
        const TYPE_ERROR=3;
        const TYPE_SECURITY=4;

        /**
         *
         * Muveletek naplozasa (ki,hol,mikor,mit csinalt)
         *
         * @param $actionId
         * @param null $object
         * @param $message
         * @param null $foreRequest
         * @param int $type
         */
        public static function addToActionLog($actionId,$object=null,$message,$foreRequest=null,$type=self::TYPE_NORMAL)
        {
            $objectStr='';
            if ($object and method_exists($object,'getId')) $objectStr=get_class($object).'.'.$object->getId();
            if ($foreRequest) $request = $foreRequest;
            else $request = $_REQUEST;

            MySQL::runCommand('INSERT INTO actionlogger (u_id,office_id,office_nametag,ipaddress,type,action_id,object,message,created,request)
                VALUES("'.(int)$_SESSION['u_id'].'","'.(int)$_SESSION['office_id'].'",
                "'.MySQL::filter($_SESSION['office_nametag']).'","'.MySQL::filter($_SERVER['REMOTE_ADDR']).'","'.(int)$type.'",
                "'.strtolower(MySQL::filter($actionId)).'","'.MySQL::filter($objectStr).'","'.MySQL::filter($message).'",NOW(),"'.MySQL::filter(ACTIONLOGGER_GZIP === true?gzcompress(json_encode($request)):json_encode($request)).'")');
        }
    }
?>