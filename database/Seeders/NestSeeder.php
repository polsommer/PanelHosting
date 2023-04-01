<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Jexactyl\Services\Nests\NestCreationService;
use Jexactyl\Contracts\Repository\NestRepositoryInterface;

class NestSeeder extends Seeder
{
    /**
     * @var \Jexactyl\Services\Nests\NestCreationService
     */
    private $creationService;

    /**
     * @var \Jexactyl\Contracts\Repository\NestRepositoryInterface
     */
    private $repository;

    /**
     * NestSeeder constructor.
     */
    public function __construct(
        NestCreationService $creationService,
        NestRepositoryInterface $repository
    ) {
        $this->creationService = $creationService;
        $this->repository = $repository;
    }

    /**
     * Run the seeder to add missing nests to the Panel.
     *
     * @throws \Jexactyl\Exceptions\Model\DataValidationException
     */
    public function run()
    {
        $items = $this->repository->findWhere([
            'author' => 'support@jexactyl.com',
        ])->keyBy('name')->toArray();

        $this->createMinecraftNest(array_get($items, 'Minecraft'));
        $this->createSourceEngineNest(array_get($items, 'Source Engine'));
        $this->createVoiceServersNest(array_get($items, 'Voice Servers'));
        $this->createRustNest(array_get($items, 'Rust'));
    }

    /**
     * Create the Minecraft nest to be used later on.
     *
     * @throws \Jexactyl\Exceptions\Model\DataValidationException
     */
    private function createMinecraftNest(array $nest = null)
    {
        if (is_null($nest)) {
            $this->creationService->handle([
                'private' => false,
                'name' => 'Minecraft',
                'description' => 'Minecraft - the classic game from Mojang. With support for Vanilla MC, Spigot, and many others',
            ], 'support@jexactyl.com');
        }
    }

    /**
     * Create the Source Engine Games nest to be used later on.
     *
     * @throws \Jexactyl\Exceptions\Model\DataValidationException
     */
    private function createSourceEngineNest(array $nest = null)
    {
        if (is_null($nest)) {
            $this->creationService->handle([
                'private' => false,
                'name' => 'Source Engine',
                'description' => 'Includes support for most Source Dedicated Server games',
            ], 'support@jexactyl.com');
        }
    }

    /**
     * Create the Voice Servers nest to be used later on.
     *
     * @throws \Jexactyl\Exceptions\Model\DataValidationException
     */
    private function createVoiceServersNest(array $nest = null)
    {
        if (is_null($nest)) {
            $this->creationService->handle([
                'private' => false,
                'name' => 'Voice Servers',
                'description' => 'Voice servers such as Mumble and Teamspeak 3',
            ], 'support@jexactyl.com');
        }
    }

    /**
     * Create the Rust nest to be used later on.
     *
     * @throws \Jexactyl\Exceptions\Model\DataValidationException
     */
    private function createRustNest(array $nest = null)
    {
        if (is_null($nest)) {
            $this->creationService->handle([
                'private' => false,
                'name' => 'Rust',
                'description' => 'Rust - A game where you must fight to survive',
            ], 'support@jexactyl.com');
        }
    }
}
