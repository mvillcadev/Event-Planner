<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }

    /* Cabecera */
    .header { background: #00113a; color: #fff; padding: 24px 32px; }
    .header-inner { display: flex; justify-content: space-between; align-items: flex-start; }
    .company-name { font-size: 22px; font-weight: 700; letter-spacing: 1px; }
    .company-sub  { font-size: 10px; color: #a0b4e8; margin-top: 3px; }
    .doc-info     { text-align: right; }
    .doc-title    { font-size: 14px; font-weight: 700; color: #e8eeff; }
    .doc-num      { font-size: 10px; color: #a0b4e8; margin-top: 4px; }
    .doc-date     { font-size: 10px; color: #a0b4e8; margin-top: 2px; }

    /* Cuerpo */
    .body { padding: 28px 32px; }

    /* Sección */
    .section-title { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 12px; margin-top: 20px; }

    /* Dos columnas info */
    .info-grid { display: table; width: 100%; margin-bottom: 4px; }
    .info-col  { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }

    .label { font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; }
    .value { font-size: 12px; font-weight: 600; color: #1a1a2e; margin-top: 2px; }
    .value-sm { font-size: 11px; color: #374151; margin-top: 2px; }

    /* Tabla de artículos */
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    thead tr { background: #f3f4f6; }
    thead th { padding: 8px 10px; text-align: left; font-size: 9px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
    thead th.right { text-align: right; }
    tbody tr { border-bottom: 1px solid #f3f4f6; }
    tbody td { padding: 9px 10px; font-size: 11px; color: #374151; }
    tbody td.right { text-align: right; font-weight: 600; }
    tbody tr:last-child { border-bottom: none; }

    /* Totales */
    .totals-box { margin-top: 16px; background: #f9fafb; border-radius: 6px; padding: 14px 16px; }
    .total-row  { display: table; width: 100%; padding: 3px 0; }
    .total-lbl  { display: table-cell; font-size: 11px; color: #6b7280; }
    .total-val  { display: table-cell; text-align: right; font-size: 11px; font-weight: 600; color: #374151; }
    .total-final-row { display: table; width: 100%; padding: 8px 0 3px; border-top: 2px solid #e5e7eb; margin-top: 6px; }
    .total-final-lbl { display: table-cell; font-size: 13px; font-weight: 700; color: #00113a; }
    .total-final-val { display: table-cell; text-align: right; font-size: 14px; font-weight: 700; color: #00113a; }
    .saldo-row  { display: table; width: 100%; padding: 3px 0; }
    .saldo-lbl  { display: table-cell; font-size: 11px; color: #dc2626; }
    .saldo-val  { display: table-cell; text-align: right; font-size: 12px; font-weight: 700; color: #dc2626; }

    /* Estado badge */
    .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-confirmada { background: #dbeafe; color: #1d4ed8; }
    .badge-completada { background: #d1fae5; color: #065f46; }
    .badge-finalizada { background: #f3f4f6; color: #374151; }
    .badge-pendiente  { background: #fef3c7; color: #92400e; }

    /* Pagos */
    .pago-row { display: table; width: 100%; padding: 5px 0; border-bottom: 1px solid #f3f4f6; }
    .pago-fecha  { display: table-cell; font-size: 10px; color: #6b7280; width: 30%; }
    .pago-metodo { display: table-cell; font-size: 10px; color: #374151; width: 35%; }
    .pago-nota   { display: table-cell; font-size: 10px; color: #9ca3af; width: 25%; }
    .pago-monto  { display: table-cell; text-align: right; font-size: 11px; font-weight: 700; color: #374151; width: 10%; }

    /* Firmas */
    .firmas { display: table; width: 100%; margin-top: 40px; }
    .firma-col { display: table-cell; width: 50%; text-align: center; padding: 0 20px; }
    .firma-line { border-top: 1px solid #9ca3af; margin-bottom: 6px; }
    .firma-name { font-size: 10px; font-weight: 700; color: #374151; }
    .firma-role { font-size: 9px; color: #9ca3af; margin-top: 2px; }

    /* Legal */
    .legal { margin-top: 20px; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fafafa; }
    .legal p { font-size: 8.5px; color: #9ca3af; line-height: 1.5; }
    .legal strong { color: #6b7280; }

    /* Pie */
    .footer { margin-top: 24px; text-align: center; }
    .footer p { font-size: 9px; color: #d1d5db; }
</style>
</head>
<body>

{{-- Cabecera --}}
<div class="header">
    <div class="header-inner">
        <div>
            <div class="company-name">Vanessa Event Planner</div>
            <div class="company-sub">Alquiler de equipos y planificación de eventos</div>
        </div>
        <div class="doc-info">
            <div class="doc-title">COMPROBANTE DE RESERVA</div>
            <div class="doc-num">N° {{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="doc-date">Emitido: {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

<div class="body">

    {{-- Datos del evento --}}
    <div class="section-title">Datos del Evento</div>
    <div class="info-grid">
        <div class="info-col">
            <div class="label">Nombre del Evento</div>
            <div class="value">{{ $reserva->descripcion ?? 'Reserva #' . $reserva->id }}</div>
        </div>
        <div class="info-col">
            <div class="label">Estado</div>
            <div class="value" style="margin-top:4px">
                <span class="badge badge-{{ $reserva->estado }}">{{ $reserva->estado }}</span>
            </div>
        </div>
    </div>
    <div class="info-grid" style="margin-top:10px">
        <div class="info-col">
            <div class="label">Fecha de Inicio</div>
            <div class="value-sm">{{ $reserva->fecha_inicio->format('d \d\e F \d\e Y') }}</div>
        </div>
        <div class="info-col">
            <div class="label">Fecha de Devolución</div>
            <div class="value-sm">{{ $reserva->fecha_fin->format('d \d\e F \d\e Y') }}</div>
        </div>
    </div>

    {{-- Datos del cliente --}}
    <div class="section-title">Datos del Cliente</div>
    <div class="info-grid">
        <div class="info-col">
            <div class="label">Nombre Completo</div>
            <div class="value">{{ $reserva->cliente->nombre_completo ?? '—' }}</div>
        </div>
        <div class="info-col">
            <div class="label">CI / NIT</div>
            <div class="value-sm">{{ $reserva->cliente->ci_nit ?? '—' }}</div>
        </div>
    </div>
    <div class="info-grid" style="margin-top:8px">
        <div class="info-col">
            <div class="label">Teléfono de Contacto</div>
            <div class="value-sm">{{ $reserva->cliente->telefono ?? '—' }}</div>
        </div>
    </div>
    @if($reserva->cliente->email || $reserva->cliente->direccion)
    <div class="info-grid" style="margin-top:10px">
        @if($reserva->cliente->email)
        <div class="info-col">
            <div class="label">Correo Electrónico</div>
            <div class="value-sm">{{ $reserva->cliente->email }}</div>
        </div>
        @endif
        @if($reserva->cliente->direccion)
        <div class="info-col">
            <div class="label">Dirección de Entrega</div>
            <div class="value-sm">{{ $reserva->cliente->direccion }}</div>
        </div>
        @endif
    </div>
    @endif

    {{-- Artículos --}}
    <div class="section-title">Detalle de Artículos</div>
    <table>
        <thead>
            <tr>
                <th>Artículo / Equipo</th>
                <th style="width:70px; text-align:center">Cantidad</th>
                <th class="right" style="width:100px">P. Unitario</th>
                <th class="right" style="width:100px">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reserva->detalles as $detalle)
            <tr>
                <td>{{ $detalle->articulo->nombre ?? '—' }}</td>
                <td style="text-align:center">{{ $detalle->cantidad }}</td>
                <td class="right">Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="right">Bs. {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <div class="totals-box">
        <div class="total-row">
            <span class="total-lbl">Subtotal</span>
            <span class="total-val">Bs. {{ number_format($reserva->monto_total, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-lbl">Total Pagado</span>
            <span class="total-val">Bs. {{ number_format($totalPagado, 2) }}</span>
        </div>
        <div class="total-final-row">
            <span class="total-final-lbl">MONTO TOTAL</span>
            <span class="total-final-val">Bs. {{ number_format($reserva->monto_total, 2) }}</span>
        </div>
        @if($saldoPendiente > 0)
        <div class="saldo-row">
            <span class="saldo-lbl">Saldo Pendiente</span>
            <span class="saldo-val">Bs. {{ number_format($saldoPendiente, 2) }}</span>
        </div>
        @endif
    </div>

    {{-- Historial de pagos --}}
    @if($reserva->pagos->isNotEmpty())
    <div class="section-title">Historial de Pagos</div>
    @foreach($reserva->pagos as $pago)
    <div class="pago-row">
        <span class="pago-fecha">{{ $pago->fecha_pago->format('d/m/Y') }}</span>
        <span class="pago-metodo">{{ ucfirst($pago->metodo) }}</span>
        <span class="pago-nota">{{ $pago->nota ?? '—' }}</span>
        <span class="pago-monto">Bs. {{ number_format($pago->monto, 2) }}</span>
    </div>
    @endforeach
    @endif

    {{-- Firmas --}}
    <div class="firmas">
        <div class="firma-col">
            <br><br><br>
            <div class="firma-line"></div>
            <div class="firma-name">{{ $reserva->cliente->nombre_completo ?? 'Cliente' }}</div>
            <div class="firma-role">Firma del Cliente</div>
        </div>
        <div class="firma-col">
            <br><br><br>
            <div class="firma-line"></div>
            <div class="firma-name">Vanessa Event Planner</div>
            <div class="firma-role">Representante Autorizado</div>
        </div>
    </div>

    {{-- Cláusulas legales --}}
    <div class="legal">
        <p><strong>TÉRMINOS Y CONDICIONES:</strong> El cliente acepta hacerse responsable de los artículos recibidos en alquiler y se compromete a devolverlos en las mismas condiciones en que fueron entregados. Cualquier daño, pérdida o deterioro será cobrado al cliente según el costo de reposición. La empresa no se hace responsable por accidentes derivados del uso inadecuado del equipo. La reserva queda confirmada con el pago del adelanto acordado. La devolución debe realizarse en la fecha indicada; en caso de retraso se aplicará un cargo adicional por día.</p>
    </div>

    <div class="footer">
        <p>Vanessa Event Planner · Cochabamba, Bolivia · Documento generado electrónicamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</div>
</body>
</html>
