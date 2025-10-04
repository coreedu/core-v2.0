<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Time extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $slug = 'time';
    protected static ?string $navigationGroup = 'Calendário';
    protected static ?string $navigationLabel = 'Horários';
}
