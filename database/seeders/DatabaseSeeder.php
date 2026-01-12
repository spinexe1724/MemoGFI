<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
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


    // Akun 5 GM dengan email berbasis nama


    // Akun 5 GM dengan email berbasis nama
    $direksi = [
        ['name' => 'Irwan Susanto', 'email' => 'irwan@gfi.co.id'],
        ['name' => 'Kwi Hui Fen', 'email' => 'afen@gfi.co.id'],
        ['name' => 'Riko Aryanto', 'email' => 'riko@gfi.co.id'],
        ['name' => 'Sastra Hamidjaja', 'email' => 'sastra@gfi.co.id'],
    ];

    foreach ($direksi as $direk) {
        User::create([
            'name' => $direk['name'],
            'email' => $direk['email'],
            'password' => bcrypt('password'),
            'role' => 'direksi',
            'level' => '3'
        ]);
    }
    
    $gm = [
        ['name' => 'vicky', 'email' => 'vicky@gfi.co.id','division' => 'HRD'],
        ['name' => 'roby', 'email' => 'roby@gfi.co.id','division' => 'Remedial'],
        ['name' => 'stefanus', 'email' => 'stefanus@gfi.co.id','division' => 'Dealer Financing'],
        ['name' => 'nafsiah', 'email' => 'nafsiah@gfi.co.id','division' => 'Operations'],
    ];

    foreach ($gm as $gms) {
        User::create([
            'name' => $gms['name'],
            'email' => $gms['email'],
            'password' => bcrypt('password'),
            'role' => 'gm',
            'level' => '2',
            'division' => $gms['division']

        ]);
    }
    $div = [
        ['name' => 'Bussiness Support', 'initial' => 'BS'],
        ['name' => 'HRD', 'initial' => 'HR'],
        ['name' => 'Internal Control', 'initial' => 'IC'],
        ['name' => 'Remedial', 'initial' => 'Remedial'],
        ['name' => 'Dealer Financing', 'initial' => 'DF'],
        ['name' => 'Collection', 'initial' => 'Col'],
        ['name' => 'Operations', 'initial' => 'OPS'],
    ];

    foreach ($div as $division) {
        division::create([
            'name' => $division['name'],
            'initial' => $division['initial'],
        ]);
    }



    // Akun 5 GM dengan email berbasis nama
    $BS = [
        
        ['name' => 'Bussiness Support', 'email' => 'bs@gfi.co.id', 'division' => 'Business Support'],
        ['name' => 'SPV Bussiness Support', 'email' => 'spvbs@gfi.co.id', 'division' => 'Business Support'],
        ['name' => 'HR', 'email' => 'hr@gfi.co.id', 'division' => 'HRD'],
        ['name' => 'SKAI', 'email' => 'skai@gfi.co.id', 'division' => 'Internal Control'],
        ['name' => 'Remedial', 'email' => 'remedial@gfi.co.id', 'division' => 'Remedial'],
        ['name' => 'SPV Remedial', 'email' => 'spvremedial@gfi.co.id', 'division' => 'Remedial'],
        ['name' => 'SPV Dealer Financing', 'email' => 'spvdf@gfi.co.id', 'division' => 'Dealer Financing'],
        ['name' => 'SPV Operation', 'email' => 'spvops@gfi.co.id', 'division' => 'Operations'],
        ['name' => 'SPV Collection', 'email' => 'spvcol@gfi.co.id', 'division' => 'Collection'],
    ];

    foreach ($BS as $bss) {
        User::create([
            'name' => $bss['name'],
            'email' => $bss['email'],
            'division' => $bss['division'], 
            'password' => bcrypt('passwordteam'),
            'role' => 'supervisor',
            'level' => '2'
        ]);
    }
}

}