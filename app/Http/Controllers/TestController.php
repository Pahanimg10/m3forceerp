<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_contact()
    {
        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/customer_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                $contact = new \App\Model\Contact();
                $contact->contact_type_id = 1;
                $contact->business_type_id = 0;
                $contact->contact_id = $row->contact_id ? $row->contact_id : 0;

                $last_id = 0;
                $last_contact = \App\Model\Contact::selectRaw('COUNT(id) AS id')->where('contact_type_id', 1)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                $last_id = $last_contact ? $last_contact->id : $last_id;

                $contact->code = 'C-MC'.sprintf('%05d', $last_id + 1);
                $contact->name = $row->name ? $row->name : '';
                $contact->nic = $row->nic ? $row->nic : '';
                $contact->address = $row->address ? $row->address : '';
                $contact->contact_no = $row->contact_no ? '0'.$row->contact_no : '';
                $contact->email = $row->email ? $row->email : '';

                $region = \App\Model\Region::where('name', $row->region)->where('is_delete', 0)->first();
                if (! $region) {
                    $region = new \App\Model\Region();
                    $region->name = $row->region ? $row->region : '';
                    $region->save();
                    $region->code = 'RG'.sprintf('%03d', $region->id);
                    $region->save();
                }

                $collection_manager = \App\Model\CollectionManager::where('name', $row->collection_manager)->where('is_delete', 0)->first();
                if (! $collection_manager) {
                    $collection_manager = new \App\Model\CollectionManager();
                    $collection_manager->name = $row->collection_manager ? $row->collection_manager : '';
                    $collection_manager->contact_no = '';
                    $collection_manager->is_active = 1;
                    $collection_manager->save();
                    $collection_manager->code = 'CM'.sprintf('%03d', $collection_manager->id);
                    $collection_manager->save();
                }

                if ($row->group_name && preg_replace('/\s+/', '', $row->group_name) != '') {
                    $c_group = \App\Model\CGroup::where('name', $row->group_name)->where('is_delete', 0)->first();
                    if (! $c_group) {
                        $c_group = new \App\Model\CGroup();
                        $c_group->name = $row->group_name ? $row->group_name : '';
                        $c_group->address = $row->group_address ? $row->group_address : '';
                        $c_group->contact_no = $row->group_contact_no ? '0'.$row->group_contact_no : '';
                        $c_group->email = $row->group_email ? $row->group_email : '';
                        $c_group->region_id = $region->id;
                        $c_group->collection_manager_id = $collection_manager->id;
                        $c_group->contact_person_1 = $row->contact_person_1 ? $row->contact_person_1 : '';
                        $c_group->contact_person_no_1 = $row->contact_person_no_1 ? '0'.$row->contact_person_no_1 : '';
                        $c_group->contact_person_2 = $row->contact_person_2 ? $row->contact_person_2 : '';
                        $c_group->contact_person_no_2 = $row->contact_person_no_2 ? '0'.$row->contact_person_no_2 : '';
                        $c_group->contact_person_3 = $row->contact_person_3 ? $row->contact_person_3 : '';
                        $c_group->contact_person_no_3 = $row->contact_person_no_3 ? '0'.$row->contact_person_no_3 : '';
                        $c_group->invoice_name = $row->invoice_name ? $row->invoice_name : '';
                        $c_group->invoice_delivering_address = $row->invoice_delivering_address ? $row->invoice_delivering_address : '';
                        $c_group->collection_address = $row->collection_address ? $row->collection_address : '';
                        $c_group->invoice_email = $row->invoice_email ? $row->invoice_email : '';
                        $c_group->vat_no = $row->vat_no ? $row->vat_no : '';
                        $c_group->svat_no = $row->svat_no ? $row->svat_no : '';
                        $c_group->monitoring_fee = $row->monitoring_fee ? $row->monitoring_fee : 0;
                        if ($c_group->save()) {
                            $c_group->code = 'C-G'.sprintf('%03d', $c_group->id);
                            $c_group->save();

                            $months = explode(',', $row->invoice_months);
                            foreach ($months as $month) {
                                $c_group_inv_month = new \App\Model\CGroupInvoiceMonth();
                                $c_group_inv_month->group_id = $c_group->id;
                                $c_group_inv_month->month = $month;
                                $c_group_inv_month->save();
                            }

                            if ($row->nbt == 1) {
                                $c_group_tax = new \App\Model\CGroupTax();
                                $c_group_tax->group_id = $c_group->id;
                                $c_group_tax->tax_id = 1;
                                $c_group_tax->save();
                            }
                            if ($row->svat == 1) {
                                $c_group_tax = new \App\Model\CGroupTax();
                                $c_group_tax->group_id = $c_group->id;
                                $c_group_tax->tax_id = 2;
                                $c_group_tax->save();
                            }
                            if ($row->vat == 1) {
                                $c_group_tax = new \App\Model\CGroupTax();
                                $c_group_tax->group_id = $c_group->id;
                                $c_group_tax->tax_id = 3;
                                $c_group_tax->save();
                            }
                        }
                    }

                    $contact->region_id = 0;
                    $contact->collection_manager_id = 0;
                    $contact->contact_person_1 = '';
                    $contact->contact_person_no_1 = '';
                    $contact->contact_person_2 = '';
                    $contact->contact_person_no_2 = '';
                    $contact->contact_person_3 = '';
                    $contact->contact_person_no_3 = '';
                    $contact->invoice_name = '';
                    $contact->invoice_delivering_address = '';
                    $contact->collection_address = '';
                    $contact->invoice_email = '';
                    $contact->vat_no = '';
                    $contact->svat_no = '';
                    $contact->monitoring_fee = 0;

                    $contact->group_id = $c_group->id;
                    $contact->is_group = 1;
                } else {
                    $contact->region_id = $region->id;
                    $contact->collection_manager_id = $collection_manager->id;
                    $contact->contact_person_1 = $row->contact_person_1 ? $row->contact_person_1 : '';
                    $contact->contact_person_no_1 = $row->contact_person_no_1 ? '0'.$row->contact_person_no_1 : '';
                    $contact->contact_person_2 = $row->contact_person_2 ? $row->contact_person_2 : '';
                    $contact->contact_person_no_2 = $row->contact_person_no_2 ? '0'.$row->contact_person_no_2 : '';
                    $contact->contact_person_3 = $row->contact_person_3 ? $row->contact_person_3 : '';
                    $contact->contact_person_no_3 = $row->contact_person_no_3 ? '0'.$row->contact_person_no_3 : '';
                    $contact->invoice_name = $row->invoice_name ? $row->invoice_name : '';
                    $contact->invoice_delivering_address = $row->invoice_delivering_address ? $row->invoice_delivering_address : '';
                    $contact->collection_address = $row->collection_address ? $row->collection_address : '';
                    $contact->invoice_email = $row->invoice_email ? $row->invoice_email : '';
                    $contact->vat_no = $row->vat_no ? $row->vat_no : '';
                    $contact->svat_no = $row->svat_no ? $row->svat_no : '';
                    $contact->monitoring_fee = $row->monitoring_fee ? $row->monitoring_fee : 0;
                    $contact->group_id = 0;
                    $contact->is_group = 0;
                    if ($contact->save()) {
                        $months = explode(',', $row->invoice_months);
                        foreach ($months as $month) {
                            $contact_inv_month = new \App\Model\ContactInvoiceMonth();
                            $contact_inv_month->contact_id = $contact->id;
                            $contact_inv_month->month = $month;
                            $contact_inv_month->save();
                        }

                        if ($row->nbt == 1) {
                            $contact_tax = new \App\Model\ContactTax();
                            $contact_tax->contact_id = $contact->id;
                            $contact_tax->tax_id = 1;
                            $contact_tax->save();
                        }
                        if ($row->svat == 1) {
                            $contact_tax = new \App\Model\ContactTax();
                            $contact_tax->contact_id = $contact->id;
                            $contact_tax->tax_id = 2;
                            $contact_tax->save();
                        }
                        if ($row->vat == 1) {
                            $contact_tax = new \App\Model\ContactTax();
                            $contact_tax->contact_id = $contact->id;
                            $contact_tax->tax_id = 3;
                            $contact_tax->save();
                        }
                    }
                }

                $contact->service_mode_id = $row->service_mode ? $row->service_mode : 0;
                $contact->client_type_id = $row->client_type ? $row->client_type : 0;
                $contact->is_active = 1;
                $contact->save();
            });
        });
    }

    public function add_installation_item()
    {
        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/import_installation_item_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->name && preg_replace('/\s+/', '', $row->name) != '') {
                    $item = \App\Model\Item::where('name', $row->name.' '.$row->model_no)->where('is_delete', 0)->first();
                    $item = $item ? $item : new \App\Model\Item();

                    $main_category = \App\Model\MainItemCategory::where('name', $row->main_category)->where('is_delete', 0)->first();
                    if (! $main_category) {
                        $main_category = new \App\Model\MainItemCategory();
                        $main_category->code = $row->main_code ? $row->main_code : '';
                        $main_category->name = $row->main_category ? $row->main_category : '';
                        $main_category->save();
                    }

                    $sub_category = \App\Model\SubItemCategory::where('name', $row->sub_category)->where('is_delete', 0)->first();
                    if (! $sub_category) {
                        $sub_category = new \App\Model\SubItemCategory();
                        $sub_category->code = $row->sub_code ? $row->sub_code : '';
                        $sub_category->name = $row->sub_category ? $row->sub_category : '';
                        $sub_category->save();
                    }

                    $item->main_category_id = $main_category->id;
                    $item->sub_category_id = $sub_category->id;
                    $item->purchase_type_id = $row->purchase_type ? $row->purchase_type : 0;

                    $last_id = 0;
                    $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $main_category->id)->where('sub_category_id', $sub_category->id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                    $last_id = $last_item ? $last_item->id : $last_id;

                    $item->code = 'IT-'.$main_category->code.'-'.$sub_category->code.sprintf('%05d', $last_id + 1);
                    $item->name = $row->name ? $row->name.' '.$row->model_no : '';
                    $item->model_no = $row->model_no ? $row->model_no : '';
                    $item->brand = $row->brand ? $row->brand : '';
                    $item->origin = '';
                    $item->unit_type_id = $row->unit_type ? $row->unit_type : 0;
                    $item->reorder_level = $row->reorder_level ? $row->reorder_level : '';
                    $item->rate = $row->rate ? $row->rate : 0;
                    $item->is_serial = $row->serial ? $row->serial : 0;
                    $item->is_warranty = $row->warranty ? $row->warranty : 0;
                    $item->is_active = 1;
                    $item->save();
                }
            });
        });

        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/local_installation_item_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->name && preg_replace('/\s+/', '', $row->name) != '') {
                    $item = \App\Model\Item::where('name', $row->name.' '.$row->model_no)->where('is_delete', 0)->first();
                    $item = $item ? $item : new \App\Model\Item();

                    $main_category = \App\Model\MainItemCategory::where('name', $row->main_category)->where('is_delete', 0)->first();
                    if (! $main_category) {
                        $main_category = new \App\Model\MainItemCategory();
                        $main_category->code = $row->main_code ? $row->main_code : '';
                        $main_category->name = $row->main_category ? $row->main_category : '';
                        $main_category->save();
                    }

                    $sub_category = \App\Model\SubItemCategory::where('name', $row->sub_category)->where('is_delete', 0)->first();
                    if (! $sub_category) {
                        $sub_category = new \App\Model\SubItemCategory();
                        $sub_category->code = $row->sub_code ? $row->sub_code : '';
                        $sub_category->name = $row->sub_category ? $row->sub_category : '';
                        $sub_category->save();
                    }

                    $item->main_category_id = $main_category->id;
                    $item->sub_category_id = $sub_category->id;
                    $item->purchase_type_id = $row->purchase_type ? $row->purchase_type : 0;

                    $last_id = 0;
                    $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $main_category->id)->where('sub_category_id', $sub_category->id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                    $last_id = $last_item ? $last_item->id : $last_id;

                    $item->code = 'IT-'.$main_category->code.'-'.$sub_category->code.sprintf('%05d', $last_id + 1);
                    $item->name = $row->name ? $row->name.' '.$row->model_no : '';
                    $item->model_no = $row->model_no ? $row->model_no : '';
                    $item->brand = $row->brand ? $row->brand : '';
                    $item->origin = '';
                    $item->unit_type_id = $row->unit_type ? $row->unit_type : 0;
                    $item->reorder_level = $row->reorder_level ? $row->reorder_level : '';
                    $item->rate = $row->rate ? $row->rate : 0;
                    $item->is_serial = $row->serial ? $row->serial : 0;
                    $item->is_warranty = $row->warranty ? $row->warranty : 0;
                    $item->is_active = 1;
                    $item->save();
                }
            });
        });
    }

    public function add_ids_item()
    {
        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/import_ids_item_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->name && preg_replace('/\s+/', '', $row->name) != '') {
                    $item = \App\Model\Item::where('name', $row->name.' '.$row->model_no)->where('is_delete', 0)->first();
                    $item = $item ? $item : new \App\Model\Item();

                    $main_category = \App\Model\MainItemCategory::where('name', $row->main_category)->where('is_delete', 0)->first();
                    if (! $main_category) {
                        $main_category = new \App\Model\MainItemCategory();
                        $main_category->code = $row->main_code ? $row->main_code : '';
                        $main_category->name = $row->main_category ? $row->main_category : '';
                        $main_category->save();
                    }

                    $sub_category = \App\Model\SubItemCategory::where('name', $row->sub_category)->where('is_delete', 0)->first();
                    if (! $sub_category) {
                        $sub_category = new \App\Model\SubItemCategory();
                        $sub_category->code = $row->sub_code ? $row->sub_code : '';
                        $sub_category->name = $row->sub_category ? $row->sub_category : '';
                        $sub_category->save();
                    }

                    $item->main_category_id = $main_category->id;
                    $item->sub_category_id = $sub_category->id;
                    $item->purchase_type_id = $row->purchase_type ? $row->purchase_type : 0;

                    $last_id = 0;
                    $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $main_category->id)->where('sub_category_id', $sub_category->id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                    $last_id = $last_item ? $last_item->id : $last_id;

                    $item->code = 'IT-'.$main_category->code.'-'.$sub_category->code.sprintf('%05d', $last_id + 1);
                    $item->name = $row->name ? $row->name.' '.$row->model_no : '';
                    $item->model_no = $row->model_no ? $row->model_no : '';
                    $item->brand = $row->brand ? $row->brand : '';
                    $item->origin = '';
                    $item->unit_type_id = $row->unit_type ? $row->unit_type : 0;
                    $item->reorder_level = $row->reorder_level ? $row->reorder_level : '';
                    $item->rate = $row->rate ? $row->rate : 0;
                    $item->is_serial = $row->serial ? $row->serial : 0;
                    $item->is_warranty = $row->warranty ? $row->warranty : 0;
                    $item->is_active = 1;
                    $item->save();
                }
            });
        });

        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/local_ids_item_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->name && preg_replace('/\s+/', '', $row->name) != '') {
                    $item = \App\Model\Item::where('name', $row->name.' '.$row->model_no)->where('is_delete', 0)->first();
                    $item = $item ? $item : new \App\Model\Item();

                    $main_category = \App\Model\MainItemCategory::where('name', $row->main_category)->where('is_delete', 0)->first();
                    if (! $main_category) {
                        $main_category = new \App\Model\MainItemCategory();
                        $main_category->code = $row->main_code ? $row->main_code : '';
                        $main_category->name = $row->main_category ? $row->main_category : '';
                        $main_category->save();
                    }

                    $sub_category = \App\Model\SubItemCategory::where('name', $row->sub_category)->where('is_delete', 0)->first();
                    if (! $sub_category) {
                        $sub_category = new \App\Model\SubItemCategory();
                        $sub_category->code = $row->sub_code ? $row->sub_code : '';
                        $sub_category->name = $row->sub_category ? $row->sub_category : '';
                        $sub_category->save();
                    }

                    $item->main_category_id = $main_category->id;
                    $item->sub_category_id = $sub_category->id;
                    $item->purchase_type_id = $row->purchase_type ? $row->purchase_type : 0;

                    $last_id = 0;
                    $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $main_category->id)->where('sub_category_id', $sub_category->id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                    $last_id = $last_item ? $last_item->id : $last_id;

                    $item->code = 'IT-'.$main_category->code.'-'.$sub_category->code.sprintf('%05d', $last_id + 1);
                    $item->name = $row->name ? $row->name.' '.$row->model_no : '';
                    $item->model_no = $row->model_no ? $row->model_no : '';
                    $item->brand = $row->brand ? $row->brand : '';
                    $item->origin = '';
                    $item->unit_type_id = $row->unit_type ? $row->unit_type : 0;
                    $item->reorder_level = $row->reorder_level ? $row->reorder_level : '';
                    $item->rate = $row->rate ? $row->rate : 0;
                    $item->is_serial = $row->serial ? $row->serial : 0;
                    $item->is_warranty = $row->warranty ? $row->warranty : 0;
                    $item->is_active = 1;
                    $item->save();
                }
            });
        });
    }

    public function add_cctv_item()
    {
        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/import_cctv_item_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->name && preg_replace('/\s+/', '', $row->name) != '') {
                    $item = \App\Model\Item::where('name', $row->name.' '.$row->model_no)->where('is_delete', 0)->first();
                    $item = $item ? $item : new \App\Model\Item();

                    $main_category = \App\Model\MainItemCategory::where('name', $row->main_category)->where('is_delete', 0)->first();
                    if (! $main_category) {
                        $main_category = new \App\Model\MainItemCategory();
                        $main_category->code = $row->main_code ? $row->main_code : '';
                        $main_category->name = $row->main_category ? $row->main_category : '';
                        $main_category->save();
                    }

                    $sub_category = \App\Model\SubItemCategory::where('name', $row->sub_category)->where('is_delete', 0)->first();
                    if (! $sub_category) {
                        $sub_category = new \App\Model\SubItemCategory();
                        $sub_category->code = $row->sub_code ? $row->sub_code : '';
                        $sub_category->name = $row->sub_category ? $row->sub_category : '';
                        $sub_category->save();
                    }

                    $item->main_category_id = $main_category->id;
                    $item->sub_category_id = $sub_category->id;
                    $item->purchase_type_id = $row->purchase_type ? $row->purchase_type : 0;

                    $last_id = 0;
                    $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $main_category->id)->where('sub_category_id', $sub_category->id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                    $last_id = $last_item ? $last_item->id : $last_id;

                    $item->code = 'IT-'.$main_category->code.'-'.$sub_category->code.sprintf('%05d', $last_id + 1);
                    $item->name = $row->name ? $row->name.' '.$row->model_no : '';
                    $item->model_no = $row->model_no ? $row->model_no : '';
                    $item->brand = $row->brand ? $row->brand : '';
                    $item->origin = '';
                    $item->unit_type_id = $row->unit_type ? $row->unit_type : 0;
                    $item->reorder_level = $row->reorder_level ? $row->reorder_level : '';
                    $item->rate = $row->rate ? $row->rate : 0;
                    $item->is_serial = $row->serial ? $row->serial : 0;
                    $item->is_warranty = $row->warranty ? $row->warranty : 0;
                    $item->is_active = 1;
                    $item->save();
                }
            });
        });

        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/local_cctv_item_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->name && preg_replace('/\s+/', '', $row->name) != '') {
                    $item = \App\Model\Item::where('name', $row->name.' '.$row->model_no)->where('is_delete', 0)->first();
                    $item = $item ? $item : new \App\Model\Item();

                    $main_category = \App\Model\MainItemCategory::where('name', $row->main_category)->where('is_delete', 0)->first();
                    if (! $main_category) {
                        $main_category = new \App\Model\MainItemCategory();
                        $main_category->code = $row->main_code ? $row->main_code : '';
                        $main_category->name = $row->main_category ? $row->main_category : '';
                        $main_category->save();
                    }

                    $sub_category = \App\Model\SubItemCategory::where('name', $row->sub_category)->where('is_delete', 0)->first();
                    if (! $sub_category) {
                        $sub_category = new \App\Model\SubItemCategory();
                        $sub_category->code = $row->sub_code ? $row->sub_code : '';
                        $sub_category->name = $row->sub_category ? $row->sub_category : '';
                        $sub_category->save();
                    }

                    $item->main_category_id = $main_category->id;
                    $item->sub_category_id = $sub_category->id;
                    $item->purchase_type_id = $row->purchase_type ? $row->purchase_type : 0;

                    $last_id = 0;
                    $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $main_category->id)->where('sub_category_id', $sub_category->id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                    $last_id = $last_item ? $last_item->id : $last_id;

                    $item->code = 'IT-'.$main_category->code.'-'.$sub_category->code.sprintf('%05d', $last_id + 1);
                    $item->name = $row->name ? $row->name.' '.$row->model_no : '';
                    $item->model_no = $row->model_no ? $row->model_no : '';
                    $item->brand = $row->brand ? $row->brand : '';
                    $item->origin = '';
                    $item->unit_type_id = $row->unit_type ? $row->unit_type : 0;
                    $item->reorder_level = $row->reorder_level ? $row->reorder_level : '';
                    $item->rate = $row->rate ? $row->rate : 0;
                    $item->is_serial = $row->serial ? $row->serial : 0;
                    $item->is_warranty = $row->warranty ? $row->warranty : 0;
                    $item->is_active = 1;
                    $item->save();
                }
            });
        });
    }

    public function add_items()
    {
        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/amended_item_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->name && preg_replace('/\s+/', '', $row->name) != '') {
                    $item = \App\Model\Item::where('name', $row->name.' '.$row->model_no)->where('is_delete', 0)->first();
                    $item = $item ? $item : new \App\Model\Item();

                    $main_category = \App\Model\MainItemCategory::where('name', $row->main_category)->where('is_delete', 0)->first();
                    if (! $main_category) {
                        $main_category = new \App\Model\MainItemCategory();
                        $main_category->code = $row->main_code ? $row->main_code : '';
                        $main_category->name = $row->main_category ? $row->main_category : '';
                        $main_category->save();
                    }

                    $sub_category = \App\Model\SubItemCategory::where('name', $row->sub_category)->where('is_delete', 0)->first();
                    if (! $sub_category) {
                        $sub_category = new \App\Model\SubItemCategory();
                        $sub_category->code = $row->sub_code ? $row->sub_code : '';
                        $sub_category->name = $row->sub_category ? $row->sub_category : '';
                        $sub_category->save();
                    }

                    $item->main_category_id = $main_category->id;
                    $item->sub_category_id = $sub_category->id;
                    $item->purchase_type_id = $row->purchase_type ? $row->purchase_type : 0;

                    $last_id = 0;
                    $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $main_category->id)->where('sub_category_id', $sub_category->id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                    $last_id = $last_item ? $last_item->id : $last_id;

                    $item->code = 'IT-'.$main_category->code.'-'.$sub_category->code.sprintf('%05d', $last_id + 1);
                    $item->name = $row->name ? $row->name.' '.$row->model_no : '';
                    $item->model_no = $row->model_no ? $row->model_no : '';
                    $item->brand = $row->brand ? $row->brand : '';
                    $item->origin = '';
                    $item->unit_type_id = $row->unit_type ? $row->unit_type : 0;
                    $item->reorder_level = $row->reorder_level ? $row->reorder_level : '';
                    $item->rate = $row->rate ? $row->rate : 0;
                    $item->is_serial = $row->serial ? $row->serial : 0;
                    $item->is_warranty = $row->warranty ? $row->warranty : 0;
                    $item->is_active = 1;
                    $item->save();
                }
            });
        });
    }

    public function add_hikvision_items()
    {
        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/hikvision_item_list.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->name && preg_replace('/\s+/', '', $row->name) != '') {
                    $item = \App\Model\Item::where('name', $row->name.' '.$row->model_no)->where('is_delete', 0)->first();
                    $item = $item ? $item : new \App\Model\Item();

                    $main_category = \App\Model\MainItemCategory::where('name', $row->main_category)->where('is_delete', 0)->first();
                    if (! $main_category) {
                        $main_category = new \App\Model\MainItemCategory();
                        $main_category->code = $row->main_code ? $row->main_code : '';
                        $main_category->name = $row->main_category ? $row->main_category : '';
                        $main_category->save();
                    }

                    $sub_category = \App\Model\SubItemCategory::where('name', $row->sub_category)->where('is_delete', 0)->first();
                    if (! $sub_category) {
                        $sub_category = new \App\Model\SubItemCategory();
                        $sub_category->code = $row->sub_code ? $row->sub_code : '';
                        $sub_category->name = $row->sub_category ? $row->sub_category : '';
                        $sub_category->save();
                    }

                    $item->main_category_id = $main_category->id;
                    $item->sub_category_id = $sub_category->id;
                    $item->purchase_type_id = $row->purchase_type ? $row->purchase_type : 0;

                    $last_id = 0;
                    $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $main_category->id)->where('sub_category_id', $sub_category->id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                    $last_id = $last_item ? $last_item->id : $last_id;

                    $item->code = 'IT-'.$main_category->code.'-'.$sub_category->code.sprintf('%05d', $last_id + 1);
                    $item->name = $row->name ? $row->name.' '.$row->model_no : '';
                    $item->model_no = $row->model_no ? $row->model_no : '';
                    $item->brand = $row->brand ? $row->brand : '';
                    $item->origin = '';
                    $item->unit_type_id = $row->unit_type ? $row->unit_type : 0;
                    $item->reorder_level = $row->reorder_level ? $row->reorder_level : '';
                    $item->rate = $row->rate ? $row->rate : 0;
                    $item->is_serial = $row->serial ? $row->serial : 0;
                    $item->is_warranty = $row->warranty ? $row->warranty : 0;
                    $item->is_active = 1;
                    $item->save();
                }
            });
        });
    }

    public function price_update()
    {
        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/price_update.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->item_id && preg_replace('/\s+/', '', $row->item_id) != '') {
                    $item = \App\Model\Item::find($row->item_id);
                    if ($item) {
                        $item->rate = $row->item_rate ? $row->item_rate : 0;
                        $item->save();

                        $grn_details = \App\Model\GoodReceiveDetails::where('item_id', $item->id)
                                ->where('is_delete', 0)
                                ->get();
                        $grn_ids = [];
                        foreach ($grn_details as $grn_detail) {
                            $grn_detail->rate = $item->rate;
                            $grn_detail->save();

                            if (! in_array($grn_detail->good_receive_id, $grn_ids)) {
                                array_push($grn_ids, $grn_detail->good_receive_id);
                            }
                        }

                        foreach ($grn_ids as $grn_id) {
                            $good_receive = \App\Model\GoodReceive::find($grn_id);

                            $good_receive_details = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive->id)
                                    ->where('is_delete', 0)
                                    ->get();
                            $total_value = 0;
                            foreach ($good_receive_details as $good_receive_detail) {
                                $total_value += $good_receive_detail->rate * $good_receive_detail->quantity;
                            }

                            foreach ($good_receive->PurchaseOrder->Contact->ContactTax as $detail) {
                                if ($detail['CTaxType']) {
                                    if ($detail['CTaxType']['id'] == 1 || $detail['CTaxType']['id'] == 3) {
                                        $total_value += $total_value * $detail['CTaxType']['percentage'] / 100;
                                    }
                                }
                            }

                            $good_receive->good_receive_value = $total_value;
                            $good_receive->save();
                        }

                        $item_issue_details = \App\Model\ItemIssueDetails::where('item_id', $item->id)
                                ->where('is_delete', 0)
                                ->get();
                        $item_issue_ids = [];
                        foreach ($item_issue_details as $item_issue_detail) {
                            if (! in_array($item_issue_detail->item_issue_id, $item_issue_ids)) {
                                array_push($item_issue_ids, $item_issue_detail->item_issue_id);
                            }
                        }

                        foreach ($item_issue_ids as $item_issue_id) {
                            $item_issue = \App\Model\ItemIssue::find($item_issue_id);
                            $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($item_issue) {
                                $query->where('item_issue_id', $item_issue->id)->where('is_delete', 0);
                            })
                                    ->where('is_delete', 0)
                                    ->get();
                            $total_value = 0;
                            foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                                $total_value += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                            }
                            $item_issue->item_issue_value = $total_value;
                            $item_issue->save();
                        }

                        $job_card_details = \App\Model\JobCardDetails::where('item_id', $item->id)
                                ->where('is_delete', 0)
                                ->get();
                        $job_card_ids = [];
                        foreach ($job_card_details as $job_card_detail) {
                            $job_card_detail->rate = $item->rate;
                            $job_card_detail->save();

                            if (! in_array($job_card_detail->job_card_id, $job_card_ids)) {
                                array_push($job_card_ids, $job_card_detail->job_card_id);
                            }
                        }

                        foreach ($job_card_ids as $job_card_id) {
                            $job_card = \App\Model\JobCard::find($job_card_id);
                            $job_card_details = \App\Model\JobCardDetails::where('job_card_id', $job_card->id)
                                    ->where('is_delete', 0)
                                    ->get();
                            $total_value = 0;
                            foreach ($job_card_details as $job_card_detail) {
                                $margin = ($job_card_detail->margin + 100) / 100;
                                $total_value += $job_card_detail->rate * $margin * $job_card_detail->quantity;
                            }
                            $job_card->job_card_value = $total_value;
                            $job_card->save();
                        }

                        $installation_sheet_details = \App\Model\InstallationSheetDetails::where('item_id', $item->id)
                                ->where('is_delete', 0)
                                ->get();
                        $installation_sheet_ids = [];
                        foreach ($installation_sheet_details as $installation_sheet_detail) {
                            $installation_sheet_detail->rate = $item->rate;
                            $installation_sheet_detail->save();

                            if (! in_array($installation_sheet_detail->installation_sheet_id, $installation_sheet_ids)) {
                                array_push($installation_sheet_ids, $installation_sheet_detail->installation_sheet_id);
                            }
                        }

                        foreach ($installation_sheet_ids as $installation_sheet_id) {
                            $installation_sheet = \App\Model\InstallationSheet::find($installation_sheet_id);
                            $installation_sheet_details = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $installation_sheet->id)
                                    ->where('is_delete', 0)
                                    ->get();
                            $total_value = 0;
                            foreach ($installation_sheet_details as $installation_sheet_detail) {
                                $total_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
                            }
                            $installation_sheet->installation_sheet_value = $total_value;
                            $installation_sheet->save();
                        }

                        $quotation_job_card_details = \App\Model\QuotationJobCardDetails::where('item_id', $item->id)
                                ->where('is_delete', 0)
                                ->get();
                        $quotation_ids = [];
                        foreach ($quotation_job_card_details as $quotation_job_card_detail) {
                            $quotation_job_card_detail->rate = $item->rate;
                            $quotation_job_card_detail->save();

                            if (! in_array($quotation_job_card_detail->QuotationJobCard->quotation_id, $quotation_ids)) {
                                array_push($quotation_ids, $quotation_job_card_detail->QuotationJobCard->quotation_id);
                            }
                        }

                        foreach ($quotation_ids as $quotation_id) {
                            $quotation = \App\Model\Quotation::find($quotation_id);

                            $equipment_installation_total = 0;

                            $job_card_ids = [];
                            foreach ($quotation->QuotationJobCard as $detail) {
                                array_push($job_card_ids, $detail['id']);
                            }
                            $cost_sheet_ids = [];
                            foreach ($quotation->QuotationCostSheet as $detail) {
                                array_push($cost_sheet_ids, $detail['id']);
                            }

                            $usd = false;
                            $usd_rate = 0;
                            if ($quotation->is_currency == 0) {
                                $usd = true;
                                $usd_rate = $quotation->usd_rate;
                            }

                            $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                                    ->where('is_delete', 0)
                                    ->get();
                            foreach ($job_card_details as $job_card_detail) {
                                $margin = ($job_card_detail->margin + 100) / 100;
                                $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity) / $usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
                                $equipment_installation_total += $value;
                            }

                            $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                                    ->where('is_delete', 0)
                                    ->get();
                            $rate_ids = [];
                            foreach ($cost_sheet_details as $main_cost_sheet_detail) {
                                if ($main_cost_sheet_detail->InstallationRate && ! in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                                    $meters = 0;
                                    foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                                        if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                                            $meters += $sub_cost_sheet_detail->meters;
                                        }
                                    }
                                    $installation_rate = $usd ? $main_cost_sheet_detail->InstallationRate->rate / $usd_rate : $main_cost_sheet_detail->InstallationRate->rate;
                                    $equipment_installation_total += $installation_rate * $meters;

                                    array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                                }
                            }

                            $manday_rate = \App\Model\Rate::find(1);
                            foreach ($cost_sheet_details as $cost_sheet_detail) {
                                $equipment_installation_total += $usd ? $cost_sheet_detail->excavation_work / $usd_rate : $cost_sheet_detail->excavation_work;
                                $equipment_installation_total += $usd ? ($cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value)) / $usd_rate : $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                                $equipment_installation_total += $usd ? $cost_sheet_detail->food / $usd_rate : $cost_sheet_detail->food;
                                $equipment_installation_total += $usd ? $cost_sheet_detail->accommodation / $usd_rate : $cost_sheet_detail->accommodation;
                                $equipment_installation_total += $usd ? $cost_sheet_detail->bata / $usd_rate : $cost_sheet_detail->bata;
                                $equipment_installation_total += $usd ? $cost_sheet_detail->other_expenses / $usd_rate : $cost_sheet_detail->other_expenses;
                            }

                            foreach ($quotation->QuotationDiscount as $detail) {
                                $equipment_installation_total -= $equipment_installation_total * $detail['percentage'] / 100;
                            }

                            $nbt_exist = $svat_exist = $vat_exist = false;
                            $nbt_description = $svat_description = $vat_description = '';
                            $nbt_percentage = $svat_percentage = $vat_percentage = 0;
                            foreach ($quotation->Inquiry->Contact->ContactTax as $detail) {
                                if ($detail['CTaxType']) {
                                    $equipment_installation_total += $equipment_installation_total * $detail['CTaxType']['percentage'] / 100;
                                }
                            }

                            $quotation->quotation_value = $equipment_installation_total;
                            $quotation->save();

                            $total_job_value = 0;
                            $confirmed_quotations = \App\Model\Quotation::where('inquiry_id', $quotation->inquiry_id)
                                    ->where('is_confirmed', 1)
                                    ->where('is_revised', 0)
                                    ->where('is_delete', 0)
                                    ->get();
                            foreach ($confirmed_quotations as $confirmed_quotation) {
                                $total_job_value += $confirmed_quotation->quotation_value;
                            }

                            $job = \App\Model\Job::where('inquiry_id', $quotation->inquiry_id)->where('is_delete', 0)->first();
                            if ($job) {
                                $job->job_value = $total_job_value;
                                $job->save();
                            }
                        }
                    }
                }
            });
        });
    }

    public function update_item_details()
    {
        \Maatwebsite\Excel\Facades\Excel::load($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/uploads/update_item_details.xlsx', function ($reader) {
            $reader->each(function ($row) {
                if ($row->id && preg_replace('/\s+/', '', $row->id) != '') {
                    $item = \App\Model\Item::find($row->id);
                    if ($item) {
                        $item->name = $row->name ? $row->name : '';
                        $item->model_no = $row->model_no ? $row->model_no : '';
                        $item->brand = $row->brand ? $row->brand : '';
                        $item->origin = $row->origin ? $row->origin : '';
                        $item->save();
                    }
                }
            });
        });
    }
}
