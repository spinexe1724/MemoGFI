@extends('layouts.app') {{--  layout --}}

@section('title', 'Home Memo System') {{-- title --}}

@section('content') {{-- content section --}}
<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-xl shadow-lg border">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Create New Account</h3>
                
                <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-semibold mb-1">Full Name</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Email Address</label>
                        <input type="email" name="email" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    </div>
                   <div>
        <label class="block font-semibold mb-1">Division</label>
        <select name="division" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            <option value="" disabled selected>Pilih Divisi</option>
            <option value="IT">IT</option>
            <option value="HRD">HRD</option>
            <option value="IC">IC</option>
            <option value="Remedial">Remedial</option>
        </select>
    </div>
                    <div>
                        <label class="block font-semibold mb-1">Account Role</label>
                        <select name="role" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                            <option value="gm">General Manager (GM)</option>
                            <option value="direktur">Direktur</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold mb-1">Password</label>
                            <input type="password" name="password" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end space-x-2">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 text-gray-500">Cancel</a>
                        <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold shadow-lg">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@endsection
