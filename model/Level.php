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
        $delta = 0.60;
        $step = (PTS_FOR_A_VOTE * 10);
        for($i = 0; $i <= self::$numberLevelLimit; $i++){
            if($i%5 == 0){
                $step += PTS_FOR_A_VOTE * 9.5;
            }
            $Level = array();

            $Level['name'] = 'Niveau '.$i;
            if($i == 0){
                $Level['needed'] = PTS_FOR_A_VOTE * (1);
            }
            else if($i == 1){
                $Level['needed'] = PTS_FOR_A_VOTE * (5);
            }
            else{
                //$Level['needed'] = $aLevels[$i-1]['needed'] + ($aLevels[$i-2]['needed'] * ($delta));
                $Level['needed'] = $aLevels[$i-1]['needed'] + ($step);
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