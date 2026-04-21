<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Budgétaire - {{ $event->title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; color: #111; margin-bottom: 5px; }
        .subtitle { font-size: 14px; color: #666; }
        .summary { margin-bottom: 40px; background: #f9fafb; padding: 20px; border-radius: 10px; }
        .summary-item { margin-bottom: 10px; font-size: 14px; }
        .summary-label { font-weight: bold; width: 150px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 12px 8px; border-bottom: 1px solid #e5e7eb; font-size: 12px; text-transform: uppercase; }
        td { padding: 12px 8px; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
        .amount { text-align: right; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding-bottom: 20px; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; background: #e0e7ff; color: #4338ca; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $event->title }}</div>
        <div class="subtitle">Rapport de Suivi Budgétaire • Généré le {{ date('d/m/Y') }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Lieu :</span> {{ $event->location }}
        </div>
        <div class="summary-item">
            <span class="summary-label">Date de l'événement :</span> {{ $event->date ? date('d/m/Y', strtotime($event->date)) : 'Non fixée' }}
        </div>
        <div class="summary-item">
            <span class="summary-label">Budget Estimé :</span> {{ number_format($event->budget_estimated, 0, ',', ' ') }} FCFA
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Dépensé :</span> {{ number_format($budget_real, 0, ',', ' ') }} FCFA
        </div>
        <div class="summary-item">
            <span class="summary-label">Solde :</span> 
            <span style="color: {{ ($event->budget_estimated - $budget_real) < 0 ? '#dc2626' : '#059669' }}">
                {{ number_format($event->budget_estimated - $budget_real, 0, ',', ' ') }} FCFA
            </span>
        </div>
    </div>

    <h3>Détail des Dépenses</h3>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th class="amount">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->expenses as $expense)
            <tr>
                <td>{{ $expense->title }}</td>
                <td>{{ $expense->category }}</td>
                <td>{{ $expense->date ? date('d/m/Y', strtotime($expense->date)) : '-' }}</td>
                <td class="amount">{{ number_format($expense->amount, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document généré par la plateforme SaaS Event Manager • Page 1 sur 1
    </div>
</body>
</html>
