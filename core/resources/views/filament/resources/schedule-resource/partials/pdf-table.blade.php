<div class="me-4 curso">
    <table class="text-sm text-center border-collapse bg-white tabelaHorario">
        <thead class="thead textBig">
            <tr>
                <th class="px-3 py-2 border" colspan="{{count($days)+1}}">{{$idxModule}}° {{$cursoName ?? 'Nome Curso'}} - {{$shift}} - {{$context}}</th>
            </tr>
            <tr>
                <th class="border">
                    
                </th>
                @foreach($days as $idxDay => $day)
                    <th class="px-3 py-2 border">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($times as $idxTime => $time)
                <tr>
                    <td class="px-3 py-2 border timeText">
                        {{$time}}
                    </td>
                    @foreach($days as $idxDay => $day)
                        @if(isset($module['days'][$idxDay]['times'][$idxTime]))
                            <td class="px-3 py-2 border textSmall">
                                <div class="group-container">
                                    @foreach($module['days'][$idxDay]['times'][$idxTime]['groups'] as $group)
                                        <div class="group block"> 
                                            <span>{{Str::limit($group['subject'], 14, '...')}}</span><br>
                                            <span>{{Str::limit($group['teacher'], 14, '...')}}</span><br>
                                            <span>{{Str::limit($group['room'], 14, '...')}}</span>
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