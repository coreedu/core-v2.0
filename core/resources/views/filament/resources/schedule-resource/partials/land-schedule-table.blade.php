@php
    use Illuminate\Support\Str;
@endphp

@if($schedule['context'])
    @foreach($schedule['context'] as $contextId => $context)
        @foreach($context['shifts'] as $shiftId => $shift)
            @foreach($shift['courses'] as $courseId => $sc)
                    <div class="d-inline-flex me-4">
                        <table class="text-sm text-center border-collapse">
                            <thead>
                                <tr>
                                    {{-- Cálculo dinâmico do colspan para o título do curso --}}
                                    @php
                                        $totalCols = 0;
                                        if(isset($sc['days'])) {
                                            foreach($sc['days'] as $d) {
                                                $totalCols += count($d['modules'] ?? []);
                                            }
                                        }
                                    @endphp
                                    <th class="px-3 py-2 border" colspan="{{ $totalCols + 1 }}">
                                        {{ ($sc['name'] ?? '') . ' - ' . ($shift['name'] ?? '')}}<br>
                                        {{ ($context['name'] ?? '') }}
                                    </th>
                                </tr>
                                <tr>
                                    <th class="border"></th>
                                    @foreach($shift['days'] as $idxDay => $day)
                                        @if(isset($sc['days'][$idxDay]['modules']))
                                            <th class="px-3 py-2 border" colspan="{{ count($sc['days'][$idxDay]['modules']) }}">
                                                {{ $day }}
                                            </th>
                                        @endif
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="px-3 py-2 border"></th>
                                    @foreach($shift['days'] as $idxDay => $day)
                                        @if(isset($sc['days'][$idxDay]['modules']))
                                            @foreach($sc['days'][$idxDay]['modules'] as $idxModule => $module)
                                                <th class="px-3 py-2 border">{{ $idxModule }}°</th>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shift['times'] as $idxTime => $time)
                                    <tr>
                                        <td class="px-3 py-2 border">
                                            {{ $time }}
                                        </td>
                                        @foreach($shift['days'] as $idxDay => $day)
                                            @if(isset($sc['days'][$idxDay]['modules']))
                                                @foreach($sc['days'][$idxDay]['modules'] as $module)
                                                    <td class="px-3 py-2 border">
                                                        @if(isset($module['times'][$idxTime]['groups']))
                                                            <div class="d-flex justify-content-center gap-4">
                                                                @foreach($module['times'][$idxTime]['groups'] as $group)
                                                                    <div class="d-flex flex-column"> 
                                                                        <span>{{ Str::limit($group['subject'], 15, '...') }}</span>
                                                                        <span>{{ Str::limit($group['teacher'], 15, '...') }}</span>
                                                                        <span>{{ Str::limit($group['room'], 15, '...') }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            @endforeach
        @endforeach
    @endforeach
@endif