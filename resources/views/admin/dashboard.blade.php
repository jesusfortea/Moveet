@extends('layouts.admin')

@section('title', 'Dashboard Admin · Moveet')

@section('content')
<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-number">{{ $total_usuarios }}</div>
        <div class="admin-stat-label">Total usuarios</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number">{{ $total_misiones }}</div>
        <div class="admin-stat-label">Total misiones</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number">{{ $total_eventos }}</div>
        <div class="admin-stat-label">Total eventos</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number">{{ $total_paseos }}</div>
        <div class="admin-stat-label">Pase de paseo</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number">{{ $total_lugares }}</div>
        <div class="admin-stat-label">Total lugares</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number">{{ $total_recompensas }}</div>
        <div class="admin-stat-label">Recompensas</div>
    </div>
</div>
@endsection
