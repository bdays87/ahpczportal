<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, Arial, sans-serif; }
        body { font-size: 10px; color: #1f2937; }
        h2 { margin: 0 0 2px 0; font-size: 16px; }
        .meta { color: #6b7280; font-size: 9px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background-color: #1d4ed8;
            color: #ffffff;
            text-align: left;
            padding: 6px 5px;
            border: 1px solid #1e40af;
            font-size: 10px;
        }
        tbody td {
            padding: 4px 5px;
            border: 1px solid #d1d5db;
            font-size: 9px;
        }
        tbody tr:nth-child(even) { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <div class="meta">Generated {{ $generatedAt }} &bull; {{ count($rows) }} contact(s)</div>

    <table>
        <thead>
            <tr>
                @foreach ($columns as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
