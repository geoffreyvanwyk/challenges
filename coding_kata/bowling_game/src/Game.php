<?php

namespace BowlingGame;

class Game
{
    /**
     * Number of pins knocked down with each roll.
     *
     * @var array|int[]
     */
    private $_rolls;

    /**
     * Index in Game::_rolls of the current roll.
     *
     * @var int
     */
    private $_currentRoll = 0;

    /**
     * Initialises attributes, then returns an instance of the class.
     *
     * @return Game
     */
    public function __construct()
    {
        $this->_rolls = array_fill(0, 21, 0);
    }

    /**
     * Called each time the player rolls a ball. Records the number of pins
     * knocked down with each roll.
     *
     * @param int $pins Number of pins knocked down.
     *
     * @return void
     */
    public function roll($pins)
    {
        $this->_rolls[$this->_currentRoll++] = $pins;
    }

    /**
     * Called only at the very end of the game. Returns total score of that
     * game.
     *
     * @return int
     */
    public function score()
    {
        $score = 0;
        $frameIndex = 0;

        for ($frame = 0; $frame < 10; $frame++) {
            if ($this->_isStrike($frameIndex)) {
                $score += 10 + $this->_strikeBonus($frameIndex);
                $frameIndex++;
            } elseif ($this->_isSpare($frameIndex)) {
                $score += 10 + $this->_spareBonus($frameIndex);
                $frameIndex += 2;
            } else {
                $score += $this->_sumOfRollsInFrame($frameIndex);
                $frameIndex += 2;
            }
        }

        return $score;
    }

    /**
     * Sum of the number of pins knocked down by each roll of a frame.
     *
     * @param int $frameIndex Index within Game::_rolls of first roll of frame.
     *
     * @return int
     */
    private function _sumOfRollsInFrame($frameIndex)
    {
        return $this->_rolls[$frameIndex] + $this->_rolls[$frameIndex + 1];
    }

    /**
     * Additional points awarded for a spare (all pins knocked down with two
     * rolls). Equals the number of pins knocked down by first roll of next
     * frame.
     *
     * @param int $frameIndex Index within Game::_rolls of first roll of frame.
     *
     * @return int
     */
    private function _spareBonus($frameIndex)
    {
        return $this->_rolls[$frameIndex + 2];
    }

    /**
     * Additional points awarded for a strike (all pins knocked down with one
     * roll). Equals the number of pins knocked down by both rolls of next
     * frame.
     *
     * @param int $frameIndex Index within Game::_rolls of first roll of frame.
     *
     * @return int
     */
    private function _strikeBonus($frameIndex)
    {
        return $this->_rolls[$frameIndex + 1] + $this->_rolls[$frameIndex + 2];
    }

    /**
     * Returns true if a frame is a spare.
     *
     * @return boolean
     */
    private function _isSpare($frameIndex)
    {
        return $this->_rolls[$frameIndex] +
               $this->_rolls[$frameIndex + 1] === 10;
    }

    /**
     * Returns true if a frame is a strike.
     *
     * @return boolean
     */
    private function _isStrike($frameIndex)
    {
        return $this->_rolls[$frameIndex] === 10;
    }
}
