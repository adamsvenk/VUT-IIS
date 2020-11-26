<?php

namespace App\Model;

use InvalidArgumentException;
use Nette\Database\Context;
use Nette\Security\Passwords;
use stdClass;

class UserService
{
    /** @var Context */
    private $db;

    /** @var Passwords */
    private $passwords;

    public function __construct(Context $db, Passwords $passwords)
    {
        $this->db = $db;
        $this->passwords = $passwords;
    }

    public function getAll(): array
    {
        $data = [];

        $users = $this->db->table('User')->fetchAll();

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'fullName' => $user->Full_name,
                'dateOfBirth' => $user->Date_of_birth,
                'function' => $user->Function,
            ];
        }

        return $data;
    }

    public function delete(int $userId): void
    {
        $user = $this->db->table('User')->get($userId);

        if ($user === null) {
            throw new InvalidArgumentException('User not found');
        }

        $this->db->table('User')->where('id', $userId)->delete();
    }

    public function update(?int $id, stdClass $values)
    {
        $tableValues = [
            'username' => $values->username,
            'role' => $values->role,
            'Full_name' => $values->fullName ?: null,
            'Date_of_birth' => Utils::dateStringToObject($values->date),
            'Function' => $values->function ?: null,
        ];

        if (!empty($values->password)) {
            $tableValues['password'] = $this->passwords->hash($values->password);
        }

        if ($id === null) {
            $this->db->table('User')->insert($tableValues);
        } else {
            $user = $this->db->table('User')->get($id);

            if ($user === null) {
                throw new InvalidArgumentException('User not found');
            }

            $user->update($tableValues);
        }
    }

    public function getDefaults(?int $userId): array
    {
        if ($userId === null) {
            return [];
        }

        /** @var object $user */
        $user = $this->db->table('User')->get($userId);

        if ($user === null) {
            return [];
        }

        return [
            'username' => $user->username,
            'role' => $user->role,
            'fullName' => $user->Full_name,
            'date' => $user->Date_of_birth ? $user->Date_of_birth->format('Y-m-d') : null,
            'function' => $user->Function,
        ];
    }

    public static function roles(): array
    {
        $roles = [
            UserManager::ROLE_PATIENT,
            UserManager::ROLE_DOCTOR,
            UserManager::ROLE_INSURANCE_WORKER,
            UserManager::ROLE_ADMIN,
        ];

        return array_combine($roles, $roles);
    }
}