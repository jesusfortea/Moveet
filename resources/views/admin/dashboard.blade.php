@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-[#1E2A28]">Dashboard</h1>
            <p class="mt-1 text-xs text-[#58706e]">Resumen general del panel de administracion.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-xl bg-[#d0dbd9] px-6 py-8 text-center shadow-sm">
                <div class="text-5xl font-black text-[#1E2A28]">{{ $total_usuarios }}</div>
                <div class="mt-4 text-lg font-semibold text-[#1E2A28]">Total usuarios</div>
            </div>

            <div class="rounded-xl bg-[#d0dbd9] px-6 py-8 text-center shadow-sm">
                <div class="text-5xl font-black text-[#1E2A28]">{{ $total_misiones }}</div>
                <div class="mt-4 text-lg font-semibold text-[#1E2A28]">Total misiones</div>
            </div>

            <div class="rounded-xl bg-[#d0dbd9] px-6 py-8 text-center shadow-sm">
                <div class="text-5xl font-black text-[#1E2A28]">{{ $total_eventos }}</div>
                <div class="mt-4 text-lg font-semibold text-[#1E2A28]">Total eventos</div>
            </div>

            <div class="rounded-xl bg-[#d0dbd9] px-6 py-8 text-center shadow-sm">
                <div class="text-5xl font-black text-[#1E2A28]">{{ $total_paseos }}</div>
                <div class="mt-4 text-lg font-semibold text-[#1E2A28]">Pase de paseo</div>
            </div>

            <div class="rounded-xl bg-[#d0dbd9] px-6 py-8 text-center shadow-sm">
                <div class="text-5xl font-black text-[#1E2A28]">{{ $total_lugares }}</div>
                <div class="mt-4 text-lg font-semibold text-[#1E2A28]">Total lugares</div>
            </div>

            <div class="rounded-xl bg-[#d0dbd9] px-6 py-8 text-center shadow-sm">
                <div class="text-5xl font-black text-[#1E2A28]">{{ $total_recompensas }}</div>
                <div class="mt-4 text-lg font-semibold text-[#1E2A28]">Recompensas</div>
            </div>
        </div>
    </div>
@endsection
