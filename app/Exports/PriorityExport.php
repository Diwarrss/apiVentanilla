<?php

namespace App\Exports;

use App\Priority;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Exportable;
//use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PriorityExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    /* public function collection()
    {
        return Priority::all();
    } */
    public function map($invoice): array
    {
      return [
        $invoice->id,
        $invoice->name,
        $invoice->initials,
        $invoice->days,
        $invoice->state ? 'Activo' : 'Inactivo',
        Date::dateTimeToExcel($invoice->created_at), //DAR FORMATO
        //$invoice->nombres .' '. $invoice->apellidos //name Facturador
      ];
    }

    //es necesario cuando se dara un formato de fecha especifico
    public function columnFormats(): array
    {
      return [
        /* 'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE, */
        'F' => NumberFormat::FORMAT_DATE_DATETIME
        //FORMATO ESPECIFICO https://docs.laravel-excel.com/2.1/reference-guide/formatting.html
      ];
    }

    //Consulta que generara la data a exportar
    public function query()
    {
      /* return Venta::join('users','users.id','=','ventas.user_id')
      ->select('ventas.id', DB::raw('CONCAT(ventas.prefijo, ventas.numFactura) AS orden'), 'ventas.nombreCliente',
              'ventas.valTotal','ventas.created_at', 'users.nombres','users.apellidos')
      ->whereBetween('ventas.created_at', [$this->fechaInicio . ' 00:00:00', $this->fechaFinal . ' 23:59:59'])
      ->where('ventas.estado', 1); */
      //return DB::table('type_documents')->get();
      //return TypeDocument::select('type_documents.id', 'type_documents.name', 'type_documents.state', 'type_documents.created_at')->get();
      return Priority::query();
    }

    public function headings(): array
    {
      return [
        'ID',
        'Nombre',
        'Iniciales',
        'Dias',
        'Estado',
        'Fecha Creacion'
      ];
    }
}
