<?php
namespace Systemovich;

if (PHP_SAPI === 'cli') {
    $fh = fopen('php://stdin', 'r');
    fscanf($fh, "%d", $testCases);

    for ($a = 1; $a <= $testCases; $a++) {
        fscanf($fh, "%d", $ladderCount);
        $ladders = [];

        for ($b = 1; $b <= $ladderCount; $b++) {
            fscanf($fh, "%d %d", $bottom, $top);
            $ladders[] = new Ladder($bottom, $top);
        }

        fscanf($fh, "%d", $snakeCount);
        $snakes = [];

        for ($c = 1; $c <= $snakeCount; $c++) {
            fscanf($fh, "%d %d", $mouth, $tail);
            $snakes[] = new Snake($mouth, $tail);
        }

        $board = new Board($ladders, $snakes);
        echo $board->minimumDiceRolls() . PHP_EOL;
    }
}

/**
 * Responsible for finding the minimum number of dice-rolls to get from square 1
 * to square 100.
 */
class Board
{
    /**
     * const int MIN_POSITION Lowest number of a square on the board.
     * const int MAX_POSITION Highest number of a square on the board.
     */
    const MIN_POSITION = 1;
    const MAX_POSITION = 100;

    /**
     * @const int MIN_LADDERS Minimum number of ladders on a board.
     * @const int MAX_LADDERS Maximum number of ladders on a board.
     * @const int MIN_SNAKES Minimum number of snakes on a board.
     * @const int MAX_SNAKES Maximum number of snakes on a board.
     */
    const MIN_LADDERS = 1;
    const MAX_LADDERS = 15;
    const MIN_SNAKES = 1;
    const MAX_SNAKES = 15;

    /**
     * @const int MIN_DICE Minimum value of a dice roll.
     * @const int MAX_DICE Maximum value of a dice roll.
     */
    const MIN_DICE = 1;
    const MAX_DICE = 6;

    /**
     * @var Ladder[] $ladders All the ladders on the board.
     * @var Snake[]  $snakes  All the snakes on the board.
     */
    private $ladders;
    private $snakes;


    /**
     * Successive squares occupied by game piece after every dice roll.
     *
     * @var int[]
     */
    private $positions = [self::MIN_POSITION];

    /**
     * Setter for Board::ladders.
     *
     * @param Ladder[] $ladders All the ladders on the board.
     *
     * @return Board
     */
    private function setLadders(array $ladders)
    {
        $ladderCount = count($ladders);

        if ($ladderCount < self::MIN_LADDERS or
            $ladderCount > self::MAX_LADDERS
        ) {
            throw new InValidArgumentException(
                'Number of ladders must be greater than or equal to ' .
                self::MIN_LADDERS . ', and less than or equal to ' .
                self::MAX_LADDERS . '. Number of ladders given: ' .
                $ladderCount . '.'
            );
        }

        foreach ($ladders as $ladder) {
            if (! is_object($ladder) or ! get_class($ladder) === 'Ladder') {
                throw new \InvalidArgumentException(
                    'First argument must be an array of Ladder objects.'
                );
            }
        }

        $this->ladders = $ladders;
        return $this;
    }

