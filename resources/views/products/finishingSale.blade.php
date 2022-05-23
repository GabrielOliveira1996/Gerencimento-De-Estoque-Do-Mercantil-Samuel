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
        {{$date}} <br>
        <table>
            <tr>
                <th></th>
                <th></th>
                <th></th>    
            </tr>

            @foreach($products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->quantity }}x</td>
                <td>{{ $p->price }}</td>
                <td>=</td>
                <td>{{ $p->price * $p->quantity }}</td>         
            </tr>
            @endforeach
        </table>
        ----------------------------------------------------------
        <br>
            <p style="text-align:center;">{{$invoice}}</p>
        <br>
        <br>
    
    INFORMAR VALORES, SUBVALORES E VALOR FINAL

    </div>
    
</body>
</html>



