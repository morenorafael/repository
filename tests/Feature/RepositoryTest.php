<?php

namespace MorenoRafael\Repository\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use MorenoRafael\Repository\Models\User;
use MorenoRafael\Repository\Repositories\UserRepository;
use MorenoRafael\Repository\Tests\TestCase;

class RepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all()
    {
        // 1. Given
        $users = factory(User::class, 10)->create();

        // 1. When
        $repository = new UserRepository();

        // 1. Then
        $this->assertSame($users->first()->name, $repository->all()->first()->name);
    }

    /** @test */
    public function find()
    {
        // 1. Given
        $user = factory(User::class)->create();

        // 1. When
        $repository = new UserRepository();

        // 1. Then
        $this->assertSame($user->name, $repository->find($user->id)->name);
    }

    /** @test */
    public function create()
    {
        // 1. Given

        // 1. When
        $repository = new UserRepository();
        $user = $repository->create([
            'name' => 'Rafael Moreno',
            'email' => 'alejandro8924@gmail.com',
            'password' => Hash::make('secret'),
        ]);

        // 1. Then
        $this->assertSame(User::first()->name, $repository->find($user->id)->name);
        $this->assertSame($user->name, $repository->find($user->id)->name);
    }
}
