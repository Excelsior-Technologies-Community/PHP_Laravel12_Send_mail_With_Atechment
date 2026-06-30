<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailHistory;
use App\Mail\SendEmailWithAttachment;
use Illuminate\Support\Facades\Mail;

class SendScheduledEmails extends Command
{
    protected $signature = 'email:send-scheduled';

    protected $description = 'Send scheduled emails';


    public function handle()
    {

        $emails = EmailHistory::where('status','Pending')
            ->where('scheduled_at','<=',now())
            ->get();


        if($emails->count() == 0)
        {
            $this->info('No scheduled emails found.');
            return;
        }


        foreach($emails as $email)
        {

            try
            {

                $attachmentPath = null;


                if($email->attachment)
                {
                    $attachmentPath = storage_path(
                        'app/public/temp_attachments/'.$email->attachment
                    );
                }


                Mail::to($email->email)
                    ->send(
                        new SendEmailWithAttachment(
                            $email->subject,
                            $email->message,
                            $attachmentPath,
                            $email->attachment
                        )
                    );


                $email->update([

                    'status'=>'Sent',
                    'sent_at'=>now()

                ]);


                $this->info(
                    "Email sent: ".$email->email
                );


            }
            catch(\Exception $e)
            {


                $email->update([

                    'status'=>'Failed'

                ]);


                $this->error(
                    $e->getMessage()
                );


            }

        }

    }
}