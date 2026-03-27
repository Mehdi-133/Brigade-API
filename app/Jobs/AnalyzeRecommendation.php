<?php

namespace App\Jobs;

use App\Models\Recommendations;
use App\Services\GroqService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeRecommendation implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public Recommendations $recommendation) {}

    public function handle(): void
    {
        $recommendation = Recommendations::find($this->recommendation->id);

        if (!$recommendation) return;

        $plat = $recommendation->plat()->with('ingredients')->first();

        if (!$plat) return;

        $user        = $recommendation->user;
        $dietaryTags = $user->dietary_tags ?? [];
        $ingredients = $plat->ingredients->toArray();

        try {
            $result = (new GroqService())->analyzeDish(
                $dietaryTags,
                $plat->name,
                $ingredients
            );

            $recommendation->update([
                'score'           => $result['score'],
                'warning_message' => $result['warning_message'] ?? null,
                'status'          => $result['status'] ?? 'pending',
            ]);

        } catch (\Exception $e) {
            Log::error('AnalyzeRecommendation failed: ' . $e->getMessage());

            $recommendation->update([
                'score'           => 0,
                'warning_message' => $e->getMessage(),
                'status'          => 'pending',
            ]);
        }
    }
}
