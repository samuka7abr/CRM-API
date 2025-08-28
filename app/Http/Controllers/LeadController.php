<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q');
        $status = $request->string('status');
        $owner = $request->integer('owner_id');

        $leads = Lead::query()
            ->with('owner:id,name,email,role')
            ->when($q, fn($qq) => $qq->where(function($x) use ($q) {
                $x->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('company_name','like',"%$q%");
            }))
            ->when($status, fn($qq) => $qq->where('status', $status))
            ->when($owner, fn($qq) => $qq->where('owner_id', $owner))
            ->latest('id')
            ->paginate(15);

        return response()->json($leads);
    }

    public function show(Lead $lead)
    {
        $lead->load('owner:id,name,email,role');
        return response()->json($lead);
    }

    public function store(Request $request)
    {
        $auth = $request->user();
        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:30'],
            'status' => ['nullable', Rule::in(['new','in_progress','won','lost'])],
            'company_name' => ['nullable','string','max:255'],
            'budget' => ['nullable','numeric','min:0'],
            'notes' => ['nullable','string'],
        ]);

        $data['owner_id'] = $auth->id;
        $lead = Lead::create($data);
        return response()->json($lead, 201);
    }

    public function update(Request $request, Lead $lead)
    {
        $auth = $request->user();
        if (!$auth || ($auth->role !== 'admin' && $lead->owner_id !== $auth->id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'email' => ['sometimes','nullable','email','max:255'],
            'phone' => ['sometimes','nullable','string','max:30'],
            'status' => ['sometimes', Rule::in(['new','in_progress','won','lost'])],
            'company_name' => ['sometimes','nullable','string','max:255'],
            'budget' => ['sometimes','numeric','min:0'],
            'notes' => ['sometimes','nullable','string'],
            'owner_id' => ['sometimes','integer','exists:users,id'],
        ]);

        if ($auth->role !== 'admin') {
            unset($data['owner_id']);
        }

        $lead->update($data);
        return response()->json($lead);
    }

    public function destroy(Request $request, Lead $lead)
    {
        $auth = $request->user();
        if (!$auth || ($auth->role !== 'admin' && $lead->owner_id !== $auth->id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $lead->delete();
        return response()->json(['message' => 'Lead deleted']);
    }
}
