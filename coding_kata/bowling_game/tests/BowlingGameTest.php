<?php

namespace BowlingGame\Test;

use BowlingGame\Game;

class BowlingGameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * An instance of the bowling game.
     *
     * @var Game
     */
    private $_game;

    protected function setUp()
    {
        $this->_game = new Game;
    }

    /**
     * Simulates $n number of rolls, knocking down $pins number of pins with
     * each roll.
     *
     * @param int $n Number of rolls to simulate.
     * @param int $pins Number of pins knocked down with each roll.
     *
     * @return void
     */
    private function _rollMany($n, $pins)
    {
        for ($i = 0; $i < $n; $i++) {
            $this->_game->roll($pins);
        }
    }

    /**
     * Simulates two rolls of the same frame, together knocking down all pins.
     *
     * @return void
     */
    private function _rollSpare()
    {
        $this->_game->roll(5);
        $this->_game->roll(5);
    }

    /**
     * Simulates one roll of the same frame, knocking down all pins.
     *
     * @return void
     */
    private function _rollStrike()
    {
        $this->_game->roll(10);
    }

    /**
     * During a gutter every game, the ball goes into the gutter with each of
     * the twenty rolls. (There are two rolls for each of the ten frames.) In
     * this case, the score for each roll is zero, as well as for the whole
     * game.
     *
     * return void
     */
    public function testGutterGame()
    {
        $this->_rollMany(20, 0);
        $this->assertEquals(0, $this->_game->score());
    }

    /**
     * With each of the twenty rolls, one pin is knocked down. In this case,
     * the score for each roll is one, and the score for the whole game will
     * be twenty.
     *
     * @return void
     */
    public function testAllOnes()
    {
        $this->_rollMany(20, 1);
        $this->assertEquals(20, $this->_game->score());
    }

    /**
     * Exactly one of the twenty rolls is a "spare". In this case, the first
     * frame is a spare (five pins are knocked down with each of the two
     * rolls), the first roll of the second frame knocks down three pins, and
     * all the other rolls knock down zero pins. The score should be:
     *
     *   1st frame: 5 + 5  + 3 (1st roll of 2nd frame) = 13
     *   2nd frame (first roll): 3 = 3
     *   Rest of rolls : 17 x 0 = 0
     *   TOTAL: 13 + 3 + 0 = 16
     *
     * @return void
     */
    public function testOneSpare()
    {
        $this->_rollSpare();
        $this->_game->roll(3);
        $this->_rollMany(17, 0);
        $this->assertEquals(16, $this->_game->score());
    }

    /**
     * Exactly one of the twenty rolls is a "strike". In this case, the first
     * frame is a strike (all ten pins are knocked down with the first roll),
     * the first roll of the second frame knocks down three pins, the second
     * roll of the second frame knocks down four pins, and all the other rolls
     * knock down zero pins. The score should be:
     *
     *   1st frame: 10  + 3 (1st of 2nd) + 4 (2nd of 2nd) = 17
     *   2nd frame: 3 + 4 = 7
     *   Rest of rolls : 16 x 0 = 0
     *   TOTAL: 17 + 7 + 0 = 24
     *
     * @return void
     */
    public function testOneStrike()
    {
        $this->_rollStrike();
        $this->_game->roll(3);
        $this->_game->roll(4);
        $this->_rollMany(16, 0);
        $this->assertEquals(24, $this->_game->score());
    }

    /**
     * Every roll is a "strike", which means twelve rolls are made, one for
     * each frame, and two extra rolls to supply bonus points for the tenth
     * roll. The eleventh and twelfth rolls count only once. Each frame will be
     * worht thirty points.
     *
     *   TOTAL: 30 x 10 = 300
     *
     * @return void
     */
    public function testPerfectGame()
    {
        $this->_rollMany(12, 10);
        $this->assertEquals(300, $this->_game->score());
    }
}
