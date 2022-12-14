<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\onion;

use Closure;
use RuntimeException;
use serve\http\request\Request;
use serve\http\response\Response;

use function array_push;
use function array_shift;
use function array_unshift;

/**
 * Array access trait.
 *
 * @author Joe J. Howard
 */
class Onion
{
    /**
     * Onion layers of middleware.
     *
     * @var array
     */
    private $layers = [];

    /**
     * Are we peeling a layer ?
     *
     * @var bool
     */
    private $locked = false;

    /**
     * Request object.
     *
     * @var \serve\http\request\Request
     */
    private $request;

    /**
     * Response object.
     *
     * @var \serve\http\response\Response
     */
    private $response;

    /**
     * Constructor.
     *
     * @param \serve\http\request\Request   $request  Request object
     * @param \serve\http\response\Response $response Response object
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;

        $this->response = $response;
    }

    /**
     * Add a layer to the onion.
     *
     * @param  mixed            $callback   Callback when layer is peeled
     * @param  mixed            $parameters Arguments to apply to callback
     * @param  bool             $inner      Add layer to the inner most layer (optional) (default false)\
     * @throws RuntimeException If the onion is currently being peeled
     */
    public function addLayer($callback, $parameters = null, bool $inner = false)
    {
        if ($this->locked)
        {
            throw new RuntimeException('Middleware can’t be added once the onion is being peeled');
        }

        $layer = new Middleware($callback, $parameters);

        return $inner ? array_unshift($this->layers, $layer) : array_push($this->layers, $layer);
    }

    /**
     * Peel the onion.
     */
    public function peel(): void
    {
        $this->peelLayer();
    }

    /**
     * Peel The next layer.
     */
    private function peelLayer(): void
    {
        if (!empty($this->layers))
        {
            $layer = array_shift($this->layers);

            $this->locked = true;

            $next = $this->getNextLayer();

            $layer->execute($this->request, $this->response, $next);

            $this->locked = false;
        }
    }

    /**
     * Return a closure for executing the next middleware layer.
     *
     * @throws \serve\http\response\exceptions\NotFoundException If the onion is finished peeling and the response is a 404
     * @return Closure
     */
    private function getNextLayer(): Closure
    {
        if (!empty($this->layers))
        {
            return function (): void
            {
                $this->peelLayer();
            };
        }

        return function (): void
        {
            $response = $this->peeled();

            if ($response->status()->get() === 404)
            {
                $this->response->notFound();
            }
        };
    }

    /**
     * When the onion is completely peeled return the response.
     *
     * @throws \serve\http\response\exceptions\NotFoundException
     */
    public function peeled(): Response
    {
        return $this->response;
    }

    /**
     * Get middleware layers.
     */
    public function layers(): array
    {
        return $this->layers;
    }
}
