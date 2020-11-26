<?php

namespace App\Model;

use Nette\Database\Context;
use stdClass;

class HealthProblemService
{
    private const HEALTH_PROBLEM_TABLE = 'Health_problem';

    /** @var Context */
    private $db;

    public function __construct(Context $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $data = [];

        $healthProblems = $this->db->table(self::HEALTH_PROBLEM_TABLE)->fetchAll();

        foreach ($healthProblems as $healthProblem) {
            $data[] = [
                'id' => $healthProblem->id,
                'name' => $healthProblem->Name,
                'description' => $healthProblem->Description,
                'user' => $healthProblem->User->Full_name,
            ];
        }

        return $data;
    }

    public function getDefaults(?int $healthProblemId)
    {
        if ($healthProblemId === null) {
            return [];
        }

        /** @var stdClass|null $user */
        $healthProblem = $this->db->table(self::HEALTH_PROBLEM_TABLE)->get($healthProblemId);

        if ($healthProblem === null) {
            return [];
        }

        return [
            'name' => $healthProblem->Name,
            'description' => $healthProblem->Description,
        ];
    }

    public function update(?int $healthProblemId, stdClass $values)
    {
        $tableValues = [
            'Name' =>  $values->name,
            'Description' => $values->description,
            'User_id' => 1,
        ];

        if ($healthProblemId === null) {
            $this->db->table(self::HEALTH_PROBLEM_TABLE)->insert($tableValues);
        } else {
            $healthProblem = $this->db->table(self::HEALTH_PROBLEM_TABLE)->get($healthProblemId);

            if (!$healthProblem) {
                throw new \InvalidArgumentException('Health problem not found');
            }

            $healthProblem->update($tableValues);
        }
    }

    public function delete(int $healthProblemId)
    {
        $healthProblem = $this->db->table(self::HEALTH_PROBLEM_TABLE)->get($healthProblemId);

        if (!$healthProblem) {
            throw new \InvalidArgumentException('Health problem not found');
        }

        $healthProblem->delete();
    }
}