<?php

namespace App\Model;

use InvalidArgumentException;
use Nette\Database\Context;
use stdClass;

class HealthProblemService
{
    public const STATE_NEW = 'new';
    public const STATE_ONGOING = 'ongoing';
    public const STATE_WAITING = 'waiting';
    public const STATE_CLOSED = 'closed';

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
                'state' => self::getStateList()[$healthProblem->state],
                'user' => $healthProblem->patient->Full_name,
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
            'state' => $healthProblem->state,
        ];
    }

    public function update(?int $healthProblemId, stdClass $values, int $doctorId)
    {
        $tableValues = [
            'Name' =>  $values->name,
            'Description' => $values->description,
            'state' => $values->state,
            'patient_id' => $values->patient,
        ];

        if ($healthProblemId === null) {
            $tableValues['doctor_id'] = $doctorId;

            $this->db->table(self::HEALTH_PROBLEM_TABLE)->insert($tableValues);
        } else {
            $healthProblem = $this->db->table(self::HEALTH_PROBLEM_TABLE)->get($healthProblemId);

            if (!$healthProblem) {
                throw new InvalidArgumentException('Health problem not found');
            }

            $healthProblem->update($tableValues);
        }
    }

    public function delete(int $healthProblemId)
    {
        $healthProblem = $this->db->table(self::HEALTH_PROBLEM_TABLE)->get($healthProblemId);

        if (!$healthProblem) {
            throw new InvalidArgumentException('Health problem not found');
        }

        $healthProblem->delete();
    }

    public static function getStateList()
    {
        return [
            self::STATE_NEW => 'Nový',
            self::STATE_ONGOING => 'Probíhající',
            self::STATE_WAITING => 'Čekající na vyšetření',
            self::STATE_CLOSED => 'Ukončený',
        ];
    }
}