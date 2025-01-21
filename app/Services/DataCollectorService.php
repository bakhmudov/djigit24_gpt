<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DataCollectorService
{
    public function collectData(): array
    {
        return [
            'bx_users' => $this->getDepartments(),
            'departments' => $this->getBxUsers(),
            'tasks' => $this->getTasks(),
        ];
    }

    private function getBxUsers(): array
    {
        return DB::table('bx_users')
            ->join('departments', 'bx_users.department_id', '=', 'departments.id')
            ->select(
                'bx_users.id',
                'bx_users.bx_id',
                'bx_users.name',
                'bx_users.last_name',
                'bx_users.email',
                'bx_users.work_position',
                'departments.name as department_name')
            ->get()
            ->toArray();
    }

    private function getDepartments(): array
    {
        return DB::table('departments')
            ->select('bx_id', 'name')
            ->get()
            ->toArray();
    }

    private function getTasks(): array
    {
        return DB::table('tasks')
            ->select(
                'bx_id',
                'title',
                'description',
                'priority',
                'created_by',
                'responsible_id',
                'closed_by',
                'deadline',
                'status',
                'sub_status',
            )
            ->get()
            ->toArray();
    }
}
