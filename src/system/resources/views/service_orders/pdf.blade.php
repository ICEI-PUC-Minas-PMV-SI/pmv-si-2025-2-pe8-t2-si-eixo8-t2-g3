<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>OS #{{ $order->id }}</title>
  <style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { font-size: 12px; }
    .row { display: flex; gap: 16px; }
    .col { flex: 1; }
    .right { text-align: right; }
    .box { border: 1px solid #ddd; border-radius: 6px; padding: 10px; margin-bottom: 10px; }
    .muted { color: #666; }
    table { width:100%; border-collapse: collapse; }
    td, th { padding: 6px; border-bottom: 1px solid #eee; }
    .tot { background: #f7f7f7; font-weight: bold; }
  </style>
</head>
<body>
  <div class="row">
    <div class="col">
      <h2>{{ config('app.name','App') }} • OS</h2>
      <div class="muted">Cliente</div>
      <div><strong>{{ $order->customer_name_snapshot }}</strong></div>
      <div>{{ $order->customer_email_snapshot ?: '—' }}</div>
      <div>{{ $order->customer_phone_snapshot ?: '—' }}</div>
    </div>
    <div class="col right">
      <h3>OS-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h3>
      <div class="muted">Abertura</div>
      <div>{{ optional($order->opened_at)->format('d/m/Y H:i') ?: '—' }}</div>
      <div class="muted">Fechamento</div>
      <div>{{ optional($order->closed_at)->format('d/m/Y H:i') ?: '—' }}</div>
      <div class="muted">Valor Total</div>
      <h3>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</h3>
    </div>
  </div>

  <div class="box">
    <strong>Descrição do problema</strong>
    <div class="muted" style="white-space: pre-wrap;">{{ $order->problem_description ?: '—' }}</div>
  </div>

  <div class="box">
    <strong>Serviços executados</strong>
    <div class="muted" style="white-space: pre-wrap;">{{ $order->services_done ?: '—' }}</div>
  </div>

  <div class="box">
    <strong>Peças / Materiais</strong>
    <div class="muted" style="white-space: pre-wrap;">{{ $order->parts_list ?: '—' }}</div>
  </div>

  @php
    $laborCost  = round(($order->labor_hours ?? 0) * ($order->technician_hour_cost_snapshot ?? 0), 2);
    $travelCost = round(($order->travel_km ?? 0)   * ($order->travel_cost_per_km_snapshot ?? 0), 2);
    $totalCost  = round(($order->parts_cost ?? 0) + $laborCost + $travelCost, 2);
  @endphp

  <table>
    <tbody>
      <tr>
        <td>Peças</td>
        <td class="right">R$ {{ number_format($order->parts_cost ?? 0, 2, ',', '.') }}</td>
      </tr>
      <tr>
        <td>Mão de obra ({{ number_format($order->labor_hours ?? 0, 2, ',', '.') }} h × R$ {{ number_format($order->technician_hour_cost_snapshot ?? 0, 2, ',', '.') }})</td>
        <td class="right">R$ {{ number_format($laborCost, 2, ',', '.') }}</td>
      </tr>
      <tr>
        <td>Deslocamento ({{ number_format($order->travel_km ?? 0, 2, ',', '.') }} km × R$ {{ number_format($order->travel_cost_per_km_snapshot ?? 0, 2, ',', '.') }}/km)</td>
        <td class="right">R$ {{ number_format($travelCost, 2, ',', '.') }}</td>
      </tr>
      <tr class="tot">
        <td>Custo total</td>
        <td class="right">R$ {{ number_format($totalCost, 2, ',', '.') }}</td>
      </tr>
      <tr>
        <td>Valor (faturado)</td>
        <td class="right">R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  @if($order->technician_name)
    <p class="muted" style="margin-top:10px">
      Técnico responsável: <strong>{{ $order->technician_name }}</strong>
    </p>
  @endif
</body>
</html>
