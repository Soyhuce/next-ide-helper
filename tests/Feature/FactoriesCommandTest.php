<?php declare(strict_types=1);

test('the command is successful', function (): void {
    config([
        'next-ide-helper.factories' => [
            'directories' => [$this->fixturePath()],
        ],
    ]);

    $this->artisan('next-ide-helper:factories')
        ->assertExitCode(0);

    $this->assertFileEquals(
        $this->expectedPath('PostFactory.stub'),
        $this->fixturePath('Factories/PostFactory.php')
    );

    $this->assertFileEquals(
        $this->expectedPath('UserFactory.stub'),
        $this->fixturePath('Factories/UserFactory.php')
    );

    $this->assertFileEquals(
        $this->expectedPath('CommentFactory.stub'),
        $this->fixturePath('Factories/CommentFactory.php')
    );
});
