<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Pega a busca como string simples
        $q = trim((string) $request->query('q', ''));

        $clients = Client::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($q2) use ($like) {
                    $q2->where('name', 'like', $like)
                    ->orWhere('cnpj', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    /**
     * ✅ MÉTODO QUE ESTAVA FALTANDO
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        \Log::debug('CLIENTE_STORE_PAYLOAD', ['data_to_create' => $data]);

        $client = Client::create($data);

        if ($request->wantsJson()) {
            return response()->json($client, 201);
        }

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Cliente criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $data = $this->validated($request, $client->id);

        \Log::debug('CLIENTE_UPDATE_PAYLOAD', [
            'data_to_update' => $data,
            'client_id' => $client->id
        ]);
        
        $client->update($data);

        if ($request->wantsJson()) {
            return response()->json($client);
        }

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente removido com sucesso!');
    }
    
    /**
     * Valida e normaliza os dados do request.
     */
    private function validated(Request $request, $id = null): array
    {
        $request->merge([
            'cnpj'  => preg_replace('/\D/', '', (string) $request->input('cnpj')),
            'phone' => $request->filled('phone')
                ? preg_replace('/\D/', '', (string) $request->input('phone'))
                : null,
            'cep'   => preg_replace('/\D/', '', (string) $request->input('cep')),
            'state' => $request->filled('state')
                ? strtoupper((string) $request->input('state'))
                : null,
            'city'      => $request->filled('city') ? trim($request->input('city')) : null,
            'latitude' => $request->filled('latitude')
                ? str_replace(',', '.', $request->input('latitude'))
                : null,
            'longitude' => $request->filled('longitude')
                ? str_replace(',', '.', $request->input('longitude'))
                : null,
        ]);

        return $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'cnpj'       => ['required', 'digits:14', Rule::unique('clients', 'cnpj')->ignore($id)],
            'email'      => ['nullable', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($id)],
            'phone'      => ['nullable', 'digits_between:10,11'],
            'street'     => ['nullable', 'string', 'max:255'],
            'complement' => ['required', 'string', 'max:150'],
            'city'       => ['nullable', 'string', 'max:255'],
            'district'   => ['nullable', 'string', 'max:120'],
            'state'      => ['nullable', 'string', 'size:2'],
            'cep'        => ['required', 'digits:8'],
            'machines'   => ['nullable', 'string'],
            'codigo_ibge' => ['nullable', 'integer'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
        ]);
    }

    public function getLocationsForMap()
    {
        $locations = Client::query()
            // 1. Garante que só pegamos clientes com coordenadas válidas
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            // 2. Seleciona apenas os dados que o mapa precisa
            ->select('id', 'name', 'latitude', 'longitude', 'street', 'city')
            ->orderBy('name')
            ->get();

        // 3. Retorna os dados como uma resposta JSON
        return response()->json($locations);
    }
}