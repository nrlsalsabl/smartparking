<!DOCTYPE html>
<html>

<head>

    <title>
        Transaction Report
    </title>

    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>

</head>

<body>

    <h2>
        TRANSACTION REPORT
    </h2>

    <table>

        <thead>

            <tr>

                <th>No</th>
                <th>Plate</th>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Checkin</th>
                <th>Checkout</th>

            </tr>

        </thead>

        <tbody>

            @foreach($transactions as $item)

            <tr>

                <td>
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $item->booking->vehicle->plate_number }}
                </td>

                <td>
                    {{ $item->booking->user->name }}
                </td>

                <td>

                    Rp
                    {{ number_format($item->total_price) }}

                </td>

                <td>
                    {{ $item->payment_status }}
                </td>

                <td>
                    {{ $item->checkin_at }}
                </td>

                <td>
                    {{ $item->checkout_at }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>