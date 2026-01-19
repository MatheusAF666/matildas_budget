<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Presupuesto #{{ $budget->budget_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #28225e;
            line-height: 1.4;
        }
        .page {
            padding: 20px;
        }
        .header {
            margin-bottom: 20px;
        }
        .company-logo {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #28225e;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 9pt;
            color: #28225e;
            line-height: 1.6;
        }
        .company-phone {
            font-weight: bold;
            font-size: 10pt;
        }
        .separator {
            border-top: 2px solid #28225e;
            margin: 15px 0;
        }
        .client-budget-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .client-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .budget-info {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-line {
            font-size: 9pt;
            margin-bottom: 3px;
        }
        .red-line {
            border-top: 2px solid #dc143c;
            width: 35%;
            margin: 10px 0 15px 0;
        }
        .project-section {
            margin: 20px 0;
        }
        .project-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table thead {
            border-bottom: 2px solid #08aebc;
        }
        .items-table th {
            text-align: left;
            padding: 8px 5px;
            font-size: 9pt;
            font-weight: bold;
        }
        .items-table td {
            padding: 8px 5px;
            font-size: 8.5pt;
        }
        .items-table .task-col {
            width: 15%;
        }
        .items-table .desc-col {
            width: 60%;
        }
        .items-table .price-col {
            width: 25%;
            text-align: right;
        }
        .total-row {
            margin-top: 10px;
            text-align: right;
            font-size: 11pt;
            font-weight: bold;
        }
        .no-iva-note {
            text-align: right;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 15px;
        }
        .conditions-section {
            background-color: #e7e7e9;
            padding: 15px;
            margin: 20px 0;
        }
        .conditions-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .conditions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .conditions-table th {
            text-align: left;
            padding: 5px;
            font-size: 8.5pt;
            border-bottom: 1px solid #08aebc;
        }
        .conditions-table td {
            padding: 5px;
            font-size: 8pt;
            border-bottom: 1px solid #08aebc;
        }
        .conditions-table .total-row {
            font-weight: bold;
            font-size: 9pt;
        }
        .payment-section {
            margin-top: 20px;
        }
        .payment-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .payment-info {
            font-size: 9pt;
            margin-bottom: 15px;
        }
        .bank-account {
            font-weight: bold;
        }
        .acceptance-text {
            font-size: 8pt;
            font-style: italic;
            font-weight: bold;
            margin: 15px 0;
            line-height: 1.5;
        }
        .validity-text {
            font-size: 9pt;
            margin-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #28225e;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 10pt;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            @if(isset($company['logo']) && $company['logo'])
                <img src="{{ public_path('storage/' . str_replace('/storage/', '', $company['logo'])) }}" class="company-logo" alt="Logo">
            @else
                <div class="company-name">{{ $company['name'] ?? 'REFORMAS' }}</div>
            @endif
            
            <div class="company-info">
                @if(isset($company['address']))
                    <div>{{ $company['address'] }}</div>
                @endif
                @if(isset($company['phone']))
                    <div class="company-phone">{{ $company['phone'] }}</div>
                @endif
                @if(isset($company['email']))
                    <div>@ {{ $company['email'] }}</div>
                @endif
                @if(isset($company['website']))
                    <div><strong>{{ $company['website'] }}</strong></div>
                @endif
            </div>
        </div>

        <div class="separator"></div>

        <!-- CLIENT AND BUDGET INFO -->
        <div class="client-budget-section">
            <div class="client-info">
                <div class="section-title">Cliente: {{ $budget->client->name ?? 'N/A' }}</div>
                <div class="info-line">DNI: {{ $budget->client->dni ?? '' }}</div>
                <div class="info-line">Teléfono: {{ $budget->client->phone ?? '' }}</div>
                <div class="info-line">
                    Calle: {{ $budget->client->street ?? '' }}, nº {{ $budget->client->number ?? '' }}
                    @if($budget->client->floor), {{ $budget->client->floor }}º @endif
                    @if($budget->client->door){{ $budget->client->door }}@endif
                </div>
                <div class="info-line">{{ $budget->client->city ?? '' }}</div>
            </div>
            <div class="budget-info">
                <div class="info-line"><strong>Fecha: {{ \Carbon\Carbon::parse($budget->issue_date)->format('d/m/Y') }}</strong></div>
                <div class="info-line"><strong>Presupuesto</strong></div>
            </div>
        </div>

        <div class="red-line"></div>

        <!-- PROJECT SECTION -->
        <div class="project-section">
            <div class="project-title">Proyecto:</div>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="task-col">Tarea</th>
                        <th class="desc-col">Descripción</th>
                        <th class="price-col">Precio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budget->budgetItem as $index => $item)
                    <tr>
                        <td class="task-col">{{ explode(' ', $item->description)[0] ?? 'Item ' . ($index + 1) }}</td>
                        <td class="desc-col">{{ $item->description }}</td>
                        <td class="price-col">{{ number_format($item->total, 2) }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-row">
                Total: {{ number_format($budget->total, 2) }} €
            </div>

            <div class="no-iva-note">
                Los precios no incluyen IVA
            </div>
        </div>

        <div class="separator"></div>

        <!-- CONDITIONS SECTION -->
        <div class="conditions-section">
            <div class="conditions-title">Condiciones generales</div>
            
            <table class="conditions-table">
                <thead>
                    <tr>
                        <th style="width: 60%">Descripción</th>
                        <th style="width: 20%; text-align: center;">Cantidad</th>
                        <th style="width: 20%; text-align: right;">Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1º Pago, firma del presupuesto</td>
                        <td style="text-align: center;">%</td>
                        <td style="text-align: right;">€</td>
                    </tr>
                    <tr>
                        <td>2º Pago, mitad obra</td>
                        <td style="text-align: center;">%</td>
                        <td style="text-align: right;">€</td>
                    </tr>
                    <tr>
                        <td>3º Pago, entrega y finalización del proyecto</td>
                        <td style="text-align: center;">%</td>
                        <td style="text-align: right;">€</td>
                    </tr>
                    <tr class="total-row">
                        <td></td>
                        <td style="text-align: center;"><strong>Total con IVA</strong></td>
                        <td style="text-align: right;"><strong>{{ number_format($budget->total * 1.21, 2) }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="separator"></div>

        <!-- PAYMENT SECTION -->
        <div class="payment-section">
            <div class="payment-title">Forma de pago</div>
            <div class="payment-info">
                Transferencia bancaria: <span class="bank-account">ES 52 0049 0865 6025 1026 5625</span>
                <br>
                <strong>Total con IVA (21%): {{ number_format($budget->total * 1.21, 2) }} €</strong>
            </div>

            <div class="acceptance-text">
                La Aceptación del presente presupuesto implica el compromiso de ejecución. En caso de cancelación por parte del cliente, 
                se reserva el derecho de retener el importe entregado en concepto de señal, así como los gastos generados hasta la fecha.
            </div>

            <div class="validity-text">
                Presupuesto válido por un mes
            </div>
        </div>
    </div>

    <div class="footer">
        Gracias por confiar en {{ $company['name'] ?? 'nosotros' }}
    </div>
</body>
</html>