    /**
     * Setter for Board::snakes.
     *
     * @param Snakes[] $snakes All the snakes on the board.
     *
     * @return Board
     */
    private function setSnakes(array $snakes)
    {
        $snakeCount = count($snakes);

        if ($snakeCount < self::MIN_SNAKES or $snakeCount > self::MAX_SNAKES) {
            throw new InValidArgumentException(
                'Number of snakes must be greater than or equal to ' .
                self::MIN_SNAKES . ', and less than or equal to ' .
                self::MAX_SNAKES . '. Number of snakes give: ' .
                $snakeCount . '.'
            );
        }

        foreach ($snakes as $snake) {
            if (! is_object($snake) or ! get_class($snake) === 'Snake') {
                throw new \InvalidArgumentException('
                    First argument must be an array of Snake objects.
                ');
            }
        }

        $this->snakes = $snakes;
        return $this;
    }

    /**
     * Validates that a ladder does not start or end where a snake starts or
     * stops.
     *
     * @return Board
     */
    private function validate()
    {
        foreach ($this->ladders as $ladder) {
            foreach ($this->snakes as $snake) {
                if ($ladder->bottom() === $snake->mouth() or
                    $ladder->bottom() === $snake->tail() or
                    $ladder->top() === $snake->mouth() or
                    $ladder->top() === $snake->tail()
                ) {
                    throw new \InvalidArgumentException('
                        A ladder cannot start or end at the same square where a
                        snake starts or ends.
                    ');
                }
            }
        }

        return $this;
    }

    /**
     * Initialises and validates all Board attributes.
     *
     * @param Ladder[] $ladders All the ladders on the board.
     * @param Snakes[] $snakes  All the snakes on the board.
     *
     * @return void
     */
    public function __construct(array $ladders, array $snakes)
    {
        $this->setLadders($ladders)
             ->setSnakes($snakes)
             ->validate()
             ->play();
    }

    /**
     * Number of square on which game piece currently sits.
     *
     * @return int
     */
    private function position()
    {
        return $this->positions[count($this->positions) - 1];
    }

    /**
     * Ladder whose bottom is the farthest from the game piece, of all the ladders
     * whose bottoms are no more than six squares away from the game piece..
     *
     * @param int $position Position of game piece.
     *
     * @return Ladder|null
     */
    private function nextLadder($position)
    {
        static $ladders = [];

        if (isset($ladders[$position])) {
            return $ladders[$position];
        }

        $maxDistance = null;
        $nearest = null;

        foreach ($this->ladders as $ladder) {
            $distance = $ladder->b9ottom() - $this->position();

            if ($distance >= self::MIN_DICE  and
                $distance <= self::MAX_DICE
            ) {
                if (is_null($maxDistance) or $distance > $maxDistance) {
                    $maxDistance = $distance;
                    $nearest = $ladder;
                }
            }
        }

        $ladders[$position] = $nearest;

        return $nearest;
    }

    /**
     * Returns true if there is a Snake exactly 6 squares ahead of the game
     * piece.
     *
     * @param int $position Position of game piece.
     *
     * @return bool
     */
    private function isSnakeAhead($position)
    {
        static $results = [];

        if (isset($results[$position])) {
            return $results[$position];
        }

        foreach ($this->snakes as $snake) {
            $distance = $snake->mouth() - $this->position();

            if ($distance === self::MAX_DICE) {
                $results[$position] = true;
                return true;
            }
        }

        $results[$position] = false;

        return false;
    }

    /**
     * Moves the game piece to a new position while obeying the rules.
     *
     * @return void
     */
    private function rollDice()
    {
        if ($this->nextLadder($this->position())) {
            $this->positions[] = $this->nextLadder($this->position())->top();
        } elseif ($this->isSnakeAhead($this->position())) {
            $this->positions[] = $this->position() + self::MAX_DICE - 1;
        } elseif (self::MAX_POSITION - $this->position() <= self::MAX_DICE) {
            $this->positions[] = self::MAX_POSITION;
        } else {
            $this->positions[] = $this->position() + self::MAX_DICE;
        }
    }

    /**
     * Moves the game piece to the last square.
     *
     * @return void
     */
    private function play()
    {
        while ($this->position() < self::MAX_POSITION) {
            $this->rollDice();
        }
    }

    /**
     * Minimum number of dice rolls required to move game piece from square 1 to
     * square 100.
     *
     * @return int
     */
    public function minimumDiceRolls()
    {
        /* Every position, except the first, was reached as a result of a dice
         * roll; therefore, the number of dice rolls equal the number of
         * positions minus one.
         */
        return count($this->positions) - 1;
    }
}

/**
 * Responsible for representing valid start and end squares for a ladder on the
 * board.
 */
class Ladder
{
    /**
     * @var int MIN_BOTTOM Minimum square where a ladder can start.
     * @var int MAX_BOTTOM Maximum square where a ladder can start.
     * @var int MIN_TOP    Minimum square where a ladder can end.
     * @var int MAX_TOP    Maximum square where a ladder can end.
     */
    const MIN_BOTTOM = 2;
    const MAX_BOTTOM = 90;
    const MIN_TOP = 11;
    const MAX_TOP = 100;

    /**
     * @var int $bottom Number of the square where the ladder starts.
     * @var int $top    Number of the square where the ladder ends.
     */
    private $bottom;
    private $top;

    /**
     * Setter for Ladder::bottom.
     *
     * @param int $bottom Number of the square where the ladder starts.
     *
     * @throws \InvalidArgumentException
     * @return Ladder
     */
    private function setBottom($bottom)
    {
        if (is_int($bottom) and
            $bottom >= self::MIN_BOTTOM and
            $bottom <= self::MAX_BOTTOM
        ) {
            $this->bottom = $bottom;
            return $this;
        }

        throw new \InvalidArgumentException(
            'First argument must be an integer greater than or equal to ' .
            self::MIN_BOTTOM . ', and less than or equal to ' .
            self::MAX_BOTTOM . '. Argument given: ' . $bottom . '.'
        );
    }

    /**
     * Setter for Ladder::top.
     *
     * @param int $top Number of the square where the ladder ends.
     *
     * @throws \InvalidArgumentException
     * @return Ladder
     */
    private function setTop($top)
    {
        if (is_int($top) and
            $top >= self::MIN_TOP and
            $top <= self::MAX_TOP
        ) {
            $this->top = $top;
            return $this;
        }

        throw new \InvalidArgumentException(
            'Second argument must be an integer greater than or equal to ' .
            self::MIN_TOP . ', and less than or equal to ' .
            self::MAX_TOP . '. Argument given: ' . $top . '.'
        );
    }

    /**
     * Validates that the ladder's bottom is at a square with a smaller number than the
     * square where its top is..
     *
     * @throws \InvalidArgumentException
     * @return Ladder
     */
    private function validate()
    {
        if ($this->bottom < $this->top) {
            return $this;
        }

        throw new \InvalidArgumentException('
            First argument must be less than second argument.
        ');
    }

    /**
     * Initialises and validates all Ladder attributes.
     *
     * @param int $bottom Number of square where ladder starts.
     * @param int $top    Number of square where ladder ends.
     *
     * @return void
     */
    public function __construct($bottom, $top)
    {
        $this->setBottom($bottom)
             ->setTop($top)
             ->validate();
    }

    /**
     * Getter for Ladder::bottom.
     *
     * @return int
     */
    public function bottom()
    {
        return $this->bottom;
    }

    /**
     * Getter for Ladder::top.
     *
     * @return int
     */
    public function top()
    {
        return $this->top;
    }
}

/**
 * Responsible for representing valid start and end squares for a snake on the
 * board.
 */
class Snake
{
    /**
     * @var int MIN_MOUTH Minimum square where a snake can start.
     * @var int MAX_MOUTH Maximum square where a snake can start.
     * @var int MIN_TAIL Minimum square where a snake can end.
     * @var int MAX_TAIL Maximum square where a snake can end.
     */
    const MIN_MOUTH = 11;
    const MAX_MOUTH = 99;
    const MIN_TAIL = 1;
    const MAX_TAIL = 90;

    /**
     * @var int $mouth Number of the square where the snake starts.
     * @var int $tail  Number of the square where the snake ends.
     */
    private $mouth;
    private $tail;

    /**
     * Setter for Snake::mouth.
     *
     * @throws \InvalidArgumentException
     * @return Snake
     */
    private function setMouth($mouth)
    {
        if (is_int($mouth) and $mouth >= self::MIN_MOUTH and $mouth <= self::MAX_MOUTH) {
            $this->mouth = $mouth;
            return $this;
        }

        throw new \InvalidArgumentException(
            'First argument must be an integer greater than or equal to ' .
            self::MIN_MOUTH . ', and less than or equal to ' .
            self::MAX_MOUTH . '. Argument given: ' . $mouth . '.'
        );
    }

    /**
     * Setter for Snake::tail.
     *
     * @throws \InvalidArgumentException
     * @return Snake
     */
    private function setTail($tail)
    {
        if (is_int($tail) and
            $tail >= self::MIN_TAIL and
            $tail <= self::MAX_TAIL
        ) {
            $this->tail = $tail;
            return $this;
        }

        throw new \InvalidArgumentException(
            'Second argument must be an integer greater than or equal to ' .
            self::MIN_TAIL . ', and less than or equal to ' .
            self::MAX_TAIL . '. Argument given: ' . $tail . '.'
        );
    }

    /**
     * Validates that the snake's mouth is at a square with a larger number than the
     * square where its tail is.
     *
     * @throws \InvalidArgumentException
     * @return Snake
     */
    private function validate()
    {
        if ($this->mouth > $this->tail) {
            return $this;
        }

        throw new \InvalidArgumentException('
            First argument must be less than
            second argument.
        ');
    }

    /**
     * Initialises and validates all Snake attributes.
     *
     * @return void
     */
    public function __construct($mouth, $tail)
    {
        $this->setMouth($mouth)
             ->setTail($tail)
             ->validate();
    }

    /**
     * Getter for Snake::mouth.
     *
     * @return int
     */
    public function mouth()
    {
        return $this->mouth;
    }

    /**
     * Getter for Snake::tail.
     *
     * @return int
     */
    public function tail()
    {
        return $this->tail;
    }
}
