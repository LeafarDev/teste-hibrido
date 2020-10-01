<?php
declare(strict_types=1);

namespace TesteHibridoApp\Service;

use Doctrine\ORM\EntityManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use TesteHibridoApp\Model\Client;
use function TesteHibridoApp\Helpers\getEntityManager;
use function TesteHibridoApp\Helpers\rootDir;

class ClientService
{
    private EntityManager $entityManager;

    public function __construct(Logger $log)
    {
        $this->entityManager = getEntityManager();
        $this->log = $log;
        $this->log->pushHandler(new StreamHandler(rootDir() . '/logs/app.log', Logger::INFO));
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
            $client = $this->setClientData($client, $data);
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            $this->log->info('Client Created', ['client' => $data]);
            return true;
        } catch (\Exception $ex) {
            // rollback if fail
            $this->log->critical('Could not create client', ['message' => $ex->getMessage(), 'data' => $data]);
            $this->entityManager->getConnection()->rollBack();
            return false;
        }
    }

    public function updateClient($data, $id)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $client = $this->find($id);
            $oldClient = $client->toArray();
            $this->setClientData($client, $data);
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            $this->log->info('Client updated', ['client before' => $oldClient, 'client now' => $client->toArray()]);
            return true;
        } catch (\Exception $ex) {
            // rollback if fail
            $this->log->critical('Could not update client', ['message' => $ex->getMessage(), 'data' => $data]);
            $this->entityManager->getConnection()->rollBack();
            return false;
        }
    }

    public function deleteClient($id)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $client = $this->find($id);
            $clientDeleted = $client->toArray();
            $this->entityManager->remove($client);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            $this->log->info('Client deleted', ['client deleted' => $clientDeleted]);
            return true;
        } catch (\Exception $ex) {
            $this->log->critical('Could not delete client', ['message' => $ex->getMessage(), 'client id' => $id]);
            $this->entityManager->getConnection()->rollBack();
            return false;
        }
    }

    /**
     * @param object|null $client
     * @param $data
     */
    private function setClientData(?object $client, $data)
    {
        $client->setName($data['name']);
        $client->setCpf(preg_replace("/[^0-9]/", "", $data['cpf']));
        $client->setEmail($data['email']);
        $client->setPhone(@$data['phone']);
        return $client;
    }
}