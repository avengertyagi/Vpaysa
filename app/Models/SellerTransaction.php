<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerTransaction extends Model
{
    use HasFactory;
    protected $table = "seller_transaction";
    protected $guarded = ['id'];
    protected $fillable = [
        'creator_id', 'joiner_id', 'title', 'is_project', 'short_description', 'specification', 'deal_condition', 'file', 'amount', 'is_broker', 'brokrage_fee', 'payment_type', 'who_pay', 'invite_buyer', 'deal_link', 'deal_code', 'status', 'payment_status', 'resolved_by', 'resolved_at', 'invoice', 'report', 'reported_by', 'favor_id', 'charge', 'buyer_invite_type', 'buyer_invite_value', 'broker_invite_type', 'broker_invite_value', 'broker_charge'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
    public function invitee()
    {
        return $this->belongsTo(User::class, 'joiner_id');
    }
}
