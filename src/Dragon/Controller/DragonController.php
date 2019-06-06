<?php declare(strict_types=1);

namespace BoneMvc\Module\Dragon\Controller;

use BoneMvc\Module\Dragon\Collection\DragonCollection;
use BoneMvc\Module\Dragon\Entity\Dragon;
use BoneMvc\Module\Dragon\Form\DragonForm;
use BoneMvc\Module\Dragon\Service\DragonService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class DragonController
{
    /** @var DragonService $service */
    private $service;

    /**
     * @param DragonService $service
     */
    public function __construct(DragonService $service)
    {
        $this->service = $service;
    }

    /**
     * Controller.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexAction(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        throw new \Exception('argh');
        $response = new Response();
        $stream = new Stream('php://memory', 'r+');

        $dragons = $this->service->getRepository();

        if (isset($args['id'])) {
            $id = $args['id'];
            /** @var Dragon $dragon */
            $dragon = $this->service->getRepository()->find($id);
            $json = $dragon->toJson();
        } else {
            $dragons = new DragonCollection($dragons->findAll());
            $json = $dragons->toJson();
        }

        $stream->write($json);
        $response = $response->withBody($stream);

        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $post = $this->getJsonPost($request);
        $form = new DragonForm('create');
        $form->populate($post);
        if ($form->isValid()) {
            $data = $form->getValues();
            $dragon = $this->service->createFromArray($data);
            $this->service->saveDragon($dragon);

            return $this->jsonResponse($response, $dragon->toArray());
        } else {
            // handle errors
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     */
    public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getJsonPost(ServerRequestInterface $request): array
    {
        return json_decode($request->getParsedBody(), true);
    }

    /**
     * @param array $data
     * @return ResponseInterface $response
     */
    public function jsonResponse(ResponseInterface $response, array $data): ResponseInterface
    {
        $json = json_encode($data);
        // create proper $response later
        header('Content-Type: application/json');
        echo $json;
        exit;
    }
}