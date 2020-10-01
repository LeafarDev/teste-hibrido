<?php
declare(strict_types=1);

namespace TesteHibridoApp\Controller;

use Illuminate\Validation\ValidationException;
use TesteHibridoApp\Helpers\Validator;
use TesteHibridoApp\Service\ClientService;
use Zend\Diactoros\ServerRequest;
use function TesteHibridoApp\Helpers\validateCpf;
use function TesteHibridoApp\Helpers\view;

class ClientController
{
    private ClientService $clientService;
    private Validator $validator;

    public function __construct(ClientService $clientService, Validator $validator)
    {
        $this->clientService = $clientService;
        $this->validator = $validator;
    }

    public function index()
    {
        return view('Clients/index.html.twig', ['clients' => $this->clientService->getAll()]);
    }

    public function create()
    {
        return view('Clients/create.html.twig', ['clients' => $this->clientService->getAll()]);
    }

    public function store(ServerRequest $request)
    {
        // validation
        $data = $request->getParsedBody();
        $rules = array(
            'name' => ['required'],
            'email' => ['required', 'email', function ($attribute, $value, $fail) {
                // check if already exists
                $client = $this->clientService->findByEmail($value);
                if ($client) {
                    $fail('Email already exists');
                }
            }],
            'cpf' => ['required', function ($attribute, $value, $fail) {
                // check cpf format
                if (!validateCpf($value)) {
                    $fail('Invalid CPF');
                }
            }, function ($attribute, $value, $fail) {
                // check if already exists
                $client = $this->clientService->findByCpf($value);
                if ($client) {
                    $fail('CPF already exists');
                }
            }],
            'phone' => ['max:20'],
        );
        // validations message
        $messages = array(
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.required' => 'Email is incorrect.',
            'cpf.required' => 'CPF is required',
            'phone.max' => 'Phone is too big');
        try {
            // call validation
            $this->validator->validateArray($data, $rules, $messages);
            // if data is ok, call service layer
            $result = $this->clientService->saveClient($data);
            if ($result) {
                return view('Clients/index.html.twig', ['clients' => $this->clientService->getAll(), 'success' => 'Client created successfully']);
            } else {
                return view('Clients/index.html.twig', ['clients' => $this->clientService->getAll(), 'failure' => 'Could not create client. Try again later']);
            }
        } catch (ValidationException $ex) {
            // if validation fail, return to form
            return view('Clients/create.html.twig', ['client' => $data, 'errors' => $ex->errors()]);
        }

    }

    public function edit($id)
    {
        return "/edit/" . $id;;
    }

    public function show($id)
    {
        return "/show/" . $id;
    }
}