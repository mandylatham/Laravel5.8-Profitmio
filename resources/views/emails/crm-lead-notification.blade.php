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
                <name part="full">{{ $appointment->first_name }} {{ $appointment->last_name }}</name>
                <email>{{ $appointment->email }}</email>
                <phone type="voice">{{ $appointment->phone_number }}</phone>
                <phone type="voice">{{ $appointment->alt_phone_number }}</phone>
                <address type="home">
                    <street linke="1">{{ $appointment->address }}</street>
                    <street linke="2" />
                    <city>{{ $appointment->city }}</city>
                    <regioncode />
                    <postalcode>{{ $appointment->zip }}</postalcode>
                </address>
            </contact>
        </customer>
        <vendor>
            <contact>
                <name part="full">{{ $campaign->agency->organization }}</name>
            </contact>
        </vendor>
        <provider>
            <id source="Profit Miner"></id>
            <name>Profit Miner</name>
        </provider>
    </prospect>
</adf>
