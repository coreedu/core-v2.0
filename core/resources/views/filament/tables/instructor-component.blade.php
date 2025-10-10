<table class="min-w-full text-sm border border-gray-200 rounded-lg">
    <thead class="bg-gray-50 text-gray-700">
        <tr>
            <th class="p-2 text-left">Código</th>
            <th class="p-2 text-left">Nome</th>
            <th class="p-2 text-left">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($components as $comp)
            <tr class="border-t">
                <td class="p-2">{{ $comp->abreviacao }}</td>
                <td class="p-2">{{ $comp->nome }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="p-2 text-gray-400 text-center">Nenhum componente vinculado.</td>
            </tr>
        @endforelse
    </tbody>
</table>
