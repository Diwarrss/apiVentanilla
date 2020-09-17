<?php

namespace App\Exports;

use App\People;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PeopleExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

   /*  public function collection()
    {
        return People::all();
    } */

    //elegir los datos y dar un formato a los mismo por medio de MAP, elegimos lo q queremos exportar
    public function map($invoice): array
    {
      return [
        $invoice->id,
        $invoice->ti_name,
        $invoice->identification,
        $invoice->names,
        $invoice->type === 'person' ? 'Persona' : 'Compañia',
        $invoice->telephone,
        $invoice->address,
        $invoice->email,
        $invoice->g_name,
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
        'K' => NumberFormat::FORMAT_DATE_DATETIME
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
      return People::join('type_identifications', 'people.type_identification_id', '=', 'type_identifications.id')
                        ->join('genders', 'people.gender_id', '=', 'genders.id')
                        ->select('people.id', 'people.identification', 'people.names', 'people.telephone', 'people.address', 'people.email', 'people.state',
                        'people.type', 'people.created_at', 'type_identifications.name as ti_name', 'genders.name as g_name');
    }

    public function headings(): array
    {
      return [
        'ID',
        'Tipo de Identificacion',
        'Identificacion',
        'Nombre',
        'Tipo',
        'Telefono',
        'Direccion',
        'E-mail',
        'Genero',
        'Estado',
        'Fecha Creacion'
      ];
    }
}
