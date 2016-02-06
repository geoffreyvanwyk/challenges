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
    }
}

/**
 * Responsible for finding the minimum number of dice-rolls to get from square 1
 * to square 100.
 */
class Board
{
    /**
     * @var int MIN_LADDERS Minimum number of ladders on a board.
     * @var int MAX_LADDERS Maximum number of ladders on a board.
     * @var int MIN_SNAKES Minimum number of snakes on a board.
     * @var int MAX_SNAKES Maximum number of snakes on a board.
     */
    const MIN_LADDERS = 1;
    const MAX_LADDERS = 15;
    const MIN_SNAKES = 1;
    const MAX_SNAKES = 15;

    /**
     * @var Ladder[] $ladders All the ladders on the board.
     * @var Snake[]  $snakes  All the snakes on the board.
     */
    private $ladders;
    private $snakes;

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

        if ($ladderCount < self::MIN_LADDERS or $ladderCount > self::MAX_LADDERS) {
            throw new InValidArgumentException("
                Number of ladders must be greater than or equal to
                {${Board::MIN_LADDERS}}, and less than or equal to
                {${Board::MAX_LADDERS}}.
            ");
        }

        foreach ($ladders as $ladder) {
            if (! is_object($ladder) or ! get_class($ladder) === 'Ladder') {
                throw new InvalidArgumentException('First argument must be an array of Ladder objects.');
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
            throw new InValidArgumentException("
                Number of snakes must be greater than or equal to
                {${Board::MIN_SNAKES}}, and less than or equal to
                {${Board::MAX_SNAKES}}.
            ");
        }

        foreach ($snakes as $snake) {
            if (! is_object($snake) or ! get_class($snake) === 'Snake') {
                throw new InvalidArgumentException('
                    First argument must be an array of Snake objects.
                ');
            }
        }

        $this->snakes = $snakes;
        return $this;
    }

    /**
     * Validates that a ladder does not start or end where a snake starts or stops.
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
                    throw new InvalidArgumentException('
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
        $this
            ->setLadders($ladders)
            ->setSnakes($snakes)
            ->validate();
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
    const MAX_TOP = 99;

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
     * @throws InvalidArgumentException
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

        throw new InvalidArgumentException('
            First argument must be an integer greater than or equal to
            {${Ladder::MIN_BOTTOM}}, and less than or equal to
            {${Ladder::MAX_BOTTOM}}.
        ');
    }

    /**
     * Setter for Ladder::top.
     *
     * @param int $top Number of the square where the ladder ends.
     *
     * @throws InvalidArgumentException
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

        throw new InvalidArgumentException('
            Second argument must be an integer greater than or equal to
            {${Ladder::MIN_END}}, and less than or equal to
            {${Ladder::MAX_END}}.
        ');
    }

    /**
     * Validates that the ladder's bottom is at a square with a smaller number than the
     * square where its top is..
     *
     * @throws InvalidArgumentException
     * @return Ladder
     */
    private function validate()
    {
        if ($this->bottom < $this->top) {
            return $this;
        }

        throw new InvalidArgumentException('
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
        $this
            ->setBottom($bottom)
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
    const MIN_TAIL = 2;
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
     * @throws InvalidArgumentException
     * @return Snake
     */
    private function setMouth($mouth)
    {
        if (is_int($mouth) and $mouth >= self::MIN_MOUTH and $mouth <= self::MAX_MOUTH) {
            $this->mouth = $mouth;
            return $this;
        }

        throw new InvalidArgumentException('
            First argument must be an integer greater than or equal to
            {${Snake::MIN_MOUTH}}, and less than or equal to {${Snake::MAX_MOUTH}}.
        ');
    }

    /**
     * Setter for Snake::tail.
     *
     * @throws InvalidArgumentException
     * @return Snake
     */
    private function setTail($tail)
    {
        if (is_int($tail) and $tail >= MIN_TAIL and $tail <= MAX_TAIL) {
            $this->tail = $tail;
            return $this;
        }

        throw new InvalidArgumentException('
            Second argument must be an integer greater than or equal to
            {${Snake::MIN_TAIL}} and less than or equal to {${Snake::MAX_TAIL}}.
        ');
    }

    /**
     * Validates that the snake's mouth is at a square with a larger number than the
     * square where its tail is.
     *
     * @throws InvalidArgumentException
     * @return Snake
     */
    private function validate()
    {
        if ($this->mouth > $this->tail) {
            return $this;
        }

        throw new InvalidArgumentException('
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
        $this
            ->setMouth($mouth)
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
