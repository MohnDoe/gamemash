<?php
/**
 * Created by PhpStorm.
 * User: Personne
 * Date: 18/09/2015
 * Time: 00:59
 */
/**
 * This class calculates ratings based on the Elo system used in chess.
 *
 * @author Michal Chovanec <michalchovaneceu@gmail.com>
 * @copyright Copyright © 2012 - 2014 Michal Chovanec
 * @license Creative Commons Attribution 4.0 International License
 */

namespace Rating;

class Rating
{

    /**
     * @var int The K Factor used.
     */
    const KFACTOR = 32;

    /**
     * Protected & private variables.
     */
    protected $_ratingA;
    protected $_ratingB;

    protected $_scoreA;
    protected $_scoreB;

    protected $_expectedA;
    protected $_expectedB;

    protected $_newRatingA;
    protected $_newRatingB;

    /**
     * Costructor function which does all the maths and stores the results ready
     * for retrieval.
     *
     * @param int Current rating of A
     * @param int Current rating of B
     * @param int Score of A
     * @param int Score of B
     */

    static $arrayFIDE = array(
        '1.00' => 800,
        '0.83' => 273,
        '0.66' => 117,
        '0.49' => -7,
        '0.32' => -133,
        '0.15' => -296,
        '0.99' => 677,
        '0.82' => 262,
        '0.65' => 110,
        '0.48' => -14,
        '0.31' => -141,
        '0.14' => -309,
        '0.98' => 589,
        '0.81' => 251,
        '0.64' => 102,
        '0.47' => -21,
        '0.30' => -149,
        '0.13' => -322,
        '0.97' => 538,
        '0.80' => 240,
        '0.63' => 95,
        '0.46' => -29,
        '0.29' => -158,
        '0.12' => -336,
        '0.96' => 501,
        '0.79' => 230,
        '0.62' => 87,
        '0.45' => -36,
        '0.28' => -166,
        '0.11' => -351,
        '0.95' => 470,
        '0.78' => 220,
        '0.61' => 80,
        '0.44' => -43,
        '0.27' => -175,
        '0.10' => -366,
        '0.94' => 444,
        '0.77' => 211,
        '0.60' => 72,
        '0.43' => -50,
        '0.26' => -184,
        '0.09' => -383,
        '0.93' => 422,
        '0.76' => 202,
        '0.59' => 65,
        '0.42' => -57,
        '0.25' => -193,
        '0.08' => -401,
        '0.92' => 401,
        '0.75' => 193,
        '0.58' => 57,
        '0.41' => -65,
        '0.24' => -202,
        '0.07' => -422,
        '0.91' => 383,
        '0.74' => 184,
        '0.57' => 50,
        '0.40' => -72,
        '0.23' => -211,
        '0.06' => -444,
        '0.90' => 366,
        '0.73' => 175,
        '0.56' => 43,
        '0.39' => -80,
        '0.22' => -220,
        '0.05' => -470,
        '0.89' => 351,
        '0.72' => 166,
        '0.55' => 36,
        '0.38' => -87,
        '0.21' => -230,
        '0.04' => -501,
        '0.88' => 336,
        '0.71' => 158,
        '0.54' => 29,
        '0.37' => -95,
        '0.20' => -240,
        '0.03' => -538,
        '0.87' => 322,
        '0.70' => 149,
        '0.53' => 21,
        '0.36' => -102,
        '0.19' => -251,
        '0.02' => -589,
        '0.86' => 309,
        '0.69' => 141,
        '0.52' => 14,
        '0.35' => -110,
        '0.18' => -262,
        '0.01' => -677,
        '0.85' => 296,
        '0.68' => 133,
        '0.51' => 7,
        '0.34' => -117,
        '0.17' => -273,
        '0.00' => -800,
        '0.84' => 284,
        '0.67' => 125,
        '0.50' => 0,
        '0.33' => -125,
        '0.16' => -284
    );
    public function  __construct($ratingA,$ratingB,$scoreA,$scoreB)
    {
        $this->_ratingA = $ratingA;
        $this->_ratingB = $ratingB;
        $this->_scoreA = $scoreA;
        $this->_scoreB = $scoreB;

        $expectedScores = $this -> _getExpectedScores($this -> _ratingA,$this -> _ratingB);
        $this->_expectedA = $expectedScores['a'];
        $this->_expectedB = $expectedScores['b'];

        $newRatings = $this ->_getNewRatings($this -> _ratingA, $this -> _ratingB, $this -> _expectedA, $this -> _expectedB, $this -> _scoreA, $this -> _scoreB);
        $this->_newRatingA = $newRatings['a'];
        $this->_newRatingB = $newRatings['b'];
    }

    /**
     * Set new input data.
     *
     * @param int Current rating of A
     * @param int Current rating of B
     * @param int Score of A
     * @param int Score of B
     */
    public function setNewSettings($ratingA,$ratingB,$scoreA,$scoreB)
    {
        $this -> _ratingA = $ratingA;
        $this -> _ratingB = $ratingB;
        $this -> _scoreA = $scoreA;
        $this -> _scoreB = $scoreB;

        $expectedScores = $this -> _getExpectedScores($this -> _ratingA,$this -> _ratingB);
        $this -> _expectedA = $expectedScores['a'];
        $this -> _expectedB = $expectedScores['b'];

        $newRatings = $this ->_getNewRatings($this -> _ratingA, $this -> _ratingB, $this -> _expectedA, $this -> _expectedB, $this -> _scoreA, $this -> _scoreB);
        $this -> _newRatingA = $newRatings['a'];
        $this -> _newRatingB = $newRatings['b'];
    }

    /**
     * Retrieve the calculated data.
     *
     * @return Array An array containing the new ratings for A and B.
     */
    public function getNewRatings()
    {
        return array (
            'a' => $this -> _newRatingA,
            'b' => $this -> _newRatingB
        );
    }

    /**
     * Protected & private functions begin here
     */

    protected function _getExpectedScores($ratingA,$ratingB)
    {
        $expectedScoreA = 1 / ( 1 + ( pow( 10 , ( $ratingB - $ratingA ) / 400 ) ) );
        $expectedScoreB = 1 / ( 1 + ( pow( 10 , ( $ratingA - $ratingB ) / 400 ) ) );

        return array (
            'a' => $expectedScoreA,
            'b' => $expectedScoreB
        );
    }

    protected function _getNewRatings($ratingA,$ratingB,$expectedA,$expectedB,$scoreA,$scoreB)
    {
        $newRatingA = $ratingA + ( self::KFACTOR * ( $scoreA - $expectedA ) );
        $newRatingB = $ratingB + ( self::KFACTOR * ( $scoreB - $expectedB ) );

        return array (
            'a' => $newRatingA,
            'b' => $newRatingB
        );
    }

    static function get_dp($p){
        $p = number_format($p, 2, '.', '');
        //$p = round($p, 2);
        return self::$arrayFIDE[strval($p)];
    }

}