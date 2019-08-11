<?php

namespace Tests\Unit;

use App\UserAvatar;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAvatarTest extends TestCase
{
    /** @test */
    public function generate_default_user_avatar_func_return_user_avatar_object_with_correct_key_default_avatar_url()
    {
        $userAvatar = (new UserAvatar())->generateDefaultUserAvatar();

        $this->assertNotNull($userAvatar);
        $this->assertInstanceOf(UserAvatar::class, $userAvatar);
        $this->assertContains('\svg\default_avatars\\', $userAvatar->default_avatar_url);
        $this->assertRegExp('/^.*\.svg$/', $userAvatar->default_avatar_url);
    }
}
