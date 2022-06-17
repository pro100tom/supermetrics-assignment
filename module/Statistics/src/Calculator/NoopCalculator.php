<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use DivisionByZeroError;
use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class NoopCalculator extends AbstractCalculator
{
    protected const UNITS = 'posts';

    /**
     * @var array
     */
    private array $posts = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $this->posts[] = $postTo;
    }

    /**
     * @inheritDoc
     * @throws DivisionByZeroError
     */
    protected function doCalculate(): StatisticsTo
    {
        $authorIds = array_map(function ($post) { return $post->getAuthorId(); }, $this->posts);
        $postCountPerUser = array_count_values($authorIds);
        $postCountPerUserAverage = array_sum($postCountPerUser) / count($postCountPerUser);

        return (new StatisticsTo())->setValue(round($postCountPerUserAverage, 2));
    }
}
