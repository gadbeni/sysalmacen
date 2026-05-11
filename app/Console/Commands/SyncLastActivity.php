<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncLastActivity extends Command
{
    protected $signature = 'users:sync-last-activity';
    protected $description = 'Lee requests.log desde el final y actualiza last_login_at de cada usuario';

    public function handle()
    {
        $logPath = storage_path('logs/requests.log');

        if (!file_exists($logPath)) {
            $this->error("No se encontró el archivo: {$logPath}");
            return 1;
        }

        // Todos los user_id existentes en la BD
        $userIds  = DB::table('users')->pluck('id')->flip()->map(fn() => null)->toArray();
        $total    = count($userIds);
        $found    = [];

        $this->info("Usuarios a buscar: {$total}");
        $this->info("Leyendo log desde el final...");

        $handle    = fopen($logPath, 'r');
        $chunkSize = 65536; // 64 KB por pasada
        $buffer    = '';
        $lines     = 0;

        fseek($handle, 0, SEEK_END);
        $position = ftell($handle);

        while ($position > 0 && count($found) < $total) {
            $read      = min($chunkSize, $position);
            $position -= $read;
            fseek($handle, $position);
            $buffer = fread($handle, $read) . $buffer;

            $parts  = explode("\n", $buffer);
            $buffer = array_shift($parts); // fragmento incompleto al inicio

            foreach (array_reverse($parts) as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $lines++;

                // Extraer timestamp del inicio de línea: [2026-05-11 12:30:18]
                if (!preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $tm)) {
                    continue;
                }

                // Solo líneas con user_id (sesiones autenticadas)
                if (!preg_match('/"user_id":(\d+)/', $line, $um)) {
                    continue;
                }

                $userId = (int) $um[1];

                if (array_key_exists($userId, $userIds) && !array_key_exists($userId, $found)) {
                    $found[$userId] = $tm[1];
                }

                if (count($found) >= $total) break;
            }
        }

        fclose($handle);

        $this->info("Líneas procesadas : {$lines}");
        $this->info("Usuarios hallados : " . count($found) . " / {$total}");

        foreach ($found as $userId => $timestamp) {
            DB::table('users')
                ->where('id', $userId)
                ->update(['last_login_at' => $timestamp]);
        }

        $this->info('Base de datos actualizada.');
        return 0;
    }
}
