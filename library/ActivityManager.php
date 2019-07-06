<?php namespace NozCore;

use NozCore\Endpoints\ActiveUsers;
use NozCore\Objects\ActiveUser;

class ActivityManager {

    /**
     * ActivityManager constructor.
     * @param Endpoint $endpoint
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function __construct(Endpoint $endpoint = null) {
        $activeUser = new ActiveUser();
        $activeUser->setProperty('id', session_id());
        $activeUser->setProperty('userId', -1);
        $activeUser->setProperty('lastActive', date('Y-m-d H:i:s'));

        if(isset($_SERVER['HTTP_USER_AGENT'])) {
            $activeUser->setProperty('userAgent', $_SERVER['HTTP_USER_AGENT']);
        }

        if(isset($_SERVER['REMOTE_ADDR'])) {
            $activeUser->setProperty('ip', $_SERVER['REMOTE_ADDR']);
        }

        if(isset($_SERVER['HTTP_X_REAL_IP'])) {
            $activeUser->setProperty('ip', $_SERVER['HTTP_X_REAL_IP']);
        }

        if(!$endpoint instanceof ActiveUsers) {
            if ($endpoint == null) {
                $activeUser->setProperty('viewing', 'Unknown');
            } else {
                $activeUser->setProperty('viewing', end(explode('\\', get_class($endpoint))));
            }
        }

        if(isset($_SESSION['user'])) {

            if(isset($_SESSION['user']['id'])) {
                $activeUser->setProperty('userId', $_SESSION['user']['id']);
            }
        }

        $activeUser->save('SERVER');
    }
}