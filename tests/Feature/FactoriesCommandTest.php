<?php declare(strict_types=1);

test('the command is successful', function (): void {
    config([
        'next-ide-helper.factories' => [
            'directories' => [$this->fixturePath()],
        ],
    ]);

    $this->artisan('next-ide-helper:factories')
        ->assertExitCode(0);

    expect($this->fixtureFile('Factories/PostFactory.php'))->toMatchSnapshot();

    expect($this->fixtureFile('Factories/UserFactory.php'))->toMatchSnapshot();

    expect($this->fixtureFile('Factories/CommentFactory.php'))->toMatchSnapshot();
});
