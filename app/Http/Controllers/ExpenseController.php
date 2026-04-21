<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Expense;
use App\Http\Resources\ExpenseResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/events/{event}/expenses",
     *      operationId="getExpensesList",
     *      tags={"Expenses"},
     *      summary="Get list of expenses for an event",
     *      description="Returns list of expenses",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="event",
     *          description="Event id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Expense"))
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
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $event->load('expenses');
        
        return Inertia::render('Expenses/Index', [
            'event' => $event,
            'expenses' => ExpenseResource::collection($event->expenses()->latest()->get())
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/events/{event}/expenses",
     *      operationId="storeExpense",
     *      tags={"Expenses"},
     *      summary="Store new expense for an event",
     *      description="Returns created expense data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="event",
     *          description="Event id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "amount", "date"},
     *              @OA\Property(property="title", type="string", example="Catering service"),
     *              @OA\Property(property="amount", type="number", format="float", example=150000.00),
     *              @OA\Property(property="category", type="string", example="Food"),
     *              @OA\Property(property="description", type="string", example="Dinner for 100 people"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-06-21")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Expense")
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $event->expenses()->create($validated);

        return redirect()->back()->with('success', 'Dépense enregistrée');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $expense->update($validated);

        return redirect()->back()->with('success', 'Dépense mise à jour');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->back()->with('success', 'Dépense supprimée');
    }
}
