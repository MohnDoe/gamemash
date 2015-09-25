<?php
/**
 * Created by PhpStorm.
 * User: Personne
 * Date: 18/09/2015
 * Time: 20:57
 */

class Level {

    static $arrayLevelsSteps = array(
        'Level 0' => 0,
        'Level 1' => 15,
        'Level 2' => 30,
        'Level 3' => 45,
        'Level 4' => 75,
        'Level 5' => 120,
        'Level 6' => 195,
        'Level 7' => 315,
        'Level 8' => 510,
        'Level 9' => 825,
        'Level 10' => 1335,
        'Level 11' => 2160,
        'Level 12' => 3495,
        'Level 13' => 5655,
        'Level 14' => 9159,
        'Level 15' => 14805,
        'Level 16' => 23964,
        'Level 17' => 38769,
        'Level 18' => 62733,
        'Level 19' => 101532,
        'Level 20' => 164265
    );

    static function getBadgeFromPoints($points){
        $i = 0;
        $founded = false;
        $founded_at = 0;
        foreach(self::$arrayLevelsSteps as $levelName => $pointsNeeded){
            //var_dump($levelName.'/ points needed : '.$pointsNeeded);
            if(!$founded && ($points <= $pointsNeeded)){
                $founded = true;
                $founded_at = $i;
            }
            if($founded && ($i == $founded_at+1)){
                $level = array($levelName => $pointsNeeded);
            }
            $i++;
        }
        if(!$founded){
            return array('Level 0' => 0);
        }
        return $level;
    }
}