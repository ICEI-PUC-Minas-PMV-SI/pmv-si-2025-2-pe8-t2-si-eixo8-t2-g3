<?php

namespace App\Http\Controllers;

use App\ServiceOrder;
use App\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ServiceOrderController extends Controller
{
    /**
     * Lista as OS com busca e filtros simples.
     */
    public function index(Request $request)
    {
        $q               = trim((string) $request->input('q', ''));
        $statusParam     = trim((string) $request->input('status', ''));
        $serviceType     = trim((string) $request->input('service_type', ''));
        $problemCategory = trim((string) $request->input('problem_category', ''));

        $from = $this->safeDate($request->input('from'));
        $to   = $this->safeDate($request->input('to'));

        $statusOptions       = $this->statusOptions();
        $validStatus         = in_array($statusParam, $statusOptions, true) ? $statusParam : null;

        // >>> ATUALIZAÇÃO AQUI: usa os novos tipos
        $serviceTypeOptions  = array_keys($this->serviceTypeMap());          // ['installation','maintenance','cleaning','parts_replacement']
        $validServiceType    = in_array($serviceType, $serviceTypeOptions, true) ? $serviceType : null;

        // (opcional) validar categoria
        $problemCategoryOpts = array_keys($this->problemCategoryMap());
        $validProblemCategory= in_array($problemCategory, $problemCategoryOpts, true) ? $problemCategory : null;

        $orders = ServiceOrder::query()
            ->with('customer:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($q2) use ($like) {
                    $q2->where('customer_name_snapshot', 'like', $like)
                    ->orWhere('customer_email_snapshot', 'like', $like)
                    ->orWhere('customer_phone_snapshot', 'like', $like)
                    ->orWhere('technician_name', 'like', $like)
                    ->orWhere('problem_description', 'like', $like)
                    ->orWhereHas('customer', function ($q3) use ($like) {
                        $q3->where('name', 'like', $like)
                            ->orWhere('cnpj', 'like', $like)
                            ->orWhere('email', 'like', $like)
                            ->orWhere('phone', 'like', $like);
                    });
                });
            })
            
            ->when($validStatus, fn($q2) => $q2->where('status', $validStatus))
            ->when($validServiceType, fn($q2) => $q2->where('service_type', $validServiceType))
            ->when($validProblemCategory, fn($q2) => $q2->where('problem_category', $validProblemCategory))
            ->when($from, fn($q2) => $q2->whereDate('opened_at', '>=', $from->toDateString()))
            ->when($to,   fn($q2) => $q2->whereDate('opened_at', '<=', $to->toDateString()))
            ->orderByDesc('id')
            ->orderByDesc('opened_at')
            ->paginate(15)
            ->appends($request->query());

        if ($orders->isEmpty() && $orders->currentPage() > 1) {
            return redirect()->route('service-orders.index', array_merge(
                $request->except('page'),
                ['page' => $orders->lastPage() ?: 1]
            ));
        }

        return view('service_orders.index', compact('orders', 'statusOptions', 'serviceTypeOptions'));
    }

    /**
     * Converte string para Carbon com segurança; retorna null se inválida.
     */
    private function safeDate($value): ?Carbon
    {
        if (!$value) return null;
        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Formulário de criação.
     */
    public function create()
    {
        $statusOptions      = $this->statusOptions();
        $serviceTypeOptions = ['installation','maintenance','cleaning','parts_replacement'];
        $clients = Client::orderBy('name')->pluck('name', 'id');

        return view('service_orders.create', compact('clients', 'statusOptions', 'serviceTypeOptions'));
    }

    /**
     * Persiste uma nova OS.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Snapshots do cliente
        $client = Client::findOrFail($data['customer_id']);
        $data['customer_name_snapshot']  = $client->name;
        $data['customer_phone_snapshot'] = $client->phone;
        $data['customer_email_snapshot'] = $client->email;

        // abertas agora, caso não venha
        $data['opened_at'] = $data['opened_at'] ?? now();

        $order = ServiceOrder::create($data);

        if ($request->wantsJson()) {
            return response()->json($order, 201);
        }

        return redirect()
            ->route('service-orders.show', $order)
            ->with('success', 'Ordem de serviço criada com sucesso!');
    }

    /**
     * Detalhe.
     */
    public function show(ServiceOrder $service_order)
    {
        $service_order->load('customer');

        $originLat = (float) env('COMPANY_BASE_LAT', -19.8562227);
        $originLng = (float) env('COMPANY_BASE_LNG', -43.9036703);

        $c = $service_order->customer;

        // compatibilidade: lat/lng OU latitude/longitude
        $destLat = $c->lat ?? $c->latitude ?? null;
        $destLng = $c->lng ?? $c->longitude ?? null;

        $routeInfo = null;
        if ($destLat !== null && $destLng !== null) {
            // tenta rota (ORS)
            $routeInfo = $this->routeKmAndMinutes($originLat, $originLng, (float)$destLat, (float)$destLng);

            // fallback: Haversine (linha reta)
            if (!$routeInfo) {
                $km = $this->haversineKm($originLat, $originLng, (float)$destLat, (float)$destLng);
                // ETA aproximada por velocidade média (opcional): 35 km/h na cidade
                $etaMinutes = (int) round(($km / 35) * 60);
                $routeInfo = ['km' => round($km, 2), 'minutes' => $etaMinutes, 'fallback' => true];
            }
        }

        return view('service_orders.show', [
            'order' => $service_order,
            'routeInfo' => $routeInfo,
        ]);
    }

    /**
     * Formulário de edição.
     */
    public function edit(ServiceOrder $service_order)
    {
        $statusOptions      = $this->statusOptions();
        $serviceTypeOptions = ['preventive','corrective','install','other'];
        $clients = Client::orderBy('name')->pluck('name', 'id');

        return view('service_orders.edit', [
            'order' => $service_order,
            'clients' => $clients,
            'statusOptions' => $statusOptions,
            'serviceTypeOptions' => $serviceTypeOptions,
        ]);
    }

    /**
     * Atualiza uma OS.
     */
    public function update(Request $request, ServiceOrder $service_order)
    {
        $data = $this->validated($request, $service_order->id);

        // Atualiza snapshots se trocar o cliente
        if (isset($data['customer_id']) && $data['customer_id'] != $service_order->customer_id) {
            $client = Client::findOrFail($data['customer_id']);
            $data['customer_name_snapshot']  = $client->name;
            $data['customer_phone_snapshot'] = $client->phone;
            $data['customer_email_snapshot'] = $client->email;
        }

        $service_order->update($data);

        if ($request->wantsJson()) {
            return response()->json($service_order);
        }

        return redirect()
            ->route('service-orders.show', $service_order)
            ->with('success', 'Ordem de serviço atualizada com sucesso!');
    }

    /**
     * Remove uma OS.
     */
    public function destroy(ServiceOrder $service_order)
    {
        $service_order->delete();

        return redirect()
            ->route('service-orders.index')
            ->with('success', 'Ordem de serviço removida com sucesso!');
    }

    /**
     * (Opcional) Encerrar a OS rapidamente.
     * Dica: você pode passar 'resolved_in_first_visit' via request se quiser marcar ao fechar.
     */
    public function close(Request $request, ServiceOrder $service_order)
    {
        $payload = [
            'status'    => 'closed',
            'closed_at' => now(),
        ];

        if ($request->has('resolved_in_first_visit')) {
            $payload['resolved_in_first_visit'] = (bool) $request->boolean('resolved_in_first_visit');
        }

        $service_order->update($payload);

        return redirect()
            ->route('service-orders.show', $service_order)
            ->with('success', 'Ordem de serviço encerrada.');
    }

    /**
     * Validação compartilhada.
     */
    private function validated(Request $request, $id = null): array
    {
        $status = $this->statusOptions();

        // pegue as chaves (values que vão pro DB)
        $serviceTypeOptions = array_keys($this->serviceTypeMap());
        $problemCats        = array_keys($this->problemCategoryMap());

        return $request->validate([
            'customer_id' => ['required', 'exists:clients,id'],

            'opened_at'   => ['nullable', 'date'],
            'closed_at'   => ['nullable', 'date', 'after_or_equal:opened_at'],

            'status'      => ['required', Rule::in($status)],

            'problem_description' => ['nullable', 'string'],
            'problem_category'    => ['nullable', Rule::in($problemCats)],

            'services_done'       => ['nullable', 'string'],
            'parts_list'          => ['nullable', 'string'],

            'service_type' => ['required', Rule::in($serviceTypeOptions)],

            'technician_name'               => ['nullable', 'string', 'max:255'],
            'labor_hours'                   => ['nullable', 'numeric', 'min:0'],
            'travel_km'                     => ['nullable', 'numeric', 'min:0'],
            'travel_cost_per_km_snapshot'   => ['nullable', 'numeric', 'min:0'],
            'technician_hour_cost_snapshot' => ['nullable', 'numeric', 'min:0'],
            'parts_cost'                    => ['nullable', 'numeric', 'min:0'],

            'resolved_in_first_visit'       => ['nullable', 'boolean'],

            'total_amount' => ['required', 'numeric', 'min:0'],
        ]);
    }


    /**
     * Lista canônica de status.
     */
    private function statusOptions(): array
    {
        return ['opened', 'in_progress', 'paused', 'closed', 'canceled'];
    }

    // Lista canônica de tipos de serviço (valor => rótulo)
    private function serviceTypeMap(): array
    {
        return [
            'installation'      => 'Instalação',
            'maintenance'       => 'Manutenção',
            'cleaning'          => 'Limpeza',
            'parts_replacement' => 'Troca de Peças',
        ];
    }

    // Lista canônica de categorias de problema (valor => rótulo)
    private function problemCategoryMap(): array
    {
        return [
            'energia_alimentacao'    => 'Energia / Alimentação',
            'mecanico_transporte'    => 'Mecânico / Transporte',
            'impressao_cabeca'       => 'Impressão / Cabeça',
            'calibracao_alinhamento' => 'Calibração / Alinhamento',
            'firmware_software'      => 'Firmware / Software',
            'rede_conectividade'     => 'Rede / Conectividade',
            'sensores_leituras'      => 'Sensores / Leituras',
            'limpeza_preventiva'     => 'Limpeza / Preventiva',
            'acabamento'             => 'Acabamento',
            'outros'                 => 'Outros',
        ];
    }

    public function pdf(ServiceOrder $service_order)
    {
        $service_order->load('customer');

        // Use um blade específico para PDF (melhor controlar CSS)
        $pdf = \PDF::loadView('service_orders.pdf', ['order' => $service_order])
                ->setPaper('a4', 'portrait'); // ou landscape

        $filename = 'OS-' . str_pad($service_order->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        // baixar:
        return $pdf->download($filename);
        // ou exibir no navegador:
        // return $pdf->stream($filename);
    }

    private function routeKmAndMinutes(float $lat1, float $lon1, float $lat2, float $lon2): ?array
    {
        try {
            $res = Http::withHeaders(['Authorization' => env('ORS_API_KEY')])
                ->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                    'coordinates' => [[$lon1, $lat1], [$lon2, $lat2]], // [lon, lat]!
                    'units' => 'km'
                ]);

            if (!$res->successful()) return null;

            $json    = $res->json();
            $summary = $json['features'][0]['properties']['summary'] ?? null;
            if (!$summary) return null;

            $km      = (float) $summary['distance'];     // já em km (por causa de units=km)
            $minutes = (float) $summary['duration'] / 60; // segundos -> minutos

            return ['km' => round($km, 2), 'minutes' => (int) round($minutes)];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        return $R * 2 * asin(min(1, sqrt($a)));
    }

}
