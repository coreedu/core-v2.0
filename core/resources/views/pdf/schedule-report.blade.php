@php
    use Illuminate\Support\Str;
@endphp

@foreach($schedule['courses'] as $sc)
    @foreach($sc['shifts'] as $idxShift => $shift)
        @foreach($shift['modules'] as $idxModule => $module)
            <div class="d-inline-flex me-4">
                <table class="text-sm text-center border-collapse bg-white">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 border" colspan="{{count($schedule['days'])}}">{{$idxModule}}° {{$sc['name'] ?? 'Nome Curso'}} - Periodo</th>
                        </tr>
                        <tr>
                            <th class="border">
                                
                            </th>
                            @foreach($schedule['days'] as $idxDay => $day)
                                <th class="px-3 py-2 border">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule['times'][$idxShift] as $idxTime => $time)
                            <tr>
                                <td class="px-3 py-2 border">
                                    {{$time}}
                                </td>
                                @foreach($schedule['days'] as $day)
                                    @if(isset($day['times'][$idxTime]))
                                        <td class="px-3 py-2 border">
                                            <div class="d-flex justify-content-center gap-4">
                                                @foreach($day['times'][$idxTime]['groups'] as $group)
                                                    <div class="d-flex flex-column"> 
                                                        <span>{{Str::limit($group['subject'], 15, '...')}}</span>
                                                        <span>{{Str::limit($group['teacher'], 15, '...')}}</span>
                                                        <span>{{Str::limit($group['room'], 15, '...')}}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    @else
                                        <td class="px-3 py-2 border">-</td>
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