<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckPasswordResetTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:password-reset-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar la estructura de la tabla password_reset_tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Verificando tabla password_reset_tokens...\n");
        
        // Verificar si la tabla existe
        if (!Schema::hasTable('password_reset_tokens')) {
            $this->error("❌ La tabla password_reset_tokens NO existe");
            $this->info("💡 Ejecuta: php artisan migrate");
            return 1;
        }
        
        $this->info("✅ Tabla password_reset_tokens existe");
        
        // Verificar estructura de la tabla
        $columns = Schema::getColumnListing('password_reset_tokens');
        $this->info("\n📋 Columnas encontradas:");
        
        $requiredColumns = [
            'email' => 'string',
            'token' => 'string', 
            'created_at' => 'timestamp'
        ];
        
        foreach ($columns as $column) {
            $columnType = DB::getSchemaBuilder()->getColumnType('password_reset_tokens', $column);
            $status = in_array($column, array_keys($requiredColumns)) ? '✅' : 'ℹ️';
            $this->line("   {$status} {$column} ({$columnType})");
        }
        
        // Verificar columnas requeridas
        $this->info("\n🔍 Verificando columnas requeridas:");
        foreach ($requiredColumns as $column => $expectedType) {
            if (in_array($column, $columns)) {
                $actualType = DB::getSchemaBuilder()->getColumnType('password_reset_tokens', $column);
                $status = $actualType === $expectedType ? '✅' : '⚠️';
                $this->line("   {$status} {$column}: esperado {$expectedType}, encontrado {$actualType}");
            } else {
                $this->error("   ❌ {$column}: NO encontrada");
            }
        }
        
        // Verificar índices
        $this->info("\n🔍 Verificando índices:");
        try {
            $indexes = DB::select("SHOW INDEX FROM password_reset_tokens");
            foreach ($indexes as $index) {
                $this->line("   ✅ Índice: {$index->Key_name} en columna {$index->Column_name}");
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️ No se pudieron verificar los índices: " . $e->getMessage());
        }
        
        // Verificar datos existentes
        $this->info("\n📊 Datos en la tabla:");
        try {
            $count = DB::table('password_reset_tokens')->count();
            $this->line("   📈 Total de tokens: {$count}");
            
            if ($count > 0) {
                $recentTokens = DB::table('password_reset_tokens')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();
                
                $this->line("   🔑 Tokens recientes:");
                foreach ($recentTokens as $token) {
                    $created = \Carbon\Carbon::parse($token->created_at);
                    $expires = $created->addMinutes(60);
                    $now = \Carbon\Carbon::now();
                    $status = $now->lt($expires) ? '✅' : '❌';
                    
                    $this->line("      {$status} {$token->email} - Creado: {$created->format('Y-m-d H:i:s')} - Expira: {$expires->format('Y-m-d H:i:s')}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error al verificar datos: " . $e->getMessage());
        }
        
        // Verificar configuración de auth
        $this->info("\n⚙️ Configuración de Auth:");
        $this->line("   📧 Tabla configurada: " . config('auth.passwords.users.table'));
        $this->line("   ⏰ Tiempo de expiración: " . config('auth.passwords.users.expire') . " minutos");
        $this->line("   🚫 Throttle: " . config('auth.passwords.users.throttle') . " segundos");
        
        $this->info("\n✅ Verificación completada!");
        
        return 0;
    }
}
