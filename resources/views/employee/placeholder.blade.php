@extends('layouts.employee')

@section('title', 'Nueva Reserva')

@section('content')
<div class="flex-1 flex items-center justify-center p-8">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 rounded-full bg-primary-fixed flex items-center justify-center text-primary mx-auto mb-6">
            <span class="material-symbols-outlined text-5xl">event_upcoming</span>
        </div>
        <h2 class="text-2xl font-bold text-primary font-headline mb-2">Módulo en Desarrollo</h2>
        <p class="text-secondary text-sm mb-8 leading-relaxed">
            El formulario de <strong>Nueva Reserva</strong> estará disponible en la próxima iteración del sistema.
            Aquí podrás registrar clientes, seleccionar artículos, definir fechas y calcular montos automáticamente.
        </p>
        <a href="{{ route('employee.dashboard') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary-container transition-all active:scale-95 shadow-md">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Volver al Dashboard
        </a>
    </div>
</div>
@endsection
