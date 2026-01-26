<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace !important;
            margin: 20px;
            background-color: #c6c6c6;
        }
        .content{
            display: flex;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;


        }
        .content-box{
           width: 600px;
           min-height:600px;
           background: #fff; 
           padding: 16px;
        }
        table {
            width: 100%;
           
        }
        th, td {
            padding: 12px 20px;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="content-box">
            <div>
                <p>Ref: {{ $invoice->id }}</p>
                <p>Customer name: {{ $invoice->getCustomer()->name }}</p>
            </div>

    <table>
    <thead>
        <tr>
            <th style="width:100px">ID</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Weight</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoice->items  as $item)
        <tr>
            <td>{{$item->product_id}}</td>
            <td>{{$item->product_name}}</td>
            <td>{{ $item->quantity}}</td>
            <td>{{ $item->total_weight }}</td>
        </tr>
            
        @endforeach
       
    </tbody>
</table>

<small>{{ __('Created at:') }} {{ $invoice->created_at }}</small>

        </div>
    </div>
</body>
</html>
