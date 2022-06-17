<?php

declare(strict_types = 1);

namespace Tests\unit;

use DateTime;
use DivisionByZeroError;
use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\NoopCalculator;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;

class NoopCalculatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testCalculateValue(): void
    {
        $calculator = new NoopCalculator();
        $params = new ParamsTo();
        $params->setStartDate(new DateTime('-1 day'));
        $params->setEndDate(new DateTime('+1 day'));
        $params->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER);
        $calculator->setParameters($params);

        $data = [
            ['author_id' => 'one', 'text' => 'dummy text'],
            ['author_id' => 'one', 'text' => 'another dummy text'],
            ['author_id' => 'two', 'text' => 'dummy text again'],
            ['author_id' => 'three', 'text' => 'still dummy text'],
            ['author_id' => 'four', 'text' => 'something smart'],
        ];

        foreach ($data as $datum) {
            $socialPostTo = new SocialPostTo();
            $socialPostTo->setAuthorId($datum['author_id']);
            $socialPostTo->setText($datum['text']);
            $socialPostTo->setDate(new DateTime());
            $calculator->accumulateData($socialPostTo);
        }

        $this->assertEquals(round(5 / 4, 2), $calculator->calculate()->getValue());
    }

    /**
     * @return void
     */
    public function testCalculateThrowsDivisionByZeroException(): void
    {
        $this->expectException(DivisionByZeroError::class);

        $calculator = new NoopCalculator();
        $params = new ParamsTo();
        $params->setStartDate(new DateTime('-1 day'));
        $params->setEndDate(new DateTime('+1 day'));
        $params->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER);
        $calculator->setParameters($params);

        $calculator->calculate();
    }
}
