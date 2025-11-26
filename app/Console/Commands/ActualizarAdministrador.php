<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActualizarAdministrador extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:actualizar {--email-viejo=wheelkorner@gmail.com : Email actual del administrador} {--email-nuevo=jose@gafi.com.br : Nuevo email del administrador} {--nombre-nuevo=Jose A Cordero : Nuevo nombre del administrador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el email y nombre de un administrador existente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $emailViejo = $this->option('email-viejo');
        $emailNuevo = $this->option('email-nuevo');
        $nombreNuevo = $this->option('nombre-nuevo');

        $this->info("Buscando administrador con email: {$emailViejo}");

        // Buscar el usuario
        $usuario = User::where('email', $emailViejo)->first();

        if (!$usuario) {
            $this->error("❌ No se encontró ningún usuario con el email: {$emailViejo}");
            return 1;
        }

        // Mostrar información actual
        $this->info("\n📋 Información actual del usuario:");
        $this->line("   ID: {$usuario->id}");
        $this->line("   Nombre: {$usuario->name}");
        $this->line("   Email: {$usuario->email}");
        $this->line("   Admin: " . ($usuario->admin ? 'Sí' : 'No'));
        $this->line("   SA: " . ($usuario->sa ? 'Sí' : 'No'));

        // Verificar si el nuevo email ya existe
        $usuarioConEmailNuevo = User::where('email', $emailNuevo)->where('id', '!=', $usuario->id)->first();
        if ($usuarioConEmailNuevo) {
            $this->error("❌ El email {$emailNuevo} ya está en uso por otro usuario (ID: {$usuarioConEmailNuevo->id})");
            return 1;
        }

        // Confirmar antes de actualizar
        if (!$this->confirm("¿Deseas actualizar este usuario?", true)) {
            $this->warn("Operación cancelada.");
            return 0;
        }

        // Actualizar en una transacción
        try {
            DB::beginTransaction();

            $usuario->email = $emailNuevo;
            $usuario->name = $nombreNuevo;
            $usuario->save();

            DB::commit();

            $this->info("\n✅ Usuario actualizado exitosamente!");
            $this->line("\n📋 Nueva información del usuario:");
            $this->line("   ID: {$usuario->id}");
            $this->line("   Nombre: {$usuario->name}");
            $this->line("   Email: {$usuario->email}");
            $this->line("   Admin: " . ($usuario->admin ? 'Sí' : 'No'));
            $this->line("   SA: " . ($usuario->sa ? 'Sí' : 'No'));

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error al actualizar el usuario: " . $e->getMessage());
            return 1;
        }
    }
}

