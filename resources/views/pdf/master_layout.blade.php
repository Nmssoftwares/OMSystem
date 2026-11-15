<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Report')</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* DomPDF me header/footer fixed karne ka tareeka */
        @page {
            margin: 100px 25px 50px 25px;
        }

        header {
            position: fixed;
            top: -75px;
            left: 0px;
            right: 0px;
            height: 60px;
            border-bottom: 2px solid #334e68;
            padding-bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .logo {
            max-height: 45px;
        }

        .company-details {
            float: right;
            text-align: right;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
        }

        table.report-table th {
            background-color: #334e68;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        table.report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>

<body>

    <!-- Har page pe same Header aayega -->
    <header>
        <div style="float: left; width: 50%;">
            <!-- Dynamic Logo with Safety Check -->
            @if(!empty($company_logo) && file_exists(public_path($company_logo)))
            <img src="{{ public_path($company_logo) }}" class="logo" alt="Logo">
            @else
            <h2 style="margin: 0; padding-top: 5px; color: #334e68;">{{ $company_name ?? 'OMSystem' }}</h2>
            @endif
        </div>

        <div class="company-details" style="float: right; width: 50%;">
            <strong>{{ $company_name ?? 'OMSystem Portal' }}</strong><br>
            <small style="color: #666;">Generated: {{ date('d-M-Y H:i A') }}</small>
        </div>

        <!-- Floats clear karna DOMPDF me zaroori hai -->
        <div style="clear: both;"></div>
    </header>

    <!-- Har page pe same Footer aayega -->
    <footer>
        OMSystem &copy; {{ date('Y') }} | Page <span class="page-number"></span>
    </footer>

    <!-- Main Content Yahan Aayega -->
    <main>
        @yield('content')
    </main>

</body>

</html>