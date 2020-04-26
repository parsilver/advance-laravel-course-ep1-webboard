<?php

namespace Tests\Feature;

use App\Topic;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TopicTest extends TestCase
{
    use DatabaseTransactions;

    public function testMustAuthenticatedBeforeCreate()
    {
        $response = $this->postJson('api/v1/topics');

        $response->assertStatus(401);
    }

    public function testShouldInvalidParameterIfNeedCreate()
    {
        $user = factory(User::class)->create();

        $this->be($user, 'api');

        $response = $this->postJson('api/v1/topics');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name'
        ]);
    }


    public function testShouldCreateSuccess()
    {
        $user = factory(User::class)->create();

        $this->be($user, 'api');

        $response = $this->postJson('api/v1/topics', [
            'name' => 'thisisnameoftopic',
            'description' => 'thisisdescription'
        ]);

        $response->dump();

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'created_at'
            ]
        ]);

        $topic = Topic::find($response->json('data.id'));

        // Owner of current topic must be creator
        $this->assertTrue($topic->user->is($user));

    }
}
