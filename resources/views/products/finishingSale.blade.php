<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante</title>

    <style>

        table{
            width: 100%;
            border-collapse: collapse;
        }

        td{
            text-align: center;
        }
    </style>
    
</head>
<body>

    <div class="container">
        <p style="font-weight:bold; text-align:center;">Base4 sistemas LTDA ME</p>
        <p style="text-align:center;">R das ruas, n°666, bairro dos bairros, Mossoró/RN - 66666-666</p>
        CNPJ: 66.666.666/0001-66 <br>
        IE: ISENTO <br>
        IM: 66666 <br>
        ----------------------------------------------------------
        <table>
            <tr>
                <th>{{$displayValue['created_at']}}</th>
                <th></th>
                <th></th>    
            </tr>

            @foreach($invoiceProducts as $product)
            <tr>
                <td>{{ $product['name'] }}</td>
                <td>{{ $product['quantity'] }}x</td>
                <td>{{ $product['price'] }}</td>
                <td>=</td>
                <td>{{ $product['price'] * $product['quantity'] }}</td>         
            </tr>
            @endforeach
        </table>
        ----------------------------------------------------------
      
            Desconto: {{ $displayValue['discount'] }}
            Subtotal: {{ $displayValue['subtotal'] }}
            Total: {{ $displayValue['total'] }}
            
        <br>
            <p style="text-align:center;">{{$displayValue['invoice']}}</p>     
        <br>
        <br>
 
    </div>
    
</body>
</html>



