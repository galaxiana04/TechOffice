<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume</title>
    <style>
        body {
            border: 4px solid black;
            padding: 20px;
            margin: 20px;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Resume</h2>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Jumlah Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resumeData as $data)
                    <tr>
                        <td>{{ $data['unit'] }}</td>
                        <td>{{ $data['document_count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>