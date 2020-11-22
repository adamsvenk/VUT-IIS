<?php

namespace App\Model;

use Nette\Database\Context;

class UserService
{
    /** @var Context  */
    private $db;

    public function __construct(Context $db)
    {
        $this->db = $db;
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
            throw new \InvalidArgumentException('User not found');
        }

        $this->db->table('User')->where('id', $userId);
    }
}