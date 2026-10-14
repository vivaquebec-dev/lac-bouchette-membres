<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Document::class);

        return view('documents.index', [
            'documents' => Document::query()->latest()->latest('id')->get(),
        ]);
    }
}