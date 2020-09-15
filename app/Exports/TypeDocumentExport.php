<?php

namespace App\Exports;

use App\TypeDocument;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TypeDocumentExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
  /**
  * @return \Illuminate\Support\Collection
  */
  use Exportable;

  //Recibir parametros que envian desde el controlador
  /* public function __construct($fechaInicio, $fechaFinal)
  {
    $this->fechaInicio = $fechaInicio;
    $this->fechaFinal = $fechaFinal;
  } */

  //elegir los datos y dar un formato a los mismo por medio de MAP, elegimos lo q queremos exportar
  public function map($invoice): array
  {
    return [
      //$invoice->id,
      $invoice->orden,
      $invoice->nombreCliente,
      $invoice->valTotal,
      Date::dateTimeToExcel($invoice->created_at), //DAR FORMATO
      $invoice->nombres .' '. $invoice->apellidos //name Facturador
    ];
  }

  //es necesario cuando se dara un formato de fecha especifico
  public function columnFormats(): array
  {
    return [
      'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
      'D' => NumberFormat::FORMAT_DATE_DATETIME
      //FORMATO ESPECIFICO https://docs.laravel-excel.com/2.1/reference-guide/formatting.html
    ];
  }

  //Consulta que generara la data a exportar
  public function query()
  {
    return Venta::join('users','users.id','=','ventas.user_id')
    ->select('ventas.id', DB::raw('CONCAT(ventas.prefijo, ventas.numFactura) AS orden'), 'ventas.nombreCliente',
            'ventas.valTotal','ventas.created_at', 'users.nombres','users.apellidos')
    ->whereBetween('ventas.created_at', [$this->fechaInicio . ' 00:00:00', $this->fechaFinal . ' 23:59:59'])
    ->where('ventas.estado', 1);
  }

  public function headings(): array
  {
    return [
      //'#',
      'Orden',
      'Cliente',
      'Valor Total',
      'Fecha Venta',
      'Vendido Por'
    ];
  }
}
