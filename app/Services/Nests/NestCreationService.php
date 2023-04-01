<?php

namespace Jexactyl\Services\Nests;

use Ramsey\Uuid\Uuid;
use Jexactyl\Models\Nest;
use Jexactyl\Contracts\Repository\NestRepositoryInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class NestCreationService
{
    /**
     * NestCreationService constructor.
     */
    public function __construct(private ConfigRepository $config, private NestRepositoryInterface $repository)
    {
    }

    /**
     * Create a new nest on the system.
     *
     * @throws \Jexactyl\Exceptions\Model\DataValidationException
     */
    public function handle(array $data, string $author = null): Nest
    {
        return $this->repository->create([
            'uuid' => Uuid::uuid4()->toString(),
            'author' => $author ?? $this->config->get('Jexactyl.service.author'),
            'name' => array_get($data, 'name'),
            'description' => array_get($data, 'description'),
            'private' => array_get($data, 'private'),
        ], true, true);
    }
}
