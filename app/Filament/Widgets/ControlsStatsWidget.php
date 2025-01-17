<?php

namespace App\Filament\Widgets;

use App\Enums\Effectiveness;
use App\Models\Control;
use Filament\Widgets\ChartWidget;

class ControlsStatsWidget extends ChartWidget
{
    protected static ?string $heading = 'Control Effectiveness';

    protected static ?string $maxHeight = '250px';

    protected int|string|array $columnSpan = '1';

    protected static ?int $sort = 2;

    protected function getData(): array
    {

        $in_scope_standards = \App\Models\Standard::where('status', 'In Scope')->get();

        $effective = Control::where('effectiveness', Effectiveness::EFFECTIVE)->count();
//            ->whereIn('standard_id', $in_scope_standards->pluck('id'))
//            ->count() ?: 0;
        $partial = Control::where('effectiveness', Effectiveness::PARTIAL)
            ->whereIn('standard_id', $in_scope_standards->pluck('id'))
            ->count() ?: 0;
        $ineffective = Control::where('effectiveness', Effectiveness::INEFFECTIVE)
            ->whereIn('standard_id', $in_scope_standards->pluck('id'))
            ->count() ?: 0;
        $unknown = Control::where('effectiveness', Effectiveness::UNKNOWN)
            ->whereIn('standard_id', $in_scope_standards->pluck('id'))
            ->count() ?: 0;

        return [
            'labels' => ['Effective', 'Partially Effective', 'Ineffective', 'Not Assessed'],
            'datasets' => [
                [
                    'data' => [$effective, $partial, $ineffective, $unknown],
                    'backgroundColor' => [
                        'rgb(45, 180, 45)',
                        'rgb(220, 180, 35)',
                        'rgb(255, 99, 132)',
                        'rgb(90, 90, 90)',
                    ],
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
