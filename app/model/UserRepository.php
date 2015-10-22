<?php

namespace Model\Repository;

use Nette;
use Nette\Security\Passwords;

class UserRepository extends Repository implements Nette\Security\IAuthenticator
{
    const
        TABLE_NAME = 'user',
        COLUMN_ID = 'id',
        COLUMN_NAME = 'email',
        COLUMN_PASSWORD_HASH = 'password';
        // COLUMN_ROLE = 'role';

    /**
     * Performs an authentication.
     *
     * @return Nette\Security\Identity
     *
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        $row = $this->connection->query('SELECT * FROM ['.self::TABLE_NAME.'] WHERE', array(self::COLUMN_NAME => $username))->fetch();

        if (!$row) {
            throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
            throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
            $row->update(array(
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ));
        }

        $arr = $row->toArray();
        unset($arr[self::COLUMN_PASSWORD_HASH]);

        $roles = $row->role ? explode(',', $row->role) : array('registered');

        return new Nette\Security\Identity($row[self::COLUMN_ID], $roles, $arr);
    }

    /**
     * Adds new user.
     *
     * @param  string
     * @param  string
     */
    public function add($username, $password)
    {
        try {
            $this->connection->query('INSERT INTO ['.table(self::TABLE_NAME).']', array(
                self::COLUMN_NAME => $username,
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ));
        } catch (Exception $e) {
            throw new DuplicateNameException();
        }
    }
    
    public function findAllSimple($orderBy = 'name') 
    {
        $query = $this->connection->select('*')->from($this->getTable());
        if ($orderBy) {
            $query->orderBy($orderBy);
        }
        return $query->fetchPairs('id', 'name');
    }
    
    public function findByEmail($email) {
        $user = $this->connection->select('*')->from($this->getTable())
            ->where(array('email%s' => $email))->fetch();
        return $user ? $this->createEntity($user) : NULL;
    }
}

class DuplicateNameException extends \Exception
{
}
