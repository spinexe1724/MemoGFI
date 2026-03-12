<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  public function run(): void {
    // Akun superadmin
   
        
   \App\Models\User::create([
    'name' => 'Super Administrator',
    'email' => 'admin@gratama.com',
    'password' => Hash::make('password123'),
    'role' => 'superadmin',
    ]);

  \App\Models\User::create([
    'name' => 'Super Administrator',
    'email' => 'romygratama@gmail.com',
    'password' => Hash::make('Gratama_2026'),
    'role' => 'superadmin',
    ]);


//     // Akun 5 GM dengan email berbasis nama


//     // Akun 5 GM dengan email berbasis nama
//     $direksi = [
//         ['name' => 'Irwan Susanto', 'email' => 'direksi5@gratama-finance.co.id',],
//         ['name' => 'Kwi Hui Fen', 'email' => 'direksi3@gratama-finance.co.id'],
//         ['name' => 'Riko Aryanto', 'email' => 'direksi4@gratama-finance.co.id'],
//         ['name' => 'Sastra Hamidjaja', 'email' => 'direksi1@gratama-finance.co.id'],
//         ['name' => 'Tohir', 'email' => 'direksi2@gratama-finance.co.id'],
//     ];

//     foreach ($direksi as $direk) {
//         User::create([
//             'name' => $direk['name'],
//             'email' => $direk['email'],
//             'password' => bcrypt('password'),
//             'role' => 'direksi',
//             'branch' => 'HO',
//             'level' => '3'
//         ]);
//     }

    
         
  
    
//     $manager = [
//         ['name' => 'vicky', 'email' => 'vicky@gfi.co.id','division' => 'HRD'],
//         ['name' => 'roby', 'email' => 'roby@gfi.co.id','division' => 'Remedial'],
//         ['name' => 'stefanus', 'email' => 'stefanus@gfi.co.id','division' => 'Dealer Financing'],
//         ['name' => 'nafsiah', 'email' => 'nafsiah@gfi.co.id','division' => 'Operations'],
//         ['name' => 'IT Support', 'email' => 'itsupport@gratama-finance.co.id','division' => 'IT Support'],
//     ];

//     foreach ($manager as $managers) {
//         User::create([
//             'name' => $managers['name'],
//             'email' => $managers['email'],
//             'password' => bcrypt('password'),
//             'role' => 'manager',
//             'level' => '2',
//             'branch' => 'HO',
//             'division' => $managers['division']

//         ]);
//     }
//     $div = [
//         ['name' => 'Bussiness Support', 'initial' => 'BS'],
//         ['name' => 'HRD', 'initial' => 'HR'],
//         ['name' => 'Internal Control', 'initial' => 'IC'],
//         ['name' => 'Remedial', 'initial' => 'RMD'],
//         ['name' => 'Dealer Financing', 'initial' => 'DF'],
//         ['name' => 'Collection', 'initial' => 'Col'],
//         ['name' => 'Operations', 'initial' => 'OPS'],
//         ['name' => 'IT Support', 'initial' => 'IT'],
//     ];

//     foreach ($div as $division) {
//         division::create([
//             'name' => $division['name'],
//             'initial' => $division['initial'],
//         ]);
//     }



//     $BS = [
        
//         ['name' => 'Bussiness Support', 'email' => 'bs@gfi.co.id', 'division' => 'Bussiness Support'],
//         ['name' => 'SPV Bussiness Support', 'email' => 'spvbs@gfi.co.id', 'division' => 'Bussiness Support'],
//         ['name' => 'HR', 'email' => 'hr@gfi.co.id', 'division' => 'HRD'],
//         ['name' => 'SKAI', 'email' => 'skai@gfi.co.id', 'division' => 'Internal Control'],
//         ['name' => 'Remedial', 'email' => 'remedial@gfi.co.id', 'division' => 'Remedial'],
//         ['name' => 'SPV Remedial', 'email' => 'spvremedial@gfi.co.id', 'division' => 'Remedial'],
//         ['name' => 'SPV Dealer Financing', 'email' => 'spvdf@gfi.co.id', 'division' => 'Dealer Financing'],
//         ['name' => 'SPV Operation', 'email' => 'spvops@gfi.co.id', 'division' => 'Operations'],
//         ['name' => 'SPV Collection', 'email' => 'spvcol@gfi.co.id', 'division' => 'Collection'],
//         ['name' => 'IT Support', 'email' => 'oki@gratama-finance.co.id', 'division' => 'IT Support'],
//     ];

//     foreach ($BS as $bss) {
//         User::create([
//             'name' => $bss['name'],
//             'email' => $bss['email'],
//             'division' => $bss['division'], 
//             'password' => bcrypt('passwordteam'),
//             'role' => 'supervisor',
//             'level' => '2'
//         ]);
//     }


// $branch = [
//         ['name' => 'Kantor Pusat Operasional', 'code' => 'HO'],
//         ['name' => 'Jakarta 1', 'code' => 'KPO'],
//         ['name' => 'Jakarta 2 ', 'code' => 'KPJ'],
//         ['name' => 'Jakarta 3 ', 'code' => 'KPK'],

//     ];

//     foreach ($branch as $branches) {
//         Branch::create([
//             'name' => $branches['name'],
//             'code' => $branches['code'],
//         ]);
//     }
//   $bm = [
//         ['name' => 'Dewi', 'email' => 'dewi@gratama-finance.co.id',   'branch' => 'Jakarta 1'],
//         ['name' => 'Asep', 'email' => 'asep@gratama-finance.co.id','branch' => 'Jakarta 2' ],
//         ['name' => 'Handoyo', 'email' => 'handoyo@gratama-finance.co.id','branch' => 'Jakarta 3'],
   
//     ];

//   foreach ($bm as $bms) {
//         User::create([
//             'name' => $bms['name'],
//             'email' => $bms['email'],
//             'password' => bcrypt('password'),
//             'role' => 'bm',
//             'level' => '2',
//             'division' =>'Branch Manager',

//         ]);
//     }

//      $admin = [
//         ['name' => 'Fita', 'email' => 'fita@gratama-finance.co.id',   'branch' => 'Jakarta 1'],
//         ['name' => 'Fri', 'email' => 'fri@gratama-finance.co.id','branch' => 'Jakarta 2' ],
//         ['name' => 'Yanti', 'email' => 'yanti@gratama-finance.co.id','branch' => 'Jakarta 3'],
   
//     ];

//   foreach ($bm as $bms) {
//         User::create([
//             'name' => $bms['name'],
//             'email' => $bms['email'],
//             'password' => bcrypt('password'),
//             'role' => 'admin',
//             'level' => '2',
//             'division' =>'Admin',

//         ]);
//     }
}
}