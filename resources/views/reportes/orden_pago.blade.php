<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden_Pago_{{ $orden->numero_orden }}</title>
    <style>
        /* Ajuste de márgenes perimetrales para ganar espacio vertical */
        @page {
            margin: 10px 30px;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #111111;
            font-size: 9.5px;
            /* Reducido un punto para textos largos */
            line-height: 1.15;
            margin: 0;
            padding: 0;
        }

        /* Reducimos ligeramente la altura de cada bloque para que encajen sí o sí */
        .talon-bloque {
            height: 45%;
            border: 1px solid #777777;
            padding: 8px 12px;
            border-radius: 6px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 12%;
            text-align: left;
            vertical-align: middle;
        }

        .logo-img {
            width: 40px;
            height: auto;
        }

        /* Logo ligeramente más compacto */
        .title-cell {
            width: 73%;
            text-align: center;
            vertical-align: middle;
        }

        .institution-title {
            font-size: 9px;
            font-weight: bold;
            color: #222222;
        }

        .document-title {
            font-size: 11px;
            font-weight: 800;
            margin-top: 1px;
            color: #000000;
            letter-spacing: 0.2px;
        }

        .form-code-cell {
            width: 15%;
            text-align: right;
            vertical-align: top;
        }

        .form-code {
            font-size: 8px;
            font-weight: bold;
            color: #444444;
        }

        .numero-orden-box {
            border: 2px solid #000000;
            width: 80px;
            height: 24px;
            float: right;
            margin-top: 2px;
            text-align: center;
        }

        .numero-orden-text {
            font-size: 11px;
            font-weight: bold;
            line-height: 24px;
            color: #000000;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .data-table td {
            padding: 1.5px 0;
            vertical-align: middle;
        }

        .label {
            font-weight: bold;
            color: #333333;
            width: 105px;
            text-transform: uppercase;
            font-size: 8.5px;
        }

        .value {
            color: #000000;
            font-size: 9.5px;
        }

        .value-bold {
            font-weight: bold;
        }

        .concepto-box {
            margin-top: 3px;
            border-top: 1px dashed #cccccc;
            padding-top: 3px;
        }

        .concepto-text {
            font-size: 8.5px;
            color: #111111;
            text-align: justify;
            line-height: 1.2;
        }

        .disclaimer-box {
            margin-top: 3px;
            font-size: 7px;
            color: #555555;
            text-align: justify;
            line-height: 1.05;
        }

        .footer-row {
            width: 100%;
            margin-top: 3px;
        }

        .fecha-text {
            text-align: right;
            font-size: 8.5px;
            font-weight: bold;
        }

        .firma-section {
            width: 100%;
            text-align: center;
            margin-top: 8px;
        }

        .linea-firma {
            width: 160px;
            border-top: 1px solid #000000;
            margin: 0 auto;
        }

        .nombre-firma {
            font-size: 7.5px;
            margin-top: 1px;
            text-transform: uppercase;
        }

        /* Línea divisoria compacta para evitar saltos */
        .divisor-tijera {
            height: 4%;
            text-align: center;
            position: relative;
            line-height: 25px;
        }

        .linea-punteada {
            border-top: 1px dashed #555555;
            position: absolute;
            top: 50%;
            width: 100%;
            z-index: 1;
        }

        .tijera-icon {
            background-color: #ffffff;
            padding: 0 10px;
            position: relative;
            z-index: 2;
            font-size: 11px;
            color: #333333;
        }
    </style>

</head>

