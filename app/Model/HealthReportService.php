<?php

namespace App\Model;

use InvalidArgumentException;
use Nette\Database\Context;
use stdClass;

class HealthReportService
{
    private const HEALTH_REPORT_TABLE = 'Health_report';
    
    private $db;
    
    public function __construct(Context $db)
    {
        $this->db = $db;
    }
    
    public function getAll(): array
    {
        $data = [];

        $healthReports = $this->db->table(self::HEALTH_REPORT_TABLE)->fetchAll();

        foreach ($healthReports as $healthReport) {
            $data[] = [
                'id' => $healthReport->id,
                'Text' => $healthReport->Text,
                'Picture' => $healthReport->Picture,
                'DateTime' =>$healthReport->DateTime,
            ];
        }

        return $data;
    }
    
    public function getDefaults(?int $healthReportId)
    {
        if ($healthReportId === null) {
            return [];
        }

        /** @var stdClass|null $user */
        $healthReport = $this->db->table(self::HEALTH_REPORT_TABLE)->get($healthReportId);

        if ($healthReport === null) {
            return [];
        }

        return [
            'Text' => $healthReport->Text,
            'Picture' => $healthReport->Picture,
            'DateTime' => $healthReport->DateTime,
        ];
    }
    
    public function update(?int $healthReportId, stdClass $values, int $doctorId)
    {
        $tableValues = [
            'Text' =>  $values->Text,
            'Picture' => $values->Picture,
            'DateTime' => $values->DateTime,
            'health_problem_id' => $values->health_problem,
        ];

        if ($healthReportId === null) {
            $tableValues['doctor_id'] = $doctorId;

            $this->db->table(self::HEALTH_REPORT_TABLE)->insert($tableValues);
        } else {
            $healthReport = $this->db->table(self::HEALTH_REPORT_TABLE)->get($healthReportId);

            if (!$healthReport) {
                throw new InvalidArgumentException('Health report not found');
            }

            $healthReport->update($tableValues);
        }
    }
    
    
    public function delete(int $healthReportId)
    {
        $healthReport = $this->db->table(self::HEALTH_REPORT_TABLETABLE)->get($healthReportId);

        if (!$healthReport) {
            throw new InvalidArgumentException('Health report not found');
        }

        $healthReport->delete();
    }
    
}
