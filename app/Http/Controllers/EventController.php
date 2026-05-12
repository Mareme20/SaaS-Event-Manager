<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Resources\EventResource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * (Redirection de sécurité - vous utilisez une modale dans Index.vue)
     */
    public function create()
    {
        return redirect()->route('events.index');
    }

    /**
     * Export the event budget report to PDF.
     */
    public function exportPdf(Event $event)
    {
        //$this->authorize('view', $event);
        
        $event->load('expenses');
        
        $pdf = Pdf::loadView('pdf.report', [
            'event' => $event,
            'budget_real' => $event->budget_real
        ]);

        return $pdf->download("Rapport-{$event->slug}.pdf");
    }

    /**
     * Display the QR Code for the event.
     */
    public function qrCode(Event $event)
    {
       // $this->authorize('view', $event);
        
        $url = route('events.public', $event->slug);
        
        return response(QrCode::size(300)->generate($url))
            ->header('Content-Type', 'image/png');
    }

    /**
     * Display the calendar view for events.
     */
    public function calendar(Request $request)
    {
        $events = $request->user()->events()
            ->select(['id', 'title', 'date', 'location', 'slug'])
            ->get();

        return Inertia::render('Events/Calendar', [
            'events' => $events
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/events",
     *      operationId="getEventsList",
     *      tags={"Events"},
     *      summary="Get list of events",
     *      description="Returns list of events for the authenticated user",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Event"))
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     * )
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $events = $request->user()->events()->latest()->paginate(12);

        if ($request->wantsJson()) {
            return EventResource::collection($events);
        }

        return Inertia::render('Events/Index', [
            'events' => EventResource::collection($events),
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/events",
     *      operationId="storeEvent",
     *      tags={"Events"},
     *      summary="Store new event",
     *      description="Returns created event data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "currency"},
     *              @OA\Property(property="title", type="string", example="Wedding of Alice & Bob"),
     *              @OA\Property(property="description", type="string", example="Grand wedding celebration"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-06-20"),
     *              @OA\Property(property="location", type="string", example="Cotonou, Benin"),
     *              @OA\Property(property="budget_estimated", type="number", format="float", example=5000000.00),
     *              @OA\Property(property="currency", type="string", example="FCFA")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Event")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     * )
     *
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Illuminate\Support\Facades\Gate::denies('create-event')) {
            return redirect()->back()->with('error', 'Vous avez atteint la limite d\'événements pour votre forfait actuel. Veuillez passer à un forfait supérieur.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'budget_estimated' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
        ]);

        $event = $request->user()->events()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'] ?? null,
            'location' => $validated['location'] ?? null,
            'budget_estimated' => $validated['budget_estimated'] ?? 0,
            'currency' => $validated['currency'] ?? 'FCFA',
            'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
            'status' => 'actif', // Ajoutez un statut par défaut
        ]);

        if ($request->wantsJson()) {
            return new EventResource($event);
        }

        return redirect()->route('events.show', $event->id)
            ->with('success', 'Événement créé avec succès !');
    }

    /**
     * @OA\Get(
     *      path="/api/events/slug/{slug}",
     *      operationId="getEventBySlug",
     *      tags={"Events"},
     *      summary="Get event by slug",
     *      description="Returns event data (Public access)",
     *      @OA\Parameter(
     *          name="slug",
     *          description="Event slug",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Event")
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * Display the public event page by slug.
     */
    public function showBySlug($slug)
    {
        $event = Event::where('slug', $slug)
            ->with(['media'])
            ->firstOrFail();

        return Inertia::render('Events/Public', [
            'event' => new EventResource($event),
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/events/{id}",
     *      operationId="getEventById",
     *      tags={"Events"},
     *      summary="Get event information",
     *      description="Returns event data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Event id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Event")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * Display the specified resource.
     */
    public function show(Event $event)
    {
       // $this->authorize('view', $event);
        
        $event->load(['expenses', 'media', 'contributions.member']);

        if (request()->wantsJson()) {
            return new EventResource($event);
        }

        return Inertia::render('Events/Show', [
            'event' => new EventResource($event),
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/events/{id}",
     *      operationId="updateEvent",
     *      tags={"Events"},
     *      summary="Update existing event",
     *      description="Returns updated event data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Event id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "currency"},
     *              @OA\Property(property="title", type="string", example="Wedding of Alice & Bob"),
     *              @OA\Property(property="description", type="string", example="Grand wedding celebration"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-06-20"),
     *              @OA\Property(property="location", type="string", example="Cotonou, Benin"),
     *              @OA\Property(property="budget_estimated", type="number", format="float", example=5000000.00),
     *              @OA\Property(property="currency", type="string", example="FCFA"),
     *              @OA\Property(property="status", type="string", example="actif")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Event")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'budget_estimated' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'status' => 'nullable|string|max:50',
        ]);

        $event->update($validated);

        if ($request->wantsJson()) {
            return new EventResource($event);
        }

        return redirect()->back()->with('success', 'Événement mis à jour !');
    }

    /**
     * Display financial reports for the event.
     */
    public function reports(Event $event)
    {
        $event->load(['expenses', 'contributions.member']);

        // 1. Évolution des cotisations par mois
        $contributionTrends = $event->contributions()
            ->selectRaw("strftime('%Y-%m', date) as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 2. Répartition des dépenses par catégorie
        $expenseDistribution = $event->expenses()
            ->selectRaw("category, SUM(amount) as total")
            ->groupBy('category')
            ->get();

        return Inertia::render('Events/Reports', [
            'event' => $event,
            'stats' => [
                'contribution_trends' => $contributionTrends,
                'expense_distribution' => $expenseDistribution,
                'total_contributions' => (float) $event->total_contributions,
                'total_expenses' => (float) $event->budget_real,
                'budget_estimated' => (float) $event->budget_estimated,
            ]
        ]);
    }
    
    /**
     * Export event data to CSV.
     */
    public function exportCsv(Event $event)
    {
        $event->load(['contributions.member', 'expenses']);
        
        $callback = function() use ($event) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Type', 'Membre/Libellé', 'Montant', 'Catégorie', 'Date', 'Description']);

            foreach ($event->contributions as $c) {
                fputcsv($file, ['Cotisation', $c->member->name, $c->amount, '-', $c->date, $c->description]);
            }

            foreach ($event->expenses as $e) {
                fputcsv($file, ['Dépense', $e->title, $e->amount, $e->category, $e->date, $e->description]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, "Rapport-Financier-{$event->slug}.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }
    
    /**
     * @OA\Delete(
     *      path="/api/events/{id}",
     *      operationId="deleteEvent",
     *      tags={"Events"},
     *      summary="Delete existing event",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Event id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Événement supprimé avec succès")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
       // $this->authorize('delete', $event);
        
        $event->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Événement supprimé avec succès']);
        }

        return redirect()->route('events.index')
            ->with('success', 'Événement supprimé avec succès');
    }
}