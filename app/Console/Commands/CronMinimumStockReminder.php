<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CronMinimumStockReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:minimumStockReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send minimum stock reminder once a day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $order_details = array();
        
        $items = \App\Model\Item::where('reorder_level', '!=', 0)                
                ->where('is_active', 1)
                ->where('is_delete', 0)
                ->get();
        foreach ($items as $item){
            $to_be_stock = $item->reorder_level * 1.5;
            if($item->reorder_level != 0 && $item->stock < $to_be_stock){
                $row = array(
                    'purchase_type' => $item->PurchaseType->name,
                    'code' => $item->code,
                    'name' => $item->name,
                    'model_no' => $item->model_no,
                    'unit_type' => $item->UnitType->code,
                    'reorder_level' => $item->reorder_level,
                    'current_stock' => $item->stock,
                    'to_be_order' => $to_be_stock - $item->stock
                );
                array_push($order_details, $row);
            }
        }
        
        $data = array(
            'details' => $order_details
        );

        Mail::send('emails.minimum_stock_details', $data, function($message){
            $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
            $message->to('procurement@m3force.com', 'Deepal Gunasekera');
            $message->to('stores@m3force.com', 'Stores Assistant');
            $message->subject('Minimum Stock Reminder');
        });
    }
}
