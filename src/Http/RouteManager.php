<?php
declare(strict_types=1);

namespace TesteHibridoApp\Http;

use DI\Container;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use MiladRahimi\PhpRouter\Router;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequest;

class RouteManager
{
    public function __invoke(Container $container)
    {
        $clientController = $container->get('TesteHibridoApp\Controller\ClientController');
        $router = new Router();


        $router->get('/', function () {
            return '<p>This is homepage!</p>';
        });

        $router->get('/clients', function () use ($clientController) {
            return $clientController->index();
        });

        $router->get('/clients/{id}', function ($id) use ($clientController) {
            return $clientController->show($id);
        });
        $router->get('/clients/create', function () use ($clientController) {
            return $clientController->create();
        });

        $router->post('/clients', function (ServerRequest $request) use ($clientController) {
            return $clientController->store($request);
        });

        $router->get('/clients/{id}/edit', function ($id) use ($clientController) {
            return $clientController->edit($id);
        });

        try {
            $router->dispatch();
        } catch (RouteNotFoundException $e) {
            $router->getPublisher()->publish(new HtmlResponse('Not found.', 404));
        } catch (Throwable $e) {
            // Log and report...
            print_r($e->getMessage());
            $router->getPublisher()->publish(new HtmlResponse('Internal error.', 500));
        }
    }
}