<div class="flex flex-col items-center w-full space-y-6">
    <!-- Linha superior: dois gráficos centralizados -->
    <div class="flex justify-center gap-6 w-10/12">
        <x-filament::card class="w-5/12 h-[250px] flex items-center justify-center">
            {{ $this->chart1 }}
        </x-filament::card>

        <x-filament::card class="w-5/12 h-[250px] flex items-center justify-center">
            {{ $this->chart2 }}
        </x-filament::card>
    </div>

    <!-- Gráfico inferior ocupando toda a largura -->
 <div class="w-10/12">
    <x-filament::card class="h-[250px] flex items-center justify-center">
        {{ $this->chart3 }}
    </x-filament::card>
</div>
