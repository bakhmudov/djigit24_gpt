<?php

namespace App\Console\Commands;

use App\Models\Department;
use Illuminate\Console\Command;
use App\Libraries\CRest;

class SyncBitrixDepartments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitrix:sync-departments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация отделов из Bitrix24';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начало синхронизации отделов из Bitrix24...');

        $response = CRest::call('department.get', []); // Получение всех отделов из Битрикса

        if (isset($response['error'])) {
            $this->error('Ошибка синхронизации: ' . $response['error_information']);
            return;
        }

        if (!empty($response['result'])) {
            foreach ($response['result'] as $department) {
                Department::updateOrCreate(
                    ['bx_id' => $department['ID']],
                    [
                        'name' => $department['NAME'],
                    ],
                );
            }

            $this->info('Синхронизация завершена успешно.');
        } else {
            $this->warn('Нет данных для синхронизации.');
        }
    }
}
