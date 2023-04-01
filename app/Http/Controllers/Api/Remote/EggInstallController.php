<?php

namespace Jexactyl\Http\Controllers\Api\Remote;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Services\Servers\EnvironmentService;
use Jexactyl\Contracts\Repository\ServerRepositoryInterface;

class EggInstallController extends Controller
{
    /**
     * EggInstallController constructor.
     */
    public function __construct(private EnvironmentService $environment, private ServerRepositoryInterface $repository)
    {
    }

    /**
     * Handle request to get script and installation information for a server
     * that is being created on the node.
     *
     * @throws \Jexactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function index(Request $request, string $uuid): JsonResponse
    {
        $node = $request->attributes->get('node');

        /** @var \Jexactyl\Models\Server $server */
        $server = $this->repository->findFirstWhere([
            ['uuid', '=', $uuid],
            ['node_id', '=', $node->id],
        ]);

        $this->repository->loadEggRelations($server);
        $egg = $server->getRelation('egg');

        return response()->json([
            'scripts' => [
                'install' => !$egg->copy_script_install ? null : str_replace(["\r\n", "\n", "\r"], "\n", $egg->copy_script_install),
                'privileged' => $egg->script_is_privileged,
            ],
            'config' => [
                'container' => $egg->copy_script_container,
                'entry' => $egg->copy_script_entry,
            ],
            'env' => $this->environment->handle($server),
        ]);
    }
}
