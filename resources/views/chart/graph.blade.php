<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->date }}</td>
                <td>${{ number_format($sale->amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>