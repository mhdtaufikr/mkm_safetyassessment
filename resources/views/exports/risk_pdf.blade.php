<!DOCTYPE html>
<html>
<head>
    <title>Risk Assessments PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h2>Risk Assessment Report</h2>
    <table>
        <thead>
            <tr>
                <th>Shop</th>
                <th>Scope</th>
                <th>Problem</th>
                <th>Hazard</th>
                <th>Severity</th>
                <th>Probability</th>
                <th>Score</th>
                <th>Level</th>
                <th>Measure</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assessments as $a)
                <tr>
                    <td>{{ $a->shop->name ?? 'Unknown' }}</td>
                    <td>{{ $a->scope_number }}</td>
                    <td>{{ $a->finding_problem }}</td>
                    <td>{{ $a->potential_hazards }}</td>
                    <td>{{ $a->severity }}</td>
                    <td>{{ $a->possibility }}</td>
                    <td>{{ $a->score }}</td>
                    <td>{{ $a->risk_level }}</td>
                    <td>{{ $a->risk_reduction_proposal }}</td>
                    <td>{{ $a->is_followed_up ? 'Close' : 'Open' }}</td>
                    <td>{{ optional($a->created_at)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
