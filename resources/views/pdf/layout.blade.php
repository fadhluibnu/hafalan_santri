<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan PDF')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 10px;
        }

        /* --- HEADER --- */
        .header {
            text-align: center;
            border-bottom: 2px solid #16a34a; /* Tailwind green-600 */
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            color: #16a34a;
            margin-bottom: 3px;
        }

        .header h2, .header p {
            font-size: 11px;
            font-weight: normal;
            color: #555;
            margin-top: 3px;
        }

        /* --- SECTIONS --- */
        .section {
            margin-bottom: 15px;
        }

        .section-title {
            background-color: #16a34a;
            color: white;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 12px;
            margin-bottom: 8px;
        }

        h3 {
            font-size: 12px;
            color: #16a34a;
            margin-top: 12px;
            margin-bottom: 6px;
        }

        /* --- TABLES --- */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Meta / Data Table (Key-Value) */
        .meta td, .data-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        
        .meta td:first-child, .data-table .label {
            width: 30%;
            font-weight: bold;
            color: #444;
        }

        .data-table .value {
            width: 70%;
        }

        /* Grid / List Table */
        .grid th, .grid td, .hafalan-table th, .hafalan-table td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            font-size: 10px;
            text-align: left;
        }

        .grid th, .hafalan-table th {
            background-color: #f0fdf4; /* Tailwind green-50 */
            color: #222;
            font-weight: bold;
        }

        .grid tr:nth-child(even), .hafalan-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* --- LAYOUT UTILS --- */
        .grid-2 {
            display: table;
            width: 100%;
        }

        .grid-2 .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        /* --- TYPOGRAPHY & MISC --- */
        .text-center { text-align: center; }
        
        .text-green { color: #16a34a; }

        .muted {
            color: #777;
            font-size: 10px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Stats Box */
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .stat-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
        }

        .stat-label {
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
