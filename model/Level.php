<?php
/**
 * Created by PhpStorm.
 * User: Personne
 * Date: 18/09/2015
 * Time: 20:57
 */

class Level {

    static $numberLevelLimit = 50;

    static $aLevels = array();

    static function init(){

        $aLevels = array();
        $delta = 0.2;
        for($i = 0; $i < self::$numberLevelLimit; $i++){
            $Level = array();

            $Level['name'] = 'LVL_'.$i;
            if($i == 0){
                $Level['needed'] = PTS_FOR_A_VOTE * (1);
            }
            else if($i == 1){
                $Level['needed'] = PTS_FOR_A_VOTE * (2);
            }
            else{
                $Level['needed'] = $aLevels[$i-1]['needed'] + ($aLevels[$i-2]['needed'] * ($delta * ($i%10 + 1)));
            }
            $Level['needed'] = round($Level['needed']);
            $Level['votesNeeded'] = round($Level['needed'] / PTS_FOR_A_VOTE);

            if($i >0){
                $Level['diffVotesNeeded'] = $Level['votesNeeded'] - $aLevels[$i-1]['votesNeeded'];
            }

            $aLevels[] = $Level;
        }

        self::$aLevels = $aLevels;
    }
}