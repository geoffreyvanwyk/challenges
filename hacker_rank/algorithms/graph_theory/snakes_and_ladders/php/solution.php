<?php

class Board {
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
     * @var Snake[] $snakes  All the snakes on the board.
     */
    private $ladders;
    private $snakes;

    /**
     * Setter for Board::ladders.
     *
     * @param Ladder[] All the ladders on the board.
     *
     * @return Board
     */
    private function setLadders(array $ladders) {
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
     * @param Snakes[] All the snakes on the board.
     *
     * @return Board
     */
    private function setSnakes(array $snakes) {
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
     * Validates that a ladder does not start or end where a snake start or stops.
     *
     * @return Board
     */
    private function validate() {
        foreach ($this->ladders as $ladder) {
            foreach ($this->snakes as $snake) {
                if ($ladder->start() === $snake->start() or
                    $ladder->start() === $snake->end() or
                    $ladder->end() === $snake->start() or
                    $ladder->end() === $snake->end()
                ){
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
     * @param Ladder[] All the ladders on the board.
     * @param Snakes[] All the snakes on the board.
     *
     * @return void
     */
    public function __construct(array $ladders, array $snakes) {
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
class Ladder {
    /**
     * @var int MIN_START Minimum square where a ladder can start.
     * @var int MAX_START Maximum square where a ladder can start.
     * @var int MIN_END Minimum square where a ladder can end.
     * @var int MAX_END Maximum square where a ladder can end.
     */
    const MIN_START = 2;
    const MAX_START = 90;
    const MIN_END = 11;
    const MAX_END = 99;

    /**
     * @var int $start Number of the square where the ladder starts.
     * @var int $end   Number of the square where the ladder ends.
     */
    private $start;
    private $end;

    /**
     * Setter for Ladder::start.

     * @param int $start Number of the square where the ladder starts.
     * @throws InvalidArgumentException
     * @return Ladder
     */
    private function setStart($start) {
        if (is_int($start) and
            $start >= self::MIN_START and
            $start <= self::MAX_START
        ){
            $this->start = $start;
            return $this;
        }

        throw new InvalidArgumentException('
            First argument must be an integer greater than or equal to
            {${Ladder::MIN_START}}, and less than or equal to
            {${Ladder::MAX_START}}.
        ');
    }

    /**
     * Setter for Ladder::end.
     *
     * @param int $end Number of the square where the ladder ends.
     * @throws InvalidArgumentException
     * @return Ladder
     */
    private function setEnd($end) {
        if (is_int($end) and
            $end >= self::MIN_END and
            $end <= self::MAX_END
        ){
            $this->end = $end;
            return $this;
        }

        throw new InvalidArgumentException('
            Second argument must be an integer greater than or equal to
            {${Ladder::MIN_END}}, and less than or equal to
            {${Ladder::MAX_END}}.
        ');
    }

    /**
     * Validates that the ladder starts at a square with a smaller number than the
     * square where it ends.
     *
     * @throws InvalidArgumentException
     * @return Ladder
     */
    private function validate() {
        if ($this->start < $this->end) {
            return $this;
        }

        throw new InvalidArgumentException('
            First argument must be less than second argument.
        ');
    }

    /**
     * Initialises and validates all Ladder attributes.
     *
     * @return void
     */
    public function __construct($start, $end) {
        $this
            ->setStart($start)
            ->setEnd($end)
            ->validate();
    }

    /**
     * Getter for Ladder::start.
     *
     * @return int
     */
    public function start() {
        return $this->start;
    }

    /**
     * Getter for Ladder::end.
     *
     * @return int
     */
    public function end() {
        return $this->end;
    }
}

/**
 * Responsible for representing valid start and end squares for a snake on the
 * board.
 */
class Snake {
    /**
     * @var int MIN_START Minimum square where a snake can start.
     * @var int MAX_START Maximum square where a snake can start.
     * @var int MIN_END Minimum square where a snake can end.
     * @var int MAX_END Maximum square where a snake can end.
     */
    const MIN_START = 2;
    const MAX_START = 90;
    const MIN_END = 11;
    const MAX_END = 99;

    /**
     * @var int Number of the square where the snake starts.
     * @var int $end Number of the square where the snake ends.
     */
    private $start;
    private $end;

    /**
     * Setter for Snake::start.
     *
     * @throws InvalidArgumentException
     * @return Snake
     */
    private function setStart($start) {
        if (is_int($start) and $start >= self::MIN_END and $start <= self::MAX_END) {
            $this->start = $start;
            return $this;
        }

        throw new InvalidArgumentException('
            First argument must be an integer greater than or equal to
            {${Snake::MIN_END}}, and less than or equal to {${Snake::MAX_END}}.
        ');
    }

    /**
     * Setter for Snake::end.
     *
     * @throws InvalidArgumentException
     * @return Snake
     */
    private function setEnd($end) {
        if (is_int($end) and $end >= 2 and $end <= 90) {
            $this->end = $end;
            return $this;
        }

        throw new InvalidArgumentException('
            Second argument must be an integer greater than or equal to 2, and
            less than or equal to 90.
        ');
    }

    /**
     * Validates that the snake starts at a square with a larger number than the
     * square where it ends.
     *
     * @throws InvalidArgumentException
     * @return Snake
     */
    private function validate() {
        if ($this->start > $this->end) {
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
    public function __construct($start, $end) {
        $this
            ->setStart($start)
            ->setEnd($end)
            ->validate();
    }

    /**
     * Getter for Snake::start.
     *
     * @return int
     */
    public function start() {
        return $this->start;
    }

    /**
     * Getter for Snake::end.
     *
     * @return int
     */
    public function end() {
        return $this->end;
    }
}