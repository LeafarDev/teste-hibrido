<?php
declare(strict_types=1);

namespace TesteHibridoApp\Controller;

use function TesteHibridoApp\Helpers\view;

class ClientController
{
    public function __construct()
    {
    }

    public function index()
    {
        #$layout = $twig->load('some_layout_template.twig');
        return view('Clients/index.html.twig', ['name' => 'John Doe',
            'occupation' => 'gardener']);
    }

    public function show($id)
    {
        return "/show/" . $id;
    }
}