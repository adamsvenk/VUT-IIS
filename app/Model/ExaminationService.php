<?php

namespace App\Model;

use InvalidArgumentException;
use Nette\Database\Context;
use stdClass;

class ExaminationService
{
    public const STATE_WAITING = 'waiting';
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_CLOSED = 'closed';

    private const EXAMINATION_TABLE = 'Examination_request';

    /** @var Context */
    private $db;

    public function __construct(Context $db)
    {
        $this->db = $db;
    }

    public function getAll(int $healthProblemId): array
    {
        $data = [];

        $reports = $this->db->table(self::EXAMINATION_TABLE)->where(['Health_problem_id' => $healthProblemId]);

        /** @var stdClass $report */
        foreach ($reports as $report) {
            $data[] = [
                'id' => $report->id,
                'state' => self::getStateList()[$report->State],
                'text' => $report->Text,
                'doctorName' => $report->doctor->Full_name,
            ];
        }

        return $data;
    }

    public function getDefaults(?int $examinationId): array
    {
        if ($examinationId === null) {
            return [];
        }

        /** @var stdClass $examination */
        $examination = $this->db->table(self::EXAMINATION_TABLE)->get($examinationId);

        if ($examination === null) {
            return [];
        }

        return [
            'state' => $examination->State,
            'text' => $examination->Text,
            'doctor' => $examination->doctor_id,
        ];
    }

    public function update(int $healthProblemId, ?int $examinationId, stdClass $values)
    {
        //tyhle hodnoty se vkládají vždy
        $tableValues = [
            'Text' => $values->text,
            'State' => $values->state,
            'doctor_id' => $values->doctor,
        ];

        if ($examinationId === null) {
            $tableValues['health_problem_id'] = $healthProblemId;

            $this->db->table(self::EXAMINATION_TABLE)->insert($tableValues);
        } else {
            $examination = $this->db->table(self::EXAMINATION_TABLE)->get($examinationId);

            if (!$examination) {
                throw new InvalidArgumentException('Examination request not found');
            }

            $examination->update($tableValues);
        }
    }

    public function delete(int $examinationId)
    {
        $examination = $this->db->table(self::EXAMINATION_TABLE)->get($examinationId);

        if (!$examination) {
            throw new InvalidArgumentException('Examination request not found');
        }

        $examination->delete();
    }

    public static function getStateList()
    {
        return [
            self::STATE_WAITING => 'Čeká na vyřízení',
            self::STATE_IN_PROGRESS => 'Vyřizuje se',
            self::STATE_CLOSED => 'Dokončeno',
        ];
    }

}