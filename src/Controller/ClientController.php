<?php
declare(strict_types=1);

namespace TesteHibridoApp\Controller;

use function TesteHibridoApp\Helpers\view;

class ClientController
{
    public function __construct() {
    }

    public function index()
    {
        return "/clients";
    }

    public function show($id)
    {
        return "/show/".$id;
    }
}