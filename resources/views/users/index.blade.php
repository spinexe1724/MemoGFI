@extends('layouts.app') {{--  layout --}}

@section('title', 'Home Memo System') {{-- title --}}

@section('content') {{-- content section --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Management</h2>
            <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow">+ Create New Account</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4 font-bold text-gray-600">Name</th>
                            <th class="p-4 font-bold text-gray-600">Email</th>
                            <th class="p-4 font-bold text-gray-600">Division</th>
                            <th class="p-4 font-bold text-gray-600">Role</th>
                            <th class="p-4 font-bold text-gray-600 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 font-semibold text-gray-800">{{ $user->name }}</td>
                            <td class="p-4 text-gray-600">{{ $user->email }}</td>
                             <td class="p-4 text-gray-800">{{ $user->division }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $user->role === 'gm' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            
                            <td class="p-4 text-center">
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 text-sm hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

@endsection