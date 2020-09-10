
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Planilla Radicación Entrada</title>
  <style>
    .template_filing{
      font-size: 16px;
    }
    .title_content {
      border: 1px solid #000;
      margin-bottom: 5px;
    }
    .title_content, h1{
      text-align: center;
      padding-top: 0;
      margin-top: 0;
      margin-bottom: 5px;
      font-size: 26px
    }
    .title_content, h3{
      padding-top: 0;
      margin-top: 0;
      margin-bottom: 5px;
      font-weight: 400;
      font-size: 16px
    }
    th{
     text-align: left;
    }
    th:nth-last-child(2) {
      width: 150px;
    }
    th:nth-last-child(1) {
      width: 130px;
    }
    th:nth-last-child(5) {
      width: 230px;
    }
    .table_filing, th{
      width: 250px;
    }
    td {
      padding-top: 20px;
    }
    .section_firm{
      border-bottom: 1px solid #000;
    }
    .section_end{
      margin-top: 15px;
      border-top: 1px solid #000;
    }
    h4{
      padding-top: 0;
      margin-top: 0;
    }
    @page { margin-top: 120px; }
    #header { position: fixed; left: 0px; top: -80px; right: 0px; height: 60px; background-color: orange; text-align: center; }
  </style>
</head>
<body>
  <div id="header">
    <h1>Planilla de entrega de radicación</h1>
    <h3>Fecha: {{ $rangeDate }} </h3>
  </div>
  <div class="template_filing">
    <table class="table_filing">
      <thead>
          <tr>
            <th>Radicado</th>
            <th>Fecha</th>
            <th>Remitente</th>
            <th>Destinatario</th>
            <th>Firma</th>
          </tr>
      </thead>
      <tbody>
          @foreach($outgoingFiling ?? '' as $data)
            @foreach ($data->people as $item)
              <tr>
                <td>{{ $data->settled }}</td>
                <td>{{ $data->created_at }}</td>
                <td>{{ $data->dependence->names }}</td>
                <td>{{ $item->names }}</td>
                <td class="section_firm"></td>
             </tr>
            @endforeach
          @endforeach
      </tbody>
  </table>
  </div>
  <div class="section_end">
    <h4>Fin del documento.</h4>
  </div>
  <script type="text/php">
    if (isset($pdf)) {
      $x = 740;
      $y = 55;
      $text = "Pág {PAGE_NUM} de {PAGE_COUNT}";
      $font = null;
      $size = 14;
      $color = array(0,0,0);
      $word_space = 0.0;  //  default
      $char_space = 0.0;  //  default
      $angle = 0.0;   //  default
      $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
  </script>
</body>
</html>
