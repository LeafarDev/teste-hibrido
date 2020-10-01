<?php
declare(strict_types=1);

namespace TesteHibridoApp\Controller;

use Illuminate\Validation\ValidationException;
use TesteHibridoApp\Helpers\FlashMessage;
use TesteHibridoApp\Helpers\Validator;
use TesteHibridoApp\Service\ClientService;
use Zend\Diactoros\ServerRequest;
use function TesteHibridoApp\Helpers\validateCpf;
use function TesteHibridoApp\Helpers\view;

class ClientController
{
    private ClientService $clientService;
    private Validator $validator;
    // validation messages
    private array $messages = array(
        'name.required' => 'Name is required.',
        'email.required' => 'Email is required.',
        'email.required' => 'Email is incorrect.',
        'cpf.required' => 'CPF is required',
        'phone.max' => 'Phone is too big');

    public function __construct(ClientService $clientService, Validator $validator)
    {
        $this->clientService = $clientService;
        $this->validator = $validator;
    }

    public function index()
    {
        $success = FlashMessage::get('success');
        $failure = FlashMessage::get('failure');
        return view('Clients/index.html.twig', ['clients' => $this->clientService->getAll(), 'success' => $success, 'failure' => $failure]);
    }

    public function show($id)
    {
        $client = $this->clientService->find($id);
        return view('Clients/show.html.twig', ['client' => $client]);
    }

    public function create()
    {
        return view('Clients/create.html.twig');
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

        try {
            // call validation
            $this->validator->validateArray($data, $rules, $this->messages);
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
        $errors = FlashMessage::get('errors');
        $client = FlashMessage::get('client');
        if (!isset($client)) {
            $client = $this->clientService->find($id);
        }
        return view('Clients/edit.html.twig', ['client' => $client, 'errors' => $errors]);
    }

    public function update(ServerRequest $request, $id)
    {
        $data = $request->getParsedBody();
        $rules = array(
            'name' => ['required'],
            'email' => ['required', 'email', function ($attribute, $value, $fail) use ($id) {
                // check if already exists
                $client = $this->clientService->findByEmail($value);
                if ($client) {
                    if ($client->getId() != $id) {
                        $fail('Email already exists');
                    }
                }
            }],
            'cpf' => ['required', function ($attribute, $value, $fail) {
                // check cpf format
                if (!validateCpf($value)) {
                    $fail('Invalid CPF');
                }
            }, function ($attribute, $value, $fail) use ($id) {
                // check if already exists
                $client = $this->clientService->findByCpf($value);
                if ($client) {
                    if ($client->getId() != $id) {
                        $fail('CPF already exists');
                    }
                }
            }],
            'phone' => ['max:20'],
        );
        try {
            // call validation
            $this->validator->validateArray($data, $rules, $this->messages);
            // if data is ok, call service layer
            $result = $this->clientService->updateClient($data, $id);
            if ($result) {
                FlashMessage::set('success', "Client updated successfully");
            } else {
                FlashMessage::set('failure', "Could not update client. Try again later.");
            }
            header("Location:/clients");
        } catch (ValidationException $ex) {
            FlashMessage::set('errors', $ex->errors());
            FlashMessage::set('client', $data);
            return header("Location:/clients/$id/edit");
        }
    }

    public function destroy($id)
    {
        $result = $this->clientService->deleteClient($id);
        if ($result) {
            FlashMessage::set('success', 'Client deleted successfully');
        } else {
            FlashMessage::set('failure', "Could not remove client. Try again later.");
        }
        header("Location:/clients");
    }
}