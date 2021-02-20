<?php

namespace App\Exports;

use App\EntryFiling;use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EntryFilingExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    /* public function collection()
    {
        return EntryFiling::all();
    } */

    //Recibir parametros que envian desde el controlador
    public function __construct($fromDate, $toDate)
    {
      $this->fromDate = $fromDate;
      $this->toDate = $toDate;
    }

    //elegir los datos y dar un formato a los mismo por medio de MAP, elegimos lo q queremos exportar
    public function map($invoice): array
    {
      return [
        $invoice->id,
        $invoice->settled,
        Date::dateTimeToExcel($invoice->created_at), //DAR FORMATO
        $invoice->p_names,
        $invoice->d_names,
        //$invoice->nombres .' '. $invoice->apellidos //name Facturador
      ];
    }

    //es necesario cuando se dara un formato de fecha especifico
    public function columnFormats(): array
    {
      return [
        /* 'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE, */
        'C' => NumberFormat::FORMAT_DATE_DATETIME
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
      if ($this->fromDate != $this->toDate) {
        return EntryFiling::Join ('dependences', 'entry_filings.dependence_id', '=', 'dependences.id')
                        ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                        ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                        ->select('entry_filings.id', 'entry_filings.settled', 'entry_filings.created_at', 'entry_filings.state', 'dependences.names as p_names', 'dependences.names as d_names')
                        ->whereBetween('entry_filings.created_at', [$this->fromDate, $this->toDate])
                        ->where('entry_filings.state', 1);
      } else {
        return EntryFiling::Join ('dependences', 'entry_filings.dependence_id', '=', 'dependences.id')
                        ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                        ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                        ->select('entry_filings.id', 'entry_filings.settled', 'entry_filings.created_at', 'entry_filings.state', 'dependences.names as p_names', 'dependences.names as d_names')
                        ->whereDate('entry_filings.created_at', [$this->fromDate])
                        ->where('entry_filings.state', 1);
      }
    }

    public function headings(): array
    {
      return [
        'Id',
        'Radicado',
        'Fecha',
        'Remitente',
        'Destinatario'
      ];
    }
}
