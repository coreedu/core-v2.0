<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Inventory extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Inventário';
    protected static ?string $navigationGroup = 'Gestão';
    protected static ?string $slug = 'inventory';
}