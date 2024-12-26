<?php

namespace App\Console\Commands;

use App\Models\BxUser;
use App\Models\Department;
use CRest\CRest;
use Illuminate\Console\Command;

class SyncBitrixUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitrix:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начало синхронизации пользователей из Bitrix24...');

        $start = 0;
        $totalProcessed = 0;

        do {
            // Запрос данных с текущим смещением
            $response = CRest::call('user.get', ['start' => $start]);

            if (isset($response['error'])) {
                $this->error('Ошибка синхронизации: ' . $response['error_information']);
                return;
            }

            if (empty($response['result'])) {
                $this->warn('Нет данных для синхронизации');
                break;
            }

            foreach ($response['result'] as $user) {
                // Проверка существования отдела
                $department = isset($user['UF_DEPARTMENT'][0])
                    ? Department::where('bx_id', $user['UF_DEPARTMENT'][0])->first()
                    : null;

                if (!$department) {
                    $this->warn('Пропущен пользователь с ID ' . $user['ID'] . ' из-за отсутствия отдела.');
                    continue;
                }

                // Добавление или обновление пользователя
                BxUser::updateOrCreate(
                    ['bx_id' => $user['ID']],
                    [
                        'active' => $user['ACTIVE'],
                        'name' => $user['NAME'],
                        'last_name' => $user['LAST_NAME'] ?? null,
                        'email' => $user['EMAIL'] ?? null,
                        'work_position' => $user['WORK_POSITION'] ?? null,
                        'department_id' => $department->id,
                    ],
                );
            }

            $totalProcessed += count($response['result']);
            $this->info('Обработано ' . $totalProcessed . ' пользователей.');

            // Увеличение смещения для следующих пользователей
            $start += 50;
        } while (!empty($response['next'])); // Проверка наличия следующей порции пользователей

        $this->info('Синхронизация пользователей завершена успешно. Всего обработано: ' . $totalProcessed);
    }
}
