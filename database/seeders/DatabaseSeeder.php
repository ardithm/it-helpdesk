<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\TicketCategory;
use App\Models\SlaPolicy;
use App\Models\Asset;
use App\Models\Ticket;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Departments
        $deptIT = Department::create(['name' => 'IT Department', 'description' => 'Information Technology']);
        $deptHR = Department::create(['name' => 'Human Resources', 'description' => 'HR Department']);
        $deptFinance = Department::create(['name' => 'Finance', 'description' => 'Finance Department']);

        // 2. Users (Password default: password123)
        $password = Hash::make('password123');
        
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@itdesk.local',
            'password' => $password,
            'role' => 'admin',
            'department_id' => $deptIT->id,
            'is_active' => true,
        ]);

        $helpdesk = User::create([
            'name' => 'Helpdesk Staff',
            'email' => 'helpdesk@itdesk.local',
            'password' => $password,
            'role' => 'helpdesk',
            'department_id' => $deptIT->id,
            'is_active' => true,
        ]);

        $technician = User::create([
            'name' => 'IT Technician',
            'email' => 'technician@itdesk.local',
            'password' => $password,
            'role' => 'technician',
            'department_id' => $deptIT->id,
            'is_active' => true,
        ]);

        $user1 = User::create([
            'name' => 'Budi HR',
            'email' => 'budi@perusahaan.com',
            'password' => $password,
            'role' => 'user',
            'department_id' => $deptHR->id,
            'is_active' => true,
        ]);
        
        $user2 = User::create([
            'name' => 'Siti Finance',
            'email' => 'siti@perusahaan.com',
            'password' => $password,
            'role' => 'user',
            'department_id' => $deptFinance->id,
            'is_active' => true,
        ]);

        // 3. Ticket Categories
        $catHardware = TicketCategory::create(['name' => 'Hardware', 'description' => 'Masalah terkait perangkat keras', 'is_active' => true]);
        $catSoftware = TicketCategory::create(['name' => 'Software', 'description' => 'Masalah terkait perangkat lunak', 'is_active' => true]);
        $catNetwork = TicketCategory::create(['name' => 'Network', 'description' => 'Masalah terkait jaringan', 'is_active' => true]);

        // 4. SLA Policies
        $slaLow = SlaPolicy::create([
            'priority' => Ticket::PRIORITY_LOW,
            'response_time_minutes' => 120, // 2 jam
            'resolution_time_minutes' => 1440, // 24 jam
            'is_active' => true,
        ]);
        $slaMedium = SlaPolicy::create([
            'priority' => Ticket::PRIORITY_MEDIUM,
            'response_time_minutes' => 60, // 1 jam
            'resolution_time_minutes' => 480, // 8 jam
            'is_active' => true,
        ]);
        $slaHigh = SlaPolicy::create([
            'priority' => Ticket::PRIORITY_HIGH,
            'response_time_minutes' => 30, // 30 menit
            'resolution_time_minutes' => 240, // 4 jam
            'is_active' => true,
        ]);
        $slaCritical = SlaPolicy::create([
            'priority' => Ticket::PRIORITY_CRITICAL,
            'response_time_minutes' => 15, // 15 menit
            'resolution_time_minutes' => 60, // 1 jam
            'is_active' => true,
        ]);

        // 5. Assets
        $asset1 = Asset::create([
            'asset_code' => 'LT-001',
            'category' => 'Laptop',
            'brand' => 'Dell',
            'model' => 'Latitude 5420',
            'serial_number' => 'SN-DELL-001',
            'user_id' => $user1->id,
            'department_id' => $deptHR->id,
            'purchase_date' => '2023-01-15',
            'status' => 'active',
        ]);

        $asset2 = Asset::create([
            'asset_code' => 'PC-001',
            'category' => 'Desktop',
            'brand' => 'HP',
            'model' => 'ProDesk 400',
            'serial_number' => 'SN-HP-001',
            'user_id' => $user2->id,
            'department_id' => $deptFinance->id,
            'purchase_date' => '2022-05-20',
            'status' => 'active',
        ]);

        // 6. Tickets
        $ticket1 = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'user_id' => $user1->id,
            'category_id' => $catHardware->id,
            'asset_id' => $asset1->id,
            'type' => Ticket::TYPE_INCIDENT,
            'title' => 'Laptop sering blue screen saat buka laporan',
            'description' => 'Laptop saya sering mengalami blue screen of death (BSOD) sejak kemarin. Mohon bantuannya segera.',
            'priority' => Ticket::PRIORITY_HIGH,
            'status' => Ticket::STATUS_OPEN,
            'sla_policy_id' => $slaHigh->id,
            'response_deadline' => now()->addMinutes($slaHigh->response_time_minutes),
            'resolution_deadline' => now()->addMinutes($slaHigh->resolution_time_minutes),
        ]);

        // Sleep sejenak agar sequence nomor tiket dan created_at beda
        sleep(1);

        $ticket2 = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'user_id' => $user2->id,
            'category_id' => $catSoftware->id,
            'asset_id' => $asset2->id,
            'type' => Ticket::TYPE_SERVICE_REQUEST,
            'title' => 'Install aplikasi Microsoft Office 2024',
            'description' => 'Mohon bantuannya untuk menginstall aplikasi Microsoft Office 2024 terbaru di PC saya untuk keperluan audit bulan depan.',
            'priority' => Ticket::PRIORITY_MEDIUM,
            'status' => Ticket::STATUS_OPEN,
            'sla_policy_id' => $slaMedium->id,
            'response_deadline' => now()->addMinutes($slaMedium->response_time_minutes),
            'resolution_deadline' => now()->addMinutes($slaMedium->resolution_time_minutes),
        ]);
        
        // Output info untuk login
        $this->command->info('Database berhasil di-seed!');
        $this->command->info('Gunakan akun berikut untuk login:');
        $this->command->line('- Admin: admin@itdesk.local / password123');
        $this->command->line('- Helpdesk: helpdesk@itdesk.local / password123');
        $this->command->line('- Technician: technician@itdesk.local / password123');
        $this->command->line('- User: budi@perusahaan.com / password123');
    }
}
