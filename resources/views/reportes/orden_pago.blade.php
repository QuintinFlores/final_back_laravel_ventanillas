<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden_Pago_{{ $orden->numero_orden }}</title>
    <style>
        /* Ajuste perimetral estricto para garantizar el espacio en tamaño Carta */
        @page {
            size: letter portrait;
            margin: 18px 25px;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #111111;
            font-size: 9.5px;
            line-height: 1.25;
            /* Le damos un poco más de separación al texto */
            margin: 0;
            padding: 0;
        }

        /* MODIFICADO: Incrementamos la altura de 325px a 360px para rellenar la hoja */
        .talon-bloque {
            height: 450px;
            border: 1px solid #777777;
            padding: 15px 18px;
            /* Más espacio interno para que se vea imponente */
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #ffffff;
            position: relative;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Ajustes para la nueva distribución de logos en la cabecera */
        .logo-cell-left {
            width: 10%;
            text-align: left;
            vertical-align: middle;
        }

        .logo-cell-right {
            width: 12%;
            text-align: right;
            vertical-align: middle;
        }

        /* Reducimos sutilmente el ancho de los títulos para dar espacio al segundo logo */
        .title-cell {
            width: 63%;
            text-align: center;
            vertical-align: middle;
        }

        /* Control estricto de dimensiones del logotipo de AGEMED */
        .logo-agemed-img {
            width: 48px;
            height: auto;
        }


        .logo-img {
            width: 42px;
            height: auto;
        }

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
            font-size: 11.5px;
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
            width: 85px;
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
            margin-top: 5px;
        }

        .data-table td {
            padding: 2px 0;
            vertical-align: middle;
        }

        .label {
            font-weight: bold;
            color: #333333;
            width: 110px;
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
            margin-top: 6px;
            border-top: 1px dashed #cccccc;
            padding-top: 6px;
        }

        .concepto-text {
            font-size: 9px;
            color: #111111;
            text-align: justify;
            line-height: 1.25;
        }

        .disclaimer-box {
            margin-top: 50px;
            font-size: 8px;
            color: #555555;
            text-align: justify;
            line-height: 1.15;
        }

        /* Ajustamos las firmas para que acompañen la nueva altura elegante del bloque */
        .footer-row {
            width: 95%;
            border-collapse: collapse;
            position: absolute;
            bottom: 30px;
            /* Excelente espacio de respeto con el borde negro */
            left: 18px;
        }

        .fecha-text {
            text-align: right;
            font-size: 8.5px;
            font-weight: bold;
            vertical-align: bottom;
        }

        .firma-section {
            width: 100%;
            text-align: center;
        }

        .linea-firma {
            width: 180px;
            border-top: 1px solid #000000;
            margin: 0 auto;
        }

        .nombre-firma {
            font-size: 7.5px;
            margin-top: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* MODIFICADO: Aumentamos el margen del divisor para centrar la tijera en el espacio restante */
        .divisor-tijera {
            height: 25px;
            line-height: 25px;
            text-align: center;
            margin: 15px 0;
        }

        .tijera-icon {
            font-size: 10px;
            color: #555555;
        }
    </style>
</head>

<body>

    <!-- ==================== 1. TALÓN SUPERIOR ==================== -->
    <div class="talon-bloque">
        <table class="header-table">
            <tr>
                <!-- Columna Izquierda: Contiene los dos logos alineados en fila -->
                <td style="width: 25%; text-align: left; vertical-align: middle; white-space: nowrap;">

                    <img src="{{ public_path('img/logo.png') }}" class="logo-agemed-img" alt="Logo AGEMED"
                        style="display: inline-block; vertical-align: middle;">
                </td>

                <!-- Columna Central: Títulos Institucionales Centrados -->
                <td class="title-cell" style="width: 60%; text-align: center; vertical-align: middle;">
                    <div class="institution-title">AGENCIA ESTATAL DE MEDICAMENTOS Y TECNOLOGÍAS EN SALUD</div>
                    <div class="document-title">ORDEN DE PAGO POR VENTA DE SERVICIOS</div>
                    <div class="institution-title" style="font-size: 7.5px; margin-top: 1px; color: #555555;">MINISTERIO
                        DE SALUD Y DEPORTES</div>
                </td>

                <!-- Columna Derecha: Código de Formulario y Número de Orden -->
                <td class="form-code-cell" style="width: 15%; text-align: right; vertical-align: top;">
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
                <td class="value" style="font-size: 8.5px; color: #444444;">Realizar el depósito en el Banco Unión Cta.
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
                <span style="color: #dc2626; font-weight: bold; font-size: 9.5px; margin-right: 6px;">
                    [{{ $orden->codigo_misa ?? 'SIN CÓDIGO' }}]
                </span>
                <strong>{{ $orden->arancel?->codigo_arancel }}</strong>.- {{ $orden->arancel?->nombre_arancel }}
                @if($orden->descripcion)
                    <br><span style="color: #555555; font-style: italic;">Glosa: {{ $orden->descripcion }}</span>
                @endif
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
                <td style="width: 50%;">
                    <div class="fecha-text">La Paz, {{ date('d') }} de
                        {{ ['January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 'April' => 'abril', 'May' => 'mayo', 'June' => 'junio', 'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre', 'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'][date('F')] }}
                        de {{ date('Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== LÍNEA DIVISORIA ==================== -->
    <div class="divisor-tijera">
        <span
            class="tijera-icon">✂-----------------------------------------------------------------------------------------</span>
    </div>
    <!-- ==================== 2. TALÓN INFERIOR ==================== -->
    <div class="talon-bloque">
        <table class="header-table">
            <tr>
                <!-- Columna Izquierda: Contiene los dos logos alineados en fila -->
                <td style="width: 25%; text-align: left; vertical-align: middle; white-space: nowrap;">

                    <img src="{{ public_path('img/logo.png') }}" class="logo-agemed-img" alt="Logo AGEMED"
                        style="display: inline-block; vertical-align: middle;">
                </td>

                <!-- Columna Central: Títulos Institucionales Centrados -->
                <td class="title-cell" style="width: 60%; text-align: center; vertical-align: middle;">
                    <div class="institution-title">AGENCIA ESTATAL DE MEDICAMENTOS Y TECNOLOGÍAS EN SALUD</div>
                    <div class="document-title">ORDEN DE PAGO POR VENTA DE SERVICIOS</div>
                    <div class="institution-title" style="font-size: 7.5px; margin-top: 1px; color: #555555;">MINISTERIO
                        DE SALUD Y DEPORTES</div>
                </td>

                <!-- Columna Derecha: Código de Formulario y Número de Orden -->
                <td class="form-code-cell" style="width: 15%; text-align: right; vertical-align: top;">
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
                <td class="value" style="font-size: 8.5px; color: #444444;">Realizar el depósito en el Banco Unión Cta.
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
                <span style="color: #dc2626; font-weight: bold; font-size: 9.5px; margin-right: 6px;">
                    [{{ $orden->codigo_misa ?? 'SIN CÓDIGO' }}]
                </span>
                <strong>{{ $orden->arancel?->codigo_arancel }}</strong>.- {{ $orden->arancel?->nombre_arancel }}
                @if($orden->descripcion)
                    <br><span style="color: #555555; font-style: italic;">Glosa: {{ $orden->descripcion }}</span>
                @endif
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
                <td style="width: 50%;">
                    <div class="fecha-text">La Paz, {{ date('d') }} de
                        {{ ['January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 'April' => 'abril', 'May' => 'mayo', 'June' => 'junio', 'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre', 'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'][date('F')] }}
                        de {{ date('Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>