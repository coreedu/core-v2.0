@if($schedule['courses'])
    @foreach($schedule['courses'] as $sc)
        <table class="max-w-full w-full text-sm text-center border-collapse">
            <thead>
                <tr>
                    <th class="px-3 py-2 border" colspan="{{$schedule['weight']}}">{{ $sc['name'] }}</th>
                </tr>
                <tr>
                    <th class="border">
                        Dias
                    </th>
                    @foreach($schedule['days'] as $idxDay => $day)
                        <th class="px-3 py-2 border" colspan="{{count($sc['days'][$idxDay]['modules'])}}">{{ $day }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="px-3 py-2 border">
                        
                    </th>
                    @foreach($schedule['days'] as $idxDay => $day)
                        @foreach($sc['days'][$idxDay]['modules'] as $idxModule => $module)
                            <th class="px-3 py-2 border">{{ $idxModule }}°</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($schedule['times'] as $idxTime => $time)
                    <tr> 
                        <td class="px-3 py-2 border">
                            {{$time}}
                        </td>
                        @foreach($schedule['days'] as $day)
                            @if(isset($sc['days'][$idxDay]['modules']))
                                @foreach($sc['days'][$idxDay]['modules'] as $module)
                                    @if(isset($module['times'][$idxTime]))
                                        <td class="px-3 py-2 border">
                                            <div class="flex justify-center gap-4">
                                                @foreach($module['times'][$idxTime]['groups'] as $group)
                                                    <div class="flex flex-column">
                                                        <span>{{$group['subject']}}</span>
                                                        <span>{{$group['teacher']}}</span>
                                                        <span>{{$group['room']}}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    @else
                                        <td class="px-3 py-2 border">-</td>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                    </tr>
                @endforeach
            </tbody>
            
        </table>
    @endforeach
@endif
