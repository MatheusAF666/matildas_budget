<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Presupuesto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #06AFBE;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .budget-card {
            background-color: #f9fafb;
            border-left: 4px solid #FF7041;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .budget-number {
            font-size: 28px;
            font-weight: bold;
            color: #FF7041;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #6b7280;
        }
        .value {
            color: #111827;
            text-align: right;
        }
        .total {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #FFCC00;
            border-radius: 10px;
        }
        .items-section {
            margin: 20px 0;
        }
        .items-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #06AFBE;
        }
        .item {
            padding: 12px;
            background-color: #f9fafb;
            margin-bottom: 8px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .item-desc {
            flex: 1;
            color: #374151;
        }
        .item-price {
            font-weight: bold;
            color: #1f2937;
            margin-left: 15px;
        }
        .message {
            background-color: #e0f2f1;
            border-left: 4px solid #06AFBE;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .contact-info {
            background-color: #fefce8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .contact-info strong {
            color: #854d0e;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f9fafb;
            color: #6b7280;
            font-size: 14px;
        }
        .footer-company {
            margin-top: 10px;
            font-weight: bold;
            color: #FF7041;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $companySettings['name'] ?? config('app.name') }}</h1>
            <p>Nuevo Presupuesto Creado</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hola <strong>{{ $budget->client->name }}</strong>,
            </div>

            <p>Hemos creado un nuevo presupuesto para ti. Encontrarás los detalles completos en el archivo PDF adjunto.</p>

            <div class="budget-card">
                <div class="budget-number">#{{ $budget->budget_number }}</div>
                
                <div class="info-row">
                    <span class="label">Fecha de Emisión:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($budget->issue_date)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Fecha de Vencimiento:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($budget->due_date)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Estado:</span>
                    <span class="value">
                        @if($budget->status === 'draft')
                            <span style="color: #6b7280;">Borrador</span>
                        @elseif($budget->status === 'sent')
                            <span style="color: #3b82f6;">Enviado</span>
                        @elseif($budget->status === 'accepted')
                            <span style="color: #10b981;">Aceptado</span>
                        @else
                            <span style="color: #ef4444;">Rechazado</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="total">
                Total: {{ number_format($budget->total, 2) }} €
                <div style="font-size: 14px; color: #6b7280; margin-top: 5px;">+ IVA (21%): {{ number_format($budget->total * 1.21, 2) }} €</div>
            </div>

            <div class="items-section">
                <div class="items-title">📋 Detalle de Items</div>
                @foreach($budget->budgetItem as $item)
                    <div class="item">
                        <div class="item-desc">
                            <strong>{{ $item->description }}</strong>
                            <div style="font-size: 12px; color: #6b7280; margin-top: 3px;">
                                Cantidad: {{ $item->quantity }} × {{ number_format($item->price, 2) }} €
                            </div>
                        </div>
                        <div class="item-price">{{ number_format($item->total, 2) }} €</div>
                    </div>
                @endforeach
            </div>

            <div class="message">
                <p style="margin: 0;">
                    <strong>📎 Archivo Adjunto:</strong> Encontrarás el presupuesto completo en formato PDF adjunto a este correo.
                </p>
            </div>

            <p>Si tienes alguna pregunta o necesitas aclaraciones sobre este presupuesto, no dudes en contactarnos.</p>

            @if(isset($companySettings['email']) || isset($companySettings['phone']))
            <div class="contact-info">
                <strong>📞 Información de Contacto:</strong><br>
                @if(isset($companySettings['email']))
                    Email: {{ $companySettings['email'] }}<br>
                @endif
                @if(isset($companySettings['phone']))
                    Teléfono: {{ $companySettings['phone'] }}
                @endif
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Este es un correo automático. Por favor no respondas directamente a este mensaje.</p>
            <div class="footer-company">
                © {{ date('Y') }} {{ $companySettings['name'] ?? config('app.name') }}. Todos los derechos reservados.
            </div>
        </div>
    </div>
</body>
</html>
