<?php

namespace App\Console\Commands;

use App\Models\BxUser;
use App\Models\Task;
use CRest\CRest;
use Illuminate\Console\Command;

class SyncBitrixTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitrix:sync-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация активных задач из Bitrix24';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начало синхронизации активных задач из Bitrix24...');

        $start = 0;
        $totalProcessed = 0;

        do {
            // Запрос активных задач из Bitrix24
            $response = CRest::call('tasks.task.list', [
                'filter' => ['REAL_STATUS' => [1, 2, 3]],
                'start' => $start,
            ]);

            if (isset($response['error'])) {
                $this->error('Ошибка синхронизации ' . $response['error_information']);
                return;
            }

            if (empty($response['result']['tasks'])) {
                $this->warn('Нет данных для синхронизации.');
                break;
            }

            foreach ($response['result']['tasks'] as $task) {
                // Выводим задачу для отладки
//                $this->info('Обработка задачи: ' . json_encode($task, JSON_UNESCAPED_UNICODE));

                // Проверка на наличие пользователя, создавшего задачу
                $creator = BxUser::where('bx_id', $task['createdBy'] ?? null)->first();
                $responsible = BxUser::where('bx_id', $task['responsibleId'] ?? null)->first();
                $closer = isset($task['closedBy']) ? BxUser::where('bx_id', $task['closedBy'])->first() : null;

                if (!$creator || !$responsible) {
                    $this->warn('Пропущена задача с ID ' . $task['ID'] . ' из-за отсутствия связанных пользователей.');
                    continue;
                }

                Task::updateOrCreate(
                    ['bx_id' => $task['id'] ?? null],
                    [
                        'title' => $task['title'] ?? 'Без названия',
                        'description' => $task['description'] ?? null,
                        'priority' => $task['priority'] ?? 0,
                        'created_by' => $creator->id,
                        'created_date' => $task['createdDate'] ?? now(),
                        'responsible_id' => $responsible->id,
                        'closed_by' => $closer?->id,
                        'deadline' => $task['deadline'] ?? null,
                        'status' => $task['status'] ?? 0,
                        'sub_status' => $task['subStatus'] ?? 0,
                    ],
                );
            }

            $totalProcessed += count($response['result']['tasks']);
            $this->info('Обработано задач: ' . $totalProcessed);

            // Устанавливаем смещение для следующего запроса
            $start += 50;
        } while (!empty($response['next']));

        $this->info('Синхронизация активных задач завершена успешно. Всего обработано: ' . $totalProcessed);
    }
}
