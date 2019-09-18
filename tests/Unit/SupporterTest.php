<?php

namespace Tests\Unit;

use App\Http\Support\Supporter;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupporterTest extends TestCase
{
    /** @test */
    public function human_readable_action_date_return_string()
    {
        $supporter = new Supporter();
        $postedDate = '2019-09-15 05:23:00';
        $updatedDate = '2019-09-15 07:30:12';

        $actionDate = $supporter->getHumanReadableActionDateAsString($postedDate, $updatedDate, Supporter::ANSWER_ACTION);

        $this->assertIsString($actionDate);
    }

    /** @test */
    public function human_readable_action_date_include_edited_date()
    {
        $supporter = new Supporter();
        $postedDate = '2019-09-15 05:23:00';
        $updatedDate = '2019-09-16 07:30:12';

        $actionDate = $supporter->getHumanReadableActionDateAsString($postedDate, $updatedDate, Supporter::ANSWER_ACTION);

        $this->assertStringContainsStringIgnoringCase('edited', $actionDate);
    }
}
