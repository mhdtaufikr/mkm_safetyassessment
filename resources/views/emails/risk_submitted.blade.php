<h2>New Risk Assessment Submitted</h2>

<p>Dear Team,</p>

<p>
We would like to inform you that a <strong>New Risk Assessment</strong> has been successfully submitted through the Safety Assessment system. Here are the details of the risk assessment:
</p>

<table class="table table-bordered" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <tbody>
        <tr>
            <th align="left">Shop</th>
            <td>{{ $assessment->shop->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th align="left">Scope</th>
            <td>{{ $assessment->scope_number }}</td>
        </tr>
        <tr>
            <th align="left">Problem</th>
            <td>{{ $assessment->finding_problem }}</td>
        </tr>
        <tr>
            <th align="left">Hazard</th>
            <td>{{ $assessment->potential_hazards }}</td>
        </tr>
        <tr>
            <th align="left">Accessor</th>
            <td>{{ $assessment->accessor }}</td>
        </tr>
        <tr>
            <th align="left">Risk Level</th>
            <td>{{ $assessment->risk_level }}</td>
        </tr>
    </tbody>
</table>

<p>
Please review it immediately and take action if necessary. Thank you for your attention and cooperation.
</p>

<p>Best regards,<br>
<strong>MKM Safety Assessment Team</strong>
</p>
