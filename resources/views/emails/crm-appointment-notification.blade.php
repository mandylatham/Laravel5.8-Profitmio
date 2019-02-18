<?adf version="1.0"?>
<?xml version="1.0"?>
<adf>
    <prospect>
        <requestdate>{{ date(DATE_ATOM, time()) }}</requestdate>
        <vehicle>
            <year>{{ $appointment->auto_year }}</year>
            <make>{{ $appointment->make }}</make>
            <model>{{ $appointment->model }}</model>
        </vehicle>
        <customer>
            <contact>
                <name part="first">{{ $appointment->first_name }}</name>
                <name part="last">{{ $appointment->last_name }}</name>
                <email>{{ $appointment->email }}</email>
                <phone type="voice">{{ $appointment->phone_number }}</phone>
                <phone type="voice">{{ $appointment->alt_phone_number }}</phone>
                <address type="home">
                    <street line="1">{{ $appointment->address }}</street>
                    <apartment/>
                    <city>{{ $appointment->city }}</city>
                    <regioncode>{{ $appointment->state }}</regioncode>
                    <postalcode>{{ $appointment->zip }}</postalcode>
                </address>
            </contact>
            <comments>The contact, {{ $appointment->first_name }} {{ $appointment->last_name }} has called to request an appointment at {{ $appointment->appointment_at }}</comments>
        </customer>
        <vendor>
            <vendorname>{{ $campaign->agency->organization }}</vendorname>
            <contact primarycontact="1">
                <name part="full">{{ $campaign->agency->organization }}</name>
            </contact>
        </vendor>
        <provider>
            <id source="Profit Miner"></id>
            <name>Profit Miner</name>
        </provider>
    </prospect>
</adf>
