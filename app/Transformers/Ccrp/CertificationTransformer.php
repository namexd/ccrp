<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Certification;
use League\Fractal\TransformerAbstract;

class CertificationTransformer extends TransformerAbstract
{
    public $availableIncludes = ['company', 'payCompany','file','files'];

    public function transform(Certification $certification)
    {
        $arr = [
            'id' => $certification->id,
            'certificate_no' => $certification->certificate_no,
            'certificate_year' => $certification->certificate_year,
            'out_date' => $certification->out_date,
            'customer' => $certification->customer,
            'customer_address' => $certification->customer_address,
            'instrument_name' => $certification->instrument_name,
            'manufacturer' => $certification->manufacturer,
            'instrument_model' => $certification->instrument_model,
            'instrument_no' => $certification->instrument_no,
            'instrument_accuracy' => $certification->instrument_accuracy,
            'file_id' => $certification->file_id,
            'file_ids' => $certification->file_ids,
            'pay_company_id' => $certification->pay_company_id,
            'company_id' => $certification->company_id
        ];
        return $arr;
    }

    public function includeCompany(Certification $certification)
    {
        return $this->item($certification->company, new CompanyListTransformer());
    }

    public function includepayCompany(Certification $certification)
    {
        return $this->item($certification->payCompany, new CompanyListTransformer());
    }
    public function includeFile(Certification $certification)
    {
        return $this->item($certification->file, new FileTransformer());
    }
    public function includeFiles(Certification $certification)
    {
        return $this->collection($certification->files(), new FileTransformer());
    }

}