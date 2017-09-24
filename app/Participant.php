<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $table    = 'participants';
    protected $fillable = ['user_id','start_date','end_date', 'email', 'event_id', 'qty','occurence', 'status'];

    public function User(){
	return $this->hasOne(User::class);
	}

	public function event(){
	return $this->belongsTo(Event::class);
	}

	public function TransactionDetail(){
	return $this->belongsToMany(TransactionDetail::class);
	}

	public function QueuedMessageModel(){
		return $this->hasMany(QueuedMessageModel::class);
	}

	public function recurringEndDate($start_date,$date,$recurring,$occurence){
		//$date = Carbon::parse($date);
		// /dd($date);
		if($date == '0000-00-00 00:00:00'){
			return $this->recurringDate($start_date,$date,$recurring,$occurence);
        }elseif($date != '0000-00-00 00:00:00'){
        	return $this->parseDate($date)->format('n/j/Y');
        }
	}

	public function recurringDate($start_date,$date,$recurring,$occurence){
		//$start_date = $this->parseDate($start_date);
		//dd($start_date);
			# code...
			switch ($recurring) {
				case 0://single
					 $no_of_occurence_end_date = '-';
				break;
                case 1://weekly    
                	 $no_of_occurence_end_date = $this->parseDate($start_date)->addWeek($occurence-1)->format('n/j/Y');
                	 //dd($no_of_occurence_end_date);
                break;
                case 2://monthly
                	 $no_of_occurence_end_date = $this->parseDate($start_date)->addMonth($occurence-1)->format('n/j/Y');
                break;
                case 3://yearly
                	 $no_of_occurence_end_date = $this->parseDate($start_date)->addYear($occurence-1)->format('n/j/Y');
                break;

            }		
            // /dd($no_of_occurence_end_date,$occurence,$start_date);
            
           return $no_of_occurence_end_date;
	}
	public function parseDate($date){
		return Carbon::parse($date);
	}
}