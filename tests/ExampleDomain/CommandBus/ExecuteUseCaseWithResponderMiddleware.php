<?php declare(strict_types=1);

namespace ExampleDomain\CommandBus;

use League\Tactician\Middleware;

final class ExecuteUseCaseWithResponderMiddleware implements Middleware
{
    private $useCases = [];

    public function registerUseCase($commandName, $useCase, $responder)
    {
        $this->useCases[$commandName]['useCase'] = $useCase;
        $this->useCases[$commandName]['responder'] = $responder;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        $commandClassName = get_class($command);

        if (! isset($this->useCases[$commandClassName])) {
            return;
        }

        $useCase = $this->useCases[$commandClassName]['useCase'];
        $responder = $this->useCases[$commandClassName]['responder'];

        $useCase->execute($command, $responder);
    }
}

