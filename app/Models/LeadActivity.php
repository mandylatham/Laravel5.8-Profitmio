<?php

namespace App\Models;

class LeadActivity
{
    const OPENED = 'opened';
    const CLOSED = 'closed';
    const VIEWED = 'viewed';
    const SENTSMS = 'sent sms';
    const REOPENED = 'reopened';
    const MARKETED = 'sent marketing';
    const SENTEMAIL = 'sent email';
    const SENTTOCRM = 'sent lead to the crm';
    const CHECKED_IN = 'checked in';
    const CALLEDBACK = 'logged call back';
    const UPDATEDNOTES = 'updated the notes';
    const SENTTOSERVICE = 'sent lead to the service department';
    const ADDEDAPPOINTMENT = 'added appointment';
}
