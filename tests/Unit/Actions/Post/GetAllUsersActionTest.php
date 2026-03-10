<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Illuminate\Support\Collection;
use Modules\Post\Actions\GetAllUsersAction;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetAllUsersActionTest extends ActionTestCase
{
    protected function getExistingUserCount(): int
    {
        // Account for users created during setUp (like from seeders)
        return User::count();
    }

    public function testExecuteReturnsCollection(): void
    {
        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testExecuteReturnsAllUsers(): void
    {
        $existingCount = $this->getExistingUserCount();
        User::factory()->count(5)->create();

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $this->assertCount($existingCount + 5, $result);
    }

    public function testExecuteReturnsUserModels(): void
    {
        User::factory()->create(['name' => 'Test User']);

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $foundUser = $result->firstWhere('name', 'Test User');
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('Test User', $foundUser->name);
    }

    public function testExecuteReturnsUsersOrderedByIdDesc(): void
    {
        User::factory()->create(['name' => 'First User']);
        User::factory()->create(['name' => 'Second User']);
        User::factory()->create(['name' => 'Third User']);

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $names = $result->pluck('name')->toArray();
        // Users should be ordered by id descending (newest first)
        $this->assertContains('Third User', $names);
        $this->assertContains('First User', $names);
    }

    public function testExecuteReturnsUsersWithRoles(): void
    {
        User::factory()->create(['name' => 'User With Role']);

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $foundUser = $result->firstWhere('name', 'User With Role');
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('User With Role', $foundUser->name);
    }

    public function testExecuteReturnsUsersWithCorrectData(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $names = $result->pluck('name')->toArray();
        $this->assertContains('John Doe', $names);
        $this->assertContains('Jane Smith', $names);
        
        $emails = $result->pluck('email')->toArray();
        $this->assertContains('john@example.com', $emails);
        $this->assertContains('jane@example.com', $emails);
    }

    public function testExecuteReturnsUsersWithPostsRelation(): void
    {
        $user = User::factory()->create(['name' => 'User With Posts']);
        \Modules\Post\Models\Post::factory()
            ->count(2)
            ->create(['user_id' => $user->id]);

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $foundUser = $result->firstWhere('name', 'User With Posts');
        $this->assertInstanceOf(User::class, $foundUser);
    }
}
