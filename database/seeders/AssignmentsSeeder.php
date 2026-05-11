<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class AssignmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignments = [
            ['name' => 'Carlos García', 'address' => 'carlos.garcia@gmail.com', 'phone_number' => 612345678, 'description' => 'Necesito medicamentos para la tensión y vitaminas del complejo B', 'status' => 'pending'],
            ['name' => 'María López', 'address' => 'maria.lopez@hotmail.com', 'phone_number' => 623456789, 'description' => 'Pedir crema hidratante y protector solar factor 50', 'status' => 'completed'],
            ['name' => 'José Martínez', 'address' => 'jose.martinez@gmail.com', 'phone_number' => 634567890, 'description' => 'Ibuprofeno 600mg y paracetamol, también gasas y vendas', 'status' => 'cancelled'],
            ['name' => 'Ana Sánchez', 'address' => 'ana.sanchez@yahoo.es', 'phone_number' => 645678901, 'description' => 'Necesito insulina y jeringas desechables para mi padre', 'status' => 'pending'],
            ['name' => 'Pedro Rodríguez', 'address' => 'pedro.rodriguez@gmail.com', 'phone_number' => 656789012, 'description' => 'Pastillas para dormir y ansiolíticos recetados por el médico', 'status' => 'completed'],
            ['name' => 'Laura Fernández', 'address' => 'laura.fernandez@outlook.com', 'phone_number' => 667890123, 'description' => 'Vitamina D3 y calcio para los huesos, también magnesio', 'status' => 'pending'],
            ['name' => 'Miguel González', 'address' => 'miguel.gonzalez@gmail.com', 'phone_number' => 678901234, 'description' => 'Antihistamínico para la alergia estacional y colirio', 'status' => 'completed'],
            ['name' => 'Isabel Díaz', 'address' => 'isabel.diaz@hotmail.es', 'phone_number' => 689012345, 'description' => 'Crema para eczema y champú medicado sin sulfatos', 'status' => 'cancelled'],
            ['name' => 'Francisco Moreno', 'address' => 'francisco.moreno@gmail.com', 'phone_number' => 690123456, 'description' => 'Omeprazol 20mg y suero oral para el estómago', 'status' => 'pending'],
            ['name' => 'Carmen Jiménez', 'address' => 'carmen.jimenez@gmail.com', 'phone_number' => 601234567, 'description' => 'Termómetro digital y tensiómetro de muñeca', 'status' => 'completed'],
            ['name' => 'Antonio Ruiz', 'address' => 'antonio.ruiz@yahoo.es', 'phone_number' => 612345670, 'description' => 'Antibiótico amoxicilina recetado y probióticos para acompañar', 'status' => 'pending'],
            ['name' => 'Lucía Álvarez', 'address' => 'lucia.alvarez@gmail.com', 'phone_number' => 623456780, 'description' => 'Leche de fórmula para bebé y crema para rozaduras', 'status' => 'completed'],
            ['name' => 'Manuel Torres', 'address' => 'manuel.torres@hotmail.com', 'phone_number' => 634567801, 'description' => 'Colirio para ojos secos y gafas de lectura graduadas', 'status' => 'cancelled'],
            ['name' => 'Sofía Ramírez', 'address' => 'sofia.ramirez@gmail.com', 'phone_number' => 645678902, 'description' => 'Pastillas anticonceptivas y ácido fólico mensual', 'status' => 'pending'],
            ['name' => 'David Flores', 'address' => 'david.flores@outlook.es', 'phone_number' => 656789013, 'description' => 'Proteína en polvo y creatina para el gimnasio', 'status' => 'completed'],
            ['name' => 'Elena Herrera', 'address' => 'elena.herrera@gmail.com', 'phone_number' => 667890124, 'description' => 'Tiritas, alcohol y agua oxigenada para el botiquín', 'status' => 'pending'],
            ['name' => 'Roberto Vargas', 'address' => 'roberto.vargas@yahoo.com', 'phone_number' => 678901235, 'description' => 'Melatonina 1mg y valeriana para mejorar el sueño', 'status' => 'cancelled'],
            ['name' => 'Marta Castro', 'address' => 'marta.castro@gmail.com', 'phone_number' => 689012346, 'description' => 'Hilo dental, cepillo eléctrico y enjuague bucal sin alcohol', 'status' => 'completed'],
            ['name' => 'Javier Ortega', 'address' => 'javier.ortega@hotmail.com', 'phone_number' => 690123457, 'description' => 'Pomada para golpes y antiinflamatorio tópico diclofenaco', 'status' => 'pending'],
            ['name' => 'Patricia Molina', 'address' => 'patricia.molina@gmail.com', 'phone_number' => 601234568, 'description' => 'Test de embarazo y ácido fólico 400mcg', 'status' => 'completed'],
            ['name' => 'Alejandro Delgado', 'address' => 'alejandro.delgado@outlook.com', 'phone_number' => 612345671, 'description' => 'Jarabe para la tos y pastillas para el dolor de garganta', 'status' => 'cancelled'],
            ['name' => 'Cristina Navarro', 'address' => 'cristina.navarro@gmail.com', 'phone_number' => 623456781, 'description' => 'Crema solar y after sun para las vacaciones en la playa', 'status' => 'pending'],
            ['name' => 'Sergio Ramos', 'address' => 'sergio.ramos@yahoo.es', 'phone_number' => 634567802, 'description' => 'Vendaje elástico y spray frío para lesión de tobillo', 'status' => 'completed'],
            ['name' => 'Natalia Iglesias', 'address' => 'natalia.iglesias@gmail.com', 'phone_number' => 645678903, 'description' => 'Mascarilla facial hidratante y sérum de vitamina C', 'status' => 'pending'],
            ['name' => 'Raúl Medina', 'address' => 'raul.medina@hotmail.es', 'phone_number' => 656789014, 'description' => 'Glucómetro y tiras reactivas para control de azúcar', 'status' => 'cancelled'],
        ];

        foreach ($assignments as $assignment) {
            DB::table('assignments')->insert([
                ...$assignment,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