<body>

    <!-- ==================== 1. TALÓN SUPERIOR (INSTITUCIONAL) ==================== -->
    <div class="talon-bloque">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="https://wikimedia.org" class="logo-img" alt="Escudo Bolivia">
                </td>
                <td class="title-cell">
                    <div class="institution-title">AGENCIA ESTATAL DE MEDICAMENTOS Y TECNOLOGÍAS EN SALUD</div>
                    <div class="document-title">ORDEN DE PAGO POR VENTA DE SERVICIOS</div>
                    <div class="institution-title" style="font-size: 8px; margin-top: 1px; color: #555555;">MINISTERIO
                        DE SALUD Y DEPORTES</div>
                </td>
                <td class="form-code-cell">
                    <div class="form-code">FORM.AGEMED-02</div>
                    <div class="numero-orden-box">
                        <div class="numero-orden-text">{{ $orden->numero_orden }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="data-table">
            <tr>
                <td class="label">Departamento:</td>
                <td class="value">AUTORIZACIÓN DE COMERCIALIZACIÓN</td>
            </tr>
            <tr>
                <td class="label">Señor(es):</td>
                <td class="value value-bold">{{ $orden->empresa?->razon_social }}</td>
            </tr>
            <tr>
                <td class="label">Cuenta Bancaria:</td>
                <td class="value" style="font-size: 9px; color: #444444;">Realizar el depósito en el Banco Unión Cta.
                    10000023848754 Agencia Estatal de Medicamentos y Tecnologías en Salud - AGEMED.</td>
            </tr>
            <tr>
                <td class="label">El importe de Bs:</td>
                <td class="value value-bold" style="font-size: 11px; color: #10b981;">
                    {{ number_format($orden->monto_total, 2) }}
                </td>
            </tr>
            <tr>
                <td class="label">Son:</td>
                <td class="value value-bold">{{ $textoLiteral }}</td>
            </tr>
        </table>

        <div class="concepto-box">
            <span class="label" style="display: block; margin-bottom: 2px;">Concepto de pago:</span>
            <div class="concepto-text">
                <strong>{{ $orden->arancel?->codigo_arancel }}</strong>.- {{ $orden->arancel?->nombre_arancel }}
                @if($orden->descripcion) <br><span style="color: #555555; font-style: italic;">Glosa:
                {{ $orden->descripcion }}</span> @endif
            </div>
        </div>

        <div class="disclaimer-box">
            Aquellas personas, sean naturales o jurídicas, que efectúen depósitos en la cuenta señalada anteriormente,
            SIN RECABAR ORDEN DE PAGO correspondiente, serán responsables de los inconvenientes que conlleven a la
            recuperación de los mismos. Por lo que la Agencia Estatal de Medicamentos y Tecnologías en Salud - AGEMED,
            deslinda cualquier responsabilidad ante estos hechos.
        </div>

        <table class="footer-row">
            <tr>
                <td style="width: 50%;">
                    <div class="firma-section">
                        <div class="linea-firma"></div>
                        <div class="nombre-firma">{{ $orden->empresa?->razon_social }}</div>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: bottom;">
                    <div class="fecha-text">La Paz,
                        {{ \Carbon\Carbon::parse($orden->fecha)->format('d \d\e F \d\e Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== LÍNEA DE CORTE (TIJERA) ==================== -->
    <div class="divisor-tijera">
        <div class="linea-punteada"></div>
        <span class="tijera-icon">✂-----------------------------------------------------------------------------</span>
    </div>

    <!-- ==================== 2. TALÓN INFERIOR (CLIENTE) ==================== -->
    <div class="talon-bloque">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="https://wikimedia.org" class="logo-img" alt="Escudo Bolivia">
                </td>
                <td class="title-cell">
                    <div class="institution-title">AGENCIA ESTATAL DE MEDICAMENTOS Y TECNOLOGÍAS EN SALUD</div>
                    <div class="document-title">ORDEN DE PAGO POR VENTA DE SERVICIOS</div>
                    <div class="institution-title" style="font-size: 8px; margin-top: 1px; color: #555555;">MINISTERIO
                        DE SALUD Y DEPORTES</div>
                </td>
                <td class="form-code-cell">
                    <div class="form-code">FORM.AGEMED-02</div>
                    <div class="numero-orden-box">
                        <div class="numero-orden-text">{{ $orden->numero_orden }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="data-table">
            <tr>
                <td class="label">Departamento:</td>
                <td class="value">AUTORIZACIÓN DE COMERCIALIZACIÓN</td>
            </tr>
            <tr>
                <td class="label">Señor(es):</td>
                <td class="value value-bold">{{ $orden->empresa?->razon_social }}</td>
            </tr>
            <tr>
                <td class="label">Cuenta Bancaria:</td>
                <td class="value" style="font-size: 9px; color: #444444;">Realizar el depósito en el Banco Unión Cta.
                    10000023848754 Agencia Estatal de Medicamentos y Tecnologías en Salud - AGEMED.</td>
            </tr>
            <tr>
                <td class="label">El importe de Bs:</td>
                <td class="value value-bold" style="font-size: 11px; color: #10b981;">
                    {{ number_format($orden->monto_total, 2) }}
                </td>
            </tr>
            <tr>
                <td class="label">Son:</td>
                <td class="value value-bold">{{ $textoLiteral }}</td>
            </tr>
        </table>

        <div class="concepto-box">
            <span class="label" style="display: block; margin-bottom: 2px;">Concepto de pago:</span>
            <div class="concepto-text">
                <strong>{{ $orden->arancel?->codigo_arancel }}</strong>.- {{ $orden->arancel?->nombre_arancel }}
                @if($orden->descripcion) <br><span style="color: #555555; font-style: italic;">Glosa:
                {{ $orden->descripcion }}</span> @endif
            </div>
        </div>

        <div class="disclaimer-box">