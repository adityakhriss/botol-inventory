<?php

namespace App\Http\Controllers;

use App\Models\Bottle;
use App\Models\CheckbotItem;
use App\Models\CheckbotRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckbotController extends Controller
{
    /**
     * @var array<string>
     */
    private array $types = [
        Bottle::TYPE_PLASTIK_BESAR,
        Bottle::TYPE_PLASTIK_KECIL,
        Bottle::TYPE_KACA_BESAR,
        Bottle::TYPE_KACA_KECIL,
    ];

    public function index(): View
    {
        $populationByType = Bottle::query()
            ->select('type', DB::raw('count(*) as total'))
            ->where('status', Bottle::STATUS_AVAILABLE)
            ->groupBy('type')
            ->pluck('total', 'type');

        $samplingPlan = collect($this->types)->mapWithKeys(function (string $type) use ($populationByType): array {
            $population = (int) ($populationByType[$type] ?? 0);

            return [
                $type => [
                    'population' => $population,
                    'sample' => $this->calculateSampleCount($population),
                ],
            ];
        });

        $runs = CheckbotRun::withCount('items')
            ->with(['creator'])
            ->latest()
            ->paginate(10);

        return view('checkbot.index', [
            'samplingPlan' => $samplingPlan,
            'runs' => $runs,
        ]);
    }

    public function storeRun(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'analyst_name' => 'required|string|max:100',
            'tested_at' => 'required|date',
        ]);

        $run = DB::transaction(function () use ($data, $request): CheckbotRun {
            $run = CheckbotRun::create([
                'analyst_name' => $data['analyst_name'],
                'tested_at' => $data['tested_at'],
                'sampling_rate' => '5%',
                'created_by' => $request->user()->id,
            ]);

            foreach ($this->types as $type) {
                $population = Bottle::where('type', $type)
                    ->where('status', Bottle::STATUS_AVAILABLE)
                    ->count();

                $sampleCount = $this->calculateSampleCount($population);

                if ($sampleCount === 0) {
                    continue;
                }

                $samples = Bottle::where('type', $type)
                    ->where('status', Bottle::STATUS_AVAILABLE)
                    ->inRandomOrder()
                    ->take($sampleCount)
                    ->get();

                foreach ($samples as $bottle) {
                    CheckbotItem::create([
                        'checkbot_run_id' => $run->id,
                        'bottle_id' => $bottle->id,
                        'bottle_code_snapshot' => $bottle->code,
                    ]);
                }
            }

            return $run;
        });

        if (!$run->items()->exists()) {
            $run->delete();

            return redirect()->route('checkbot.index')->withErrors([
                'run' => 'Tidak ada botol AVAILABLE untuk disampling.',
            ]);
        }

        return redirect()
            ->route('checkbot.runs.show', $run)
            ->with('success', 'Sampel checkbot 5% berhasil dibuat. Silakan isi hasil uji.');
    }

    public function showRun(CheckbotRun $run): View
    {
        $run->load(['items.bottle', 'creator']);

        return view('checkbot.show', [
            'run' => $run,
            'groupedItems' => $run->items->groupBy(fn (CheckbotItem $item): string => $item->bottle?->type ?? '-'),
        ]);
    }

    public function saveResults(Request $request, CheckbotRun $run): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:checkbot_items,id',
            'items.*.parameter' => 'required|string|max:1000',
            'items.*.test_result' => 'required|string|max:2000',
            'items.*.status' => 'required|in:LULUS,BELUM_LULUS',
        ]);

        $itemsPayload = collect($validated['items']);

        $validIds = $run->items()->pluck('id');
        $payloadIds = $itemsPayload->pluck('id');

        if ($payloadIds->diff($validIds)->isNotEmpty()) {
            return back()->withErrors([
                'items' => 'Ada data item yang tidak valid untuk run ini.',
            ]);
        }

        DB::transaction(function () use ($itemsPayload): void {
            $itemsPayload->each(function (array $itemData): void {
                CheckbotItem::whereKey($itemData['id'])->update([
                    'parameter' => $itemData['parameter'],
                    'test_result' => $itemData['test_result'],
                    'status' => $itemData['status'],
                ]);
            });
        });

        return redirect()->route('checkbot.runs.show', $run)
            ->with('success', 'Hasil uji kualitas botol berhasil disimpan.');
    }

    private function calculateSampleCount(int $population): int
    {
        if ($population <= 0) {
            return 0;
        }

        return max(1, (int) ceil($population * 0.05));
    }
}
