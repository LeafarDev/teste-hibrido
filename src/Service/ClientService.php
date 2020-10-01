<?php
declare(strict_types=1);

namespace TesteHibridoApp\Service;

use Doctrine\ORM\EntityManager;
use TesteHibridoApp\Model\Client;
use function TesteHibridoApp\Helpers\getEntityManager;

class ClientService
{
    private EntityManager $entityManager;

    public function __construct()
    {
        $this->entityManager = getEntityManager();
    }

    public function find($id)
    {
        $client = $this->entityManager->find('TesteHibridoApp\Model\Client', $id);
        return $client;
    }

    public function findByEmail($email)
    {
        $qb = $this->entityManager->createQueryBuilder('clients c');
        $query = $qb->select('c')
            ->from('TesteHibridoApp\Model\Client', 'c')
            ->where('c.email = :email')
            ->setParameter('email', $email);
        return $query->getQuery()->getOneOrNullResult();
    }

    public function findByCpf($cpf)
    {
        $qb = $this->entityManager->createQueryBuilder('clients c');
        $query = $qb->select('c')
            ->from('TesteHibridoApp\Model\Client', 'c')
            ->where('c.cpf = :cpf')
            ->setParameter('cpf', $cpf);
        return $query->getQuery()->getOneOrNullResult();
    }

    function getAll()
    {
        $clientRepository = $this->entityManager->getRepository('TesteHibridoApp\Model\Client');
        $clientRepository->clear();
        $clients = $clientRepository->findAll();
        return $clients;
    }

    public function saveClient($data)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $client = new Client();
            $client->setName($data['name']);
            $client->setCpf($data['cpf']);
            $client->setEmail($data['email']);
            $client->setPhone(@$data['phone']);
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return true;
        } catch (\Exception $ex) {
            // rollback if fail
            $this->entityManager->getConnection()->rollBack();
            return false;
        }
    }

    public function updateClient($data, $id)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $client = $this->find($id);
            $client->setName($data['name']);
            $client->setCpf($data['cpf']);
            $client->setEmail($data['email']);
            $client->setPhone(@$data['phone']);
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return true;
        } catch (\Exception $ex) {
            // rollback if fail
            $this->entityManager->getConnection()->rollBack();
            return false;
        }
    }

    public function deleteClient($id)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $client = $this->find($id);
            $this->entityManager->remove($client);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return true;
        } catch (\Exception $ex) {
            $this->entityManager->getConnection()->rollBack();
            return false;
        }
    }
}