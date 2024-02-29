<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_post_get(): void
    {
        $user = User::query()->create([
            'name' => 'Milos',
            'email' => 'test@email.com',
            'password' => '12345678'
        ]);

        $token = $user->createToken('access_token')->plainTextToken;

        $post = Post::query()->create([
            'user_id' => 1,
            'content' => 'test',
            'image_name' => 'test_img',
            'image_path' => 'test_path'
        ]);

        $response = $this->getJson('api/posts/1', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'post',
            ],
        ]);
    }

    public function test_post_create(): void
    {
        $user = User::query()->create([
            'name' => 'Milos',
            'email' => 'test2@email.com',
            'password' => '12345678'
        ]);

        $token = $user->createToken('access_token')->plainTextToken;


        $data = [
            'user_id' => 1,
            'content' => 'test 2',
            'image_name' => null,
            'image_path' => null
        ];

        $response = $this->postJson('api/posts', $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);


        $this->assertDatabaseHas('posts', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'post',
            ],
        ]);
    }

    public function test_post_update(): void
    {
        $user = User::query()->create([
            'name' => 'Milos',
            'email' => 'test2@email.com',
            'password' => '12345678'
        ]);

        $token = $user->createToken('access_token')->plainTextToken;

        $post = Post::query()->create([
            'user_id' => 1,
            'content' => 'test',
            'image_name' => null,
            'image_path' => null
        ]);

        $data = [
            'content' => 'test 3',
        ];

        $response = $this->patchJson('api/posts/' . $post->id,$data, [
            'Authorization' => 'Bearer ' . $token,
        ]);


        $this->assertDatabaseHas('posts', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'post',
            ],
        ]);
    }

    public function test_post_delete(): void
    {
        $user = User::query()->create([
            'name' => 'Milos',
            'email' => 'test5@email.com',
            'password' => '12345678'
        ]);

        $token = $user->createToken('access_token')->plainTextToken;

        $post = Post::query()->create([
            'user_id' => 1,
            'content' => 'test',
            'image_name' => null,
            'image_path' => null
        ]);

        $response = $this->deleteJson('api/posts/' . $post->id,[], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }
}
