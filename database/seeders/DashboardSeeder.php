<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Factura;
use App\Models\Mensaje;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'Juan',   'apellido' => 'Pérez',    'email' => 'juan@email.com',   'telefono' => '+57 300 123 4567', 'estado' => 'activo',   'segmento' => 'premium',   'total_compras' => 12],
            ['nombre' => 'María',  'apellido' => 'García',   'email' => 'maria@email.com',  'telefono' => '+57 310 987 6543', 'estado' => 'activo',   'segmento' => 'regular',   'total_compras' => 8],
            ['nombre' => 'Carlos', 'apellido' => 'López',    'email' => 'carlos@email.com', 'telefono' => '+57 320 456 7890', 'estado' => 'inactivo', 'segmento' => 'ocasional', 'total_compras' => 3],
            ['nombre' => 'Ana',    'apellido' => 'Martínez', 'email' => 'ana@email.com',    'telefono' => '+57 315 234 5678', 'estado' => 'activo',   'segmento' => 'premium',   'total_compras' => 21],
            ['nombre' => 'Pedro',  'apellido' => 'Sánchez',  'email' => 'pedro@email.com',  'telefono' => '+57 312 789 0123', 'estado' => 'inactivo', 'segmento' => 'ocasional', 'total_compras' => 0],
        ];

        foreach ($clientes as $data) {
            Cliente::firstOrCreate(['email' => $data['email']], $data);
        }

        $juan   = Cliente::where('email', 'juan@email.com')->first();
        $maria  = Cliente::where('email', 'maria@email.com')->first();
        $carlos = Cliente::where('email', 'carlos@email.com')->first();
        $ana    = Cliente::where('email', 'ana@email.com')->first();
        $pedro  = Cliente::where('email', 'pedro@email.com')->first();

        $ventas = [
            ['numero_orden' => '#1045', 'cliente_id' => $juan->id,   'producto' => 'Laptop Pro',     'total' => 1500.00, 'estado' => 'completado'],
            ['numero_orden' => '#1044', 'cliente_id' => $maria->id,  'producto' => 'Smartphone X',   'total' => 890.00,  'estado' => 'pendiente'],
            ['numero_orden' => '#1043', 'cliente_id' => $carlos->id, 'producto' => 'Tablet Plus',    'total' => 450.00,  'estado' => 'completado'],
            ['numero_orden' => '#1042', 'cliente_id' => $ana->id,    'producto' => 'Auriculares',    'total' => 120.00,  'estado' => 'devuelto'],
            ['numero_orden' => '#1041', 'cliente_id' => $pedro->id,  'producto' => 'Cámara Digital', 'total' => 680.00,  'estado' => 'en_camino'],
        ];

        foreach ($ventas as $data) {
            Venta::create($data);
        }

        $venta1 = Venta::where('numero_orden', '#1045')->first();
        $venta2 = Venta::where('numero_orden', '#1044')->first();
        $venta3 = Venta::where('numero_orden', '#1043')->first();
        $venta4 = Venta::where('numero_orden', '#1042')->first();
        $venta5 = Venta::where('numero_orden', '#1041')->first();

        $facturas = [
            ['numero_factura' => 'FAC-2024-001', 'cliente_id' => $juan->id,   'venta_id' => $venta1->id, 'concepto' => 'Laptop Pro x1',     'monto' => 1500.00, 'fecha_emision' => '2024-06-01', 'fecha_vencimiento' => '2024-06-15', 'estado' => 'pagada'],
            ['numero_factura' => 'FAC-2024-002', 'cliente_id' => $maria->id,  'venta_id' => $venta2->id, 'concepto' => 'Smartphone X x2',   'monto' => 1780.00, 'fecha_emision' => '2024-06-02', 'fecha_vencimiento' => '2024-06-16', 'estado' => 'pendiente'],
            ['numero_factura' => 'FAC-2024-003', 'cliente_id' => $carlos->id, 'venta_id' => $venta3->id, 'concepto' => 'Tablet Plus x3',    'monto' => 1350.00, 'fecha_emision' => '2024-05-20', 'fecha_vencimiento' => '2024-06-03', 'estado' => 'vencida'],
            ['numero_factura' => 'FAC-2024-004', 'cliente_id' => $ana->id,    'venta_id' => $venta4->id, 'concepto' => 'Auriculares x5',    'monto' => 600.00,  'fecha_emision' => '2024-06-05', 'fecha_vencimiento' => '2024-06-19', 'estado' => 'pagada'],
            ['numero_factura' => 'FAC-2024-005', 'cliente_id' => $pedro->id,  'venta_id' => $venta5->id, 'concepto' => 'Cámara Digital x1', 'monto' => 680.00,  'fecha_emision' => '2024-06-06', 'fecha_vencimiento' => '2024-06-20', 'estado' => 'pendiente'],
        ];

        foreach ($facturas as $data) {
            Factura::create($data);
        }

        $mensajes = [
            ['cliente_id' => $juan->id,   'contenido' => 'Necesito información sobre mi pedido #1045',               'tipo' => 'recibido', 'leido' => false],
            ['cliente_id' => $juan->id,   'contenido' => 'Hola Juan, con mucho gusto te ayudo.',                     'tipo' => 'enviado',  'leido' => true],
            ['cliente_id' => $juan->id,   'contenido' => 'El seguimiento dice que está detenido desde hace 2 días.', 'tipo' => 'recibido', 'leido' => false],
            ['cliente_id' => $maria->id,  'contenido' => '¿Cuándo llega mi envío?',                                  'tipo' => 'recibido', 'leido' => false],
            ['cliente_id' => $carlos->id, 'contenido' => 'Gracias por la atención rápida.',                          'tipo' => 'recibido', 'leido' => true],
            ['cliente_id' => $ana->id,    'contenido' => 'Quiero cambiar mi dirección de envío.',                    'tipo' => 'recibido', 'leido' => true],
            ['cliente_id' => $pedro->id,  'contenido' => 'Factura incorrecta, por favor revisar.',                   'tipo' => 'recibido', 'leido' => false],
        ];

        foreach ($mensajes as $data) {
            Mensaje::create($data);
        }

        $this->command->info('✅ Datos creados correctamente.');
    }
}